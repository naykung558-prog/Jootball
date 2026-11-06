<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Modules\Player\Models\PlayerMergeModel;

/**
 * Bulk auto-merge of high-confidence duplicate players: active rows whose id_card is a
 * VALID Thai national id (13 digits + mod-11, not a repeated-digit placeholder) and
 * normalizes to the same value. Placeholder / name-only dups are NOT touched here —
 * those go to the admin merge UI for human review.
 *
 * Canonical (kept) id is academy-anchored:
 *   1. has an active + primary enrollment
 *   2. then has any active enrollment
 *   3. then more child-table history
 *   4. then lowest id
 *
 *   php spark dedup:bulk                 # dry-run (default), writes CSV
 *   php spark dedup:bulk --limit 50      # dry-run, first 50 groups
 *   php spark dedup:bulk --execute       # apply merges
 *   php spark dedup:bulk --execute --limit 50
 */
class DedupBulk extends BaseCommand
{
    protected $group       = 'Dedup';
    protected $name        = 'dedup:bulk';
    protected $description = 'Bulk-merge duplicate players sharing a valid id_card (academy-anchored canonical).';
    protected $usage       = 'dedup:bulk [--execute] [--limit N]';

    /** table => player-referencing column (matches PlayerMergeModel classification) */
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

    public function run(array $params)
    {
        helper('id_card');
        $execute = CLI::getOption('execute') !== null;
        $limit   = CLI::getOption('limit') !== null ? (int) CLI::getOption('limit') : 0;
        $db      = db_connect();

        // 1) load active players with a candidate id_card; group by VALID normalized id_card
        $rows = $db->query(
            "SELECT id, first_name, last_name, id_card, team_id
             FROM player
             WHERE is_active = 1 AND merged_into_id IS NULL
               AND id_card IS NOT NULL AND id_card NOT IN ('', '0', '1')"
        )->getResultArray();

        $groups = [];
        foreach ($rows as $r) {
            if (!id_card_is_valid($r['id_card'])) {
                continue;
            }
            $norm = id_card_normalize($r['id_card']);
            $groups[$norm][] = $r;
        }
        $groups = array_filter($groups, fn($g) => count($g) > 1);

        CLI::write('valid-id_card duplicate groups: ' . count($groups), 'yellow');
        if (!$groups) {
            CLI::write('nothing to merge.');
            return;
        }

        // 2) batch signals for every candidate id
        $ids = [];
        foreach ($groups as $g) {
            foreach ($g as $r) {
                $ids[] = (int) $r['id'];
            }
        }
        $enr   = $this->enrollmentSignals($db, $ids);   // id => ['primary'=>bool,'active'=>bool]
        $child = $this->childTotals($db, $ids);          // id => total child rows

        // 3) pick canonical per group + build plan
        $plan = [];
        foreach ($groups as $norm => $g) {
            usort($g, function ($a, $b) use ($enr, $child) {
                $sa = $this->score((int) $a['id'], $enr, $child);
                $sb = $this->score((int) $b['id'], $enr, $child);
                return $sb <=> $sa; // highest score first = canonical
            });
            $keep  = $g[0];
            $drops = array_slice($g, 1);
            $plan[] = ['norm' => $norm, 'keep' => $keep, 'drops' => $drops];
        }

        if ($limit > 0) {
            $plan = array_slice($plan, 0, $limit);
        }

        // 4) dry-run CSV or execute
        if (!$execute) {
            $csv = WRITEPATH . 'dedup_dryrun_' . date('Ymd_His') . '.csv';
            $fh  = fopen($csv, 'w');
            fputcsv($fh, ['id_card_norm', 'keep_id', 'keep_name', 'drop_ids', 'drop_count', 'keep_child_rows']);
            foreach ($plan as $p) {
                fputcsv($fh, [
                    $p['norm'],
                    $p['keep']['id'],
                    trim($p['keep']['first_name'] . ' ' . $p['keep']['last_name']),
                    implode('|', array_column($p['drops'], 'id')),
                    count($p['drops']),
                    $child[(int) $p['keep']['id']] ?? 0,
                ]);
            }
            fclose($fh);
            $totalDrops = array_sum(array_map(fn($p) => count($p['drops']), $plan));
            CLI::write("DRY-RUN: {$totalDrops} player(s) จะถูกยุบ ใน " . count($plan) . " กลุ่ม", 'green');
            CLI::write("CSV: {$csv}");
            CLI::write('ตรวจ CSV แล้วรันซ้ำด้วย --execute เพื่อ apply', 'yellow');
            return;
        }

        $model    = new PlayerMergeModel();
        $merged   = 0;
        $skipped  = 0;
        $groupsDone = 0;
        foreach ($plan as $p) {
            $keepId = (int) $p['keep']['id'];
            foreach ($p['drops'] as $d) {
                $dropId = (int) $d['id'];
                try {
                    $model->merge($keepId, $dropId, 'bulk', null);
                    $merged++;
                } catch (\Throwable $e) {
                    $skipped++;
                    CLI::write("  SKIP {$dropId}->{$keepId}: " . $e->getMessage(), 'red');
                }
            }
            $groupsDone++;
            if ($groupsDone % 100 === 0) {
                CLI::write("  ... {$groupsDone}/" . count($plan) . " groups, merged={$merged}");
            }
        }
        CLI::write("EXECUTE DONE: groups={$groupsDone} merged={$merged} skipped={$skipped}", 'green');
    }

    /** higher = better canonical (academy anchor first) */
    private function score(int $id, array $enr, array $child): float
    {
        $s = 0.0;
        if (!empty($enr[$id]['primary'])) {
            $s += 1e15;
        }
        if (!empty($enr[$id]['active'])) {
            $s += 1e12;
        }
        $s += ($child[$id] ?? 0) * 1e3;
        $s -= $id * 1e-6; // tiebreak: lower id wins
        return $s;
    }

    private function enrollmentSignals($db, array $ids): array
    {
        $out = [];
        foreach (array_chunk($ids, 1000) as $chunk) {
            $in  = implode(',', array_map('intval', $chunk));
            $res = $db->query(
                "SELECT player_id,
                        MAX(CASE WHEN is_primary = 1 AND is_active = 1 THEN 1 ELSE 0 END) AS prim,
                        MAX(is_active) AS act
                 FROM academy_player_enrollments
                 WHERE player_id IN ({$in})
                 GROUP BY player_id"
            )->getResultArray();
            foreach ($res as $r) {
                $out[(int) $r['player_id']] = ['primary' => (int) $r['prim'] === 1, 'active' => (int) $r['act'] === 1];
            }
        }
        return $out;
    }

    private function childTotals($db, array $ids): array
    {
        $out = array_fill_keys(array_map('intval', $ids), 0);
        foreach (self::CHILD_TABLES as $table => $col) {
            foreach (array_chunk($ids, 1000) as $chunk) {
                $in  = implode(',', array_map('intval', $chunk));
                $res = $db->query(
                    "SELECT `{$col}` AS pid, COUNT(*) AS c FROM `{$table}` WHERE `{$col}` IN ({$in}) GROUP BY `{$col}`"
                )->getResultArray();
                foreach ($res as $r) {
                    $out[(int) $r['pid']] = ($out[(int) $r['pid']] ?? 0) + (int) $r['c'];
                }
            }
        }
        return $out;
    }
}
