<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCoachIdsToTeamPlayerChartSelection extends Migration
{
    public function up()
    {
        $this->forge->addColumn('team_player_chart_selection', [
            'coach_ids' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'player_ids',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('team_player_chart_selection', 'coach_ids');
    }
}
