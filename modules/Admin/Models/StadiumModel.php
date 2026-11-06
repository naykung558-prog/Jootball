<?php

namespace Modules\Admin\Models;

use CodeIgniter\Model;
use \Hermawan\DataTables\DataTable;

class StadiumModel extends Model
{
    protected $table      = "stadium";
    protected $primaryKey = "id";
    protected $allowedFields = [];

    function get_Stadium($id = '')
    {
        if ($id) {
            $builder = $this->db->table($this->table);
            $builder->select($this->table . ".*");
            $builder->where("id", $id);
            $builder->where("deleted_at IS NULL");
            $result = $builder->get()->getRowArray();
            return $result;
        } else {
            $builder = $this->db->table($this->table);
            $builder->select("stadium.id, stadium.name, stadium.name_en, stadium.address, stadium.is_active, COALESCE(si.image_path, '') as cover_image");
            $builder->join("stadium_images si", "si.stadium_id = stadium.id AND si.is_cover = 1", "LEFT");
            $builder->where("stadium.deleted_at IS NULL");

            return DataTable::of($builder)
                ->edit('cover_image', function ($row) {
                    if (!empty($row->cover_image)) {
                        $img_link = base_url() . '/' . $row->cover_image;
                        return '<img src="' . $img_link . '" class="rounded" style="width:60px;height:40px;object-fit:cover">';
                    }
                    return '<div class="bg-light rounded d-flex align-items-center justify-content-center" style="width:60px;height:40px"><i class="fas fa-image text-muted"></i></div>';
                })
                ->edit('is_active', function ($row) {
                    if ($row->is_active == 1) {
                        return '<span class="mb-1 badge text-center rounded-pill text-bg-success">' . lang('Common.active') . '</span>';
                    } else {
                        return '<span class="mb-1 badge text-center rounded-pill text-bg-danger">' . lang('Common.inactive') . '</span>';
                    }
                })
                ->add('action', function ($row) {
                    $link = base_url('admin/stadium/manage/' . $row->id);
                    $action = "<a class='btn btn-primary btn-sm rounded-pill' href='" . $link . "'><i class='ti ti-edit text-white' style='font-size: 16px;'></i></a>";
                    $action .= " <button onclick='deleteStadium(" . $row->id . ")' class='btn btn-danger btn-sm rounded-pill'><i class='ti ti-trash text-white' style='font-size: 16px;'></i></button>";
                    return $action;
                })
                ->setSearchableColumns(['name', 'name_en', 'address'])
                ->addNumbering()
                ->toJson(true);
        }
    }

    function saveStadium($input)
    {
        $builder = $this->db->table($this->table);

        if (!empty($input['name'])) {
            $builder->set('name', $input['name']);
        }
        if (isset($input['name_en'])) {
            $builder->set('name_en', $input['name_en']);
        }
        if (isset($input['address'])) {
            $builder->set('address', $input['address']);
        }
        if (isset($input['latitude']) && $input['latitude'] !== '') {
            $builder->set('latitude', $input['latitude']);
        }
        if (isset($input['longitude']) && $input['longitude'] !== '') {
            $builder->set('longitude', $input['longitude']);
        }
        if (isset($input['is_active'])) {
            $builder->set('is_active', $input['is_active']);
        }

        if (!empty($input['id'])) {
            $builder->set('updated_by', session()->get('member_id'));
            $builder->set('updated_date', date('Y-m-d H:i:s'));
            $builder->where('id', $input['id']);
            $builder->update();
            return $input['id'];
        } else {
            $builder->set('created_by', session()->get('member_id'));
            $builder->insert();
            return $this->db->insertID();
        }
    }

    function deleteStadium($id)
    {
        $builder = $this->db->table($this->table);
        $builder->set('deleted_at', date('Y-m-d H:i:s'));
        $builder->where('id', $id);
        $builder->update();
        return "success";
    }

    function getImages($stadium_id)
    {
        $builder = $this->db->table('stadium_images');
        $builder->select('*');
        $builder->where('stadium_id', $stadium_id);
        $builder->orderBy('is_cover', 'DESC');
        $builder->orderBy('sort_order', 'ASC');
        return $builder->get()->getResultArray();
    }

    function saveImage($stadium_id, $image_path)
    {
        $builder = $this->db->table('stadium_images');
        $builder->set('stadium_id', $stadium_id);
        $builder->set('image_path', $image_path);

        // ถ้ายังไม่มีรูปเลย ตั้งเป็น cover อัตโนมัติ
        $existing = $this->db->table('stadium_images')->where('stadium_id', $stadium_id)->countAllResults();
        if ($existing == 0) {
            $builder->set('is_cover', 1);
        }

        $builder->insert();
        return $this->db->insertID();
    }

    function deleteImage($image_id)
    {
        $builder = $this->db->table('stadium_images');
        $image = $builder->where('id', $image_id)->get()->getRowArray();

        if ($image) {
            // ลบไฟล์จริง
            $filePath = FCPATH . $image['image_path'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            // ลบ record
            $this->db->table('stadium_images')->where('id', $image_id)->delete();

            // ถ้าเป็น cover ให้ตั้งรูปแรกที่เหลือเป็น cover
            if ($image['is_cover'] == 1) {
                $first = $this->db->table('stadium_images')
                    ->where('stadium_id', $image['stadium_id'])
                    ->orderBy('sort_order', 'ASC')
                    ->limit(1)
                    ->get()->getRowArray();
                if ($first) {
                    $this->db->table('stadium_images')
                        ->where('id', $first['id'])
                        ->update(['is_cover' => 1]);
                }
            }
        }
        return "success";
    }

    function setCoverImage($stadium_id, $image_id)
    {
        // ยกเลิก cover เดิม
        $this->db->table('stadium_images')
            ->where('stadium_id', $stadium_id)
            ->update(['is_cover' => 0]);

        // ตั้ง cover ใหม่
        $this->db->table('stadium_images')
            ->where('id', $image_id)
            ->where('stadium_id', $stadium_id)
            ->update(['is_cover' => 1]);

        return "success";
    }

    function getActiveStadiums()
    {
        $builder = $this->db->table($this->table);
        $builder->select('id, name, name_en');
        $builder->where('is_active', 1);
        $builder->where('deleted_at IS NULL');
        $builder->orderBy('name', 'ASC');
        return $builder->get()->getResultArray();
    }

    function checkDuplicateName($name)
    {
        $builder = $this->db->table($this->table);
        $builder->select('id, name, name_en');
        $builder->where('name', $name);
        $builder->where('deleted_at IS NULL');
        return $builder->get()->getRowArray();
    }
}
