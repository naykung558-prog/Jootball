<?php

namespace Modules\Admin\Controllers;

use App\Controllers\BaseController;
use Modules\Admin\Models\UserManagerModel;
use Modules\Team\Models\TeamModel;

class UserManager extends BaseController
{
    protected $userManagerModel;
    protected $teamModel;

    public function __construct()
    {
        $this->userManagerModel = new UserManagerModel();
        $this->teamModel = new TeamModel();
    }

    /**
     * Main page - list users & members
     */
    public function index()
    {
        return view('Modules\Admin\Views\UserManager\index');
    }

    // ==================== USER (Regular) ====================

    /**
     * AJAX - Get users for DataTable
     */
    public function ajax_users()
    {
        return $this->userManagerModel->getUsers();
    }

    /**
     * AJAX - Get single user data
     */
    public function getUser($id)
    {
        $user = $this->userManagerModel->getUsers($id);
        if ($user) {
            unset($user['password']);
            return $this->response->setJSON(['status' => true, 'data' => $user]);
        }
        return $this->response->setJSON(['status' => false, 'message' => lang('Common.data_not_found')]);
    }

    /**
     * Save user (create or update)
     */
    public function saveUser()
    {
        $input = $this->request->getPost();

        // Validate username uniqueness
        if (!empty($input['username'])) {
            $excludeId = !empty($input['id']) ? $input['id'] : null;
            if ($this->userManagerModel->userUsernameExists($input['username'], $excludeId)) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => lang('Common.username_already_exists')
                ]);
            }
        }

        // Require password for new user with username
        if (empty($input['id']) && !empty($input['username']) && empty($input['password'])) {
            return $this->response->setJSON([
                'status' => false,
                'message' => lang('Common.password_required_for_new_user')
            ]);
        }

        $result = $this->userManagerModel->saveUser($input);

        if ($result) {
            return $this->response->setJSON([
                'status' => true,
                'message' => lang('Common.save_data_success')
            ]);
        }

        return $this->response->setJSON([
            'status' => false,
            'message' => lang('Common.error_occurred_msg')
        ]);
    }

    /**
     * Delete user
     */
    public function deleteUser()
    {
        $id = $this->request->getPost('id');

        if ($this->userManagerModel->deleteUser($id)) {
            return $this->response->setJSON([
                'status' => true,
                'message' => lang('Common.delete_data_success')
            ]);
        }

        return $this->response->setJSON([
            'status' => false,
            'message' => lang('Common.error_occurred_msg')
        ]);
    }

    // ==================== MEMBER (Admin) ====================

    /**
     * AJAX - Get members for DataTable
     */
    public function ajax_members()
    {
        return $this->userManagerModel->getMembers();
    }

    /**
     * AJAX - Get single member data
     */
    public function getMember($id)
    {
        $member = $this->userManagerModel->getMembers($id);
        if ($member) {
            unset($member['password']);
            return $this->response->setJSON(['status' => true, 'data' => $member]);
        }
        return $this->response->setJSON(['status' => false, 'message' => lang('Common.data_not_found')]);
    }

    /**
     * Save member (create or update)
     */
    public function saveMember()
    {
        $input = $this->request->getPost();

        // Validate required fields
        if (empty($input['username'])) {
            return $this->response->setJSON([
                'status' => false,
                'message' => lang('Common.username_required')
            ]);
        }

        // Validate username uniqueness
        $excludeId = !empty($input['id']) ? $input['id'] : null;
        if ($this->userManagerModel->memberUsernameExists($input['username'], $excludeId)) {
            return $this->response->setJSON([
                'status' => false,
                'message' => lang('Common.username_already_exists')
            ]);
        }

        // Require password for new member
        if (empty($input['id']) && empty($input['password'])) {
            return $this->response->setJSON([
                'status' => false,
                'message' => lang('Common.password_required_for_new_user')
            ]);
        }

        $result = $this->userManagerModel->saveMember($input);

        if ($result) {
            return $this->response->setJSON([
                'status' => true,
                'message' => lang('Common.save_data_success')
            ]);
        }

        return $this->response->setJSON([
            'status' => false,
            'message' => lang('Common.error_occurred_msg')
        ]);
    }

    /**
     * Delete member
     */
    public function deleteMember()
    {
        $id = $this->request->getPost('id');

        // Prevent self-deletion
        if ($id == session()->get('member_id')) {
            return $this->response->setJSON([
                'status' => false,
                'message' => lang('Common.cannot_delete_self')
            ]);
        }

        if ($this->userManagerModel->deleteMember($id)) {
            return $this->response->setJSON([
                'status' => true,
                'message' => lang('Common.delete_data_success')
            ]);
        }

        return $this->response->setJSON([
            'status' => false,
            'message' => lang('Common.error_occurred_msg')
        ]);
    }

    public function impersonateUser($id)
    {
        if (!$this->isSuperAdmin()) {
            return redirect()->to(base_url('admin/access-denied'))->with('error', 'เฉพาะ Super Admin เท่านั้น');
        }

        $user = $this->userManagerModel->getUsers($id);
        if (!$user) {
            return redirect()->back()->with('error', lang('Common.data_not_found'));
        }

        $session = session();
        $this->clearFrontendSession($session);

        $team = $this->teamModel->getUserteam($user['id']);
        $displayName = trim((string) ($user['name'] ?? ''));
        if ($displayName === '') {
            $displayName = trim((string) ($user['username'] ?? $user['email'] ?? ('User #' . $user['id'])));
        }

        $session->set([
            'user_id' => $user['id'],
            'team' => $team,
            'user_type' => 'user',
            'isLoggedIn' => true,
            'frontend_impersonation' => [
                'type' => 'user',
                'id' => (int) $user['id'],
                'label' => $displayName,
            ],
        ]);

        log_message('warning', '[ADMIN IMPERSONATE][user] member_id=' . session()->get('member_id') . ' user_id=' . $user['id']);

        // หา academy จาก membership จริงก่อน (academy_members) แล้วค่อย fallback ไปทีม
        // กันเคส user เป็น manager ของทีมใน academy อื่น → redirect ไปหน้าที่ตัวเองไม่ได้เป็นสมาชิก → "ไม่มีสิทธิ์"
        $academyUrl = getUserAcademyUrl() ?: getUserTeamAcademyUrl();
        if ($academyUrl) {
            return redirect()->to($academyUrl)->with('success', 'เข้าสู่ระบบหน้าบ้านในฐานะ ' . $displayName);
        }

        return redirect()->to(base_url('team/info'))->with('success', 'เข้าสู่ระบบหน้าบ้านในฐานะ ' . $displayName);
    }

    public function impersonateCoach($coachId)
    {
        if (!$this->isSuperAdmin()) {
            return redirect()->to(base_url('admin/access-denied'))->with('error', 'เฉพาะ Super Admin เท่านั้น');
        }

        $frontendCoachModel = new \Modules\Coach\Models\CoachModel();
        $coach = $frontendCoachModel->getCoachById($coachId);

        if (!$coach || empty($coach['coach_user_id'])) {
            return redirect()->back()->with('error', 'ไม่พบบัญชีผู้ฝึกสอนที่ใช้งานได้');
        }

        $coachData = $frontendCoachModel->getCoachByCoachUserId($coach['coach_user_id']);
        if (!$coachData) {
            return redirect()->back()->with('error', 'ไม่พบข้อมูลโปรไฟล์ผู้ฝึกสอน');
        }

        $coachTeams = $frontendCoachModel->getCoachTeams($coachData['id']);

        $session = session();
        $this->clearFrontendSession($session);

        $displayName = trim(($coachData['first_name'] ?? '') . ' ' . ($coachData['last_name'] ?? ''));
        if ($displayName === '') {
            $displayName = 'Coach #' . $coachData['id'];
        }

        $session->set([
            'coach_user_id' => $coachData['coach_user_id'],
            'coach_id' => $coachData['id'],
            'coach_data' => $coachData,
            'team' => !empty($coachTeams) ? $coachTeams[0] : null,
            'user_type' => 'coach',
            'isLoggedIn' => true,
            'frontend_impersonation' => [
                'type' => 'coach',
                'id' => (int) $coachData['id'],
                'label' => $displayName,
            ],
        ]);

        log_message('warning', '[ADMIN IMPERSONATE][coach] member_id=' . session()->get('member_id') . ' coach_id=' . $coachData['id']);

        return redirect()->to(base_url('coach/dashboard'))->with('success', 'เข้าสู่ระบบหน้าบ้านในฐานะ ' . $displayName);
    }

    public function returnToAdmin()
    {
        if (!$this->isSuperAdmin()) {
            return redirect()->to(base_url('admin/access-denied'))->with('error', 'เฉพาะ Super Admin เท่านั้น');
        }

        if (session()->get('frontend_impersonation')) {
            $this->clearFrontendSession(session());
        }

        return redirect()->to(base_url('admin/dashboard'))->with('success', 'กลับสู่โหมดผู้ดูแลแล้ว');
    }

    private function clearFrontendSession($session): void
    {
        $session->remove([
            'profile',
            'uid_line',
            'user_id',
            'team',
            'coach_user_id',
            'coach_id',
            'coach_data',
            'user_type',
            'isLoggedIn',
            'frontend_impersonation',
            // ล้าง state super-academy เดิม กันค้างจาก session ก่อนหน้า (เช่นเคย impersonate super user)
            'is_super_academy',
            'current_academy_id',
            'current_academy_name',
        ]);
    }

    private function isSuperAdmin(): bool
    {
        return (bool) session()->get('is_super_admin');
    }
}
