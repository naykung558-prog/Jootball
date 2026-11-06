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

trait AcademyMemberTrait
{
    /**
     * All coaches across all teams in the academy
     * GET /academy/coaches/{academy_id}
     */
    public function coaches($academy_id = null)
    {
        $access = $this->ensureAcademyAccess($academy_id);
        if ($access['redirect']) return $access['redirect'];
        $academy_id = $access['academy_id'];
        $data = $access['data'];

        $data['coaches'] = $this->academyModel->getAcademyCoaches($academy_id);
        $data['available_teams'] = $this->academyModel->getAcademyTeams($academy_id);
        $data['pending_invitations'] = $this->academyModel->getPendingInvitations($academy_id);
        $data['team'] = null; // Academy-wide view, no specific team

        return view('Modules\Academy\Views\coach_list.php', $data);
    }

    /**
     * Search coaches by name (AJAX)
     * POST /academy/coach-search
     */
    public function coachSearch()
    {
        $keyword = trim($this->request->getPost('keyword') ?? '');
        $academyId = (int)($this->request->getPost('academy_id') ?? 0);

        if (mb_strlen($keyword) < 2) {
            return $this->response->setJSON(['status' => 'success', 'data' => []]);
        }

        // ดึง team IDs ของ academy เพื่อ exclude โค้ชที่อยู่แล้ว
        // ยกเว้นโค้ชที่มีคำเชิญ pending — ให้ยังขึ้นในผลค้นหา (ติด marker รอตอบรับ)
        $excludeIds = [];
        $pendingIds = [];
        if ($academyId) {
            $existingCoaches = $this->academyModel->getAcademyCoaches($academyId);
            $excludeIds = array_column($existingCoaches, 'id');
            $pendingIds = $this->academyModel->getPendingCoachIds($academyId);
            $excludeIds = array_values(array_diff($excludeIds, $pendingIds)); // pending ไม่ถูก exclude
        }

        $coaches = $this->db->table('coach')
            ->select('coach.id, coach.first_name, coach.last_name, coach.nick_name, coach.position, coach.image_path, coach_users.line_picture')
            ->join('coach_users', 'coach_users.id = coach.coach_user_id', 'left')
            ->where('coach.is_active', 1)
            ->groupStart()
                ->like('coach.first_name', $keyword)
                ->orLike('coach.last_name', $keyword)
                ->orLike('coach.nick_name', $keyword)
            ->groupEnd()
            ->limit(10)
            ->get()
            ->getResultArray();

        // Filter out existing coaches
        if (!empty($excludeIds)) {
            $coaches = array_filter($coaches, function ($c) use ($excludeIds) {
                return !in_array($c['id'], $excludeIds);
            });
            $coaches = array_values($coaches);
        }

        // Add image URL + สถานะ pending (มีคำเชิญค้างอยู่)
        foreach ($coaches as &$c) {
            if (!empty($c['image_path'])) {
                $c['image_url'] = base_url($c['image_path']);
            } elseif (!empty($c['line_picture'])) {
                $c['image_url'] = (filter_var($c['line_picture'], FILTER_VALIDATE_URL))
                    ? $c['line_picture']
                    : base_url($c['line_picture']);
            } else {
                $c['image_url'] = base_url('public/img/football-player.webp');
            }
            $c['is_pending'] = in_array((int)$c['id'], $pendingIds, true);
        }

        return $this->response->setJSON(['status' => 'success', 'data' => $coaches]);
    }

    /**
     * Invite a coach to a team (AJAX)
     * POST /academy/coach-invite
     */
    public function coachInvite()
    {
        $coachId = (int)($this->request->getPost('coach_id') ?? 0);
        $teamId = (int)($this->request->getPost('team_id') ?? 0);

        if (!$coachId || !$teamId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ข้อมูลไม่ครบถ้วน']);
        }

        // ตรวจสอบว่ามีคำเชิญ pending อยู่แล้วหรือไม่
        $existing = $this->db->table('coach_invitations')
            ->where('coach_id', $coachId)
            ->where('team_id', $teamId)
            ->where('status', 'pending')
            ->get()->getRowArray();

        if ($existing) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ได้ส่งคำเชิญไปแล้ว รอการตอบรับ']);
        }

        // ตรวจสอบว่าโค้ชอยู่ในทีมนี้แล้วหรือไม่
        $alreadyInTeam = $this->db->table('coach')
            ->where('id', $coachId)
            ->where('team_id', $teamId)
            ->where('is_active', 1)
            ->get()->getRowArray();

        if ($alreadyInTeam) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ผู้ฝึกสอนอยู่ในทีมนี้แล้ว']);
        }

        $teamName = $this->db->table('team')->select('name')->where('id', $teamId)->get()->getRowArray()['name'] ?? '';
        $userId = session()->get('user_id');

        $this->db->table('coach_invitations')->insert([
            'coach_id' => $coachId,
            'team_id' => $teamId,
            'invited_by' => $userId,
            'status' => 'pending',
            'message' => 'คุณได้รับคำเชิญให้เข้าร่วมทีม ' . $teamName,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return $this->response->setJSON(['status' => 'success', 'message' => 'ส่งคำเชิญเรียบร้อย รอการยืนยัน']);
    }

    /**
     * Coach list for a specific team within academy
     * GET /academy/coach-list/{academy_id}/{team_id}
     */
    public function coach_list($academy_id, $team_id = null)
    {
        $access = $this->ensureAcademyAccess($academy_id);
        if ($access['redirect']) return $access['redirect'];
        $academy_id = $access['academy_id'];
        $data = $access['data'];

        $teamResult = $this->resolveTeamForAcademy($academy_id, $team_id);
        if ($teamResult['redirect']) return $teamResult['redirect'];
        $data['team'] = $teamResult['team'];

        return view('Modules\Academy\Views\coach_list.php', $data);
    }

    // =====================================================
    // Academy Members Management
    // =====================================================

    /**
     * View academy members
     * GET /academy/members/{academy_id}
     */
    public function members($academy_id = null)
    {
        $access = $this->ensureAcademyAccess($academy_id);
        if ($access['redirect']) return $access['redirect'];
        $academy_id = $access['academy_id'];
        $data = $access['data'];

        $data['members'] = $this->academyModel->getAcademyMembers($academy_id);

        return view('Modules\Academy\Views\members.php', $data);
    }

    /**
     * Permission & Member Management — รวมสมาชิก, ผู้ฝึกสอน และคู่มือสิทธิ์
     * GET /academy/permissions/{academy_id}
     */
    public function permissions($academy_id = null)
    {
        $access = $this->ensureAcademyAccess($academy_id);
        if ($access['redirect']) return $access['redirect'];
        $academy_id = $access['academy_id'];
        $data = $access['data'];

        $data['myRole']              = $this->getCachedRole($academy_id) ?? 'viewer';
        $data['isSuperAcademy']      = (bool) session()->get('is_super_academy');
        $data['members']             = $this->academyModel->getAcademyMembers($academy_id);
        $data['coaches']             = $this->academyModel->getAcademyCoaches($academy_id);
        $data['pending_invitations'] = $this->academyModel->getPendingInvitations($academy_id);
        $data['available_teams']     = $this->academyModel->getAcademyTeams($academy_id);
        $data['customPerms']         = $this->academyModel->getAcademyRolePermissions($academy_id);

        $data['title'] = lang('Common.academy_permissions');
        return view('Modules\Academy\Views\permissions.php', $data);
    }

    /**
     * AJAX: toggle a single role permission for this academy
     * POST /academy/permission-toggle
     */
    public function permissionToggle()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setBody('Forbidden');
        }

        $input      = $this->getInput();
        $academy_id = (int)($input['academy_id'] ?? 0);

        if (!$academy_id || $this->getCachedRole($academy_id) !== 'owner') {
            return $this->response->setJSON(['status' => false, 'message' => lang('Common.no_permission')]);
        }

        $key     = trim($input['permission_key'] ?? '');
        $role    = trim($input['role'] ?? '');
        $allowed = (bool)($input['allowed'] ?? false);

        if (!$key || !in_array($role, ['admin', 'coach', 'viewer'])) {
            return $this->response->setJSON(['status' => false, 'message' => 'Invalid input']);
        }

        $ok = $this->academyModel->togglePermission($academy_id, $key, $role, $allowed);
        return $this->response->setJSON(['status' => $ok]);
    }

    /**
     * Add or edit academy member
     * POST /academy/member-save
     */
    public function memberSave()
    {
        $input = $this->request->getPost();
        $academy_id = $input['academy_id'] ?? null;

        if (!$academy_id) {
            return $this->response->setJSON([
                'status' => false,
                'message' => lang('Common.academy_not_found')
            ]);
        }

        if (!$this->hasPermission($academy_id, 'perm_manage_members')) {
            return $this->response->setJSON([
                'status' => false,
                'message' => lang('Common.no_permission')
            ]);
        }

        // เฉพาะ Super Academy Operator (academy.admin@jootball.com) เท่านั้นที่ตั้งค่า role = owner ได้
        $requestedRole = $input['role'] ?? 'viewer';
        if ($requestedRole === 'owner' && !session()->get('is_super_academy')) {
            return $this->response->setJSON([
                'status' => false,
                'message' => lang('Common.no_permission')
            ]);
        }

        $memberId = $input['member_id'] ?? null;

        if ($memberId) {
            // Update existing member role
            $role = $requestedRole;
            $this->academyModel->updateMemberRole($memberId, $role);
            return $this->response->setJSON([
                'status' => true,
                'message' => lang('Common.academy_role_updated')
            ]);
        } else {
            // Add new member
            $memberData = [
                'role' => $requestedRole,
                'status' => 'approved',
            ];

            if (!empty($input['user_id'])) {
                $memberData['user_id'] = $input['user_id'];
            }
            if (!empty($input['coach_user_id'])) {
                $memberData['coach_user_id'] = $input['coach_user_id'];
            }

            // Check if already a member
            $existingRole = $this->academyModel->getMemberRole(
                $academy_id,
                $memberData['user_id'] ?? null,
                $memberData['coach_user_id'] ?? null
            );

            if ($existingRole !== null) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => lang('Common.academy_already_member')
                ]);
            }

            $this->academyModel->addMember($academy_id, $memberData);
            return $this->response->setJSON([
                'status' => true,
                'message' => lang('Common.academy_member_added')
            ]);
        }
    }

    /**
     * Remove academy member
     * POST /academy/member-remove/{id}
     */
    public function memberRemove($id)
    {
        // Get the member record to find the academy_id
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

        // Cannot remove the owner
        if ($member['role'] === 'owner') {
            return $this->response->setJSON([
                'status' => false,
                'message' => lang('Common.academy_cannot_remove_owner')
            ]);
        }

        if (!$this->hasPermission($member['academy_id'], 'perm_manage_members')) {
            return $this->response->setJSON([
                'status' => false,
                'message' => lang('Common.no_permission')
            ]);
        }

        // ถ้าเป็น coach ให้ลบ coach_teams ของทีมใน academy นี้ออกด้วย
        if (!empty($member['coach_user_id'])) {
            $academyTeamIds = array_column(
                $this->academyModel->getAcademyTeams($member['academy_id']),
                'id'
            );
            if (!empty($academyTeamIds)) {
                $coachRow = $this->db->table('coach')
                    ->where('coach_user_id', $member['coach_user_id'])
                    ->where('is_active', 1)
                    ->get()->getRowArray();
                if ($coachRow) {
                    $this->db->table('coach_teams')
                        ->where('coach_id', $coachRow['id'])
                        ->whereIn('team_id', $academyTeamIds)
                        ->update(['is_active' => 0]);
                }
            }
        }

        $this->academyModel->removeMember($id);

        return $this->response->setJSON([
            'status' => true,
            'message' => lang('Common.academy_member_deleted')
        ]);
    }

    /**
     * Search users to add as academy members (AJAX)
     * POST /academy/member-search
     */
    public function memberSearch()
    {
        $query = $this->request->getPost('query');
        $academy_id = $this->request->getPost('academy_id');

        if (!$this->hasPermission($academy_id, 'perm_manage_members')) {
            return $this->response->setJSON(['status' => false, 'results' => []]);
        }

        if (empty($query) || strlen($query) < 2) {
            return $this->response->setJSON(['status' => false, 'results' => []]);
        }

        // Search users by name or username (user table columns: id, name, username, password, uid_line, profile_image, create_date)
        $users = $this->db->table('user')
            ->select('id, name, username, profile_image')
            ->groupStart()
                ->like('name', $query)
                ->orLike('username', $query)
            ->groupEnd()
            ->limit(10)
            ->get()
            ->getResultArray();

        $results = [];
        foreach ($users as $user) {
            $displayName = !empty($user['name']) ? $user['name'] : (!empty($user['username']) ? $user['username'] : 'User #' . $user['id']);
            $results[] = [
                'id' => $user['id'],
                'display_name' => $displayName,
                'username' => $user['username'] ?? '',
                'profile_image' => $user['profile_image'] ?? '',
            ];
        }

        return $this->response->setJSON(['status' => true, 'results' => $results]);
    }
}
