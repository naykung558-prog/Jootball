<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * One-time fixup after the player bulk-merge: team_player_chart_selection.player_ids is a
 * JSON array (not an FK column), so the merge's FK repoint missed it. This rewrites every
 * dropped player id to its canonical keep id (from logs_player_merge) and de-duplicates,
 * so chart exports/displays and the submission stale-guard see the live canonical players.
 *
 *   php spark dedup:remap-chart            # apply
 *   php spark dedup:remap-chart --dry-run  # report only
 *
 * Idempotent: re-running finds nothing to change.
 */
class DedupRemapChart extends BaseCommand
{
    protected $group       = 'Dedup';
    protected $name        = 'dedup:remap-chart';
    protected $description = 'Remap merged player ids inside team_player_chart_selection.player_ids JSON.';
    protected $usage       = 'dedup:remap-chart [--dry-run]';

    public function run(array $params)
    {
        $dryRun = CLI::getOption('dry-run') !== null;
        $db     = db_connect();

        // drop_id => keep_id
        $map = [];
        foreach ($db->table('logs_player_merge')->select('drop_id, keep_id')->get()->getResultArray() as $r) {
            $map[(int) $r['drop_id']] = (int) $r['keep_id'];
        }
        if (empty($map)) {
            CLI::write('logs_player_merge is empty — nothing to remap.', 'yellow');
            return;
        }

        $rows    = $db->table('team_player_chart_selection')
            ->select('team_id, player_ids')
            ->where('player_ids IS NOT NULL')->where('player_ids !=', '')
            ->get()->getResultArray();
        $changed = 0;
        $refs    = 0;

        foreach ($rows as $row) {
            $ids = json_decode($row['player_ids'], true);
            if (!is_array($ids)) {
                continue;
            }
            $new      = [];
            $rowDirty = false;
            foreach ($ids as $id) {
                $id = (int) $id;
                if (isset($map[$id])) {
                    $id = $map[$id];
                    $rowDirty = true;
                    $refs++;
                }
                if (!in_array($id, $new, true)) {
                    $new[] = $id; // remap may collapse drop+keep into one → dedup
                }
            }
            if ($rowDirty) {
                $changed++;
                if (!$dryRun) {
                    $db->table('team_player_chart_selection')
                        ->where('team_id', $row['team_id'])
                        ->update(['player_ids' => json_encode($new)]);
                }
            }
        }

        $verb = $dryRun ? 'WOULD remap' : 'remapped';
        CLI::write("{$verb} {$changed} chart-selection row(s), {$refs} stale player reference(s).", 'green');
    }
}
