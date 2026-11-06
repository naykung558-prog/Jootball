<?php

namespace Modules\Home\Models;

use CodeIgniter\Model;

class SponsorsModel extends Model
{
    protected $table      = 'sponsors';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'name_en', 'logo', 'start', 'end', 'is_active'];

    /**
     * ดึงข้อมูล Sponsor สำหรับแสดงใน Navbar (เอาแค่ 1 โลโก้)
     * @return array|null
     */
    public function getNavbarSponsor()
    {
        $builder = $this->db->table($this->table);
        $builder->select('id, name, name_en, logo, start, end');
        $builder->where('is_active', '1');
        $builder->where('start <=', date('Y-m-d H:i:s'));
        $builder->where('end >=', date('Y-m-d H:i:s'));
        $builder->orderBy('start', 'ASC');
        $builder->limit(1);
        
        return $builder->get()->getRowArray();
    }

    /**
     * ดึงข้อมูล Sponsors ทั้งหมดสำหรับแสดงในหน้า Home
     * @return array
     */
    public function getActiveSponsors()
    {
        $builder = $this->db->table($this->table);
        $builder->select('id, name, name_en, logo, start, end');
        $builder->where('is_active', '1');
        $builder->where('start <=', date('Y-m-d H:i:s'));
        $builder->where('end >=', date('Y-m-d H:i:s'));
        $builder->orderBy('start', 'ASC');
        
        return $builder->get()->getResultArray();
    }

    /**
     * ตรวจสอบว่า Sponsor ยังอยู่ในช่วงเวลาที่ยังไม่หมดอายุ
     * @param int $id
     * @return bool
     */
    public function isActiveSponsor($id)
    {
        $sponsor = $this->find($id);
        
        if (!$sponsor) {
            return false;
        }

        $now = date('Y-m-d H:i:s');
        
        return $sponsor['is_active'] == '1' 
            && $sponsor['start'] <= $now 
            && $sponsor['end'] >= $now;
    }
}
