<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateNotificationFavoritesTable extends Migration
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
            'favorite_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
            ],
            'favorite_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
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
        $this->forge->addUniqueKey(['user_id', 'user_type', 'favorite_type', 'favorite_id'], 'notification_favorite_unique');
        $this->forge->addKey(['favorite_type', 'favorite_id']);
        $this->forge->createTable('notification_favorites');
    }

    public function down()
    {
        $this->forge->dropTable('notification_favorites');
    }
}
