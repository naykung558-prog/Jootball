<?php

namespace Modules\Admin\Models;

use CodeIgniter\Model;
use \Hermawan\DataTables\DataTable;
use \DateTime;

class ReportGuestModel extends Model
{
    protected $table      = "";
    protected $primaryKey = "";
    protected $allowedFields = [];

    function getAverageScorePlayer($limit = 0)
    {
        $builder = $this->db->table('player_match_state');
        $builder->select("
            player.id,
            player.first_name,
            player.last_name,
            player.image_path,
            COUNT(match_id) AS matches_played,
            ROUND((
                SUM(
                    (shots * 0.5) +
                    (shots_on_target * 0.5) +
                    (success_passed * 0.5) +
                    (tackles * 0.5) +
                    (interceptions * 1.0) +
                    (saves * 2) +
                    ( (select count(*)
                        from tournament_match_event
                        where tournament_match_event.event_id = '1' and tournament_match_event.player_1_id = player.id) * 1.5) +
                    ( (select count(*)
                        from tournament_match_event
                        where tournament_match_event.event_id = '1' and tournament_match_event.player_2_id = player.id) * 1) -
                    (fouls_committed * 1) -
                    (offside * 0.5)
                ) / COUNT(match_id)
            ) / 20 * 10, 2) AS average_score_scaled
        ");
        $builder->join("player", "player_match_state.player_id = player.id", "left");
        $builder->groupBy("player.id, player.first_name, player.last_name, player.image_path");
        $builder->orderBy("average_score_scaled", "DESC");
        if ($limit > 0) {
            $builder->limit($limit);
        }
        $result = $builder->get()->getResultArray();
        return $result;
    }

    function getPopularPosition()
    {
        $builder = $this->db->table('player');
        $builder->select("coarse.name_th, COUNT(*) AS total");
        $builder->join("academy_player_enrollments ape_pop", "ape_pop.player_id = player.id AND ape_pop.is_primary = 1 AND ape_pop.is_active = 1", "INNER");
        $builder->join("tbl_player_position fine_pop", "fine_pop.id = ape_pop.position_id", "INNER");
        $builder->join("tbl_player_position coarse", "coarse.id = COALESCE(NULLIF(fine_pop.parent_id,0), fine_pop.id)", "INNER");
        $builder->whereIn("coarse.id", [1, 2, 3, 4]);
        $builder->groupBy("coarse.name_th");
        $builder->orderBy("total DESC");
        $builder->limit(1);
        $result = $builder->get()->getRowArray();
        return $result;
    }

    function getAverageAgePlayer()
    {
        $builder = $this->db->table('player');
        $builder->select("AVG(TIMESTAMPDIFF(YEAR, player.birth_date, CURDATE())) AS average_age");
        $builder->join("academy_player_enrollments ape_avg", "ape_avg.player_id = player.id AND ape_avg.is_primary = 1 AND ape_avg.is_active = 1", "INNER");
        $builder->join("tbl_player_position fine_avg", "fine_avg.id = ape_avg.position_id", "INNER");
        $builder->where("COALESCE(NULLIF(fine_avg.parent_id,0), fine_avg.id) IN (1,2,3,4)", null, false);
        $result = $builder->get()->getRowArray();
        return ceil($result['average_age']);
    }

    function getPlayerGroupByPosition()
    {
        $builder = $this->db->table('tbl_player_position');

        $builder->select('
            tbl_player_position.id,
	        tbl_player_position.name_th,
	        (SELECT COUNT(DISTINCT ape_cnt.player_id) FROM academy_player_enrollments ape_cnt JOIN tbl_player_position fine_cnt ON fine_cnt.id = ape_cnt.position_id WHERE COALESCE(NULLIF(fine_cnt.parent_id,0), fine_cnt.id) = tbl_player_position.id AND ape_cnt.is_active = 1) as count_value
        ');
        $builder->whereIn('tbl_player_position.id', [1, 2, 3, 4]);
        $result = $builder->get()->getResultArray();
        return $result;
    }

    function getPlayersList()
    {
        $builder = $this->db->table('player');
        $builder->select("
            player.id, 
            player.first_name, 
            player.last_name, 
            player.birth_date,
            COALESCE(NULLIF(tbl_player_position.parent_id,0), tbl_player_position.id) as player_position_id,
            pt_rg1.shirt_number as shirt_number,
            coarse_rg1.name_th as position_name_th,
            coarse_rg1.name_en as position_name_en,
            player.image_path,
            team.name as team_name");
        $builder->join("academy_player_enrollments pt_rg1", "pt_rg1.player_id = player.id AND pt_rg1.is_primary = 1 AND pt_rg1.is_active = 1", "LEFT");
        $builder->join("tbl_player_position", "tbl_player_position.id = pt_rg1.position_id", "LEFT");
        $builder->join("tbl_player_position coarse_rg1", "coarse_rg1.id = COALESCE(NULLIF(tbl_player_position.parent_id,0), tbl_player_position.id)", "LEFT");
        $builder->join("team", "team.id = pt_rg1.team_id", "LEFT");
        $builder->where("COALESCE(NULLIF(tbl_player_position.parent_id,0), tbl_player_position.id) IN (1,2,3,4)", null, false);
        $builder->where("is_active", 1);
        $result = $builder->get()->getResultArray();
        return $result;
    }

    function getCountPlayer()
    {
        $builder = $this->db->table('player');
        $builder->select("COUNT(*) as count_value");
        $builder->join("academy_player_enrollments pt_rg2", "pt_rg2.player_id = player.id AND pt_rg2.is_primary = 1 AND pt_rg2.is_active = 1", "LEFT");
        $builder->join("tbl_player_position", "tbl_player_position.id = pt_rg2.position_id", "LEFT");
        $builder->join("team", "team.id = pt_rg2.team_id", "LEFT");
        $builder->where("COALESCE(NULLIF(tbl_player_position.parent_id,0), tbl_player_position.id) IN (1,2,3,4)", null, false);
        $builder->where("player.is_active", 1);
        $result = $builder->get()->getRowArray();
        return $result['count_value'];
    }

    function getPlayersList_ajax()
    {
        $builder = $this->db->table('player');
        if(isset($_POST['search']['value']) && $_POST['search']['value']!='')
        {
            $builder->select("
                player.id, 
                player.first_name, 
                player.last_name, 
                player.birth_date,
                COALESCE(NULLIF(tbl_player_position.parent_id,0), tbl_player_position.id) as player_position_id,
                pt_rg3.shirt_number as shirt_number,
                coarse_rg3.name_th as position_name_th,
                coarse_rg3.name_en as position_name_en,
                player.image_path,
                team.name as team_name");
            $builder->join("academy_player_enrollments pt_rg3", "pt_rg3.player_id = player.id AND pt_rg3.is_primary = 1 AND pt_rg3.is_active = 1", "LEFT");
            $builder->join("tbl_player_position", "tbl_player_position.id = pt_rg3.position_id", "LEFT");
            $builder->join("tbl_player_position coarse_rg3", "coarse_rg3.id = COALESCE(NULLIF(tbl_player_position.parent_id,0), tbl_player_position.id)", "LEFT");
            $builder->join("team", "team.id = pt_rg3.team_id", "LEFT");
            $builder->where("COALESCE(NULLIF(tbl_player_position.parent_id,0), tbl_player_position.id) IN (1,2,3,4)", null, false);
            $builder->where("player.is_active", 1);
        }else {
            $builder->select("
                player.*");
            $builder->where("player.is_active", 999);
        }

        return DataTable::of($builder)
            ->add('player_detail', function ($row) {
                $img = '';
                if (!empty($row->image_path)) {
                    if (file_exists('./' . $row->image_path)) {
                        $img = '<img src="' . base_url($row->image_path) . '" class="rounded-circle" width="40" height="40" alt="modernize-img">';
                    } else {
                        $img = '<img src="' . base_url("public/img/football-player.webp") . '" class="rounded-circle" width="40" height="40" alt="modernize-img">';
                    }
                } else {
                    $img = '<img src="' . base_url("public/img/football-player.webp") . '" class="rounded-circle" width="40" height="40" alt="modernize-img">';
                }
                $detail_links = base_url('admin/report-guest/player-details/' . $row->id);
                $html = <<<HTML
                    <div class="d-flex align-items-center">
                        <div class="me-2 pe-1">
                            $img            
                        </div>
                        <a href="$detail_links" target="_blank">
                            <h6 class="fw-semibold mb-1">$row->first_name  $row->last_name</h6>
                            <p class="fs-2 mb-0 text-muted">เบอร์ $row->shirt_number</p>
                        </a>
                    </div>
                   HTML;
                return $html;
            })
            ->add('position', function ($row) {
                if ($row->player_position_id == 1) {
                    $badge = <<<HTML
                        <span class="badge fw-semibold py-1 w-85 bg-danger-subtle text-danger">$row->position_name_th</span>
                    HTML;
                }
                if ($row->player_position_id == 2) {
                    $badge = <<<HTML
                        <span class="badge fw-semibold py-1 w-85 bg-success-subtle text-success">$row->position_name_th</span>
                    HTML;
                }
                if ($row->player_position_id == 3) {
                    $badge = <<<HTML
                        <span class="badge fw-semibold py-1 w-85 bg-info-subtle text-info">$row->position_name_th</span>
                    HTML;
                }
                if ($row->player_position_id == 4) {
                    $badge = <<<HTML
                        <span class="badge fw-semibold py-1 w-85 bg-warning-subtle text-warning">$row->position_name_th</span>
                    HTML;
                }
                return $badge;
            })
            ->add('age', function ($row) {
                return $this->calculateAge($row->birth_date) . ' ปี';
            })
            ->filter(function ($builder, $request) {
                if (isset($request->player_position_id) && $request->player_position_id)
                    $builder->where("COALESCE(NULLIF(tbl_player_position.parent_id,0), tbl_player_position.id) =", $request->player_position_id);
            })
            ->addNumbering()
            ->setSearchableColumns(['player.first_name', 'player.last_name', 'pt_rg3.shirt_number', 'team.name', 'coarse_rg3.name_th', 'coarse_rg3.name_en'])
            ->toJson(true);
    }

    function getPlayerData($id)
    {
        $builder = $this->db->table('player');
        $builder->select("player.*,
            COALESCE(NULLIF(tbl_player_position.parent_id,0), tbl_player_position.id) as player_position_id,
            pt_rg4.shirt_number as shirt_number,
            team.name as team_name,
            coarse_rg4.name_th as position_name,
            coarse_rg4.name_en as position_name_en,
            (
                select count(*)
                from tournament_match_event
                where tournament_match_event.event_id = '1' and tournament_match_event.player_1_id = player.id
            ) as goals,
            (
                select count(*)
                from tournament_match_event
                where tournament_match_event.event_id = '1' and tournament_match_event.player_2_id = player.id
            ) as assits
        ");
        $builder->join("academy_player_enrollments pt_rg4", "pt_rg4.player_id = player.id AND pt_rg4.is_primary = 1 AND pt_rg4.is_active = 1", "left");
        $builder->join("team", "team.id = pt_rg4.team_id", "left");
        $builder->join("tbl_player_position", "tbl_player_position.id = pt_rg4.position_id", 'left');
        $builder->join("tbl_player_position coarse_rg4", "coarse_rg4.id = COALESCE(NULLIF(tbl_player_position.parent_id,0), tbl_player_position.id)", "left");
        $builder->where('player.id', $id);
        $result = $builder->get()->getRowArray();
        $result['age'] = $this->calculateAge($result['birth_date']);

        $stateBuilder = $this->db->table("player_match_state");
        $stateBuilder->select("
            sum(player_match_state.shots) as shots,
            sum(player_match_state.shots_on_target) as shots_on_target,
            sum(player_match_state.total_passes) as total_passes,
            sum(player_match_state.success_passed) as success_passed,
            sum(player_match_state.tackles) as tackles,
            sum(player_match_state.interceptions) as interceptions,
            sum(player_match_state.fouls_committed) as fouls_committed,
            sum(player_match_state.offside) as offside,
            sum(player_match_state.saves) as saves
        ");
        $stateBuilder->where("player_match_state.player_id", $result['id']);
        $states = $stateBuilder->get()->getRowArray();
        $result['states'] = $states;

        $stateBuilder = $this->db->table('player_match_state');
        $stateBuilder->select("
            COUNT(match_id) AS matches_played,
            ROUND((
                SUM(
                    (shots * 0.5) +
                    (shots_on_target * 0.5) +
                    (success_passed * 0.5) +
                    (tackles * 0.5) +
                    (interceptions * 1.0) +
                    (saves * 2) +
                    ( (select count(*)
                        from tournament_match_event
                        where tournament_match_event.event_id = '1' and tournament_match_event.player_1_id = {$id}) * 1.5) +
                    ( (select count(*)
                        from tournament_match_event
                        where tournament_match_event.event_id = '1' and tournament_match_event.player_2_id = {$id}) * 1) -
                    (fouls_committed * 1) -
                    (offside * 0.5)
                ) / COUNT(match_id)
            ) / 20 * 10, 2) AS average_score_scaled
        ");
        $stateBuilder->where("player_match_state.player_id", $id);
        $stateScore = $stateBuilder->get()->getRowArray();
        $result['average_score'] = $stateScore['average_score_scaled'];

        return $result;
    }

    function calculateAge($birthdate)
    {
        $birthDate = new DateTime($birthdate);
        $today = new DateTime('today');
        $age = $birthDate->diff($today)->y;
        return $age;
    }
}
