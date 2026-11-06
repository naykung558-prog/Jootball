<?php

namespace Modules\Admin\Models;

use CodeIgniter\Model;

/**
 * Read-side queries for the admin player-merge UI: surface the duplicate groups that
 * bulk auto-merge intentionally left for human review, and assemble the side-by-side
 * compare data. The actual merge is done by Modules\Player\Models\PlayerMergeModel.
 *
 * Two duplicate signals (both on ACTIVE, non-merged players):
 *   - id_card : same normalized id_card (these are the invalid-checksum dups auto-merge skipped)
 *   - name    : same first+last name AND same birth_date
 */
class PlayerDedupModel extends Model
{
    /** child table => player-referencing column (mirror of PlayerMergeModel) */
    private const CHILD_TABLES = [
        'academy_player_enrollments'     => 'player_id',
        'academy_assessment_results'     => 'player_id',
        'academy_training_attendance'    => 'player_id',
        'academy_training_group_players' => 'player_id',
        'player_edit_permissions'        => 'player_id',
        'mapping_glo_player'             => 'jootball_player_id',
        'idp_ai_analyses'                => 'player_id',
        'idp_assessment_results'         => 'player_id',
        'idp_plans'                      => 'player_id',
        'logs_player'                    => 'player_id',
        'player_match_state'             => 'player_id',
        'player_physical_history'        => 'player_id',
        'player_to_match'                => 'player_id',
        'tournament_match_line_up'       => 'player_id',
        'tournament_mvp_players'         => 'player_id',
        'tournament_submitted_players'   => 'player_id',
    ];

    /**
     * Duplicate groups for the list view.
     * @return array<int,array{type:string,key:string,label:string,member_count:int,sample:string}>
     */
    public function groups(): array
    {
        $db  = $this->db;
        $out = [];

        // id_card dups (normalized) — residual after auto-merge
        $idRows = $db->query(
            "SELECT REPLACE(REPLACE(id_card,'-',''),' ','') AS k,
                    COUNT(*) AS c,
                    GROUP_CONCAT(CONCAT(first_name,' ',last_name) SEPARATOR ', ') AS names
             FROM player
             WHERE is_active = 1 AND merged_into_id IS NULL
               AND id_card IS NOT NULL
               AND REPLACE(REPLACE(id_card,'-',''),' ','') REGEXP '^[0-9]{13}$'
             GROUP BY k HAVING c > 1
             ORDER BY c DESC"
        )->getResultArray();
        foreach ($idRows as $r) {
            $out[] = [
                'type'         => 'id_card',
                'key'          => $r['k'],
                'label'        => $r['k'],
                'member_count' => (int) $r['c'],
                'sample'       => $r['names'],
            ];
        }

        // name + birth_date dups
        $nameRows = $db->query(
            "SELECT LOWER(TRIM(first_name)) AS f, LOWER(TRIM(last_name)) AS l, birth_date AS b,
                    COUNT(*) AS c,
                    GROUP_CONCAT(CONCAT(first_name,' ',last_name) SEPARATOR ', ') AS names
             FROM player
             WHERE is_active = 1 AND merged_into_id IS NULL
               AND first_name <> '' AND last_name <> '' AND birth_date IS NOT NULL
             GROUP BY f, l, b HAVING c > 1
             ORDER BY c DESC"
        )->getResultArray();
        foreach ($nameRows as $r) {
            $out[] = [
                'type'         => 'name',
                'key'          => $r['f'] . '|' . $r['l'] . '|' . $r['b'],
                'label'        => $r['names'],
                'member_count' => (int) $r['c'],
                'sample'       => 'เกิด ' . $r['b'],
            ];
        }

        return $out;
    }

    /**
     * Members of one duplicate group, with full data + child-row counts + enrollment flags,
     * for the compare view. Returns [] if the group no longer has >1 member.
     * @return array<int,array<string,mixed>>
     */
    public function members(string $type, string $key): array
    {
        $db = $this->db;
        if ($type === 'id_card') {
            $players = $db->query(
                "SELECT * FROM player
                 WHERE is_active = 1 AND merged_into_id IS NULL
                   AND REPLACE(REPLACE(id_card,'-',''),' ','') = ?
                 ORDER BY id",
                [$key]
            )->getResultArray();
        } else {
            [$f, $l, $b] = array_pad(explode('|', $key, 3), 3, '');
            $players = $db->query(
                "SELECT * FROM player
                 WHERE is_active = 1 AND merged_into_id IS NULL
                   AND LOWER(TRIM(first_name)) = ? AND LOWER(TRIM(last_name)) = ? AND birth_date = ?
                 ORDER BY id",
                [$f, $l, $b]
            )->getResultArray();
        }

        foreach ($players as &$p) {
            $p['child_total']    = $this->childTotal((int) $p['id']);
            $p['has_primary']    = $this->hasActivePrimary((int) $p['id']);
            $p['team_names']     = $this->teamNames((int) $p['id']);
        }
        return $players;
    }

    private function childTotal(int $pid): int
    {
        $t = 0;
        foreach (self::CHILD_TABLES as $table => $col) {
            $t += (int) $this->db->table($table)->where($col, $pid)->countAllResults();
        }
        return $t;
    }

    private function hasActivePrimary(int $pid): bool
    {
        return (int) $this->db->table('academy_player_enrollments')
            ->where('player_id', $pid)->where('is_primary', 1)->where('is_active', 1)
            ->countAllResults() > 0;
    }

    private function teamNames(int $pid): string
    {
        $rows = $this->db->query(
            "SELECT DISTINCT t.name
             FROM academy_player_enrollments e
             JOIN team t ON t.id = e.team_id
             WHERE e.player_id = ? AND e.is_active = 1",
            [$pid]
        )->getResultArray();
        return implode(', ', array_column($rows, 'name'));
    }
}
