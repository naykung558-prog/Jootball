<?php

namespace Modules\Admin\Models\Traits;

use CodeIgniter\Model;
use \Hermawan\DataTables\DataTable;
use Modules\Admin\Models\Traits\TournamentScoringQueriesTrait;
use Modules\Admin\Models\Traits\TournamentMatchQueriesTrait;
use Modules\Admin\Models\Traits\TournamentSubmissionQueriesTrait;

trait TournamentStatsQueriesTrait
{
    function getStatTourAll($tour_id='') {

        //match_played = จำนวนที่แข่ง , goal_for = ประตูที่ได้ , goal_against = ประตูที่เสีย, goal_diff = ผลต่าง , draw_count = เสมอ
        $builder = $this->db->table('tournament_match_result');
        $builder->select("
            COUNT(*) AS match_played,

            SUM(team1_score + team2_score) AS goal_for,

            SUM(team1_score + team2_score) AS goal_against,

            0 AS goal_diff,

            SUM(CASE WHEN result = 1 THEN 1 ELSE 0 END) AS won_count,
            SUM(CASE WHEN result = 0 THEN 1 ELSE 0 END) AS draw_count,
            SUM(CASE WHEN result = 2 THEN 1 ELSE 0 END) AS lost_count,

            SUM(CASE WHEN team1_score = 0 AND team2_score=0 AND result!=0 AND match_status!=0 THEN 1 ELSE 0 END) AS won_pen
        ");
        $builder->where('match_status!=', 0);
        if($tour_id!='') {
            $builder->where('tour_id', $tour_id);
        }
        $result = $builder->get()->getRowArray();

        // match_played
        $builder = $this->db->table('tournament_match_result tmr');
        $builder->select('COUNT(*) AS match_played');

        if ($tour_id != '') {
            $builder->where('tmr.tour_id', $tour_id);
        }

        $matchPlayed = $builder->get()->getRow()->match_played;


        // team_count (distinct team_id)
        $sql = "
            SELECT COUNT(DISTINCT team_id) AS team_count
            FROM (
                SELECT team1_id AS team_id
                FROM tournament_match_result
                WHERE tour_id = ?
                AND team1_id > 0

                UNION ALL

                SELECT team2_id AS team_id
                FROM tournament_match_result
                WHERE tour_id = ?
                AND team2_id > 0
            ) t
        ";
        //die($sql);

        $teamCount = $this->db->query($sql, [$tour_id, $tour_id])
                            ->getRow()
                            ->team_count;


        // รวมผลลัพธ์
        $result['match_played_pre'] = (int) $matchPlayed;
        $result['team_count'] =  (int) $teamCount;
        $result['coach_count'] =  (int) $teamCount;

        //$result['match_played_pre'] = $result1['match_played']; //จำนวนการแข่งขัน
        //$result['team_count'] = $result1['team_count']; //ทีมที่ลงทะเบียน
        //$result['coach_count'] = $result1['team_count']; //ผู้ฝึกสอน

        //นักเตะ *
        $players = array();
        if($tour_id!='') {
            //$main_table = "tournament_submitted_players";
            $main_table = "tournament_submitted_players";
            $players = $this->db->table($main_table.' tsp')
                ->select('
                    tsp.*,
                    p.id AS player_id,
                    p.first_name,p.last_name,
                    p.image_path,
                    pos.name_th AS position_name_th,
                    pos.name_en AS position_name_en,
                    tt.tour_team_id,
                    team.name as team_name,team.name_en as team_name_en,team.logo as team_logo,
                    COALESCE(ape_st.shirt_number, p.shirt_number) AS shirt_number
                ', false)
                ->join('tournament_team tt', 'tt.tour_team_id = tsp.tour_team_id', 'inner')
                ->join('player p', 'p.id = tsp.player_id', 'inner')
                ->join('academy_player_enrollments ape_st', 'ape_st.player_id = p.id AND ape_st.team_id = tt.team_id AND ape_st.is_active = 1', 'left', false)
                ->join('tbl_player_position pos', 'pos.id = COALESCE(NULLIF(ape_st.position_id,0), p.player_position_id)', 'left', false)
                ->join('team team', 'tt.team_id = team.id', 'left');

            $players->where('tt.tour_id', $tour_id);

            $players = $players->orderBy('tt.tour_team_id', 'ASC')
                ->orderBy('pos.id', 'ASC')
                ->orderBy('p.first_name', 'ASC')
                ->get()
                ->getResultArray();
        }
        $result['player_count'] = @count($players); //ทีมที่ลงทะเบียน summition
        //
        $result['sum_card'] = 0;
        $result['sum_best_player'] = 0;
        //


        // คำนวณช่วงอายุนักเตะ: 0-10, 11-12, 13-14, 15-16 (แทน 14-16 เพื่อไม่ให้ทับซ้อน), 16+ และ N/A
        $builder = $this->db->table('player');
        $builder->select("
            SUM(CASE WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) <= 10
                     AND birth_date IS NOT NULL
                     AND CAST(birth_date AS CHAR) != '0000-00-00'
                     AND CAST(birth_date AS CHAR) != ''
                THEN 1 ELSE 0 END) AS age_0_10,

            SUM(CASE WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 11 AND 12
                     AND birth_date IS NOT NULL
                     AND CAST(birth_date AS CHAR) != '0000-00-00'
                     AND CAST(birth_date AS CHAR) != ''
                THEN 1 ELSE 0 END) AS age_11_12,

            SUM(CASE WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 13 AND 14
                     AND birth_date IS NOT NULL
                     AND CAST(birth_date AS CHAR) != '0000-00-00'
                     AND CAST(birth_date AS CHAR) != ''
                THEN 1 ELSE 0 END) AS age_13_14,

            SUM(CASE WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 15 AND 16
                     AND birth_date IS NOT NULL
                     AND CAST(birth_date AS CHAR) != '0000-00-00'
                     AND CAST(birth_date AS CHAR) != ''
                THEN 1 ELSE 0 END) AS age_14_16,

            SUM(CASE WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) > 16
                     AND birth_date IS NOT NULL
                     AND CAST(birth_date AS CHAR) != '0000-00-00'
                     AND CAST(birth_date AS CHAR) != ''
                THEN 1 ELSE 0 END) AS age_16_plus,

            SUM(CASE WHEN birth_date IS NULL
                     OR CAST(birth_date AS CHAR) = '0000-00-00'
                     OR CAST(birth_date AS CHAR) = ''
                THEN 1 ELSE 0 END) AS age_na
        ");

        // นับเฉพาะนักเตะที่ยัง active (กันนับตัวที่ถูกยุบจากการ merge / soft-deleted)
        $builder->where('is_active', 1);

        $age_result = $builder->get()->getRowArray();

        // นำค่าไปใส่ตัวแปรเพื่อส่งไป View
        $result['age_0_10']    = $age_result['age_0_10'];
        $result['age_11_12']   = $age_result['age_11_12'];
        $result['age_13_14']   = $age_result['age_13_14'];
        $result['age_14_16']   = $age_result['age_14_16']; // จริงๆ คือ 15-16
        $result['age_16_plus'] = $age_result['age_16_plus'];
        $result['age_na']      = $age_result['age_na'];

        //die(print_r($result));
        return $result;
    }

    function getStatTeamAll($team_id='') {

        //match_played = จำนวนที่แข่ง , goal_for = ประตูที่ได้ , goal_against = ประตูที่เสีย, goal_diff = ผลต่าง , draw_count = เสมอ
        $builder = $this->db->table('tournament_match_result');
        $builder->select("
            COUNT(*) AS match_played,

            SUM(
                CASE
                    WHEN team1_id = {$team_id} THEN team1_score
                    WHEN team2_id = {$team_id} THEN team2_score
                    ELSE 0
                END
            ) AS goal_for,

            SUM(
                CASE
                    WHEN team1_id = {$team_id} THEN team2_score
                    WHEN team2_id = {$team_id} THEN team1_score
                    ELSE 0
                END
            ) AS goal_against,

            (
                SUM(
                    CASE
                        WHEN team1_id = {$team_id} THEN team1_score
                        WHEN team2_id = {$team_id} THEN team2_score
                        ELSE 0
                    END
                )
                -
                SUM(
                    CASE
                        WHEN team1_id = {$team_id} THEN team2_score
                        WHEN team2_id = {$team_id} THEN team1_score
                        ELSE 0
                    END
                )
            ) AS goal_diff,

            SUM(CASE WHEN result = 0 THEN 1 ELSE 0 END) AS draw_count
        ");

        $builder->groupStart()
                ->where('team1_id', $team_id)
                ->orWhere('team2_id', $team_id)
                ->groupEnd();

        $result = $builder->get()->getRowArray();
        //

        // match_home_played = แมตที่แข่งในบ้าน , win_home_count = ชนะในบ้าน
        $builder = $this->db->table('tournament_match_result');
        $builder->select('
            COUNT(*) AS match_home_played,
            COUNT(team1_id) AS win_home_count
        ');
        $builder->where('team1_id', $team_id);
        $builder->where('result', 1);
        $tmp = $builder->get()->getRowArray();
        $result['match_home_played'] = $tmp['match_home_played'];
        $result['win_home_count'] = $tmp['win_home_count'];
        //

        // match_away_played = แมตที่แข่งในบ้าน , win_count = ชนะในบ้าน
        $builder = $this->db->table('tournament_match_result');
        $builder->select('
            COUNT(*) AS match_away_played,
            COUNT(team2_id) AS win_away_count
        ');
        $builder->where('team2_id', $team_id);
        $builder->where('result', 2);
        $tmp = $builder->get()->getRowArray();
        $result['match_away_played'] = $tmp['match_away_played'];
        $result['win_away_count'] = $tmp['win_away_count'];
        //
        $result['win_match'] =  $result['win_home_count']+$result['win_away_count'];

        $row = $this->db->table('tournament_match_result')
            ->select("
                COUNT(
                    CASE
                        WHEN team1_id = {$team_id} AND result = 2 THEN 1
                        WHEN team2_id = {$team_id} AND result = 1 THEN 1
                    END
                ) AS match_lost
            ")
            ->get()
            ->getRowArray();

        $result['match_lost'] = (int) ($row['match_lost'] ?? 0);
        return $result;
    }

    function getCntGoal($tour_id='',$team_id='') {
        $builder = $this->db->table('tournament_match_result');

        $builder->select('
            SUM(
                IFNULL(team1_score,0) + IFNULL(team2_score,0)
            ) AS total_goals
        ');

        if ($tour_id != '') {
            $builder->where('tour_id', $tour_id);
        }
        if ($team_id != '') {
            $builder->groupStart()
                    ->where('team1_id', $team_id)
                    ->orWhere('team2_id', $team_id)
                    ->groupEnd();
        }

        $row = $builder->get()->getRowArray();

        return $row['total_goals'] ?? 0;
    }
}
