<?php

namespace Modules\Player\Models;

use CodeIgniter\Model;

class DistrictModel extends Model
{
    protected $table            = 'tbl_districts'; // ชื่อตารางอำเภอ
    protected $primaryKey       = 'district_id';
    protected $allowedFields    = ['district_code', 'district_name', 'geo_id', 'province_id'];
}
