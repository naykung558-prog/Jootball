<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Addteamstotournamentstages extends Migration
{
	    public function up()
    {
        $fields = [
            'teams_json' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'JSON data of teams in this stage'
            ],
        ];
        $this->forge->addColumn('tournament_stages', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('tournament_stages', 'teams_json');
    }
}
