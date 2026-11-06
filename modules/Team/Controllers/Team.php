<?php

namespace Modules\Team\Controllers;

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
use Modules\Team\Controllers\Traits\TeamSubmitCoachTrait;
use Modules\Team\Controllers\Traits\TeamExportTrait;

class Team extends BaseController
{
    use TeamExportTrait;
    use TeamSubmitCoachTrait;
    use TeamNotificationTrait;
    use ChartSelectionTrait;
    use PlayerAjaxTrait;
    use JerseyManagementTrait;
    private $db;
    protected $teamModel;
    protected $userModel;
    protected $playerModel;

    public function __construct()
    {
        $this->db = db_connect();
        $this->teamModel = new TeamModel;
        $this->userModel = new UserModel;
        $this->playerModel = new PlayerModel;
    }

    public function register()
    {
        // v2: ปิดการสร้างทีมเดี่ยว — ทีมต้องสร้างผ่าน Academy เท่านั้น
        return $this->academyFirstRedirect();
    }

    /** v2: ไม่อนุญาตสร้างทีมเดี่ยว — พาผู้ใช้ไปสร้าง/จัดการทีมผ่าน Academy */
    private function academyFirstRedirect()
    {
        helper('academy');
        $academyId = getUserAcademyId();
        if ($academyId) {
            return redirect()->to(base_url('academy/teams/' . $academyId))
                ->with('info', 'กรุณาสร้างทีมผ่านหน้า Academy ของคุณ');
        }
        return redirect()->to(base_url('academy/register'))
            ->with('info', 'ต้องสร้าง Academy ก่อนจึงจะสร้างทีมได้');
    }

    public function manage($team_id = null)
    {
        $data = [];

        // v2: สร้างทีม "ใหม่" (ยังไม่มีทีมเดิม) → ต้องผ่าน Academy เท่านั้น (กันทีมเดี่ยว)
        //     แก้ไขทีมเดิม (มี team_id หรือมีทีมใน session) ยังทำได้ปกติ
        if (!$team_id && empty($this->teamModel->getUserteam(session()->get('user_id')))) {
            return $this->academyFirstRedirect();
        }

        // Phase 8: ตรวจสอบว่าทีมอยู่ใน Academy → redirect ไป Academy module
        $checkTeamId = $team_id ?? session()->get('team.id');
        if ($checkTeamId) {
            $academyUrl = getAcademyUrlForTeam($checkTeamId, 'manage');
            if ($academyUrl) {
                return redirect()->to($academyUrl)->with('info', lang('Common.team_redirected_to_academy'));
            }
        }

        if ($team_id) {
            // ดึงข้อมูลทีม
            $teamData = $this->teamModel->get_Teams($team_id);

            // ตรวจสอบสิทธิ์การเข้าถึงทีม
            if (session()->get('user_type') === 'coach') {
                // ตรวจสอบว่าผู้ฝึกสอนคนนี้ดูแลทีมนี้หรือไม่ (รวมทีมย่อย)
                list($coachTeams, $teamIds) = $this->getCoachTeamIds();
                if (!in_array($team_id, $teamIds)) {
                    if (!empty($coachTeams)) {
                        $firstTeamId = $coachTeams[0]['id'];
                        return redirect()->to(base_url('team/info/' . $firstTeamId))->with('error', lang('Common.no_access_team'));
                    } else {
                        return redirect()->to('/coach/dashboard')->with('error', lang('Common.no_access_team'));
                    }
                }
            } else {
                // ตรวจสอบว่าเป็นเจ้าของทีมนี้หรือผู้ฝึกสอนหรือไม่
                $isManager = false;
                $managers = $this->teamModel->getTeamManagers($team_id);
                foreach ($managers as $mgr) {
                    if ($mgr['user_id'] == session()->get('user_id')) {
                        $isManager = true;
                        break;
                    }
                }

                if (!$this->canManageTeam($team_id) && !$isManager) {
                    // ถ้าไม่ใช่เจ้าของและไม่ใช่ผู้จัดการ redirect ไปทีมของตัวเอง
                    return redirect()->to(base_url('team/manage'))->with('error', lang('Common.no_access_team'));
                }
            }

            $data['team'] = $teamData;
        } else {
            // สำหรับเจ้าของทีม (พฤติกรรมเดิม)
            $data['team'] = $this->teamModel->getUserteam(session()->get('user_id'));
        }

        $data['zone'] = $this->teamModel->get_zone();
        $data['countries'] = $this->teamModel->getCountries();
        $data['provinces'] = $this->teamModel->getProvinces();
        return view('Modules\Team\Views\manage.php', $data);
    }

    public function post_register()
    {
        $input = $this->request->getPost();

        // v2: สร้างทีมใหม่ (ไม่มี id) ต้องผ่าน Academy เท่านั้น — กันสร้างทีมเดี่ยว
        //     แก้ไขทีมเดิม (มี id) ยังทำได้ปกติ
        if (empty($input['id'])) {
            return $this->academyFirstRedirect();
        }

        // Check for duplicate name
        if (!empty($input['name'])) {
            $existingId = !empty($input['id']) ? $input['id'] : null;
            if ($this->teamModel->checkDuplicateName($input['name'], $existingId)) {
                return redirect()->to(base_url('team/join?keyword=' . urlencode($input['name'])))
                    ->with('error', 'ทีมนี้มีอยู่ในระบบแล้ว คุณสามารถขอเข้าร่วมทีมได้');
            }
        }

        if ($input['base64_image']) {
            $random_name = substr(number_format(time() * rand(), 0, '', ''), 0, 10);
            // $img = $this->replaceUnderscoreWithSlash($input);
            $img = $input['base64_image'];
            $img = str_replace('data:image/png;base64,', '', $img);
            $img = str_replace(' ', '+', $img);
            $data = base64_decode($img);
            $set_name_img = $random_name . '.jpeg';
            $file = "public/uploads/logo-team/" . $set_name_img;
            file_put_contents($file, $data);
            $input['logo'] = 'public/uploads/logo-team/' . $set_name_img;
        }

        $response = $this->teamModel->saveTeam($input);

        // set session
        if (session()->get('user_type') === 'coach') {
            // สำหรับผู้ฝึกสอน ไม่ต้อง update user session team หลัก
            // แต่ต้อง update ข้อมูลทีมที่กำลังจัดการใน session ให้เป็นปัจจุบันถ้าเป็นทีมเดียวกัน?
            // หรือไม่จำเป็นต้อง update session team ก็ได้ เพราะ session team ของ coach อาจจะเป็นทีมหลักที่เค้าคุม

            // ถ้าต้องการ redirect กลับไปที่ manage ของทีมเดิม
            return redirect()->to(base_url('team/info/' . $input['id']))->with('success', 'บันทึกข้อมูลเรียบร้อยแล้ว');
        } else {
            $team = $this->teamModel->getUserteam(session()->get('user_id'));
            $ses_data = [
                'team' => $team
            ];
            session()->set($ses_data);
            // สำหรับเจ้าของทีม ใช้ URL แบบเดิม
            return redirect()->to(base_url('team/info'))->with('success', 'บันทึกข้อมูลเรียบร้อยแล้ว');
        }
    }

    public function index($team_id = null)
    {
        $data = [];

        // Phase 8: ตรวจสอบว่าทีมอยู่ใน Academy → redirect ไป Academy module
        $checkTeamId = $team_id ?? session()->get('team.id');
        if ($checkTeamId) {
            $academyUrl = getAcademyUrlForTeam($checkTeamId, 'info');
            if ($academyUrl) {
                return redirect()->to($academyUrl)->with('info', lang('Common.team_redirected_to_academy'));
            }
        } elseif (!$team_id) {
            // user ไม่มีทีมใน session แต่อาจมี academy membership
            $academyUrl = getUserAcademyUrl();
            if ($academyUrl) {
                return redirect()->to($academyUrl)->with('info', lang('Common.team_redirected_to_academy'));
            }
        }

        if ($team_id) {
            // ดึงข้อมูลทีม
            $teamData = $this->teamModel->get_Teams($team_id);

            // ตรวจสอบสิทธิ์การเข้าถึงทีม
            if (session()->get('user_type') === 'coach') {
                // ตรวจสอบว่าผู้ฝึกสอนคนนี้ดูแลทีมนี้หรือไม่ (รวมทีมย่อย)
                list($coachTeams, $teamIds) = $this->getCoachTeamIds();
                if (!in_array($team_id, $teamIds)) {
                    if (!empty($coachTeams)) {
                        $firstTeamId = $coachTeams[0]['id'];
                        return redirect()->to(base_url('team/info/' . $firstTeamId))->with('error', lang('Common.no_access_team'));
                    } else {
                        return redirect()->to('/coach/dashboard')->with('error', lang('Common.no_access_team'));
                    }
                }

                // Set session team for coach context
                session()->set('team', $teamData);
            } else {
                // ตรวจสอบว่าเป็นเจ้าของทีมนี้หรือทีมหลักหรือไม่ (รวมทีมย่อย)
                if (!$this->canManageTeam($team_id)) {
                    return redirect()->to(base_url('team/info'))->with('error', lang('Common.no_access_team'));
                }
            }

            $data['team'] = $teamData;
        } else {
            // สำหรับเจ้าของทีม (พฤติกรรมเดิม)
            $data['team'] = $this->teamModel->getUserteam(session()->get('user_id'));
        }

        // Get user's join requests if no team
        if (empty($data['team'])) {
            $data['userRequests'] = $this->teamModel->getUserRequests(session()->get('user_id'));
        }

        // ดึงข้อมูลนักเตะจัดกลุ่มตามตำแหน่ง
        $team_id_for_players = $team_id ?? session()->get('team.id');
        $data['position'] = $this->playerModel->player_position();
        $data['data'] = $this->playerModel->get_PlayerGroupByPosition($team_id_for_players);

        // ดึงข้อมูลทีมรอง (Sub-Teams) - แสดงเฉพาะถ้าเป็นทีมหลัก (ไม่มี parent_team_id)
        if (!empty($data['team']) && empty($data['team']['parent_team_id'])) {
            $data['subteams'] = $this->teamModel->getSubTeams($data['team']['id']);
        }

        return view('Modules\Team\Views\index.php', $data);
    }

    public function player_list($team_id = null)
    {
        $data = [];

        // Phase 8: ตรวจสอบว่าทีมอยู่ใน Academy → redirect ไป Academy module
        $checkTeamId = $team_id ?? session()->get('team.id');
        if ($checkTeamId) {
            $academyUrl = getAcademyUrlForTeam($checkTeamId, 'player-list');
            if ($academyUrl) {
                return redirect()->to($academyUrl)->with('info', lang('Common.team_redirected_to_academy'));
            }
        }

        if ($team_id) {
            // ดึงข้อมูลทีม
            $teamData = $this->teamModel->get_Teams($team_id);

            // ตรวจสอบสิทธิ์การเข้าถึงทีม
            if (session()->get('user_type') === 'coach') {
                // ตรวจสอบว่าผู้ฝึกสอนคนนี้ดูแลทีมนี้หรือไม่ (รวมทีมย่อย)
                list($coachTeams, $teamIds) = $this->getCoachTeamIds();
                if (!in_array($team_id, $teamIds)) {
                    if (!empty($coachTeams)) {
                        $firstTeamId = $coachTeams[0]['id'];
                        return redirect()->to(base_url('team/info/' . $firstTeamId))->with('error', lang('Common.no_access_team'));
                    } else {
                        return redirect()->to('/coach/dashboard')->with('error', lang('Common.no_access_team'));
                    }
                }
            } else {
                // ตรวจสอบว่าเป็นเจ้าของทีมนี้หรือทีมหลักหรือไม่ (รวมทีมย่อย)
                if (!$this->canManageTeam($team_id)) {
                    return redirect()->to(base_url('team/player-list'))->with('error', lang('Common.no_access_team'));
                }
            }

            $data['team'] = $teamData;
        } else {
            // สำหรับเจ้าของทีม (พฤติกรรมเดิม)
            $data['team'] = $this->teamModel->getUserteam(session()->get('user_id'));
        }

        return view('Modules\Team\Views\player_list.php', $data);
    }

    public function coach_list($team_id = null)
    {
        $data = [];

        // Phase 8: ตรวจสอบว่าทีมอยู่ใน Academy → redirect ไป Academy module
        $checkTeamId = $team_id ?? session()->get('team.id');
        if ($checkTeamId) {
            $academyUrl = getAcademyUrlForTeam($checkTeamId, 'coach-list');
            if ($academyUrl) {
                return redirect()->to($academyUrl)->with('info', lang('Common.team_redirected_to_academy'));
            }
        }

        if ($team_id) {
            // ดึงข้อมูลทีม
            $teamData = $this->teamModel->get_Teams($team_id);

            // ตรวจสอบสิทธิ์การเข้าถึงทีม
            if (session()->get('user_type') === 'coach') {
                // ตรวจสอบว่าผู้ฝึกสอนคนนี้ดูแลทีมนี้หรือไม่ (รวมทีมย่อย)
                list($coachTeams, $teamIds) = $this->getCoachTeamIds();
                if (!in_array($team_id, $teamIds)) {
                    if (!empty($coachTeams)) {
                        $firstTeamId = $coachTeams[0]['id'];
                        return redirect()->to(base_url('team/info/' . $firstTeamId))->with('error', lang('Common.no_access_team'));
                    } else {
                        return redirect()->to('/coach/dashboard')->with('error', lang('Common.no_access_team'));
                    }
                }
            } else {
                // ตรวจสอบว่าเป็นเจ้าของทีมนี้หรือทีมหลักหรือไม่ (รวมทีมย่อย)
                if (!$this->canManageTeam($team_id)) {
                    return redirect()->to(base_url('team/coach-list'))->with('error', lang('Common.no_access_team'));
                }
            }

            $data['team'] = $teamData;
        } else {
            // สำหรับเจ้าของทีม (พฤติกรรมเดิม)
            $data['team'] = $this->teamModel->getUserteam(session()->get('user_id'));
        }

        return view('Modules\Team\Views\coach_list.php', $data);
    }

    public function football_player_chart($team_id = null)
    {
        // v2: หน้า team player-chart ถูกแทนที่ด้วย Academy module ทั้งหมดแล้ว
        // (ทุกทีมที่มี roster ผูกกับ academy → ใช้ academy/player-chart) → redirect เสมอ
        // ทีมที่อยู่ใน academy → academy chart · ทีมที่ไม่อยู่ academy → กลับหน้า team info
        $checkTeamId = $team_id ?? session()->get('team.id');
        if ($checkTeamId) {
            $academyUrl = getAcademyUrlForTeam($checkTeamId, 'player-chart');
            if ($academyUrl) {
                return redirect()->to($academyUrl)->with('info', lang('Common.team_redirected_to_academy'));
            }
            return redirect()->to(base_url('team/info/' . $checkTeamId));
        }
        return redirect()->to(base_url('team/info'));
    }


    public function manage_user()
    {
        $data['user'] = $this->userModel->getOneUser(session()->get('user_id'), '');

        // Generate LINE sync link if user hasn't linked LINE yet
        if (empty($data['user']['uid_line'])) {
            $line = new \App\Libraries\LineLogin();
            $data['line_sync_link'] = $line->getLink('sync');
        }

        return view('Modules\Team\Views\manage_user.php', $data);
    }

    public function save_user()
    {
        $input = $this->request->getPost();

        // Validate password strength if provided
        if (!empty($input['password'])) {
            if ($input['password'] !== ($input['confirm_password'] ?? '')) {
                return redirect()->back()->withInput()->with('error', lang('Common.password_not_match'));
            }
            if (strlen($input['password']) < 8 || !preg_match('/[A-Z]/', $input['password']) || !preg_match('/[a-z]/', $input['password']) || !preg_match('/[0-9]/', $input['password'])) {
                return redirect()->back()->withInput()->with('error', lang('Common.password_requirements'));
            }
        }

        // Clean non-DB fields
        unset($input['confirm_password'], $input['profile_image_changed'], $input['new_profile_image']);
        // Remove CSRF token field if present
        $csrfToken = csrf_token();
        unset($input[$csrfToken]);

        // Handle Profile Image Upload
        $file = $this->request->getFile('new_profile_image');

        if ($file && $file->isValid() && !$file->hasMoved()) {
            $validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];

            if (!in_array($file->getMimeType(), $validTypes)) {
                return redirect()->back()->withInput()->with('error', 'กรุณาอัปโหลดไฟล์รูปภาพเท่านั้น (JPG, PNG, GIF)');
            }

            if ($file->getSize() > 5 * 1024 * 1024) { // 5MB
                return redirect()->back()->withInput()->with('error', 'ขนาดไฟล์รูปภาพต้องไม่เกิน 5MB');
            }

            $uploadPath = FCPATH . 'public/uploads/profiles/';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            // ลบรูปเก่าถ้ามี (ยกเว้นไฟล์ default)
            $currentUser = $this->userModel->getOneUser(session()->get('user_id'), '');
            if (!empty($currentUser['profile_image']) && file_exists(FCPATH . $currentUser['profile_image'])) {
                @unlink(FCPATH . $currentUser['profile_image']);
            }

            $newName = 'profile_' . session()->get('user_id') . '_' . time() . '.' . $file->getExtension();
            if ($file->move($uploadPath, $newName)) {
                $input['profile_image'] = 'public/uploads/profiles/' . $newName;
            }
        }

        $this->userModel->editUser($input);
        return redirect()->to('/team/user-manage')->with('success', lang('Common.success'));
    }

    /**
     * Assign players to a specific position
     * POST /team/assign-player-position
     *
     * @return JSON response with success status
     */
    public function assign_player_position()
    {
        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ไม่ได้รับการยืนยันตัวตน'
            ])->setStatusCode(401);
        }

        try {
            $input = $this->request->getJSON();

            // Validate input is not null
            if (!$input) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'ข้อมูล JSON ไม่ถูกต้อง'
                ])->setStatusCode(400);
            }

            $positionId = $input->position_id ?? null;
            $playersData = $input->players ?? [];

            // Validate position_id
            if (!$positionId) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'ไม่พบตำแหน่ง'
                ])->setStatusCode(400);
            }

            // Parse players data
            $players = is_string($playersData) ? json_decode($playersData, true) : (is_object($playersData) ? (array)$playersData : $playersData);

            if (empty($players) || !is_array($players)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'ไม่พบข้อมูลนักแตะ'
                ])->setStatusCode(400);
            }

            // Get team_id from session or parameter
            $teamId = session()->get('team.id');
            if (!$teamId) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'ไม่พบข้อมูลทีม'
                ])->setStatusCode(400);
            }

            // Verify team ownership/access (รวมทีมย่อย)
            if (!$this->canManageTeam($teamId)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'ไม่มีสิทธิ์เข้าถึงทีมนี้'
                ])->setStatusCode(403);
            }

            // Start transaction
            $this->db->transBegin();

            // Update each player's position
            foreach ($players as $player) {
                // Handle both array and object formats
                $playerObj = is_object($player) ? $player : (object)$player;
                $playerId = $playerObj->id ?? null;

                if (!$playerId) {
                    continue;
                }

                // Verify player belongs to this team
                $playerData = $this->playerModel->get_Player($playerId);
                if (!$playerData || $playerData['team_id'] != $teamId) {
                    throw new \Exception('นักแตะ ID ' . $playerId . ' ไม่อยู่ในทีมของคุณ');
                }

                // Update player's position
                $this->db->table('player')
                    ->where('id', $playerId)
                    ->update(['player_position_id' => $positionId]);
            }

            // Commit transaction
            if ($this->db->transStatus() === false) {
                $this->db->transRollback();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล'
                ])->setStatusCode(500);
            }

            $this->db->transCommit();

            return $this->response->setJSON([
                'success' => true,
                'message' => 'เพิ่มนักแตะเข้าตำแหน่งเรียบร้อย'
            ]);
        } catch (\Exception $e) {
            $this->db->transRollback();

            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Remove player from position
     * POST /team/remove-player-position
     *
     * @return JSON response with success status
     */
    public function remove_player_position()
    {
        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ไม่ได้รับการยืนยันตัวตน'
            ])->setStatusCode(401);
        }

        try {
            $input = $this->request->getJSON();

            if (!$input) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'ข้อมูล JSON ไม่ถูกต้อง'
                ])->setStatusCode(400);
            }

            $playerId = $input->player_id ?? null;

            if (!$playerId) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'ไม่พบข้อมูลนักแตะ'
                ])->setStatusCode(400);
            }

            // Get team_id from session
            $teamId = session()->get('team.id');
            if (!$teamId) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'ไม่พบข้อมูลทีม'
                ])->setStatusCode(400);
            }

            // Verify team ownership/access (รวมทีมย่อย)
            if (!$this->canManageTeam($teamId)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'ไม่มีสิทธิ์เข้าถึงทีมนี้'
                ])->setStatusCode(403);
            }

            // Verify player belongs to this team
            $playerData = $this->playerModel->get_Player($playerId);
            if (!$playerData || $playerData['team_id'] != $teamId) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'นักแตะคนนี้ไม่อยู่ในทีมของคุณ'
                ])->setStatusCode(403);
            }

            // Update player's position to NULL (remove from position)
            $this->db->table('player')
                ->where('id', $playerId)
                ->update(['player_position_id' => null]);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'ลบนักแตะออกจากตำแหน่งเรียบร้อย'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    // --- get_player: shared logic ใน App\Traits\PlayerAjaxTrait ---
    protected function playerAjaxMessages(): array
    {
        return [
            'auth_required'      => 'ไม่ได้รับการยืนยันตัวตน',
            'player_not_found'   => 'ไม่พบข้อมูลนักแตะ',
            'team_not_found'     => 'ไม่พบข้อมูลทีม',
            'player_not_in_team' => 'นักแตะคนนี้ไม่อยู่ในทีมของคุณ',
        ];
    }

    /**
     * Update player data (ชื่อ, นามสกุล, เลขเสื้อ)
     * POST /team/update-player
     *
     * @return JSON response with success status
     */
    public function update_player()
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ไม่ได้รับการยืนยันตัวตน'
            ])->setStatusCode(401);
        }

        try {
            $input = $this->request->getJSON();

            if (!$input) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'ข้อมูล JSON ไม่ถูกต้อง'
                ])->setStatusCode(400);
            }

            $playerId = $input->player_id ?? null;
            $firstName = $input->first_name ?? null;
            $lastName = $input->last_name ?? null;
            $shirtNumber = $input->shirt_number ?? null;

            if (!$playerId || !$firstName) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'ข้อมูลไม่ครบถ้วน'
                ])->setStatusCode(400);
            }

            $teamId = session()->get('team.id');
            if (!$teamId) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'ไม่พบข้อมูลทีม'
                ])->setStatusCode(400);
            }

            // Verify team ownership/access (รวมทีมย่อย)
            if (!$this->canManageTeam($teamId)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'ไม่มีสิทธิ์เข้าถึงทีมนี้'
                ])->setStatusCode(403);
            }

            // Verify player belongs to this team
            $playerData = $this->playerModel->get_Player($playerId);
            if (!$playerData || $playerData['team_id'] != $teamId) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'นักแตะคนนี้ไม่อยู่ในทีมของคุณ'
                ])->setStatusCode(403);
            }

            // Prepare update data
            $updateData = [
                'first_name' => $firstName,
                'last_name' => $lastName ?? ''
            ];

            // Add shirt number if provided
            if ($shirtNumber !== null && $shirtNumber !== '') {
                $updateData['shirt_number'] = $shirtNumber;
            }

            // Update player's data
            $this->db->table('player')
                ->where('id', $playerId)
                ->update($updateData);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'อัปเดตข้อมูลนักแตะเรียบร้อย'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Update player position
     * POST /team/update-player-position
     *
     * @return JSON response with success status
     */
    public function update_player_position()
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ไม่ได้รับการยืนยันตัวตน'
            ])->setStatusCode(401);
        }

        try {
            $input = $this->request->getJSON();

            if (!$input) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'ข้อมูล JSON ไม่ถูกต้อง'
                ])->setStatusCode(400);
            }

            $playerId = $input->player_id ?? null;
            $positionId = $input->position_id ?? null;

            if (!$playerId || !$positionId) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'ข้อมูลไม่ครบถ้วน'
                ])->setStatusCode(400);
            }

            $teamId = session()->get('team.id');
            if (!$teamId) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'ไม่พบข้อมูลทีม'
                ])->setStatusCode(400);
            }

            // Verify team ownership/access (รวมทีมย่อย)
            if (!$this->canManageTeam($teamId)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'ไม่มีสิทธิ์เข้าถึงทีมนี้'
                ])->setStatusCode(403);
            }

            // Verify player belongs to this team
            $playerData = $this->playerModel->get_Player($playerId);
            if (!$playerData || $playerData['team_id'] != $teamId) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'นักแตะคนนี้ไม่อยู่ในทีมของคุณ'
                ])->setStatusCode(403);
            }

            // Update player's position
            $this->db->table('player')
                ->where('id', $playerId)
                ->update(['player_position_id' => $positionId]);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'อัปเดตตำแหน่งนักแตะเรียบร้อย'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    public function join_team_page()
    {
        return view('Modules\Team\Views\join_team.php');
    }

    public function api_search()
    {
        $keyword = $this->request->getGet('keyword');
        if (!$keyword) return $this->response->setJSON([]);

        $results = $this->teamModel->searchTeams($keyword);
        return $this->response->setJSON($results);
    }

    public function api_join_request()
    {
        $input = $this->request->getJSON();
        if (!$input || empty($input->team_id)) {
            return $this->response->setJSON(['success' => false, 'message' => 'ข้อมูลไม่ถูกต้อง']);
        }

        $requesterId = session()->get('user_id');
        $teamId = (int)$input->team_id;
        $result = $this->teamModel->requestJoinTeam($teamId, $requesterId);

        if (!empty($result['status'])) {
            try {
                $team = $this->teamModel->get_Teams($teamId);
                $ownerId = $team['create_by'] ?? null;
                if ($ownerId && class_exists('\Modules\Coach\Models\NotificationModel')) {
                    $requester = (new UserModel())->find($requesterId);
                    $requesterName = $requester['name'] ?? ($requester['username'] ?? 'ผู้ใช้');
                    (new \Modules\Coach\Models\NotificationModel())->createUserNotification(
                        $ownerId,
                        'team_join_request',
                        'มีคำขอเข้าร่วมทีมใหม่',
                        $requesterName . ' ขอเข้าร่วมทีม ' . ($team['name'] ?? ''),
                        ['team_id' => $teamId, 'requester_id' => $requesterId]
                    );
                }
            } catch (\Throwable $e) {
                log_message('error', 'team_join_request notification failed: ' . $e->getMessage());
            }
        }

        return $this->response->setJSON(['success' => $result['status'], 'message' => $result['message']]);
    }

    public function requests($team_id = null)
    {
        if (!$team_id) $team_id = session()->get('team.id');
        if (!$team_id) return redirect()->to('/team/manage');

        // Reuse access check logic or wrap it in a private method
        $teamData = $this->teamModel->get_Teams($team_id);
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
            return redirect()->to('/team/manage')->with('error', lang('Common.no_access_team'));
        }

        $data['team'] = $teamData;
        $data['requests'] = $this->teamModel->getPendingRequests($team_id);
        $data['managers'] = $managers;

        return view('Modules\Team\Views\requests.php', $data);
    }

    public function approve_request($id)
    {
        // Security check omitted for brevity in this chunk but ideally check team access
        // For now relying on team_id checks if we passed it, but here we just have ID.
        // We should verify the user has rights to the team of this request.

        // TODO: Strict security check

        $this->teamModel->updateRequestStatus($id, 'approved');
        return redirect()->back()->with('success', 'อนุมัติเรียบร้อย');
    }

    public function reject_request($id)
    {
        $teamModel = new TeamModel();
        $teamModel->updateRequestStatus($id, 'rejected');

        return $this->response->setJSON(['status' => true, 'message' => 'ปฏิเสธคำขอเรียบร้อยแล้ว']);
    }

    public function remove_manager($id)
    {
        try {
            $teamModel = new TeamModel();
            $request = $teamModel->db->table('team_managers')->where('id', $id)->get()->getRowArray();

            if (!$request) {
                return $this->response->setJSON(['status' => false, 'message' => 'ไม่พบข้อมูลผู้จัดการ']);
            }

            // Verify that the current user is the owner of the team
            $team = $teamModel->where('id', $request['team_id'])->first();

            if (!$team) {
                return $this->response->setJSON(['status' => false, 'message' => 'ไม่พบข้อมูลทีม']);
            }

            if (!$this->canManageTeam($request['team_id'])) {
                return $this->response->setJSON(['status' => false, 'message' => 'คุณไม่มีสิทธิ์ในการลบผู้ฝึกสอน']);
            }

            $teamModel->removeManager($id);

            return $this->response->setJSON(['status' => true, 'message' => 'ลบผู้ฝึกสอนเรียบร้อยแล้ว']);
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON(['status' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
        }
    }

    /**
     * Get all team IDs accessible by the current coach (including sub-teams)
     * Returns [coachTeams, teamIds]
     */
    private function getCoachTeamIds()
    {
        $coachModel = new \Modules\Coach\Models\CoachModel();
        $coachTeams = $coachModel->getCoachTeams(session()->get('coach_id'));
        $teamIds = array_column($coachTeams, 'id');

        // รวมทีมย่อยของทุกทีมที่ coach ดูแล
        foreach ($coachTeams as $ct) {
            $subTeams = $this->teamModel->getSubTeams($ct['id']);
            foreach ($subTeams as $st) {
                $teamIds[] = $st['id'];
            }
        }

        return [$coachTeams, $teamIds];
    }

    /**
     * Check if current user (regular user or coach) can manage this team
     */
    private function canManageTeam($teamId)
    {
        if (session()->get('user_type') === 'coach') {
            list(, $teamIds) = $this->getCoachTeamIds();
            return in_array($teamId, $teamIds);
        }

        // Super academy operator → จัดการได้ทุกทีม (virtual owner เหมือนฝั่ง Academy)
        helper('academy');
        if (isSuperAcademyUser(session()->get('user_id'))) return true;

        $userId = session()->get('user_id');
        $team = $this->teamModel->get_Teams($teamId);
        if (!$team) return false;

        // เจ้าของทีมโดยตรง
        if ($team['create_by'] == $userId) return true;

        // ผู้ฝึกสอน (manager) ที่ได้รับอนุมัติแล้ว
        $isManager = $this->db->table('team_managers')
            ->where('team_id', $teamId)
            ->where('user_id', $userId)
            ->where('status', 'approved')
            ->countAllResults() > 0;
        if ($isManager) return true;

        // ทีมย่อย: เช็คว่าเป็นเจ้าของหรือผู้ฝึกสอนหลักหรือไม่
        if (!empty($team['parent_team_id'])) {
            $parentTeam = $this->teamModel->get_Teams($team['parent_team_id']);
            if (!$parentTeam) return false;

            if ($parentTeam['create_by'] == $userId) return true;

            return $this->db->table('team_managers')
                ->where('team_id', $team['parent_team_id'])
                ->where('user_id', $userId)
                ->where('status', 'approved')
                ->countAllResults() > 0;
        }

        return false;
    }

    /**
     * Manage sub-team (create/edit)
     * GET /team/subteam/{parent_team_id}
     * GET /team/subteam/{parent_team_id}/{subteam_id}
     */
    public function subteam_manage($parent_team_id, $subteam_id = null)
    {
        $data = [];

        // Get parent team
        $parentTeam = $this->teamModel->get_Teams($parent_team_id);
        if (!$parentTeam) {
            return redirect()->to(base_url('team/info'))->with('error', 'ไม่พบข้อมูลสโมสรหลัก');
        }

        // Verify ownership (regular user or coach)
        if (!$this->canManageTeam($parent_team_id)) {
            return redirect()->to(base_url('team/info'))->with('error', 'คุณไม่มีสิทธิ์ในการจัดการทีมรองของสโมสรนี้');
        }

        $data['parent_team'] = $parentTeam;
        $data['zone'] = $this->teamModel->get_zone();
        $data['countries'] = $this->teamModel->getCountries();
        $data['provinces'] = $this->teamModel->getProvinces();

        // If editing existing sub-team
        if ($subteam_id) {
            $subTeam = $this->teamModel->get_Teams($subteam_id);
            if (!$subTeam || $subTeam['parent_team_id'] != $parent_team_id) {
                return redirect()->to(base_url('team/info'))->with('error', 'ไม่พบข้อมูลทีมรอง');
            }
            $data['team'] = $subTeam;
        }

        return view('Modules\Team\Views\subteam_manage.php', $data);
    }

    /**
     * Save sub-team
     * POST /team/subteam-save
     */
    public function save_subteam()
    {
        $input = $this->request->getPost();

        // Validate parent_team_id
        if (empty($input['parent_team_id'])) {
            return redirect()->to(base_url('team/info'))->with('error', 'ไม่พบข้อมูลสโมสรหลัก');
        }

        // Verify ownership (regular user or coach)
        if (!$this->canManageTeam($input['parent_team_id'])) {
            return redirect()->to(base_url('team/info'))->with('error', 'คุณไม่มีสิทธิ์ในการจัดการทีมรองของสโมสรนี้');
        }

        // Validate duplicates name (check only sub-teams or all teams? usually all)
        // ใช้ logic เหมือน post_register - ถ้าชื่อซ้ำให้ redirect ไปหน้า join team
        if (!empty($input['name'])) {
            $existingId = !empty($input['id']) ? $input['id'] : null;
            if ($this->teamModel->checkDuplicateName($input['name'], $existingId)) {
                return redirect()->to(base_url('team/join?keyword=' . urlencode($input['name'])))
                    ->with('error', 'ทีมนี้มีอยู่ในระบบแล้ว คุณสามารถขอเข้าร่วมทีมได้');
            }
        }

        // Handle Image Upload
        if (!empty($input['base64_image'])) {
            $random_name = substr(number_format(time() * rand(), 0, '', ''), 0, 10);
            $img = $input['base64_image'];
            $img = str_replace('data:image/png;base64,', '', $img);
            $img = str_replace(' ', '+', $img);
            $data = base64_decode($img);

            if ($data === false) {
                return redirect()->back()->withInput()->with('error', 'รูปแบบรูปภาพไม่ถูกต้อง');
            }

            $set_name_img = $random_name . '.jpeg';
            $file_path = "public/uploads/logo-team/" . $set_name_img;

            if (file_put_contents($file_path, $data) === false) {
                return redirect()->back()->withInput()->with('error', 'บันทึกรูปภาพไม่สำเร็จ');
            }

            $input['logo'] = 'public/uploads/logo-team/' . $set_name_img;
        }

        // Call model to save
        $this->teamModel->saveTeam($input);

        return redirect()->to(base_url('team/info/' . $input['parent_team_id']))->with('success', 'บันทึกข้อมูลทีมรองเรียบร้อยแล้ว');
    }

    /**
     * Save coach chart selection via AJAX
     * POST /team/save-coach-chart-selection
     */
    public function save_coach_chart_selection()
    {
        $teamId = $this->request->getPost('team_id');
        $coachIds = $this->request->getPost('coach_ids'); // expecting array or empty

        if (!$teamId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Team ID required']);
        }

        // Ensure coach_ids is array
        if (!is_array($coachIds)) {
            $coachIds = [];
        }

        // Filter out invalid coach IDs
        // ถ้าเป็นทีมย่อย ให้ validate กับโค้ชของทีมหลัก (อคาเดมี่)
        $coachValidationTeamId = $teamId;
        $teamDataForValidation = $this->teamModel->get_Teams($teamId);
        if ($teamDataForValidation && !empty($teamDataForValidation['parent_team_id'])) {
            $coachValidationTeamId = $teamDataForValidation['parent_team_id'];
        }
        $validCoaches = $this->teamModel->getTeamCoaches($coachValidationTeamId);
        $validCoachIds = array_column($validCoaches, 'id');

        // Keep only IDs that are valid for this team (or parent team)
        $coachIds = array_filter($coachIds, function ($id) use ($validCoachIds) {
            return in_array($id, $validCoachIds);
        });

        // Reset array keys
        $coachIds = array_values($coachIds);

        $result = $this->teamModel->saveCoachChartSelection($teamId, $coachIds);

        if ($result) {
            return $this->response->setJSON(['status' => 'success']);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to save']);
        }
    }

    // --- chart selection: shared logic ใน App\Traits\ChartSelectionTrait ---
    protected function chartModel()
    {
        return $this->teamModel;
    }

    // =====================================================
    // Jersey Upload Management
    // =====================================================

    // --- Jersey management: shared logic ใน App\Traits\JerseyManagementTrait ---
    protected function jerseyEntityModel()
    {
        return $this->teamModel;
    }

    protected function jerseyMessages(): array
    {
        return [
            'auth_required'    => 'ไม่ได้รับการยืนยันตัวตน',
            'data_incomplete'  => 'ข้อมูลไม่ครบถ้วน',
            'kit_type_invalid' => 'ประเภทชุดไม่ถูกต้อง',
            'image_read_error' => 'ไม่สามารถอ่านไฟล์รูปภาพได้',
            'kit_uploaded'     => 'อัปโหลดรูปชุดสำเร็จ',
            'kit_deleted'      => 'ลบรูปชุดสำเร็จ',
        ];
    }


    /**
     * Get default ID card template values when no custom template exists
     */
    private function getDefaultIdCardTemplate(string $cardType = 'player'): array
    {
        return [
            'card_type'        => $cardType,
            'card_width_mm'    => 90,
            'card_height_mm'   => 130,
            'template_image'   => '',
            'photo_x'          => 32.5,
            'photo_y'          => 30,
            'photo_width'      => 25,
            'photo_height'     => 30,
            'name_x'           => 5,
            'name_y'           => 65,
            'name_font_size'   => 14,
            'name_color'       => '#1a1a2e',
            'team_x'           => 5,
            'team_y'           => 72,
            'team_font_size'   => 11,
            'team_color'       => '#16213e',
            'number_x'         => 35,
            'number_y'         => 8,
            'number_font_size' => 28,
            'number_color'     => '#FFFFFF',
        ];
    }

}
