<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Modules\Player\Models\PlayerMergeModel;

/**
 * Merge one duplicate player into a canonical id (manual / testing entry point).
 *   php spark dedup:merge <keepId> <dropId>
 */
class DedupMerge extends BaseCommand
{
    protected $group       = 'Dedup';
    protected $name        = 'dedup:merge';
    protected $description = 'Merge player dropId into keepId (repoint FKs, soft-delete drop).';
    protected $usage       = 'dedup:merge <keepId> <dropId>';

    public function run(array $params)
    {
        if (count($params) < 2) {
            CLI::error('Usage: dedup:merge <keepId> <dropId>');
            return;
        }
        $keepId = (int) $params[0];
        $dropId = (int) $params[1];

        $model = new PlayerMergeModel();
        try {
            $moved = $model->merge($keepId, $dropId, 'admin', null);
            CLI::write("MERGE OK {$dropId} -> {$keepId}", 'green');
            foreach ($moved as $table => $counts) {
                CLI::write(sprintf('  %-32s updated=%d deleted=%d', $table, $counts['updated'], $counts['deleted']));
            }
        } catch (\Throwable $e) {
            CLI::error('MERGE FAILED: ' . $e->getMessage());
        }
    }
}
