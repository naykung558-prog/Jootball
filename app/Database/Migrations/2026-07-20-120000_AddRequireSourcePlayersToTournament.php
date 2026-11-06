<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRequireSourcePlayersToTournament extends Migration
{
    public function up()
    {
        $this->forge->addColumn('tournament', [
            'require_source_players' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'null'       => false,
                'default'    => 0,
                'after'      => 'prevent_duplicate_player',
                'comment'    => 'อนุญาตเฉพาะนักเตะที่เคยถูกส่งรายชื่อในทัวร์นาเมนต์ที่เลือกไว้ (tournament_source_check_link)',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('tournament', 'require_source_players');
    }
}
