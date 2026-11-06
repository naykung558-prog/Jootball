<?php

namespace Modules\Team\Controllers\Traits;

use App\Controllers\BaseController;
use Modules\User\Models\UserModel;
use Modules\Team\Models\TeamModel;
use Modules\Player\Models\PlayerModel;
use \Hermawan\DataTables\DataTable;
use App\Libraries\Date_thai;
use App\Traits\JerseyManagementTrait;
use App\Traits\PlayerAjaxTrait;
use App\Traits\ChartSelectionTrait;
use Modules\Team\Controllers\Traits\TeamNotificationTrait;

trait TeamSubmitCoachTrait
{
    /**
     * Submit player list for a tournament
     * POST /team/submit-player-list
     */
    public function submitPlayerList()
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'ไม่ได้รับการยืนยันตัวตน'
            ])->setStatusCode(401);
        }

        try {
            $tourTeamId = $this->request->getPost('tour_team_id');

            if (!$tourTeamId) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'กรุณาเลือกรายการแข่งขัน'
                ]);
            }

            // Get tournament model
            $tournamentModel = new \Modules\Admin\Models\TournamentModel();

            // Get team_id and tour_id from tournament_team using tour_team_id
            $tourTeamData = $this->db->table('tournament_team')
                ->where('tour_team_id', $tourTeamId)
                ->get()
                ->getRowArray();

            if (!$tourTeamData || !isset($tourTeamData['team_id'])) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'ไม่พบข้อมูลรายการแข่งขัน'
                ]);
            }

            $teamId = $tourTeamData['team_id'];
            $tourId = $tourTeamData['tour_id'];

            // Get saved selection from database for this team
            $selection = $this->teamModel->getPlayerChartSelection($teamId);

            if (empty($selection)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'กรุณาบันทึกการเลือกนักเตะก่อนทำการส่งรายชื่อ'
                ]);
            }

            // Filter out stale player IDs (players no longer in team)
            // รองรับ sub-team: เช็ค parent_team_id ด้วย
            $teamInfo = $this->db->table('team')->select('id, parent_team_id')->where('id', $teamId)->get()->getRowArray();
            $teamIdsToCheck = [$teamId];
            if ($teamInfo && !empty($teamInfo['parent_team_id'])) {
                $teamIdsToCheck[] = $teamInfo['parent_team_id'];
            }

            // ใช้ player_teams table (multi-team support) พร้อม fallback player.team_id
            $validPlayerIds = $this->db->table('player')
                ->select('player.id')
                ->join('academy_player_enrollments', 'academy_player_enrollments.player_id = player.id AND academy_player_enrollments.is_active = 1', 'LEFT')
                ->where('player.is_active', 1)
                ->groupStart()
                    ->whereIn('academy_player_enrollments.team_id', $teamIdsToCheck)
                    ->orWhereIn('player.team_id', $teamIdsToCheck)
                ->groupEnd()
                ->groupBy('player.id')
                ->get()->getResultArray();
            $validIds = array_column($validPlayerIds, 'id');
            $selection = array_values(array_filter($selection, function ($id) use ($validIds) {
                return in_array($id, $validIds);
            }));

            // Get tournament details to check player_count limit
            $tournament = $tournamentModel->getOneTournament($tourId);

            if ($tournament && !empty($tournament['player_count'])) {
                $maxPlayers = (int) $tournament['player_count'];
                $selectedCount = count($selection);

                if ($selectedCount > $maxPlayers) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => "จำนวนนักเตะที่เลือก ({$selectedCount} คน) เกินกว่าที่กำหนดไว้ในรายการแข่งขัน (สูงสุด {$maxPlayers} คน)"
                    ]);
                }
            }

            // Blacklist: players previously submitted in the configured source tournament(s) cannot be submitted
            if ($tournament && !empty($tournament['require_source_players'])) {
                $sourceTourIds = array_column($tournamentModel->getSourceTournaments($tourId), 'source_tour_id');
                if (!empty($sourceTourIds)) {
                    $ineligible = $tournamentModel->checkIneligibleSourcePlayers($tourTeamId, $selection, $sourceTourIds);
                    if (!empty($ineligible)) {
                        $ineligibleMessages = [];
                        foreach ($ineligible as $inel) {
                            $shirt = !empty($inel['shirt_number']) ? ' (' . $inel['shirt_number'] . ')' : '';
                            $line = $inel['player_name'] . $shirt;
                            if (!empty($inel['history_text'])) {
                                $line .= ' — ' . lang('Common.source_prev_submitted_label') . ': ' . $inel['history_text'];
                            }
                            $ineligibleMessages[] = $line;
                        }
                        return $this->response->setJSON([
                            'status' => 'error',
                            'message' => lang('Common.source_player_not_eligible_found'),
                            'ineligible_players' => $ineligible,
                            'ineligible_list' => implode("\n", $ineligibleMessages)
                        ]);
                    }
                } else {
                    // require_source_players เปิดอยู่ แต่ยังไม่ได้ตั้ง source tournaments
                    // Fail-safe: บล็อกการส่ง (กันกรณี config ไม่สมบูรณ์แล้วปล่อยนักเตะทุกคนผ่าน)
                    log_message('warning', "require_source_players enabled for tour {$tourId} but no source tournaments configured");
                    return $this->response->setJSON([
                        'status'  => 'error',
                        'message' => lang('Common.source_config_incomplete')
                    ]);
                }
            }

            // ตรวจสอบนักเตะซ้ำข้ามทีม (ถ้าเปิดใช้งาน)
            if ($tournament && !empty($tournament['prevent_duplicate_player'])) {
                $linkedTours = $tournamentModel->getLinkedTournaments($tourId);
                $linkedTourIds = array_column($linkedTours, 'linked_tour_id');
                $duplicates = $tournamentModel->checkDuplicatePlayers($tourId, $tourTeamId, $selection, $linkedTourIds);

                if (!empty($duplicates)) {
                    $duplicateMessages = [];
                    foreach ($duplicates as $dup) {
                        $duplicateMessages[] = $dup['player_name'] . ' (' . lang('Common.duplicate_claimed_by') . ': ' . $dup['claimed_by_team'] . ')';
                    }
                    $duplicateList = implode("\n", $duplicateMessages);

                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => lang('Common.duplicate_player_found'),
                        'duplicates' => $duplicates,
                        'duplicate_list' => $duplicateList
                    ]);
                }
            }

            // Save submitted players ก่อน — ถ้า fail จะไม่ set status (กัน status=submitted แต่ไม่มีรายชื่อ)
            if (!$tournamentModel->saveSubmittedPlayers($tourTeamId, $selection)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'เกิดข้อผิดพลาดในการบันทึกรายชื่อนักเตะ กรุณาลองใหม่อีกครั้ง'
                ]);
            }

            // Update player list status (หลังบันทึกรายชื่อสำเร็จแล้วเท่านั้น)
            $result = $tournamentModel->submitPlayerListStatus($tourTeamId);

            // (ยกเลิกแล้ว) เดิมคัดลอกรูปชุดมาลง tournament_team.jersey_*
            // ตอนนี้ชุดถูกแก้ที่ tournament_team โดยตรง (home_set_img/away_set_img/...) ต่อทัวร์นาเมนต์

            if ($result) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'ยืนยันการส่งรายชื่อนักเตะเรียบร้อยแล้ว'
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'ไม่สามารถบันทึกข้อมูลได้'
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Get submitted players for a tournament team
     * @param int $tourTeamId
     * @return \CodeIgniter\HTTP\Response
     */
    public function getSubmittedPlayers($tourTeamId)
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'ไม่ได้รับการยืนยันตัวตน'
            ])->setStatusCode(401);
        }

        try {
            $tournamentModel = new \Modules\Admin\Models\TournamentModel();
            $playerIds = $tournamentModel->getSubmittedPlayers($tourTeamId);

            return $this->response->setJSON([
                'status' => 'success',
                'data' => $playerIds
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    // =====================================================
    // Coach Join Request Management (Team Side)
    // =====================================================

    /**
     * หน้าจัดการคำขอเข้าร่วมจาก coach
     */
    public function coachRequests($team_id = null)
    {
        if (!$team_id) {
            $team_id = session()->get('team.id');
        }

        if (!$team_id) {
            return redirect()->to('/team/info')->with('error', lang('Common.team_not_found'));
        }

        // Get team data
        $teamData = $this->teamModel->get_Teams($team_id);
        if (!$teamData) {
            return redirect()->to('/team/info')->with('error', lang('Common.team_not_found'));
        }

        // Check access - only owner or manager can view coach requests
        $isOwner = $teamData['create_by'] == session()->get('user_id');
        $isManager = false;
        $managers = $this->teamModel->getTeamManagers($team_id);
        foreach ($managers as $mgr) {
            if ($mgr['user_id'] == session()->get('user_id')) {
                $isManager = true;
                break;
            }
        }

        if (!$isOwner && !$isManager) {
            return redirect()->to('/team/info')->with('error', lang('Common.no_access_team'));
        }

        $data['team'] = $teamData;
        $data['pendingRequests'] = $this->teamModel->getPendingCoachRequests($team_id);
        $data['allRequests'] = $this->teamModel->getAllCoachRequests($team_id);

        return view('Modules\Team\Views\coach_requests.php', $data);
    }

    /**
     * อนุมัติคำขอเข้าร่วมจาก coach
     */
    public function approveCoachRequest($requestId)
    {
        // Get request details
        $request = $this->teamModel->getCoachJoinRequest($requestId);
        if (!$request) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => lang('Common.request_not_found')
            ]);
        }

        // Verify access
        $teamData = $this->teamModel->get_Teams($request['team_id']);
        $isOwner = $teamData && $teamData['create_by'] == session()->get('user_id');
        $isManager = false;
        if ($teamData) {
            $managers = $this->teamModel->getTeamManagers($request['team_id']);
            foreach ($managers as $mgr) {
                if ($mgr['user_id'] == session()->get('user_id')) {
                    $isManager = true;
                    break;
                }
            }
        }

        if (!$isOwner && !$isManager) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => lang('Common.no_permission')
            ]);
        }

        $message = $this->request->getPost('message');

        // Log for debugging
        log_message('debug', "approveCoachRequest - Request data: " . json_encode($request));
        log_message('debug', "approveCoachRequest - team_id from request: " . ($request['team_id'] ?? 'null'));

        $result = $this->teamModel->respondToCoachJoinRequest(
            $requestId,
            $request['team_id'],
            session()->get('user_id'),
            'approved',
            $message
        );

        // Handle different return types for debugging
        if (is_array($result) && isset($result['error'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Model error: ' . $result['error'],
                'debug' => $result
            ]);
        }

        if ($result === true) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => lang('Common.request_approved')
            ]);
        } else {
            // Get more info for debugging
            $debugRequest = $this->db->table('coach_team_requests')
                ->where('id', $requestId)
                ->get()
                ->getRowArray();

            log_message('error', "approveCoachRequest failed - Request in DB: " . json_encode($debugRequest));

            return $this->response->setJSON([
                'status' => 'error',
                'message' => lang('Common.error_approving_request'),
                'debug' => [
                    'request_id' => $requestId,
                    'db_status' => $debugRequest['status'] ?? 'not_found',
                    'db_is_active' => $debugRequest['is_active'] ?? 'not_found',
                    'db_team_id' => $debugRequest['team_id'] ?? 'not_found',
                    'passed_team_id' => $request['team_id'] ?? 'not_found'
                ]
            ]);
        }
    }

    /**
     * ปฏิเสธคำขอเข้าร่วมจาก coach
     */
    public function rejectCoachRequest($requestId)
    {
        // Get request details
        $request = $this->teamModel->getCoachJoinRequest($requestId);
        if (!$request) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => lang('Common.request_not_found')
            ]);
        }

        // Verify access
        $teamData = $this->teamModel->get_Teams($request['team_id']);
        $isOwner = $teamData && $teamData['create_by'] == session()->get('user_id');
        $isManager = false;
        if ($teamData) {
            $managers = $this->teamModel->getTeamManagers($request['team_id']);
            foreach ($managers as $mgr) {
                if ($mgr['user_id'] == session()->get('user_id')) {
                    $isManager = true;
                    break;
                }
            }
        }

        if (!$isOwner && !$isManager) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => lang('Common.no_permission')
            ]);
        }

        $message = $this->request->getPost('message');
        $result = $this->teamModel->respondToCoachJoinRequest(
            $requestId,
            $request['team_id'],
            session()->get('user_id'),
            'rejected',
            $message
        );

        if ($result) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => lang('Common.request_rejected')
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => lang('Common.error_rejecting_request')
            ]);
        }
    }
}
