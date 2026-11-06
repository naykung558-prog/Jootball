<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateNotificationDevicesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'user_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'default'    => 'user',
            ],
            'token' => [
                'type'       => 'TEXT',
            ],
            'token_hash' => [
                'type'       => 'CHAR',
                'constraint' => 64,
            ],
            'platform' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'timezone' => [
                'type'       => 'VARCHAR',
                'constraint' => 80,
                'default'    => 'Asia/Bangkok',
            ],
            'locale' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'default'    => 'th',
            ],
            'enabled' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],
            'last_seen_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('token_hash');
        $this->forge->addKey(['user_id', 'user_type']);
        $this->forge->createTable('notification_devices');
    }

    public function down()
    {
        $this->forge->dropTable('notification_devices');
    }
}
