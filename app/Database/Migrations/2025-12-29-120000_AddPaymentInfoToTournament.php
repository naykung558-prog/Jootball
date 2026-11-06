<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPaymentInfoToTournament extends Migration
{
    public function up()
    {
        $fields = [
            'bank_name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'after' => 'tournament_end_date'
            ],
            'account_name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'after' => 'bank_name'
            ],
            'account_no' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'after' => 'account_name'
            ],
            'promptpay' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'after' => 'account_no'
            ],
            'qrcode' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'promptpay'
            ],
        ];

        $this->forge->addColumn('tournament', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('tournament', ['bank_name', 'account_name', 'account_no', 'promptpay', 'qrcode']);
    }
}
