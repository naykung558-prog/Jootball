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

trait AcademyRequestsTrait
{
    /**
     * View coach join requests for teams in the academy
     * GET /academy/coach-requests/{academy_id}
     * GET /academy/coach-requests/{academy_id}/{team_id}
     */
    public function coachRequests($academy_id = null, $team_id = null)
    {
        $access = $this->ensureAcademyAccess($academy_id, true);
        if ($access['redirect']) return $access['redirect'];
        $academy_id = $access['academy_id'];
        $data = $access['data'];

        $data['teams'] = $this->academyModel->getAcademyTeams($academy_id);

        $teamIds = $this->getAcademyTeamIds($academy_id);

        if ($team_id) {
            if (!$this->validateTeamInAcademy($academy_id, $team_id)) {
                return redirect()->to(base_url('academy/coach-requests/' . $academy_id))
                    ->with('error', lang('Common.academy_team_not_in_academy'));
            }
            $data['team'] = $this->academyModel->getTeam($team_id);
            $data['pendingRequests'] = $this->academyModel->getPendingCoachRequests($team_id);
        } else {
            // Show all pending requests across all academy teams
            $data['team'] = null;
            $data['pendingRequests'] = [];
            foreach ($teamIds as $tid) {
                $requests = $this->academyModel->getPendingCoachRequests($tid);
                $data['pendingRequests'] = array_merge($data['pendingRequests'], $requests);
            }
        }

        // All requests (all statuses) for history section
        $data['allRequests'] = [];
        if (!empty($teamIds)) {
            $data['allRequests'] = $this->db->table('coach_team_requests')
                ->select('coach_team_requests.*,
                          coach.first_name as coach_first_name, coach.last_name as coach_last_name,
                          coach.nick_name as coach_nick_name, coach.image_path as coach_image,
                          coach_users.line_picture as coach_line_picture,
                          responder.name as responder_name')
                ->join('coach', 'coach.id = coach_team_requests.coach_id', 'left')
                ->join('coach_users', 'coach_users.id = coach.coach_user_id', 'left')
                ->join('user as responder', 'responder.id = coach_team_requests.responded_by', 'left')
                ->whereIn('coach_team_requests.team_id', $teamIds)
                ->where('coach_team_requests.is_active', 1)
                ->orderBy('coach_team_requests.requested_at', 'DESC')
                ->get()->getResultArray();
        }

        return view('Modules\Academy\Views\coach_requests.php', $data);
    }

    /**
     * Approve coach join request
     * POST /academy/coach-request/approve/{id}
     */
    public function approveCoachRequest($requestId)
    {
        $request = $this->academyModel->getCoachJoinRequest($requestId);
        if (!$request) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => lang('Common.request_not_found')
            ]);
        }

        // Verify access through academy membership
        $academy = $this->academyModel->getAcademyByTeamId($request['team_id']);
        if (!$academy || !$this->hasPermission($academy['id'], 'perm_approve_coach_requests')) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => lang('Common.no_permission')
            ]);
        }

        $message = $this->request->getPost('message');

        $result = $this->academyModel->respondToCoachJoinRequest(
            $requestId,
            $request['team_id'],
            session()->get('user_id') ?? session()->get('coach_user_id'),
            'approved',
            $message
        );

        if ($result === true) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => lang('Common.request_approved')
            ]);
        } else {
            log_message('error', "approveCoachRequest failed for request ID: " . $requestId);
            return $this->response->setJSON([
                'status' => 'error',
                'message' => lang('Common.error_approving_request')
            ]);
        }
    }

    /**
     * Reject coach join request
     * POST /academy/coach-request/reject/{id}
     */
    public function rejectCoachRequest($requestId)
    {
        $request = $this->academyModel->getCoachJoinRequest($requestId);
        if (!$request) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => lang('Common.request_not_found')
            ]);
        }

        // Verify access through academy membership
        $academy = $this->academyModel->getAcademyByTeamId($request['team_id']);
        if (!$academy || !$this->hasPermission($academy['id'], 'perm_approve_coach_requests')) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => lang('Common.no_permission')
            ]);
        }

        $message = $this->request->getPost('message');
        $result = $this->academyModel->respondToCoachJoinRequest(
            $requestId,
            $request['team_id'],
            session()->get('user_id') ?? session()->get('coach_user_id'),
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

    // =====================================================
    // Join Academy & Member Requests (replaces join team)
    // =====================================================

    /**
     * Join academy page (search for academies)
     * GET /academy/join
     */
    public function join_team_page()
    {
        return view('Modules\Academy\Views\join_team.php');
    }

    /**
     * Search academies via AJAX
     * GET /academy/api/search?keyword=xxx
     */
    public function api_search()
    {
        $keyword = $this->request->getGet('keyword');
        if (!$keyword) return $this->response->setJSON([]);

        $results = $this->academyModel->searchAcademies($keyword);
        return $this->response->setJSON($results);
    }

    /**
     * Send join request to an academy
     * POST /academy/api/join-request
     */
    public function api_join_request()
    {
        $input = $this->request->getJSON();
        if (!$input || empty($input->academy_id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => lang('Common.academy_data_invalid')
            ]);
        }

        $academyId = $input->academy_id;
        $userId = session()->get('user_id');
        $coachUserId = session()->get('coach_user_id');

        // Check if already a member
        $existingRole = $this->academyModel->getMemberRole($academyId, $userId, $coachUserId);
        if ($existingRole !== null) {
            return $this->response->setJSON([
                'success' => false,
                'message' => lang('Common.academy_already_joined')
            ]);
        }

        // Check if already has a pending request
        $builder = $this->db->table('academy_members')
            ->where('academy_id', $academyId)
            ->where('status', 'pending');

        if ($userId) {
            $builder->where('user_id', $userId);
        } elseif ($coachUserId) {
            $builder->where('coach_user_id', $coachUserId);
        }

        $existing = $builder->countAllResults();
        if ($existing > 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => lang('Common.academy_pending_request')
            ]);
        }

        // Create pending member request
        $memberData = [
            'role' => $input->role ?? 'viewer',
            'status' => 'pending',
        ];

        if ($userId) {
            $memberData['user_id'] = $userId;
        }
        if ($coachUserId) {
            $memberData['coach_user_id'] = $coachUserId;
        }

        $this->academyModel->addMember($academyId, $memberData);

        return $this->response->setJSON([
            'success' => true,
            'message' => lang('Common.academy_join_sent')
        ]);
    }

    /**
     * View member join requests for the academy
     * GET /academy/requests/{academy_id}
     */
    public function requests($academy_id = null)
    {
        $access = $this->ensureAcademyAccess($academy_id, true);
        if ($access['redirect']) return $access['redirect'];
        $academy_id = $access['academy_id'];
        $data = $access['data'];

        // Get pending member requests
        $data['requests'] = $this->db->table('academy_members')
            ->select('academy_members.*,
                      user.name as user_name, user.username, user.profile_image,
                      coach_users.username as coach_username, coach.first_name as coach_first_name,
                      coach.last_name as coach_last_name, coach.image_path as coach_image')
            ->join('user', 'user.id = academy_members.user_id', 'left')
            ->join('coach_users', 'coach_users.id = academy_members.coach_user_id', 'left')
            ->join('coach', 'coach.coach_user_id = academy_members.coach_user_id', 'left')
            ->where('academy_members.academy_id', $academy_id)
            ->where('academy_members.status', 'pending')
            ->orderBy('academy_members.created_at', 'DESC')
            ->get()
            ->getResultArray();

        // Get current approved members
        $data['members'] = $this->academyModel->getAcademyMembers($academy_id);

        return view('Modules\Academy\Views\requests.php', $data);
    }

    /**
     * Approve member join request
     * GET /academy/request/approve/{id}
     */
    public function approve_request($id)
    {
        $member = $this->db->table('academy_members')
            ->where('id', $id)
            ->get()
            ->getRowArray();

        if (!$member) {
            return redirect()->back()->with('error', lang('Common.academy_request_not_found'));
        }

        if (!$this->hasPermission($member['academy_id'], 'perm_approve_requests')) {
            return redirect()->back()->with('error', lang('Common.no_permission'));
        }

        $this->academyModel->updateMemberStatus($id, 'approved');

        return redirect()->back()->with('success', lang('Common.academy_approved_success'));
    }

    /**
     * Reject member join request
     * GET /academy/request/reject/{id}
     */
    public function reject_request($id)
    {
        $member = $this->db->table('academy_members')
            ->where('id', $id)
            ->get()
            ->getRowArray();

        if (!$member) {
            return redirect()->back()->with('error', lang('Common.academy_request_not_found'));
        }

        if (!$this->hasPermission($member['academy_id'], 'perm_approve_requests')) {
            return redirect()->back()->with('error', lang('Common.no_permission'));
        }

        $this->academyModel->updateMemberStatus($id, 'rejected');

        return redirect()->back()->with('success', lang('Common.academy_rejected_success'));
    }

    /**
     * Remove a manager/member from the academy
     * POST /academy/remove-manager/{id}
     */
    public function remove_manager($id)
    {
        try {
            $member = $this->db->table('academy_members')
                ->where('id', $id)
                ->get()
                ->getRowArray();

            if (!$member) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => lang('Common.academy_member_not_found')
                ]);
            }

            if ($member['role'] === 'owner') {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => lang('Common.academy_cannot_remove_owner')
                ]);
            }

            if (!$this->hasPermission($member['academy_id'], 'perm_manage_members')) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => lang('Common.academy_no_remove_permission')
                ]);
            }

            $this->academyModel->removeMember($id);

            return $this->response->setJSON([
                'status' => true,
                'message' => lang('Common.academy_member_deleted_success')
            ]);
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => false,
                'message' => lang('Common.academy_error_occurred') . ': ' . $e->getMessage()
            ]);
        }
    }

    // =====================================================
    // User Profile Management
    // =====================================================

    /**
     * Export Academy User Guide as PDF
     * GET /academy/user-guide-pdf
     */
    public function exportUserGuidePdf()
    {
        set_time_limit(120);
        ini_set('memory_limit', '256M');

        $html = view('Modules\Academy\Views\export\user_guide_pdf');

        $this->response->setHeader('Content-Type', 'application/pdf');

        $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];
        $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        $mpdf = new \Mpdf\Mpdf([
            'fontDir' => $fontDirs,
            'fontdata' => $fontData,
            'default_font' => 'garuda',
            'default_font_size' => 14,
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_top' => 20,
            'margin_left' => 15,
            'margin_right' => 15,
            'margin_bottom' => 20,
            'margin_header' => 5,
            'margin_footer' => 8,
            'orientation' => 'P',
            'tempDir' => WRITEPATH . 'cache',
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
        ]);

        $mpdf->SetHTMLHeader('
            <div style="text-align: center; border-bottom: 1px solid #ccc; padding-bottom: 5px;">
                <span style="font-size: 10px; color: #999; font-family: garuda;">Jootball Academy - User Guide</span>
            </div>
        ');

        $mpdf->SetHTMLFooter('
            <div style="text-align: center; border-top: 1px solid #ccc; padding-top: 5px;">
                <span style="font-size: 10px; color: #999; font-family: garuda;">Jootball Academy &copy; 2569 | {PAGENO}/{nbpg}</span>
            </div>
        ');

        $mpdf->WriteHTML($html);
        $mpdf->Output('Jootball_Academy_UserGuide.pdf', 'I');
    }

    /**
     * User profile management page
     * GET /academy/user-manage
     */
    public function manage_user()
    {
        $data['user'] = $this->userModel->getOneUser(session()->get('user_id'), '');
        return view('Modules\Academy\Views\manage_user.php', $data);
    }

    /**
     * Save user profile
     * POST /academy/user-save
     */
    public function save_user()
    {
        $input = $this->request->getPost();

        // Handle Profile Image Upload
        $file = $this->request->getFile('new_profile_image');

        if ($file && $file->isValid() && !$file->hasMoved()) {
            $validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];

            if (!in_array($file->getMimeType(), $validTypes)) {
                return redirect()->back()->withInput()
                    ->with('error', lang('Common.academy_upload_image_only'));
            }

            if ($file->getSize() > 5 * 1024 * 1024) {
                return redirect()->back()->withInput()
                    ->with('error', lang('Common.academy_image_max_5mb'));
            }

            $uploadPath = FCPATH . 'public/uploads/profiles/';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            // Delete old profile image (except defaults)
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
        return redirect()->to('/academy/user-manage')->with('success', lang('Common.success'));
    }
}
