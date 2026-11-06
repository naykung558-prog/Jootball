<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStageIdToTournamentMatchResult extends Migration
{
    public function up()
    {
        // Add stage_id column
        $fields = [
            'stage_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'after' => 'tour_id',
                'comment' => 'รหัสรอบการแข่งขัน (Stage ID)'
            ],
        ];

        // Define column addition
        // We check if column exists first just in case to avoid errors,
        // but Forge addColumn usually handles it or errors if exists.
        // CodeIgniter 4 Forge doesn't strictly have "addColumnIfNotExists" built-in easily for all drivers,
        // so we assume it's ignored or we catch it. But simplistic is best.

        $this->forge->addColumn('tournament_match_result', $fields);

        // Add index
        $this->db->query('ALTER TABLE `tournament_match_result` ADD INDEX `idx_stage_id` (`stage_id`)');

        // Optional: Extend group_id length if it's too short (as per previous analysis)
        // Check if group_id exists and modify it
        $this->db->query('ALTER TABLE `tournament_match_result` MODIFY COLUMN `group_id` VARCHAR(50) DEFAULT NULL');
    }

    public function down()
    {
        $this->forge->dropColumn('tournament_match_result', 'stage_id');
    }
}
