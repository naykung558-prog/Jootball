<?php

namespace Modules\Tournament\Models;

use CodeIgniter\Model;
use \Hermawan\DataTables\DataTable;

class TournamentModel extends Model
{
    protected $table      = "tournament";
    protected $primaryKey = "tour_id";
    protected $allowedFields = [];

    function CountTeams($tour_id){
        $builder = $this->db->table($this->table);
        $builder->select('count(tournament_team.team_id) as teams');
        $builder->where("{$this->table}.tour_id", $tour_id);
        $builder->join('tournament_team', 'tournament_team.tour_id = tournament.tour_id');
        $result = $builder->get()->getRowArray();
        return $result['teams'];
    }

    function CountApprovedTeams($tour_id){
        $builder = $this->db->table($this->table);
        $builder->select('count(tournament_team.team_id) as teams');
        $builder->where("{$this->table}.tour_id", $tour_id);
        $builder->where("tournament_team.register_status", 1);
        $builder->join('tournament_team', 'tournament_team.tour_id = tournament.tour_id');
        $result = $builder->get()->getRowArray();
        return $result['teams'];
    }

    function CounPlayers($tour_id){
        $builder = $this->db->table($this->table);
        $builder->select('count(DISTINCT player.id) as players');
        $builder->where("{$this->table}.tour_id", $tour_id);
        $builder->join('tournament_team', 'tournament_team.tour_id = tournament.tour_id');
        $builder->join('academy_player_enrollments pt_cnt', 'pt_cnt.team_id = tournament_team.team_id AND pt_cnt.is_active = 1');
        $builder->join('player', 'player.id = pt_cnt.player_id');
        $builder->where('player.is_active', 1);
        $result = $builder->get()->getRowArray();
        return $result['players'];
    }

    function CounCoachs($tour_id){
        $builder = $this->db->table($this->table);
        $builder->select('count(coach.id) as coachs');
        $builder->where("{$this->table}.tour_id", $tour_id);
        $builder->join('tournament_team', 'tournament_team.tour_id = tournament.tour_id');
        $builder->join('team', 'team.id = tournament_team.team_id');
        $builder->join('coach', 'team.id = coach.team_id');
        $result = $builder->get()->getRowArray();
        return $result['coachs'];
    }

    function getRounds($tour_id){
        $data = array();
        $builder = $this->db->table('tournament_match_result');
        $builder->where("tour_id", $tour_id);
        $builder->select('match_round');
        $builder->groupBy('match_round');
        $builder->orderBy('create_date','DESC');
        $result = $builder->get()->getResultArray();
        foreach ($result as $key => $value) {
            $data[$value['match_round']]= $value['match_round'];
        }
        return $data;
    }

    function getMatch($tour_id,$filter){
        $builder = $this->db->table('tournament_match_result');
        $builder->select("tournament_match_result.*, 
            team1.name AS team1_name, team1.logo as team1_logo,
            team2.name AS team2_name, team2.logo as team2_logo"
        );
        $builder->join("team AS team1", "tournament_match_result.team1_id = team1.id", "LEFT");
        $builder->join("team AS team2", "tournament_match_result.team2_id = team2.id", "LEFT");
        $builder->where("tournament_match_result.tour_id", $tour_id);
        if(!empty($filter['round'])){
            $builder->where("tournament_match_result.round_id", $filter['round']);
        }
        if(!empty($filter['group_id'])){
            $builder->where("tournament_match_result.group_id", $filter['group_id']);
        }
        if(!empty($filter['cate_id'])){
            $builder->where("tournament_match_result.category_id", $filter['cate_id']);
        }
        
        // $builder->where("{$this->table}.match_status", 3);

        return $builder->get()->getResultArray();
    }

    function update_tour(){
        $data = array();
        $builder = $this->db->table('temp_tour_match');
        $builder->where("tour_id is not null");
        $builder->select('*');
        $result = $builder->get()->getResultArray();
        foreach ($result as $key => $value) {
            $builder_set = $this->db->table('tournament_match_result');
            $builder_set->set('tour_id',$value['tour_id']);
            $builder_set->set('round_id',$value['round_id']);
            $builder_set->set('category_id',$value['category_id']);
            $builder_set->where('match_id',$value['match_id']);
            $builder_set->update();

            echo '<pre>'; print_r($value); echo '</pre><br>';
        }
        return $data;
    }

    function update_tour_team(){
        $data = array();
        $builder = $this->db->table('tournament_match_result');
        $builder->select('team1_id,team2_id,tour_id');
        $builder->where('tour_id !=',0);
        $builder->groupBy('team1_id,team2_id');
        $result = $builder->get()->getResultArray();
        foreach ($result as $key => $value) {
            $data[$value['tour_id']][$value['team1_id']] = $value['team1_id'];
            $data[$value['tour_id']][$value['team2_id']] = $value['team2_id'];
        }


        foreach($data as $tour_id=>$d){
            foreach($d as $team_id){
                $builder = $this->db->table('tournament_team');
                $builder->where('tour_id',$tour_id);
                $builder->where('team_id',$team_id);
                $builder->select('*');
                $result = $builder->get()->getRowArray();
                if(empty($result['tour_id'])){
                    if($tour_id==13||$tour_id==14){
                        // $builder_set = $this->db->table('tournament_team');
                        // $builder_set->set('tour_id',$tour_id);
                        // $builder_set->set('team_id',$team_id);
                        // $builder_set->set('register_status',1);
                        // $builder_set->insert();
                        echo  $tour_id.' || '.$team_id. '<br>';
                    }
                    
                }

            }

        }

    }

}