<?php

namespace Modules\Player\Models;

use CodeIgniter\Model;
use \Hermawan\DataTables\DataTable;

class ProvinceModel extends Model
{
    /**
     * ชื่อตารางในฐานข้อมูล
     */
    protected $table = "tbl_provinces";

    /**
     * Primary Key ของตาราง
     */
    protected $primaryKey = "province_id";

    /**
     * รายชื่อคอลัมน์ที่อนุญาตให้เพิ่มหรือแก้ไขข้อมูลได้
     */
    protected $allowedFields = [
        'province_code',
        'province_name',
        'geo_id'
    ];
}