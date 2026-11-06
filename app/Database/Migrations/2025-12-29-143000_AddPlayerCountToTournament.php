<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPlayerCountToTournament extends Migration
{
    public function up()
    {
        $fields = [
            'player_count' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => 0,
                'comment'    => 'Number of athletes allowed per team to submit'
            ],
        ];
        $this->forge->addColumn('tournament', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('tournament', 'player_count');
    }
}
