<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRolesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'unique' => true,
            ],
            'display_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'status' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
                'comment' => '1=Active, 0=Inactive',
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
        
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('roles');
        
        // Insert default roles
        $data = [
            [
                'name' => 'super_admin',
                'display_name' => 'Super Admin',
                'description' => 'มีสิทธิ์เข้าถึงทุกระบบ',
                'status' => 1,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'admin',
                'display_name' => 'Admin',
                'description' => 'ผู้ดูแลระบบทั่วไป',
                'status' => 1,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'tournament_manager',
                'display_name' => 'Tournament Manager',
                'description' => 'จัดการทัวร์นาเมนต์เท่านั้น',
                'status' => 1,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'team_manager',
                'display_name' => 'Team Manager',
                'description' => 'จัดการทีมและนักเตะเท่านั้น',
                'status' => 1,
                'created_at' => date('Y-m-d H:i:s'),
            ],
        ];
        
        $this->db->table('roles')->insertBatch($data);
    }

    public function down()
    {
        $this->forge->dropTable('roles');
    }
}