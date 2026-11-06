<?php

namespace Modules\Detail\Controllers;

use App\Controllers\BaseController;
use \Hermawan\DataTables\DataTable;
use App\Libraries\Date_thai;
use App\Models\Main_Model;

class Detail extends BaseController
{
    protected $Main_Model;
    protected $db;

    public function __construct()
    {
        $this->Main_Model = new Main_Model();
        $this->db = \Config\Database::connect();
    }

    public function team($team_id)
    {
        $data = array();
        $data['team'] = $this->Main_Model->find_ji('team','id',$team_id);

        // ดึง position map ทั้งหมดสำหรับ lookup sub-position
        $allPositions = $this->db->table('tbl_player_position')->get()->getResultArray();
        $positionMap = [];
        foreach ($allPositions as $pos) {
            $positionMap[$pos['id']] = $pos;
        }

        // Check for parent team (to share player/coach pool)
        $teamInfo = $this->db->table('team')->select('id, parent_team_id')->where('id', $team_id)->get()->getRowArray();

        // Fetch players with position data using JOIN
        $builder = $this->db->table('player');
        // ครอบคอลัมน์จากตาราง join ด้วย ANY_VALUE() เพื่อให้เข้ากันได้กับ sql_mode=ONLY_FULL_GROUP_BY (MySQL 8 default)
        // เพราะใช้ GROUP BY player.id แต่ดึงคอลัมน์จากตารางที่ join มา
        $builder->select('player.*, ANY_VALUE(COALESCE(NULLIF(tbl_player_position.parent_id,0), tbl_player_position.id)) as player_position_id, ANY_VALUE(pt_det.shirt_number) as shirt_number, ANY_VALUE(pt_det.sub_position_ids) as player_sub_position_id, ANY_VALUE(coarse_det.name_th) as position_name, ANY_VALUE(coarse_det.name_en) as position_name_en', false);
        $builder->join('academy_player_enrollments pt_det', 'pt_det.player_id = player.id AND pt_det.is_active = 1', 'INNER');
        $builder->join('tbl_player_position', 'tbl_player_position.id = pt_det.position_id', 'LEFT');
        $builder->join('tbl_player_position coarse_det', 'coarse_det.id = COALESCE(NULLIF(tbl_player_position.parent_id,0), tbl_player_position.id)', 'LEFT');

        if ($teamInfo && !empty($teamInfo['parent_team_id'])) {
            // If sub-team, show players from both this team and parent team
            $builder->groupStart()
                ->where('pt_det.team_id', $team_id)
                ->orWhere('pt_det.team_id', $teamInfo['parent_team_id'])
                ->groupEnd();
        } else {
            $builder->where('pt_det.team_id', $team_id);
        }

        $builder->where('player.is_active', 1);
        $builder->groupBy('player.id');
        $builder->orderBy('player_position_id', 'ASC', false); // ใช้ alias (ANY_VALUE แล้ว) — ปลอดภัยกับ ONLY_FULL_GROUP_BY
        $builder->orderBy('ANY_VALUE(CAST(pt_det.shirt_number AS UNSIGNED))', 'ASC', false);
        $players = $builder->get()->getResultArray();

        // แปลง sub-position ของแต่ละนักเตะ เป็นชื่อตำแหน่ง
        foreach ($players as &$player) {
            $subPositionNames = [];
            if (!empty($player['player_sub_position_id'])) {
                $decoded = json_decode(html_entity_decode($player['player_sub_position_id']), true);
                if (!is_array($decoded)) {
                    $decoded = array_filter(explode(',', html_entity_decode($player['player_sub_position_id'])));
                }
                foreach ($decoded as $subId) {
                    $subId = (int) $subId;
                    if (isset($positionMap[$subId])) {
                        $subPositionNames[] = $positionMap[$subId]['name_en'];
                        // ถ้าไม่มี player_position_id ให้ใช้ parent_id ของ sub-position แทน
                        if (empty($player['player_position_id']) && !empty($positionMap[$subId]['parent_id'])) {
                            $parentId = $positionMap[$subId]['parent_id'];
                            $player['player_position_id'] = $parentId;
                            $player['position_name'] = $positionMap[$parentId]['name_th'] ?? 'ไม่ระบุตำแหน่ง';
                            $player['position_name_en'] = $positionMap[$parentId]['name_en'] ?? 'Unspecified';
                        }
                    }
                }
            }
            $player['sub_position_display'] = !empty($subPositionNames) ? implode(', ', $subPositionNames) : '';
        }
        unset($player);

        // Group players by position
        $playersByPosition = [];
        foreach ($players as $player) {
            $positionId = $player['player_position_id'] ?? 0;
            if (!isset($playersByPosition[$positionId])) {
                $playersByPosition[$positionId] = [
                    'position_name' => $player['position_name'] ?? 'ไม่ระบุตำแหน่ง',
                    'position_name_en' => $player['position_name_en'] ?? 'Unspecified',
                    'players' => []
                ];
            }
            $playersByPosition[$positionId]['players'][] = $player;
        }

        // Define the order of positions (GK first, then Defender, Midfielder, Attacker)
        $positionOrder = [4, 3, 2, 1]; // Goalkeeper, Defender, Midfielder, Attacker
        $sortedPlayersByPosition = [];

        // Sort by predefined order
        foreach ($positionOrder as $posId) {
            if (isset($playersByPosition[$posId])) {
                $sortedPlayersByPosition[$posId] = $playersByPosition[$posId];
                unset($playersByPosition[$posId]);
            }
        }

        // Add any remaining positions (like unspecified)
        foreach ($playersByPosition as $posId => $posData) {
            $sortedPlayersByPosition[$posId] = $posData;
        }

        $data['playersByPosition'] = $sortedPlayersByPosition;
        $data['player'] = $players; // Keep original for backward compatibility

        // ดึงประวัติการแข่งขัน: ทัวร์นาเมนต์ที่ทีมเคยลงแข่ง
        $data['tournaments'] = $this->db->table('tournament_team tt')
            ->select('t.tour_id, t.name, t.name_en, t.logo, t.start_date, t.end_date,
                      t.tournament_type, tt.register_status, tt.create_date as join_date,
                      (SELECT COUNT(*) FROM tournament_match_result tmr
                       WHERE (tmr.team1_id = tt.team_id OR tmr.team2_id = tt.team_id)
                         AND tmr.tour_id = t.tour_id) as match_count', false)
            ->join('tournament t', 't.tour_id = tt.tour_id')
            ->where('tt.team_id', (int)$team_id)
            ->where('tt.register_status', 1)
            ->where('t.is_deleted', 0)
            ->orderBy('t.start_date', 'DESC')
            ->get()
            ->getResultArray();

        // ดึง Academy ที่ทีมนี้สังกัด
        $data['academy'] = $this->db->table('academy_teams')
            ->select('academy.id, academy.name, academy.name_en, academy.logo')
            ->join('academy', 'academy.id = academy_teams.academy_id')
            ->where('academy_teams.team_id', (int)$team_id)
            ->where('academy.is_active', 1)
            ->get()
            ->getRowArray();

        // ดึงโค้ช: รองรับทั้ง coach.team_id (legacy) และ coach_teams junction table (multi-team)
        $tid = (int)$team_id;
        if ($teamInfo && !empty($teamInfo['parent_team_id'])) {
            $pid = (int)$teamInfo['parent_team_id'];
            $data['coach'] = $this->db->query("
                SELECT DISTINCT c.*
                FROM coach c
                WHERE c.is_active = 1
                  AND (
                      c.team_id IN ({$tid}, {$pid})
                      OR EXISTS (
                          SELECT 1 FROM coach_teams ct
                          WHERE ct.coach_id = c.id
                            AND ct.team_id IN ({$tid}, {$pid})
                            AND ct.is_active = 1
                      )
                  )
                ORDER BY c.first_name ASC
            ")->getResultArray();
        } else {
            $data['coach'] = $this->db->query("
                SELECT DISTINCT c.*
                FROM coach c
                WHERE c.is_active = 1
                  AND (
                      c.team_id = {$tid}
                      OR EXISTS (
                          SELECT 1 FROM coach_teams ct
                          WHERE ct.coach_id = c.id
                            AND ct.team_id = {$tid}
                            AND ct.is_active = 1
                      )
                  )
                ORDER BY c.first_name ASC
            ")->getResultArray();
        }
        return view('Modules\Detail\Views\team.php', $data);
    }
}