<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBankTable extends Migration
{
    public function up()
    {
        // Create Table
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'name_en' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'short_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'code' => [
               'type'       => 'VARCHAR',
               'constraint' => 20,
               'null'       => true,
            ],
            'logo' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'is_active' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('tbl_bank');

        // Seed Data
        $data = [
            ['name' => 'ธนาคารกสิกรไทย', 'name_en' => 'Kasikornbank', 'short_name' => 'KBANK', 'code' => '004'],
            ['name' => 'ธนาคารไทยพาณิชย์', 'name_en' => 'Siam Commercial Bank', 'short_name' => 'SCB', 'code' => '014'],
            ['name' => 'ธนาคารกรุงเทพ', 'name_en' => 'Bangkok Bank', 'short_name' => 'BBL', 'code' => '002'],
            ['name' => 'ธนาคารกรุงไทย', 'name_en' => 'Krung Thai Bank', 'short_name' => 'KTB', 'code' => '006'],
            ['name' => 'ธนาคารทหารไทยธนชาต', 'name_en' => 'TMBThanachart Bank', 'short_name' => 'TTB', 'code' => '011'],
            ['name' => 'ธนาคารกรุงศรีอยุธยา', 'name_en' => 'Bank of Ayudhya', 'short_name' => 'KRUNGSRI', 'code' => '025'],
            ['name' => 'ธนาคารออมสิน', 'name_en' => 'Government Savings Bank', 'short_name' => 'GSB', 'code' => '030'],
            ['name' => 'ธนาคารเกียรตินาคินภัทร', 'name_en' => 'Kiatnakin Phatra Bank', 'short_name' => 'KKP', 'code' => '069'],
            ['name' => 'ธนาคารซีไอเอ็มบี ไทย', 'name_en' => 'CIMB Thai Bank', 'short_name' => 'CIMBT', 'code' => '022'],
            ['name' => 'ธนาคารทิสโก้', 'name_en' => 'TISCO Bank', 'short_name' => 'TISCO', 'code' => '059'],
            ['name' => 'ธนาคารยูโอบี', 'name_en' => 'United Overseas Bank (Thai)', 'short_name' => 'UOB', 'code' => '024'],
            ['name' => 'ธนาคารแลนด์ แอนด์ เฮ้าส์', 'name_en' => 'Land and Houses Bank', 'short_name' => 'LHBANK', 'code' => '073'],
            ['name' => 'ธนาคารสแตนดาร์ดชาร์เตอร์ด (ไทย)', 'name_en' => 'Standard Chartered Bank (Thai)', 'short_name' => 'SCBT', 'code' => '020'],
            ['name' => 'ธนาคารเพื่อการเกษตรและสหกรณ์การเกษตร', 'name_en' => 'Bank for Agriculture and Agricultural Cooperatives', 'short_name' => 'BAAC', 'code' => '034'],
            ['name' => 'ธนาคารอาคารสงเคราะห์', 'name_en' => 'Government Housing Bank', 'short_name' => 'GHB', 'code' => '033'],
        ];

        $this->db->table('tbl_bank')->insertBatch($data);
    }

    public function down()
    {
        $this->forge->dropTable('tbl_bank');
    }
}
