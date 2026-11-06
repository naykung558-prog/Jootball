<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPlayerListStatusToTournamentTeam extends Migration
{
    public function up()
    {
        $this->forge->addColumn('tournament_team', [
            'player_list_status' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'null'       => false,
                'after'      => 'register_status',
            ],
            'player_list_submitted_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'player_list_status',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('tournament_team', 'player_list_status');
        $this->forge->dropColumn('tournament_team', 'player_list_submitted_at');
    }
}
