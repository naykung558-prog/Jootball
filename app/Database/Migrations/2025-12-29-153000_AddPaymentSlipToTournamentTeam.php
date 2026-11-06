<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPaymentSlipToTournamentTeam extends Migration
{
    public function up()
    {
        $fields = [
            'payment_slip' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'default' => null,
                'after' => 'register_status'
            ],
        ];

        // Ensure table exists before adding column
        if ($this->db->tableExists('tournament_team')) {
              $this->forge->addColumn('tournament_team', $fields);
        }
    }

    public function down()
    {
        if ($this->db->tableExists('tournament_team')) {
            $this->forge->dropColumn('tournament_team', 'payment_slip');
        }
    }
}
