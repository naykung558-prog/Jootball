<?php

namespace Modules\Home\Models;

use CodeIgniter\Model;

class HelpGuideModel extends Model
{
    protected $table      = 'help_guide';
    protected $primaryKey = 'id';

    public function getActiveGuides()
    {
        $builder = $this->db->table($this->table);
        $builder->select('id, title_th, title_en, link, sort_order');
        $builder->where('is_active', 1);
        $builder->orderBy('sort_order', 'ASC');
        $builder->orderBy('id', 'ASC');

        return $builder->get()->getResultArray();
    }
}
