<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePermissionsTable extends Migration
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
            'module' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'comment' => 'Module group for organizing permissions',
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
        $this->forge->createTable('permissions');

        // Insert default permissions
        $data = [
            // Dashboard
            ['name' => 'dashboard.view', 'display_name' => 'ดูแดชบอร์ด', 'description' => 'เข้าถึงหน้าแดชบอร์ด', 'module' => 'dashboard', 'created_at' => date('Y-m-d H:i:s')],

            // Tournament Management
            ['name' => 'tournament.view', 'display_name' => 'ดูทัวร์นาเมนต์', 'description' => 'ดูรายการทัวร์นาเมนต์', 'module' => 'tournament', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'tournament.create', 'display_name' => 'สร้างทัวร์นาเมนต์', 'description' => 'สร้างทัวร์นาเมนต์ใหม่', 'module' => 'tournament', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'tournament.edit', 'display_name' => 'แก้ไขทัวร์นาเมนต์', 'description' => 'แก้ไขข้อมูลทัวร์นาเมนต์', 'module' => 'tournament', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'tournament.delete', 'display_name' => 'ลบทัวร์นาเมนต์', 'description' => 'ลบทัวร์นาเมนต์', 'module' => 'tournament', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'tournament.manage_matches', 'display_name' => 'จัดการแมทช์', 'description' => 'จัดการแมทช์การแข่งขัน', 'module' => 'tournament', 'created_at' => date('Y-m-d H:i:s')],

            // Team Management
            ['name' => 'team.view', 'display_name' => 'ดูทีม', 'description' => 'ดูรายการทีม', 'module' => 'team', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'team.create', 'display_name' => 'สร้างทีม', 'description' => 'สร้างทีมใหม่', 'module' => 'team', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'team.edit', 'display_name' => 'แก้ไขทีม', 'description' => 'แก้ไขข้อมูลทีม', 'module' => 'team', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'team.delete', 'display_name' => 'ลบทีม', 'description' => 'ลบทีม', 'module' => 'team', 'created_at' => date('Y-m-d H:i:s')],

            // Player Management
            ['name' => 'player.view', 'display_name' => 'ดูนักเตะ', 'description' => 'ดูข้อมูลนักเตะ', 'module' => 'player', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'player.create', 'display_name' => 'เพิ่มนักเตะ', 'description' => 'เพิ่มนักเตะใหม่', 'module' => 'player', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'player.edit', 'display_name' => 'แก้ไขนักเตะ', 'description' => 'แก้ไขข้อมูลนักเตะ', 'module' => 'player', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'player.delete', 'display_name' => 'ลบนักเตะ', 'description' => 'ลบนักเตะ', 'module' => 'player', 'created_at' => date('Y-m-d H:i:s')],

            // Reports
            ['name' => 'report.view', 'display_name' => 'ดูรายงาน', 'description' => 'ดูรายงานต่างๆ', 'module' => 'report', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'report.export', 'display_name' => 'ส่งออกรายงาน', 'description' => 'ส่งออกรายงาน', 'module' => 'report', 'created_at' => date('Y-m-d H:i:s')],

            // Scout Management
            ['name' => 'scout.view', 'display_name' => 'ดู Scout', 'description' => 'ดูข้อมูล Scout', 'module' => 'scout', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'scout.manage', 'display_name' => 'จัดการ Scout', 'description' => 'จัดการข้อมูล Scout', 'module' => 'scout', 'created_at' => date('Y-m-d H:i:s')],

            // User Management
            ['name' => 'user.view', 'display_name' => 'ดูผู้ใช้', 'description' => 'ดูรายการผู้ใช้', 'module' => 'user', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'user.create', 'display_name' => 'สร้างผู้ใช้', 'description' => 'สร้างผู้ใช้ใหม่', 'module' => 'user', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'user.edit', 'display_name' => 'แก้ไขผู้ใช้', 'description' => 'แก้ไขข้อมูลผู้ใช้', 'module' => 'user', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'user.delete', 'display_name' => 'ลบผู้ใช้', 'description' => 'ลบผู้ใช้', 'module' => 'user', 'created_at' => date('Y-m-d H:i:s')],

            // Role Management
            ['name' => 'role.view', 'display_name' => 'ดูบทบาท', 'description' => 'ดูรายการบทบาท', 'module' => 'role', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'role.create', 'display_name' => 'สร้างบทบาท', 'description' => 'สร้างบทบาทใหม่', 'module' => 'role', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'role.edit', 'display_name' => 'แก้ไขบทบาท', 'description' => 'แก้ไขบทบาท', 'module' => 'role', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'role.delete', 'display_name' => 'ลบบทบาท', 'description' => 'ลบบทบาท', 'module' => 'role', 'created_at' => date('Y-m-d H:i:s')],
        ];

        $this->db->table('permissions')->insertBatch($data);
    }

    public function down()
    {
        $this->forge->dropTable('permissions');
    }
}