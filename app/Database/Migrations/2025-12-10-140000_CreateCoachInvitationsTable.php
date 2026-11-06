<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCoachInvitationsTable extends Migration
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
            'coach_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'team_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'invited_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => false,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'accepted', 'rejected'],
                'default'    => 'pending',
            ],
            'message' => [
                'type'       => 'TEXT',
                'null'       => true,
            ],
            'is_active' => [
                'type'       => 'INT',
                'constraint' => 1,
                'default'    => 1,
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
            ],
            'responded_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('coach_invitations', true); // true param works as IF NOT EXISTS
    }

    public function down()
    {
        $this->forge->dropTable('coach_invitations');
    }
}
