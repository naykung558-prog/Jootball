<?php

namespace Modules\Academy\Controllers\Traits;

use App\Controllers\BaseController;
use Modules\User\Models\UserModel;
use Modules\Academy\Models\AcademyModel;
use Modules\Player\Models\PlayerModel;
use \Hermawan\DataTables\DataTable;
use App\Libraries\Date_thai;
use Modules\Academy\Controllers\Traits\AcademyAiTrait;
use Modules\Academy\Controllers\Traits\AcademyPlayerTrait;
use Modules\Academy\Controllers\Traits\AcademyIdpTrait;
use Modules\Academy\Controllers\Traits\AcademyReportTrait;
use Modules\Academy\Controllers\Traits\AcademyAssessmentTrait;
use Modules\Academy\Controllers\Traits\AcademyTrainingTrait;
use App\Traits\JerseyManagementTrait;
use App\Traits\PlayerAjaxTrait;
use App\Traits\ChartSelectionTrait;
use Modules\Academy\Controllers\Traits\AcademyRequestsTrait;
use Modules\Academy\Controllers\Traits\AcademyExportTrait;
use Modules\Academy\Controllers\Traits\AcademySubmitTrait;
use Modules\Academy\Controllers\Traits\AcademyPositionTrait;
use Modules\Academy\Controllers\Traits\AcademyMemberTrait;
use Modules\Academy\Controllers\Traits\AcademyPlayerListTrait;

trait AcademyTeamTrait
{
    /**
     * Academy Registration Form
     * GET /academy/register
     */
    public function register()
    {
        $data['countries'] = $this->academyModel->getCountries();
        $data['provinces'] = $this->academyModel->getProvinces();
        return view('Modules\Academy\Views\manage.php', $data);
    }

    /**
     * Save Academy (Create or Update)
     * POST /academy/post-register
     *
     * For new academies, saveAcademy() auto-creates the owner membership.
     * For updates, validates that user can manage the academy.
     */
    public function post_register()
    {
        $input = $this->request->getPost();

        // v2: normalize ชื่อ (ตัดช่องว่างหน้า-หลัง + ยุบช่องว่างซ้อนเป็นช่องเดียว)
        // กันสร้าง academy ชื่อซ้ำด้วยการแทรกช่องว่างนำหน้า/ภายใน (checkDuplicateName เทียบ exact)
        if (isset($input['name'])) {
            $input['name'] = trim(preg_replace('/\s+/u', ' ', $input['name']));
        }
        if (isset($input['name_en'])) {
            $input['name_en'] = trim(preg_replace('/\s+/u', ' ', $input['name_en']));
        }

        // Check for duplicate name
        if (!empty($input['name'])) {
            $existingId = !empty($input['id']) ? $input['id'] : null;
            if ($this->academyModel->checkDuplicateName($input['name'], $existingId)) {
                return redirect()->to(base_url('academy/join?keyword=' . urlencode($input['name'])))
                    ->with('error', lang('Common.academy_duplicate_name'));
            }
        }

        // If updating, verify management access
        if (!empty($input['id'])) {
            if (!$this->hasPermission($input['id'], 'perm_manage_academy_info')) {
                return redirect()->to(base_url('academy/info/' . $input['id']))
                    ->with('error', lang('Common.no_access_team'));
            }
        }

        // Handle logo upload (base64)
        if (!empty($input['base64_image'])) {
            $random_name = substr(number_format(time() * rand(), 0, '', ''), 0, 10);
            $img = $input['base64_image'];
            $img = str_replace('data:image/png;base64,', '', $img);
            $img = str_replace(' ', '+', $img);
            $data = base64_decode($img);

            if ($data !== false) {
                $set_name_img = $random_name . '.jpeg';
                $uploadDir = FCPATH . 'public/uploads/logo-team/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                $file = $uploadDir . $set_name_img;
                file_put_contents($file, $data);
                $input['logo'] = 'public/uploads/logo-team/' . $set_name_img;
            }
        }

        $academyId = $this->academyModel->saveAcademy($input);

        return redirect()->to(base_url('academy/info/' . $academyId))
            ->with('success', lang('Common.academy_save_success'));
    }

    /**
     * Academy Dashboard
     * GET /academy/info/{academy_id?}
     *
     * Shows academy info, stats, and list of teams.
     * If no academy_id param, finds user's first academy.
     */
    public function index($academy_id = null)
    {
        $academy_id = $this->resolveAcademyId($academy_id);

        if (!$academy_id) {
            // No academy found — show registration prompt or join page
            $data = [
                'academy' => null,
                'userRequests' => [],
            ];
            // Check if user has pending join requests
            $userId = session()->get('user_id');
            $coachUserId = session()->get('coach_user_id');
            if ($userId || $coachUserId) {
                $builder = $this->db->table('academy_members')
                    ->select('academy_members.*, academy.name as academy_name, academy.logo')
                    ->join('academy', 'academy.id = academy_members.academy_id');

                if ($userId && $coachUserId) {
                    $builder->groupStart()
                        ->where('academy_members.user_id', $userId)
                        ->orWhere('academy_members.coach_user_id', $coachUserId)
                    ->groupEnd();
                } elseif ($userId) {
                    $builder->where('academy_members.user_id', $userId);
                } else {
                    $builder->where('academy_members.coach_user_id', $coachUserId);
                }

                $data['userRequests'] = $builder
                    ->whereIn('academy_members.status', ['pending', 'approved', 'rejected'])
                    ->orderBy('academy_members.created_at', 'DESC')
                    ->get()
                    ->getResultArray();
            }
            return view('Modules\Academy\Views\index.php', $data);
        }

        // Verify view access
        if (!$this->canViewAcademy($academy_id)) {
            return redirect()->to(base_url('academy/join'))
                ->with('error', lang('Common.academy_no_permission'));
        }

        $academy = $this->academyModel->getAcademy($academy_id);
        if (!$academy) {
            return redirect()->to(base_url('academy/register'))
                ->with('error', lang('Common.academy_not_found'));
        }

        $data = [];
        $data['academy'] = $academy;
        $data['stats'] = $this->academyModel->getAcademyStats($academy_id);
        $data['teams'] = $this->academyModel->getAcademyTeams($academy_id);
        $data['canManage'] = $this->hasPermission($academy_id, 'perm_manage_academy_info');
        $data['perms'] = $this->getAllUserPermissions($academy_id);

        return view('Modules\Academy\Views\index.php', $data);
    }

    /**
     * Edit Academy Information
     * GET /academy/manage/{academy_id}
     */
    public function manage($academy_id = null)
    {
        $access = $this->ensurePermAccess($academy_id, 'perm_manage_academy_info');
        if ($access['redirect']) return $access['redirect'];
        $academy_id = $access['academy_id'];
        $data = $access['data'];

        $data['countries'] = $this->academyModel->getCountries();
        $data['provinces'] = $this->academyModel->getProvinces();

        return view('Modules\Academy\Views\manage.php', $data);
    }

    // =====================================================
    // Team Management within Academy
    // =====================================================

    /**
     * List all teams in the academy
     * GET /academy/teams/{academy_id}
     */
    public function teams($academy_id = null)
    {
        $access = $this->ensureAcademyAccess($academy_id);
        if ($access['redirect']) return $access['redirect'];
        $academy_id = $access['academy_id'];
        $data = $access['data'];

        $data['teams']         = $this->academyModel->getAcademyTeams($academy_id);
        $data['canCreateTeam'] = $this->hasPermission($academy_id, 'perm_create_edit_team');

        return view('Modules\Academy\Views\teams.php', $data);
    }

    /**
     * Create team form within academy
     * GET /academy/team-create/{academy_id}
     */
    public function teamCreate($academy_id, $team_id = null)
    {
        $access = $this->ensureAcademyAccess($academy_id);
        if ($access['redirect']) return $access['redirect'];
        if (!$this->hasPermission($academy_id, 'perm_create_edit_team')) {
            return redirect()->to(base_url('academy/teams/' . $academy_id))
                ->with('error', lang('Common.no_access_team'));
        }
        $academy_id = $access['academy_id'];
        $data = $access['data'];

        $data['zone'] = $this->academyModel->get_zone();
        $data['countries'] = $this->academyModel->getCountries();
        $data['provinces'] = $this->academyModel->getProvinces();
        $data['team'] = null;

        // Edit mode: load existing team data
        if ($team_id) {
            if (!$this->validateTeamInAcademy($academy_id, $team_id)) {
                return redirect()->to(base_url('academy/teams/' . $academy_id))
                    ->with('error', lang('Common.academy_team_not_in_academy'));
            }
            $data['team'] = $this->academyModel->getTeam($team_id);
        }

        return view('Modules\Academy\Views\team_create.php', $data);
    }

    /**
     * Save new team within academy
     * POST /academy/team-save
     */
    public function teamSave()
    {
        $input = $this->request->getPost();
        $academy_id = $input['academy_id'] ?? null;

        if (!$academy_id) {
            return redirect()->to(base_url('academy/register'))
                ->with('error', lang('Common.academy_not_found'));
        }

        if (!$this->hasPermission($academy_id, 'perm_create_edit_team')) {
            return redirect()->to(base_url('academy/teams/' . $academy_id))
                ->with('error', lang('Common.no_access_team'));
        }

        // Handle logo upload (base64)
        if (!empty($input['base64_image'])) {
            $random_name = substr(number_format(time() * rand(), 0, '', ''), 0, 10);
            $img = $input['base64_image'];
            $img = str_replace('data:image/png;base64,', '', $img);
            $img = str_replace(' ', '+', $img);
            $data = base64_decode($img);

            if ($data !== false) {
                $set_name_img = $random_name . '.jpeg';
                $uploadDir = FCPATH . 'public/uploads/logo-team/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                file_put_contents($uploadDir . $set_name_img, $data);
                $input['logo'] = 'public/uploads/logo-team/' . $set_name_img;
            }
        }

        // Prepare team data (exclude academy_id from team table fields)
        $teamData = [];
        $teamFields = ['name', 'name_en', 'zone_id', 'province_id', 'amphure_id',
                        'tambon_id', 'country_id', 'logo', 'generation_name',
                        'contact_name', 'contact_phone', 'contact_email', 'contact_line'];
        foreach ($teamFields as $field) {
            if (isset($input[$field])) {
                $teamData[$field] = $input[$field] ?: null;
            }
        }

        // Check if updating existing team or creating new
        $team_id = $input['team_id'] ?? null;

        if ($team_id) {
            // Update existing team
            $teamData['update_date'] = date('Y-m-d H:i:s');
            $this->db->table('team')->where('id', $team_id)->update($teamData);
            return redirect()->to(base_url('academy/teams/' . $academy_id))
                ->with('success', lang('Common.save_success'));
        }

        // Create new team
        $result = $this->academyModel->createTeamForAcademy($academy_id, $teamData);

        if ($result['status']) {
            return redirect()->to(base_url('academy/teams/' . $academy_id))
                ->with('success', lang('Common.academy_team_created'));
        } else {
            return redirect()->to(base_url('academy/team-create/' . $academy_id))
                ->with('error', $result['message']);
        }
    }

    /**
     * View team info within academy context
     * GET /academy/team-info/{academy_id}/{team_id}
     */
    public function teamInfo($academy_id, $team_id)
    {
        $access = $this->ensureAcademyAccess($academy_id);
        if ($access['redirect']) return $access['redirect'];
        $academy_id = $access['academy_id'];
        $data = $access['data'];

        if (!$this->validateTeamInAcademy($academy_id, $team_id)) {
            return redirect()->to(base_url('academy/teams/' . $academy_id))
                ->with('error', lang('Common.academy_team_not_in_academy'));
        }

        $teamData = $this->academyModel->getTeam($team_id);
        if (!$teamData) {
            return redirect()->to(base_url('academy/teams/' . $academy_id))
                ->with('error', lang('Common.academy_team_not_found'));
        }

        // Set session team for backward compatibility with other modules
        session()->set('team', $teamData);

        $data['team'] = $teamData;
        $data['allPlayers'] = $data['canManage']
            ? $this->academyModel->getAcademyAllPlayersForTeamPicker($academy_id, $team_id)
            : [];

        // ดึงประวัติการแข่งขันของทีม (รวม pending=0 และ approved=1)
        $db = db_connect();
        $data['tournaments'] = $db->table('tournament_team tt')
            ->select('t.tour_id, t.name, t.name_en, t.logo, t.start_date, t.end_date,
                      t.tournament_type, tt.register_status, tt.create_date as join_date,
                      (SELECT COUNT(*) FROM tournament_match_result tmr
                       WHERE (tmr.team1_id = tt.team_id OR tmr.team2_id = tt.team_id)
                         AND tmr.tour_id = t.tour_id) as match_count', false)
            ->join('tournament t', 't.tour_id = tt.tour_id')
            ->where('tt.team_id', (int)$team_id)
            ->whereIn('tt.register_status', [0, 1])
            ->where('t.is_deleted', 0)
            ->orderBy('t.start_date', 'DESC')
            ->get()
            ->getResultArray();

        // ดึงรายชื่อโค้ชในทีม
        $data['coaches'] = $this->academyModel->getTeamCoaches($team_id);

        return view('Modules\Academy\Views\index.php', $data);
    }

    /**
     * แสดงฟอร์มใบส่งชื่อนักกีฬา (แบบ checkbox)
     * GET /academy/player-document-form/{team_id}/{tournament_id}/{match_id}
     */
    public function playerDocumentForm($team_id, $tournament_id = null, $match_id = null)
    {
        $teamData = $this->academyModel->getTeam($team_id);
        if (!$teamData) {
            return redirect()->to(base_url('academy'))
                ->with('error', lang('Common.academy_team_not_found'));
        }

        // Get tournament data
        if (!$tournament_id) {
            return redirect()->to(base_url('academy/teams'))
                ->with('error', 'กรุณาเลือกทัวร์นาเมนต์');
        }

        $tournament = $this->db->table('tournament')
            ->where('tour_id', $tournament_id)
            ->where('is_deleted', 0)
            ->get()
            ->getRowArray();

        if (!$tournament) {
            return redirect()->to(base_url('academy/teams'))
                ->with('error', 'ไม่พบข้อมูลทัวร์นาเมนต์');
        }

        $data['team'] = $teamData;
        $data['tournament'] = $tournament;
        $data['tournament_id'] = $tournament_id;
        $data['match_id'] = $match_id;

        // Get all players from team
        $data['players'] = $this->playerModel->get_PlayerList($team_id, [], '');

        return view('Modules\Academy\Views\export\player_document_form', $data);
    }

    /**
     * บันทึกข้อมูลใบส่งชื่อนักกีฬา (AJAX POST)
     * POST /academy/player-document-save/{team_id}
     */
    public function playerDocumentSave($team_id)
    {
        $input = $this->request->getJSON(true);

        if (empty($input->tournament_id)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'กรุณาเลือกทัวร์นาเมนต์']);
        }

        $tourId = (int)$input->tournament_id;
        $teamId = (int)$team_id;
        $matchId = isset($input->match_id) ? (int)$input->match_id : 0;

        // ตรวจสอบว่ามี tour_team_id หรือไม่
        $tourTeam = $this->db->table('tournament_team')
            ->where('tour_id', $tourId)
            ->where('team_id', $teamId)
            ->get()
            ->getRowArray();

        if (!$tourTeam) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ไม่พบข้อมูลทีมในทัวร์นาเมนต์']);
        }

        // ลบข้อมูลเก่าของทีมนี้ในทัวร์นาเมนต์นี้ออกก่อน
        $this->db->table('tournament_match_line_up')
            ->where('tour_id', $tourId)
            ->where('team_id', $teamId)
            ->where('match_id', $matchId)
            ->delete();

        // บันทึกข้อมูลใหม่
        $players = $input->players ?? [];
        $insertData = [];

        foreach ($players as $playerId => $matches) {
            foreach ($matches as $matchKey => $statuses) {
                // Extract round number from match_key (e.g., 'match_1' -> 1)
                $round = (int)str_replace('match_', '', $matchKey);

                foreach ($statuses as $status => $value) {
                    if ($value == 1) {
                        $insertData[] = [
                            'tour_id' => $tourId,
                            'team_id' => $teamId,
                            'match_id' => $matchId,
                            'player_id' => (int)$playerId,
                            'player_order' => $status,
                            'round' => $round,
                            'created_at' => date('Y-m-d H:i:s')
                        ];
                    }
                }
            }
        }

        if (!empty($insertData)) {
            $this->db->table('tournament_match_line_up')->insertBatch($insertData);
        }

        return $this->response->setJSON(['status' => 'success', 'message' => lang('Common.save_success')]);
    }

    /**
     * โหลดข้อมูลที่บันทึกไว้แล้ว (AJAX GET)
     * GET /academy/player-document-load/{team_id}?tournament_id={id}&match_id={id}
     */
    public function playerDocumentLoad($team_id)
    {
        $tournamentId = $this->request->getGet('tournament_id');
        $matchId = $this->request->getGet('match_id');

        if (!$tournamentId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ไม่มี tournament_id']);
        }
        if (!$matchId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ไม่มี match_id']);
        }

        $lineUps = $this->db->table('tournament_match_line_up')
            ->where('tour_id', (int)$tournamentId)
            ->where('team_id', (int)$team_id)
            ->where('match_id', (int)$matchId)
            ->get()
            ->getResultArray();

        $result = [];
        foreach ($lineUps as $lineUp) {
            $playerId = $lineUp['player_id'];
            $round = $lineUp['round'];
            $status = $lineUp['player_order'];

            if (!isset($result[$playerId])) {
                $result[$playerId] = [];
            }
            if (!isset($result[$playerId]['match_' . $round])) {
                $result[$playerId]['match_' . $round] = [];
            }
            $result[$playerId]['match_' . $round][$status] = 1;
        }

        return $this->response->setJSON(['status' => 'success', 'data' => $result]);
    }

    /**
     * บันทึกการจัดการนักเตะในทีม (AJAX POST)
     * POST /academy/team-player/save/{academy_id}/{team_id}
     */
    public function teamPlayerSave($academy_id, $team_id)
    {
        $err = $this->ensureAjaxPermAccess($academy_id, 'perm_create_edit_team');
        if ($err) return $err;
        if (!$this->validateTeamInAcademy($academy_id, $team_id)) {
            return $this->response->setJSON(['status' => 'error', 'message' => lang('Common.academy_team_not_in_academy')]);
        }

        $input      = $this->request->getJSON(true);
        $add_ids    = isset($input['add'])    ? array_map('intval', (array)$input['add'])    : [];
        $remove_ids = isset($input['remove']) ? array_map('intval', (array)$input['remove']) : [];

        if (empty($add_ids) && empty($remove_ids)) {
            return $this->response->setJSON(['status' => 'success', 'message' => lang('Common.no_changes')]);
        }

        $ok = $this->academyModel->updateTeamPlayers($academy_id, $team_id, $add_ids, $remove_ids);
        if ($ok) {
            return $this->response->setJSON(['status' => 'success', 'message' => lang('Common.save_success')]);
        }
        return $this->response->setJSON(['status' => 'error', 'message' => lang('Common.save_failed')]);
    }

    /**
     * หน้าจัดการนักเตะในทีม (full page)
     * GET /academy/team-player/manage/{academy_id}/{team_id}
     */
    public function teamPlayerManage($academy_id, $team_id)
    {
        $access = $this->ensureAcademyAccess($academy_id, true);
        if ($access['redirect']) return $access['redirect'];
        $academy_id = $access['academy_id'];
        $data = $access['data'];

        if (!$this->validateTeamInAcademy($academy_id, $team_id)) {
            return redirect()->to(base_url('academy/teams/' . $academy_id))
                ->with('error', lang('Common.academy_team_not_in_academy'));
        }

        $data['team']       = $this->academyModel->getTeam($team_id);
        $data['allPlayers'] = $this->academyModel->getAcademyAllPlayersForTeamPicker($academy_id, $team_id);
        $data['title']      = lang('Common.team_player_manager');

        return view('Modules\Academy\Views\team_player_manage', $data);
    }

    /**
     * หน้าจัดการโค้ชในทีม (full page)
     * GET /academy/team-coach/manage/{academy_id}/{team_id}
     */
    public function teamCoachManage($academy_id, $team_id)
    {
        $access = $this->ensureAcademyAccess($academy_id, true);
        if ($access['redirect']) return $access['redirect'];
        $academy_id = $access['academy_id'];
        $data = $access['data'];

        if (!$this->validateTeamInAcademy($academy_id, $team_id)) {
            return redirect()->to(base_url('academy/teams/' . $academy_id))
                ->with('error', lang('Common.academy_team_not_in_academy'));
        }

        $data['team']       = $this->academyModel->getTeam($team_id);
        $data['allCoaches'] = $this->academyModel->getAcademyAllCoachesForTeamPicker($academy_id, $team_id);
        $data['title']      = lang('Common.team_coach_manager');

        return view('Modules\Academy\Views\team_coach_manage', $data);
    }

    /**
     * บันทึกการจัดการโค้ชในทีม (AJAX POST)
     * POST /academy/team-coach/save/{academy_id}/{team_id}
     */
    public function teamCoachSave($academy_id, $team_id)
    {
        $err = $this->ensureAjaxPermAccess($academy_id, 'perm_create_edit_team');
        if ($err) return $err;
        if (!$this->validateTeamInAcademy($academy_id, $team_id)) {
            return $this->response->setJSON(['status' => 'error', 'message' => lang('Common.academy_team_not_in_academy')]);
        }

        $input      = $this->request->getJSON(true);
        $add_ids    = isset($input['add'])    ? array_map('intval', (array)$input['add'])    : [];
        $remove_ids = isset($input['remove']) ? array_map('intval', (array)$input['remove']) : [];

        if (empty($add_ids) && empty($remove_ids)) {
            return $this->response->setJSON(['status' => 'success', 'message' => lang('Common.no_changes')]);
        }

        $ok = $this->academyModel->updateTeamCoaches($academy_id, $team_id, $add_ids, $remove_ids);
        if ($ok) {
            return $this->response->setJSON(['status' => 'success', 'message' => lang('Common.save_success')]);
        }
        return $this->response->setJSON(['status' => 'error', 'message' => lang('Common.save_failed')]);
    }

    /**
     * Remove team from academy (does not delete the team itself)
     * POST /academy/team-remove
     */
    public function teamRemove()
    {
        $input = $this->request->getPost();
        $academy_id = $input['academy_id'] ?? null;
        $team_id = $input['team_id'] ?? null;

        if (!$academy_id || !$team_id) {
            return $this->response->setJSON([
                'status' => false,
                'message' => lang('Common.academy_data_incomplete')
            ]);
        }

        if (!$this->hasPermission($academy_id, 'perm_create_edit_team')) {
            return $this->response->setJSON([
                'status' => false,
                'message' => lang('Common.no_access_team')
            ]);
        }

        $this->academyModel->removeTeamFromAcademy($academy_id, $team_id);

        return $this->response->setJSON([
            'status' => true,
            'message' => lang('Common.academy_team_removed')
        ]);
    }
}
