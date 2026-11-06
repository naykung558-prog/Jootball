<?php

namespace Modules\Coach\Controllers;

use App\Controllers\BaseController;
use Modules\Coach\Models\CoachModel;
use Modules\Player\Models\PlayerModel;

class TeamManagement extends BaseController
{
    protected $coachModel;
    protected $playerModel;

    public function __construct()
    {
        $this->coachModel = new CoachModel();
        $this->playerModel = new PlayerModel();
    }

    public function index($teamId = null)
    {
        $session = session();
        $coachId = $session->get('coach_id');

        // ตรวจสอบว่าผู้ฝึกสอนมีสิทธิ์เข้าถึงทีมนี้หรือไม่
        if (!$this->hasTeamAccess($coachId, $teamId)) {
            return redirect()->to('/coach/dashboard')->with('error', 'คุณไม่มีสิทธิ์เข้าถึงทีมนี้');
        }

        $data['team'] = $this->getTeamInfo($teamId);
        $data['players'] = $this->getTeamPlayers($teamId);
        $data['coachTeams'] = $this->coachModel->getCoachTeams($coachId);

        return view('Modules\Coach\Views\team_management', $data);
    }

    public function players($teamId)
    {
        $session = session();
        $coachId = $session->get('coach_id');

        if (!$this->hasTeamAccess($coachId, $teamId)) {
            return redirect()->to('/coach/dashboard')->with('error', 'คุณไม่มีสิทธิ์เข้าถึงทีมนี้');
        }

        $data['team'] = $this->getTeamInfo($teamId);
        $data['players'] = $this->getTeamPlayers($teamId);

        return view('Modules\Coach\Views\players', $data);
    }

    public function addPlayer($teamId)
    {
        $session = session();
        $coachId = $session->get('coach_id');

        if (!$this->hasTeamAccess($coachId, $teamId)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'คุณไม่มีสิทธิ์เข้าถึงทีมนี้'
            ]);
        }

        $input = $this->request->getPost();
        $input['team_id'] = $teamId;

        try {
            $result = $this->playerModel->savePlayer($input);

            if ($result) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'เพิ่มนักเตะสำเร็จ'
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'เกิดข้อผิดพลาดในการเพิ่มนักเตะ'
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ]);
        }
    }

    public function editPlayer($teamId, $playerId)
    {
        $session = session();
        $coachId = $session->get('coach_id');

        if (!$this->hasTeamAccess($coachId, $teamId)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'คุณไม่มีสิทธิ์เข้าถึงทีมนี้'
            ]);
        }

        $input = $this->request->getPost();
        $input['id'] = $playerId;
        $input['team_id'] = $teamId;

        try {
            $result = $this->playerModel->savePlayer($input);

            if ($result) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'แก้ไขข้อมูลนักเตะสำเร็จ'
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'เกิดข้อผิดพลาดในการแก้ไขข้อมูลนักเตะ'
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ]);
        }
    }

    public function removePlayer($teamId, $playerId)
    {
        $session = session();
        $coachId = $session->get('coach_id');

        if (!$this->hasTeamAccess($coachId, $teamId)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'คุณไม่มีสิทธิ์เข้าถึงทีมนี้'
            ]);
        }

        try {
            $result = $this->playerModel->deletePlayer($playerId);

            if ($result) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'ลบนักเตะสำเร็จ'
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'เกิดข้อผิดพลาดในการลบนักเตะ'
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ]);
        }
    }

    private function hasTeamAccess($coachId, $teamId)
    {
        $teams = $this->coachModel->getCoachTeams($coachId);
        foreach ($teams as $team) {
            if ($team['id'] == $teamId) {
                return true;
            }
        }
        return false;
    }

    private function getTeamInfo($teamId)
    {
        $db = db_connect();
        $builder = $db->table('team');
        $builder->select('*');
        $builder->where('id', $teamId);
        $builder->where('is_active', 1);
        return $builder->get()->getRowArray();
    }

    private function getTeamPlayers($teamId)
    {
        $db = db_connect();
        $builder = $db->table('player');
        $builder->select('player.*,
                          COALESCE(NULLIF(tbl_player_position.parent_id,0), tbl_player_position.id) as player_position_id,
                          pt_tm.shirt_number as shirt_number,
                          pt_tm.sub_position_ids as player_sub_position_id,
                          coarse_tm.name_th,
                          tbl_provinces.province_name,
                          tbl_districts.district_name as amphure_name,
                          tbl_subdistricts.subdistrict_name as tambon_name')
                ->join('academy_player_enrollments pt_tm', 'pt_tm.player_id = player.id AND pt_tm.is_active = 1', 'INNER')
                ->join("tbl_player_position", "tbl_player_position.id = pt_tm.position_id", "LEFT")
                ->join("tbl_player_position coarse_tm", "coarse_tm.id = COALESCE(NULLIF(tbl_player_position.parent_id,0), tbl_player_position.id)", "LEFT")
                ->join("tbl_provinces", "player.province_id = tbl_provinces.province_id", "LEFT")
                ->join("tbl_districts", "player.amphure_id = tbl_districts.district_id", "LEFT")
                ->join("tbl_subdistricts", "player.tambon_id = tbl_subdistricts.subdistrict_id", "LEFT")
                ->where('pt_tm.team_id', $teamId)
                ->where('player.is_active', 1)
                ->groupBy('player.id')
                ->orderBy('CAST(pt_tm.shirt_number AS unsigned)', 'ASC');

        return $builder->get()->getResultArray();
    }
}