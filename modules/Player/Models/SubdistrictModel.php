<?php

namespace Modules\Player\Models;

use CodeIgniter\Model;

class SubdistrictModel extends Model
{
    protected $table            = 'tbl_subdistricts'; // ชื่อตารางตำบล
    protected $primaryKey       = 'subdistrict_id';
    protected $allowedFields    = ['subdistrict_code', 'subdistrict_nam', 'district_id', 'province_id', 'geo_id'];
}
