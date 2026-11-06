<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddParentTeamIdToTeam extends Migration
{
    public function up()
    {
        // Add parent_team_id column to team table
        $fields = [
            'parent_team_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'after'      => 'id',
            ],
        ];
        $this->forge->addColumn('team', $fields);

        // Add index for better query performance
        $this->db->query('ALTER TABLE `team` ADD INDEX `idx_parent_team_id` (`parent_team_id`)');
    }

    public function down()
    {
        // Remove index first
        $this->db->query('ALTER TABLE `team` DROP INDEX `idx_parent_team_id`');

        // Remove column
        $this->forge->dropColumn('team', 'parent_team_id');
    }
}
