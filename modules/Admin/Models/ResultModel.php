<?php

namespace Modules\Admin\Models;

use CodeIgniter\Model;
use \Hermawan\DataTables\DataTable;

class ResultModel extends Model
{
    protected $table      = "tournament_match_result";
    protected $primaryKey = "";
    protected $allowedFields = [];

    function getMatch($tour_id = ''){
        $builder = $this->db->table($this->table);
        $builder->select("{$this->table}.*, 
            team1.name AS team1_name, 
            team2.name AS team2_name"
        );
        $builder->join("team AS team1", "{$this->table}.team1_id = team1.id", "LEFT");
        $builder->join("team AS team2", "{$this->table}.team2_id = team2.id", "LEFT");
        $builder->where("{$this->table}.tour_id", $tour_id);
        $builder->where("{$this->table}.match_status", 3);

        return $builder->get()->getResultArray();

    }

}