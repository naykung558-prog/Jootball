<?php

namespace Modules\Admin\Models;

use CodeIgniter\Model;
use \Hermawan\DataTables\DataTable;

class IdCardModel extends Model
{
    protected $table      = "tournament_id_card";
    protected $primaryKey = "id";
    protected $allowedFields = [];

    function get_IdCards()
    {
        helper('permission');

        $builder = $this->db->table($this->table);
        $builder->select("tournament_id_card.id, tournament_id_card.tour_id, tournament_id_card.card_type, tournament.name as tournament_name, tournament_id_card.template_image, tournament_id_card.is_active");
        $builder->join("tournament", "tournament.tour_id = tournament_id_card.tour_id", "LEFT");
        $builder->where("tournament_id_card.deleted_at IS NULL");

        // Admins see every card; regular users only see the ones they created.
        if (!isSuperAdmin() && !hasRole('admin')) {
            $builder->where("tournament_id_card.created_by", session()->get('member_id'));
        }

        return DataTable::of($builder)
            ->edit('template_image', function ($row) {
                if (!empty($row->template_image)) {
                    $img_link = base_url() . '/' . $row->template_image;
                    return '<img src="' . $img_link . '" class="rounded" style="width:60px;height:80px;object-fit:cover">';
                }
                return '<div class="bg-light rounded d-flex align-items-center justify-content-center" style="width:60px;height:80px"><i class="fas fa-id-card text-muted"></i></div>';
            })
            ->edit('is_active', function ($row) {
                if ($row->is_active == 1) {
                    return '<span class="mb-1 badge text-center rounded-pill text-bg-success">' . lang('Common.active') . '</span>';
                } else {
                    return '<span class="mb-1 badge text-center rounded-pill text-bg-danger">' . lang('Common.inactive') . '</span>';
                }
            })
            ->add('action', function ($row) {
                $link = base_url('admin/idcard/manage/' . $row->id);
                $previewLink = base_url('admin/idcard/preview/' . $row->id);
                $action = "<a class='btn btn-info btn-sm rounded-pill me-1' href='" . $previewLink . "' target='_blank' title='Preview'><i class='fas fa-eye text-white' style='font-size: 14px;'></i></a>";
                $action .= "<a class='btn btn-primary btn-sm rounded-pill me-1' href='" . $link . "'><i class='ti ti-edit text-white' style='font-size: 16px;'></i></a>";
                $action .= " <button onclick='deleteIdCard(" . $row->id . ")' class='btn btn-danger btn-sm rounded-pill'><i class='ti ti-trash text-white' style='font-size: 16px;'></i></button>";
                return $action;
            })
            ->edit('card_type', function ($row) {
                if ($row->card_type == 'manager') {
                    return '<span class="badge rounded-pill text-bg-warning">Manager</span>';
                }
                return '<span class="badge rounded-pill text-bg-primary">Player</span>';
            })
            ->setSearchableColumns(['tournament.name'])
            ->addNumbering()
            ->toJson(true);
    }

    function get_IdCardsAll($card_type = 'player')
    {
        $builder = $this->db->table($this->table);
        $builder->select($this->table . ".*, tournament.name as tournament_name");
        $builder->join("tournament", "tournament.tour_id = tournament_id_card.tour_id", "LEFT");
        $builder->where("tournament_id_card.deleted_at IS NULL");
        $builder->where('card_type', $card_type);
        $builder->orderBy('tournament.tour_id', 'ASC');
        return $builder->get()->getResultArray();
    }

    function get_IdCard($id)
    {
        $builder = $this->db->table($this->table);
        $builder->select($this->table . ".*, tournament.name as tournament_name");
        $builder->join("tournament", "tournament.tour_id = tournament_id_card.tour_id", "LEFT");
        $builder->where("tournament_id_card.id", $id);
        $builder->where("tournament_id_card.deleted_at IS NULL");
        return $builder->get()->getRowArray();
    }

    function getByTourId($tour_id, $card_type = 'player')
    {
        $builder = $this->db->table($this->table);
        $builder->where('tour_id', $tour_id);
        $builder->where('card_type', $card_type);
        $builder->where('is_active', 1);
        $builder->where('deleted_at IS NULL');
        return $builder->get()->getRowArray();
    }

    function saveIdCard($input)
    {
        $builder = $this->db->table($this->table);

        if (!empty($input['tour_id'])) {
            $builder->set('tour_id', $input['tour_id']);
        }
        if (!empty($input['card_type'])) {
            $builder->set('card_type', $input['card_type']);
        }
        if (!empty($input['template_image'])) {
            $builder->set('template_image', $input['template_image']);
        }

        // Card dimensions
        if (isset($input['card_width_mm']) && $input['card_width_mm'] !== '') {
            $builder->set('card_width_mm', $input['card_width_mm']);
        }
        if (isset($input['card_height_mm']) && $input['card_height_mm'] !== '') {
            $builder->set('card_height_mm', $input['card_height_mm']);
        }

        // Photo position
        if (isset($input['photo_x']) && $input['photo_x'] !== '') {
            $builder->set('photo_x', $input['photo_x']);
        }
        if (isset($input['photo_y']) && $input['photo_y'] !== '') {
            $builder->set('photo_y', $input['photo_y']);
        }
        if (isset($input['photo_width']) && $input['photo_width'] !== '') {
            $builder->set('photo_width', $input['photo_width']);
        }
        if (isset($input['photo_height']) && $input['photo_height'] !== '') {
            $builder->set('photo_height', $input['photo_height']);
        }

        // Name position
        if (isset($input['name_x']) && $input['name_x'] !== '') {
            $builder->set('name_x', $input['name_x']);
        }
        if (isset($input['name_y']) && $input['name_y'] !== '') {
            $builder->set('name_y', $input['name_y']);
        }
        if (isset($input['name_font_size']) && $input['name_font_size'] !== '') {
            $builder->set('name_font_size', $input['name_font_size']);
        }
        if (isset($input['name_color']) && $input['name_color'] !== '') {
            $builder->set('name_color', $input['name_color']);
        }

        // Team position
        if (isset($input['team_x']) && $input['team_x'] !== '') {
            $builder->set('team_x', $input['team_x']);
        }
        if (isset($input['team_y']) && $input['team_y'] !== '') {
            $builder->set('team_y', $input['team_y']);
        }
        if (isset($input['team_font_size']) && $input['team_font_size'] !== '') {
            $builder->set('team_font_size', $input['team_font_size']);
        }
        if (isset($input['team_color']) && $input['team_color'] !== '') {
            $builder->set('team_color', $input['team_color']);
        }

        // Number position
        if (isset($input['number_x']) && $input['number_x'] !== '') {
            $builder->set('number_x', $input['number_x']);
        }
        if (isset($input['number_y']) && $input['number_y'] !== '') {
            $builder->set('number_y', $input['number_y']);
        }
        if (isset($input['number_font_size']) && $input['number_font_size'] !== '') {
            $builder->set('number_font_size', $input['number_font_size']);
        }
        if (isset($input['number_color']) && $input['number_color'] !== '') {
            $builder->set('number_color', $input['number_color']);
        }

        // Status
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

    function deleteIdCard($id)
    {
        $builder = $this->db->table($this->table);
        $builder->set('deleted_at', date('Y-m-d H:i:s'));
        $builder->where('id', $id);
        $builder->update();
        return "success";
    }

    function getTournamentOptions($cardType = 'player', $excludeTourId = null)
    {
        $builder = $this->db->table('tournament');
        $builder->select('tournament.tour_id, tournament.name');
        $builder->where('tournament.is_deleted', 0);
        $builder->where('tournament.is_active', 1);

        // Exclude tournaments that already have this card_type template
        $excludeSql = "tournament.tour_id NOT IN (SELECT tour_id FROM tournament_id_card WHERE deleted_at IS NULL AND card_type = '" . $this->db->escapeString($cardType) . "'";
        if ($excludeTourId) {
            $excludeSql .= " AND tour_id != " . intval($excludeTourId);
        }
        $excludeSql .= ")";
        $builder->where($excludeSql, null, false);

        $builder->orderBy('tournament.name', 'ASC');

        return $builder->get()->getResultArray();
    }
}
