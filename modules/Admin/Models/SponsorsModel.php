<?php

namespace Modules\Admin\Models;

use CodeIgniter\Model;
use \Hermawan\DataTables\DataTable;

class SponsorsModel extends Model
{
    protected $table      = "sponsors";
    protected $primaryKey = "id";
    protected $allowedFields = ['name', 'name_en', 'start', 'end', 'logo', 'create_by', 'is_active'];

    function get_Sponsors($id = '')
    {
        if ($id) {
            $builder = $this->db->table($this->table);
            $builder->select($this->table . ".*");
            $builder->where("id", $id);
            $result = $builder->get()->getRowArray();
            return $result;
        } else {
            $builder = $this->db->table($this->table);
            $builder->select("sponsors.id, sponsors.name, sponsors.name_en, sponsors.start, sponsors.end, sponsors.logo, sponsors.is_active, sponsors.create_date");
            $builder->where("sponsors.is_active IS NOT NULL");

            return DataTable::of($builder)
                ->edit('logo', function ($row) {
                    if (!empty($row->logo)) {
                        $img_link = base_url() . '/' . $row->logo;
                        return '<img src="' . $img_link . '" class="rounded" style="width:60px;height:40px;object-fit:contain">';
                    }
                    return '<div class="bg-light rounded d-flex align-items-center justify-content-center" style="width:60px;height:40px"><i class="fas fa-image text-muted"></i></div>';
                })
                ->add('is_active_raw', function ($row) {
                    return $row->is_active;
                })
                ->edit('is_active', function ($row) {
                    if ($row->is_active == 1) {
                        return '<span class="mb-1 badge text-center rounded-pill text-bg-success">' . lang('Common.active') . '</span>';
                    } else {
                        return '<span class="mb-1 badge text-center rounded-pill text-bg-danger">' . lang('Common.inactive') . '</span>';
                    }
                })
                ->edit('start', function ($row) {
                    if (!empty($row->start)) {
                        return date('d/m/Y', strtotime($row->start));
                    }
                    return '-';
                })
                ->edit('end', function ($row) {
                    if (!empty($row->end)) {
                        return date('d/m/Y', strtotime($row->end));
                    }
                    return '-';
                })
                ->add('action', function ($row) {
                    $link = base_url('admin/sponsors/manage/' . $row->id);
                    $action = "<a class='btn btn-primary btn-sm rounded-pill' href='" . $link . "'><i class='ti ti-edit text-white' style='font-size: 16px;'></i></a>";
                    $action .= " <button onclick='deleteSponsors(" . $row->id . ")' class='btn btn-danger btn-sm rounded-pill'><i class='ti ti-trash text-white' style='font-size: 16px;'></i></button>";
                    return $action;
                })
                ->setSearchableColumns(['name', 'name_en'])
                ->addNumbering()
                ->toJson(true);
        }
    }

    function saveSponsors($input)
    {
        $builder = $this->db->table($this->table);

        if (!empty($input['name'])) {
            $builder->set('name', $input['name']);
        }
        if (isset($input['name_en']) && $input['name_en'] !== '') {
            $builder->set('name_en', $input['name_en']);
        }
        if (isset($input['start']) && $input['start'] !== '') {
            $builder->set('start', $input['start']);
        }
        if (isset($input['end']) && $input['end'] !== '') {
            $builder->set('end', $input['end']);
        }
        if (isset($input['is_active'])) {
            $builder->set('is_active', $input['is_active']);
        }

        if (!empty($input['id'])) {
            $builder->set('update_date', date('Y-m-d H:i:s'));
            $builder->where('id', $input['id']);
            $builder->update();
            return $input['id'];
        } else {
            $builder->set('create_by', session()->get('member_id'));
            $builder->set('create_date', date('Y-m-d H:i:s'));
            $builder->set('update_date', date('Y-m-d H:i:s'));
            $builder->insert();
            return $this->db->insertID();
        }
    }

    function deleteSponsors($id)
    {
        $builder = $this->db->table($this->table);
        $builder->where('id', $id);
        $builder->delete();
    }

    function toggleStatus($id, $isActive)
    {
        $builder = $this->db->table($this->table);
        $builder->set('is_active', $isActive);
        $builder->set('update_date', date('Y-m-d H:i:s'));
        $builder->where('id', $id);
        return $builder->update();
    }
}
