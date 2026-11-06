<?php

namespace Modules\Admin\Models;

use CodeIgniter\Model;
use \Hermawan\DataTables\DataTable;

class HelpGuideModel extends Model
{
    protected $table      = 'help_guide';
    protected $primaryKey = 'id';
    protected $allowedFields = ['title_th', 'title_en', 'link', 'sort_order', 'is_active', 'create_by'];

    public function get_one($id)
    {
        return $this->db->table($this->table)->where('id', $id)->get()->getRowArray();
    }

    public function get_active_list()
    {
        return $this->db->table($this->table)
            ->where('is_active', 1)
            ->orderBy('sort_order', 'ASC')
            ->orderBy('id', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function ajax_list()
    {
        $builder = $this->db->table($this->table);
        $builder->select('id, title_th, title_en, link, sort_order, is_active');

        return DataTable::of($builder)
            ->edit('link', function ($row) {
                $url = esc($row->link);
                return '<a href="' . $url . '" target="_blank" rel="noopener" class="text-decoration-none">'
                     . $url . ' <i class="fas fa-external-link-alt small ms-1"></i></a>';
            })
            ->add('is_active_raw', function ($row) {
                return $row->is_active;
            })
            ->edit('is_active', function ($row) {
                if ($row->is_active == 1) {
                    return '<span class="mb-1 badge text-center rounded-pill text-bg-success">' . lang('Common.active') . '</span>';
                }
                return '<span class="mb-1 badge text-center rounded-pill text-bg-danger">' . lang('Common.inactive') . '</span>';
            })
            ->add('action', function ($row) {
                $link = base_url('admin/help-guide/manage/' . $row->id);
                $action  = "<a class='btn btn-primary btn-sm rounded-pill' href='" . $link . "'><i class='ti ti-edit text-white' style='font-size: 16px;'></i></a>";
                $action .= " <button onclick='deleteHelpGuide(" . $row->id . ")' class='btn btn-danger btn-sm rounded-pill'><i class='ti ti-trash text-white' style='font-size: 16px;'></i></button>";
                return $action;
            })
            ->setSearchableColumns(['title_th', 'title_en', 'link'])
            ->addNumbering()
            ->toJson(true);
    }

    public function save_item($input)
    {
        $builder = $this->db->table($this->table);

        $fields = ['title_th', 'title_en', 'link', 'sort_order', 'is_active'];
        foreach ($fields as $f) {
            if (array_key_exists($f, $input)) {
                $builder->set($f, $input[$f]);
            }
        }

        if (!empty($input['id'])) {
            $builder->set('update_date', date('Y-m-d H:i:s'));
            $builder->where('id', $input['id']);
            $builder->update();
            return $input['id'];
        }

        $builder->set('create_by', session()->get('member_id'));
        $builder->set('create_date', date('Y-m-d H:i:s'));
        $builder->set('update_date', date('Y-m-d H:i:s'));
        $builder->insert();
        return $this->db->insertID();
    }

    public function delete_item($id)
    {
        return $this->db->table($this->table)->where('id', $id)->delete();
    }

    public function toggle_status($id, $isActive)
    {
        return $this->db->table($this->table)
            ->where('id', $id)
            ->update([
                'is_active'   => $isActive,
                'update_date' => date('Y-m-d H:i:s'),
            ]);
    }
}
