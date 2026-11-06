<?php

namespace Modules\Player\Models;

use CodeIgniter\Model;

/**
 * Merge two player rows into one canonical id: repoint every child-table FK to the
 * kept id, then soft-delete the dropped row. Runs inside a single transaction —
 * any failure rolls the whole merge back. Never hard-deletes a player row.
 *
 * Table classification is verified against the live schema (docs/DEDUP_table_map.md,
 * 2026-06-11). Re-verify with tools/dedup/discover_player_refs.php after any migration.
 */
class PlayerMergeModel extends Model
{
    /** Child tables with no UNIQUE over the player column — repoint player_id directly. */
    private const SIMPLE_TABLES = [
        'idp_ai_analyses',
        'idp_assessment_results',
        'idp_plans',
        'logs_player',
        'player_match_state',
        'player_physical_history',
        'player_to_match',
        'tournament_match_line_up',
        'tournament_mvp_players',
        'tournament_submitted_players',
    ];

    /**
     * Tables with a UNIQUE index covering the player column: repointing the dropped id
     * could collide with a row the canonical already owns. Strategy: UPDATE IGNORE the
     * non-colliding rows over, then DELETE whatever leftovers still point at the drop.
     * Map: table => player-referencing column (NOT always 'player_id').
     */
    private const UNIQUE_SIBLING_TABLES = [
        'academy_assessment_results'     => 'player_id',
        'academy_training_attendance'    => 'player_id',
        'academy_training_group_players' => 'player_id',
        'player_edit_permissions'        => 'player_id',
        // focal_player_id is the external Focal-system id, NOT jootball.player.id — never touch it.
        'mapping_glo_player'             => 'jootball_player_id',
    ];

    /** Player columns to backfill onto the canonical when it is empty and the drop has a value. */
    private const FILL_FIELDS = [
        'id_card_image', 'image_path', 'birth_date', 'nation', 'sex',
        'province_id', 'amphure_id', 'tambon_id', 'height', 'weight',
        'dominant_foot', 'nick_name', 'contract', 'contract_start',
        'contract_end', 'contract_note',
    ];

    /**
     * @return array<string,array{updated:int,deleted:int}> moved summary per table
     * @throws \RuntimeException on validation failure (no data touched) or merge error (rolled back)
     */
    public function merge(int $keepId, int $dropId, string $source = 'admin', ?int $adminUserId = null): array
    {
        if ($keepId === $dropId) {
            throw new \RuntimeException('keep_id กับ drop_id เป็นตัวเดียวกัน');
        }

        $db   = $this->db;
        $keep = $db->table('player')->where('id', $keepId)->get()->getRowArray();
        $drop = $db->table('player')->where('id', $dropId)->get()->getRowArray();

        if (!$keep || !$drop) {
            throw new \RuntimeException('ไม่พบ player id ที่ระบุ');
        }
        if (!empty($drop['merged_into_id'])) {
            throw new \RuntimeException("player {$dropId} ถูก merge ไปแล้ว (merged_into_id={$drop['merged_into_id']})");
        }
        if (!empty($keep['merged_into_id'])) {
            throw new \RuntimeException("player {$keepId} เป็นตัวที่ถูกยุบไปแล้ว ใช้เป็น canonical ไม่ได้");
        }

        $moved = [];
        $db->transBegin();
        try {
            // 1) enrollments — field-merge (uq_player_academy_team; เบอร์/ตำแหน่ง/is_primary ห้ามหาย)
            $moved['academy_player_enrollments'] = $this->mergeEnrollments($keepId, $dropId);

            // 2) UNIQUE-sibling tables — UPDATE IGNORE col over, DELETE leftovers
            foreach (self::UNIQUE_SIBLING_TABLES as $table => $col) {
                $db->query("UPDATE IGNORE `{$table}` SET `{$col}` = ? WHERE `{$col}` = ?", [$keepId, $dropId]);
                $updated = $db->affectedRows();
                $db->query("DELETE FROM `{$table}` WHERE `{$col}` = ?", [$dropId]);
                $deleted = $db->affectedRows();
                $moved[$table] = ['updated' => $updated, 'deleted' => $deleted];
            }

            // 3) simple repoint
            foreach (self::SIMPLE_TABLES as $table) {
                $db->query("UPDATE `{$table}` SET player_id = ? WHERE player_id = ?", [$keepId, $dropId]);
                $moved[$table] = ['updated' => $db->affectedRows(), 'deleted' => 0];
            }

            // 3b) remap team_player_chart_selection.player_ids — JSON array, not an FK column,
            //     so the FK repoints above miss it. Rewrite dropId -> keepId and de-dup.
            $moved['team_player_chart_selection'] = ['updated' => $this->remapChartSelections($keepId, $dropId), 'deleted' => 0];

            // 4) backfill empty canonical fields from the dropped row (never override existing)
            $fill = [];
            foreach (self::FILL_FIELDS as $f) {
                $keepEmpty = ($keep[$f] === null || $keep[$f] === '');
                $dropHas   = ($drop[$f] !== null && $drop[$f] !== '');
                if ($keepEmpty && $dropHas) {
                    $fill[$f] = $drop[$f];
                }
            }
            if ($fill) {
                $fill['update_date'] = date('Y-m-d H:i:s');
                $db->table('player')->where('id', $keepId)->update($fill);
            }

            // 5) soft-delete the dropped row + release its id_card (clears the way for UNIQUE in Task 8)
            $db->table('player')->where('id', $dropId)->update([
                'is_active'      => 0,
                'merged_into_id' => $keepId,
                'id_card'        => null,
                'update_date'    => date('Y-m-d H:i:s'),
            ]);

            // 6) audit log
            $db->table('logs_player_merge')->insert([
                'keep_id'         => $keepId,
                'drop_id'         => $dropId,
                'moved_summary'   => json_encode($moved, JSON_UNESCAPED_UNICODE),
                'dropped_id_card' => $drop['id_card'],
                'source'          => $source,
                'admin_user_id'   => $adminUserId,
                'created_at'      => date('Y-m-d H:i:s'),
            ]);

            if ($db->transStatus() === false) {
                throw new \RuntimeException('transaction status = false');
            }
            $db->transCommit();
        } catch (\Throwable $e) {
            $db->transRollback();
            throw new \RuntimeException("merge {$dropId} -> {$keepId} ล้มเหลว: " . $e->getMessage(), 0, $e);
        }

        return $moved;
    }

    /**
     * Enrollments carry uq(player_id, team_id). If the canonical already has an enrollment
     * for the same team, merge the fields (fill empty shirt/position, OR the active/primary
     * flags) and delete the duplicate; otherwise just repoint it.
     *
     * @return array{updated:int,deleted:int}
     */
    private function mergeEnrollments(int $keepId, int $dropId): array
    {
        $db       = $this->db;
        $dropRows = $db->table('academy_player_enrollments')->where('player_id', $dropId)->get()->getResultArray();
        $updated  = 0;
        $deleted  = 0;

        foreach ($dropRows as $row) {
            // match ตาม unique key uq_player_academy_team (player_id, academy_id, team_id)
            // ต้องรวม academy_id ด้วย ไม่งั้น team_id=0 คนละ academy จะถูกยุบรวมผิด
            $existing = $db->table('academy_player_enrollments')
                ->where('player_id', $keepId)
                ->where('academy_id', $row['academy_id'])
                ->where('team_id', $row['team_id'])
                ->get()->getRowArray();

            if (!$existing) {
                $db->table('academy_player_enrollments')->where('id', $row['id'])->update(['player_id' => $keepId]);
                $updated++;
                continue;
            }

            // same team on both — fold the dropped enrollment into the canonical's
            $patch = [];
            foreach (['shirt_number', 'position_id', 'sub_position_ids', 'shirt_size'] as $f) {
                $existingEmpty = ($existing[$f] === null || $existing[$f] === '');
                $rowHas        = ($row[$f] !== null && $row[$f] !== '');
                if ($existingEmpty && $rowHas) {
                    $patch[$f] = $row[$f];
                }
            }
            if ((int) $row['is_primary'] === 1 && (int) $existing['is_primary'] !== 1) {
                $patch['is_primary'] = 1;
            }
            if ((int) $row['is_active'] === 1 && (int) $existing['is_active'] !== 1) {
                $patch['is_active'] = 1;
            }
            if ($patch) {
                $db->table('academy_player_enrollments')->where('id', $existing['id'])->update($patch);
            }
            $db->table('academy_player_enrollments')->where('id', $row['id'])->delete();
            $deleted++;
        }

        // a player may hold at most one primary enrollment — keep the legacy-team one, demote the rest
        // (enrollments has no updated_at; tiebreak on enrolled_at then id)
        $primaries = $db->query(
            "SELECT id FROM academy_player_enrollments
             WHERE player_id = ? AND is_primary = 1
             ORDER BY (team_id = (SELECT team_id FROM player WHERE id = ?)) DESC, enrolled_at DESC, id DESC",
            [$keepId, $keepId]
        )->getResultArray();
        foreach (array_slice($primaries, 1) as $p) {
            $db->table('academy_player_enrollments')->where('id', $p['id'])->update(['is_primary' => 0]);
        }

        return ['updated' => $updated, 'deleted' => $deleted];
    }

    /**
     * Rewrite dropId -> keepId inside any team_player_chart_selection.player_ids JSON array
     * (and de-dup). Returns the number of selection rows changed.
     */
    private function remapChartSelections(int $keepId, int $dropId): int
    {
        $db   = $this->db;
        $rows = $db->table('team_player_chart_selection')
            ->select('team_id, player_ids')
            ->like('player_ids', (string) $dropId)
            ->get()->getResultArray();

        $changed = 0;
        foreach ($rows as $row) {
            $ids = json_decode($row['player_ids'], true);
            if (!is_array($ids)) {
                continue;
            }
            $new   = [];
            $dirty = false;
            foreach ($ids as $id) {
                $id = (int) $id;
                if ($id === $dropId) {
                    $id    = $keepId;
                    $dirty = true;
                }
                if (!in_array($id, $new, true)) {
                    $new[] = $id;
                }
            }
            if ($dirty) {
                $db->table('team_player_chart_selection')
                    ->where('team_id', $row['team_id'])
                    ->update(['player_ids' => json_encode($new)]);
                $changed++;
            }
        }
        return $changed;
    }
}
