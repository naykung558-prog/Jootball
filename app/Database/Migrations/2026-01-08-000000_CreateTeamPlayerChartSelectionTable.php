<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTeamPlayerChartSelectionTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'team_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'player_ids' => [
                'type' => 'TEXT', // Will store JSON array of player IDs
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('team_id');
        $this->forge->createTable('team_player_chart_selection');
    }

    public function down()
    {
        $this->forge->dropTable('team_player_chart_selection');
    }
}
