<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTournamentSubmittedPlayers extends Migration
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
            'tour_team_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'player_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('tour_team_id');
        $this->forge->createTable('tournament_submitted_players');
    }

    public function down()
    {
        $this->forge->dropTable('tournament_submitted_players');
    }
}
