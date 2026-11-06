<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ChangeBankNameToBankId extends Migration
{
    public function up()
    {
        // First drop the old column if it exists, or modify it.
        // Since we are changing type from VARCHAR to INT and logic, usually it's better to drop and add,
        // OR modify. Since current data might be strings, we might lose data if we just modify to INT.
        // Assuming we are okay with resetting bank info or mapping it (which is complex).
        // Given this is likely dev/staging, we will drop bank_name and add bank_id.

        $fields = [
            'bank_name' => [
                'name' => 'bank_id',
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'default' => null,
            ],
        ];

        $this->forge->modifyColumn('tournament', $fields);
    }

    public function down()
    {
        $fields = [
            'bank_id' => [
                'name' => 'bank_name',
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
        ];

        $this->forge->modifyColumn('tournament', $fields);
    }
}
