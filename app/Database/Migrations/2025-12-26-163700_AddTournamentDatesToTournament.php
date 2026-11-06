<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTournamentDatesToTournament extends Migration
{
    public function up()
    {
        $fields = [
            'tournament_start_date' => [
                'type' => 'DATE',
                'null' => true,
                'default' => null,
                'after' => 'end_date',
            ],
            'tournament_end_date' => [
                'type' => 'DATE',
                'null' => true,
                'default' => null,
                'after' => 'tournament_start_date',
            ],
        ];

        $this->forge->addColumn('tournament', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('tournament', 'tournament_start_date');
        $this->forge->dropColumn('tournament', 'tournament_end_date');
    }
}
