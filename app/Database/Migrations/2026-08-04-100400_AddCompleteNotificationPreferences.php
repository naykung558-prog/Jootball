<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCompleteNotificationPreferences extends Migration
{
    public function up()
    {
        $this->forge->addColumn('notification_preferences', [
            'team_red_cards' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
                'after'      => 'team_goals',
            ],
            'team_substitutions' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'after'      => 'team_red_cards',
            ],
            'competition_final_scores' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
                'after'      => 'competition_matches',
            ],
            'player_red_cards' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
                'after'      => 'player_goals',
            ],
            'player_substitutions' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'after'      => 'player_cards',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('notification_preferences', [
            'team_red_cards',
            'team_substitutions',
            'competition_final_scores',
            'player_red_cards',
            'player_substitutions',
        ]);
    }
}
