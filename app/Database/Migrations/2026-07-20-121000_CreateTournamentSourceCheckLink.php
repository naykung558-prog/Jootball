<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTournamentSourceCheckLink extends Migration
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
            'tour_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'source_tour_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['tour_id', 'source_tour_id']);
        $this->forge->addKey('tour_id');
        $this->forge->createTable('tournament_source_check_link');
    }

    public function down()
    {
        $this->forge->dropTable('tournament_source_check_link');
    }
}
