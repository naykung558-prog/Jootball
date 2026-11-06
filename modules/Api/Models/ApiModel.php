<?php

namespace Modules\Api\Models;

use CodeIgniter\Model;

class ApiModel extends Model
{
    function getEvents() {
        $builder = $this->db->table('tbl_event');
        $builder->select('tbl_event.id as action_id, 
            tbl_event.name as action_name,
            tbl_event.name_en as action_name_eng');
        $result = $builder->get()->getResultArray();
        return $result;
    }

    function getMatchResult($match_id)
    {
        $matchBuilder = $this->db->table('tournament_match_result');
        $matchBuilder->select("
            mapping_glo_match.focal_match_id as match_id,
            mpt1.focal_team_id as home_team_id,
            t1.name as home_team,
            mpt2.focal_team_id as away_team_id,
            t2.name as away_team,
            tournament_match_result.team1_score as home_team_score,
            tournament_match_result.team2_score as away_team_score,
        ");
        $matchBuilder->join("team t1", "t1.id = tournament_match_result.team1_id", "left");
        $matchBuilder->join("team t2", "t2.id = tournament_match_result.team2_id", "left");
        $matchBuilder->join("mapping_glo_team mpt1", "mpt1.jootball_team_id = tournament_match_result.team1_id", "left");
        $matchBuilder->join("mapping_glo_team mpt2", "mpt2.jootball_team_id = tournament_match_result.team2_id", "left");
        $matchBuilder->join("mapping_glo_match", "mapping_glo_match.jootball_match_id = '" . $match_id . "'", "LEFT");
        $matchBuilder->where("tournament_match_result.match_id", $match_id);
        $result = $matchBuilder->get()->getRowArray();

        $result['events'] = [];
        $eventBuilder = $this->db->table('tournament_match_event');
        $eventBuilder->select("
            tournament_match_event.minute,
            mapping_glo_team.focal_team_id as teamId,
            team.name as team,
            tbl_event.name as action,
            tournament_match_event.player_1_id as playerMainAction,
            tournament_match_event.player_2_id as playerSubAction
        ");
        $eventBuilder->join("team", "tournament_match_event.team_id = team.id", "left");
        $eventBuilder->join("mapping_glo_team", "mapping_glo_team.jootball_team_id = tournament_match_event.team_id", "LEFT");
        $eventBuilder->join("tbl_event", "tournament_match_event.event_id = tbl_event.id", "left");
        $eventBuilder->where('match_id', $match_id);
        $eventBuilder->orderBy("minute", "ASC");
        $events = $eventBuilder->get()->getResultArray();

        foreach ($events as $key => $e) {
            $playerBuilder = $this->db->table("player");
            $player_main_action = $playerBuilder->select("mapping_glo_player.focal_player_id as id, first_name as firstName, last_name as lastName, pt_glo.shirt_number as jerseyNumber")
            ->where('player.id', $e['playerMainAction'])
            ->join("mapping_glo_player", "mapping_glo_player.jootball_player_id = player.id", "LEFT")
            ->join("academy_player_enrollments pt_glo", "pt_glo.player_id = player.id AND pt_glo.is_primary = 1 AND pt_glo.is_active = 1", "LEFT")
            ->get()->getRowArray();
            $events[$key]['playerMainAction'] = $player_main_action;

            $player_main_action = $playerBuilder->select("mapping_glo_player.focal_player_id as id, first_name as firstName, last_name as lastName, pt_glo.shirt_number as jerseyNumber")
            ->where('player.id', $e['playerSubAction'])
            ->join("mapping_glo_player", "mapping_glo_player.jootball_player_id = player.id", "LEFT")
            ->join("academy_player_enrollments pt_glo", "pt_glo.player_id = player.id AND pt_glo.is_primary = 1 AND pt_glo.is_active = 1", "LEFT")
            ->get()->getRowArray();
            $events[$key]['playerSubAction'] = $player_main_action;
        }
        $result['events'] = $events;

        return  $result;
    }

    function getPlayerState($player_id)
    {
        $playerBuilder = $this->db->table('player');
        $playerBuilder->select("
            CONCAT(player.first_name, ' ',player.last_name) name,
            coarse_aps.name_th as position");
        $playerBuilder->join("academy_player_enrollments pt_aps", "pt_aps.player_id = player.id AND pt_aps.is_primary = 1 AND pt_aps.is_active = 1", "LEFT");
        $playerBuilder->join("tbl_player_position", "tbl_player_position.id = pt_aps.position_id", "LEFT");
        $playerBuilder->join("tbl_player_position coarse_aps", "coarse_aps.id = COALESCE(NULLIF(tbl_player_position.parent_id,0), tbl_player_position.id)", "LEFT");
        $player = $playerBuilder->where('player.id', $player_id)->get()->getRowArray();

        $player['states'] = [];

        $eventBuilder = $this->db->table('tbl_event');
        $events = $eventBuilder->select("*")->whereNotIn("id", [2])->get()->getResultArray();

        foreach ($events as $key => $event) {
            $matchEvent = $this->db->table('tournament_match_event');
            $resultCount =  $matchEvent->select("count(*) as count")->where('player_1_id', $player_id)->where('event_id', $event['id'])->get()->getRowArray();
            $player['states'][$key] = array(
                "name" => $event['name'],
                "value" => (int)$resultCount['count']
            );
        }

        return $player;
    }
}
