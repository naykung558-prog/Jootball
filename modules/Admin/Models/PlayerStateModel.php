<?php

namespace Modules\Admin\Models;

use CodeIgniter\Model;

class PlayerStateModel extends Model
{
    protected $table      = "";
    protected $primaryKey = "";
    protected $allowedFields = [];

    function getPlayerToMatch($tour_id, $match_id, $team_id, $is_substitute = '')
    {
        $builder = $this->db->table("player_to_match");
        $builder->select("player_to_match.player_id, player.first_name, player.last_name, pt_ptm.shirt_number as shirt_number, coarse_ptm.name_th as position_name, COALESCE(NULLIF(tbl_player_position.parent_id,0), tbl_player_position.id) as player_position_id");
        $builder->join("player", "player_to_match.player_id = player.id", "left");
        $builder->join("academy_player_enrollments pt_ptm", "pt_ptm.player_id = player.id AND pt_ptm.team_id = " . (int)$team_id . " AND pt_ptm.is_active = 1", "left");
        $builder->join("tbl_player_position", "tbl_player_position.id = pt_ptm.position_id", "left");
        $builder->join("tbl_player_position coarse_ptm", "coarse_ptm.id = COALESCE(NULLIF(tbl_player_position.parent_id,0), tbl_player_position.id)", "left");
        $result = $builder->where('tournament_id', $tour_id)
            ->where('player_to_match.match_id', $match_id)
            ->where('player_to_match.team_id', $team_id)
            ->where('player_to_match.is_substitute', $is_substitute);
        $result = $builder->get()->getResultArray();
        return $result;
    }

    function getPlayerDataToMatch($tour_id, $match_id, $team_id, $is_substitute = '')
    {
        $builder = $this->db->table("player_to_match");
        $builder->select("player.id, player.first_name, player.last_name, player.birth_date, COALESCE(NULLIF(tbl_player_position.parent_id,0), tbl_player_position.id) as player_position_id, pt_ptm2.shirt_number as shirt_number
            , coarse_ptm2.name_th as position_name_th, coarse_ptm2.name_en as position_name_en, player.image_path");
        $builder->join('player', 'player_to_match.player_id = player.id', 'left');
        $builder->join("academy_player_enrollments pt_ptm2", "pt_ptm2.player_id = player.id AND pt_ptm2.team_id = " . (int)$team_id . " AND pt_ptm2.is_active = 1", "LEFT");
        $builder->join("tbl_player_position", "tbl_player_position.id = pt_ptm2.position_id", "LEFT");
        $builder->join("tbl_player_position coarse_ptm2", "coarse_ptm2.id = COALESCE(NULLIF(tbl_player_position.parent_id,0), tbl_player_position.id)", "LEFT");
        $result = $builder->where('tournament_id', $tour_id)
            ->where('player_to_match.match_id', $match_id)
            ->where('player_to_match.team_id', $team_id);
        if ($is_substitute != '') {
            $builder->where('player_to_match.is_substitute', $is_substitute);
        }
        $builder->orderBy("CAST(pt_ptm2.shirt_number AS unsigned) ASC");
        $result = $builder->get()->getResultArray();
        // echo "<pre>";
        // print_r($result);die;
        return $result;
    }

    function savePlayerToMatch($players, $tour_id, $match_id, $team_id, $is_substitute)
    {
        $builder = $this->db->table("player_to_match");
        // clear old data
        $builder->where('tournament_id', $tour_id)
            ->where('match_id', $match_id)
            ->where('team_id', $team_id)
            ->where('is_substitute', $is_substitute)->delete();
        foreach ($players as $player) {
            $builder->set('player_id', $player);
            $builder->set('team_id', $team_id);
            $builder->set('tournament_id', $tour_id);
            $builder->set('match_id', $match_id);
            $builder->set('is_substitute', $is_substitute);
            $builder->insert();
        }
    }

    function getPlayerState($player_id, $tour_id, $match_id)
    {
        $builder = $this->db->table("player_match_state");
        $builder->select("*");
        $builder->where("player_id", $player_id)
            ->where("tournament_id", $tour_id)
            ->where("match_id", $match_id);
        $result = $builder->get()->getRowArray();
        return $result;
    }

    function savePlayerState($input)
    {
        $builder = $this->db->table("player_match_state");

        $checkId = $builder->select('*')
            ->where('tournament_id', $input['tour_id'])
            ->where('match_id', $input['match_id'])
            ->where('player_id', $input['player_id'])
        ->get()->getRowArray();

        if (!empty($input['state_key'])) {
            $builder->set($input['state_key'], $input['value']);
        }

        if (!empty($checkId)) {
            $builder->where('id', $checkId['id']);
            $builder->update();
        } else {
            if (!empty($input['player_id'])) {
                $builder->set('player_id', $input['player_id']);
            }
            if (!empty($input['match_id'])) {
                $builder->set('match_id', $input['match_id']);
            }
            if (!empty($input['tour_id'])) {
                $builder->set('tournament_id', $input['tour_id']);
            }
            $builder->insert();
        }
        return "success";
    }
}
