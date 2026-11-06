<?php

namespace Modules\Academy\Controllers;

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
use Modules\Academy\Controllers\Traits\AcademyTeamTrait;
use Modules\Academy\Controllers\Traits\AcademyJerseyKitTrait;

/**
 * Academy Controller
 *
 * Academy-centric controller where Academy is the top-level entity.
 * An Academy has many Teams (via academy_teams mapping table).
 * Members/roles are managed via academy_members (roles: owner, admin, coach, viewer).
 *
 * Access control uses canManageAcademy() checking academy_members table.
 */
class Academy extends BaseController
{
    use AcademyTeamTrait;
    use AcademyJerseyKitTrait;
    use AcademyPlayerListTrait;
    use AcademyMemberTrait;
    use AcademyPositionTrait;
    use AcademySubmitTrait;
    use AcademyExportTrait;
    use AcademyRequestsTrait;
    use ChartSelectionTrait;
    use PlayerAjaxTrait;
    use JerseyManagementTrait;
    use AcademyTrainingTrait;
    use AcademyAssessmentTrait;
    use AcademyReportTrait;
    use AcademyIdpTrait;
    use AcademyPlayerTrait;
    use AcademyAiTrait;
    private $db;
    protected $academyModel;
    protected $userModel;
    protected $playerModel;
    private $cachedRoles = []; // Per-request cache for academy role lookups

    // Attachment constants
    const TRAINING_ATTACH_ALLOWED_MIME = [
        'image/jpeg', 'image/png', 'image/jpg',
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    ];
    const TRAINING_ATTACH_MAX_SIZE_MB = 20;
    const TRAINING_ATTACH_MAX_FILES   = 10;

    public function __construct()
    {
        $this->db = db_connect();
        $this->academyModel = new AcademyModel;
        $this->userModel = new UserModel;
        $this->playerModel = new PlayerModel;
    }

    // =====================================================
    // Access Control Helpers
    // =====================================================

    /**
     * Read input from either POST form data or JSON body.
     * Solves: AJAX fetch() with Content-Type: application/json sends data
     * in the raw body, but getPost() only reads $_POST (form data).
     *
     * @param string|null $key Specific key to get, or null for all data
     * @return mixed
     */
    private function getInput($key = null)
    {
        // Try regular POST first
        $value = $this->request->getPost($key);
        // When $key is null, getPost returns [] for JSON requests — check non-empty
        if ($key !== null && $value !== null) {
            return $value;
        }
        if ($key === null && !empty($value)) {
            return $value;
        }

        // Fallback to JSON body
        $json = $this->request->getJSON(true);
        if ($json) {
            return $key ? ($json[$key] ?? null) : $json;
        }

        return null;
    }

    /**
     * Check if current user can manage academy (owner or admin role).
     *
     * @param int $academy_id
     * @return bool
     */
    private function canManageAcademy($academy_id)
    {
        $role = $this->getCachedRole($academy_id);
        return in_array($role, ['owner', 'admin']);
    }

    /**
     * Default permission matrix (mirrors permissions.php view hardcoded defaults).
     * key => [owner, admin, coach, viewer]
     */
    private static $defaultPermMatrix = [
        'perm_manage_academy_info'    => ['owner'=>true,  'admin'=>true,  'coach'=>false, 'viewer'=>false],
        'perm_create_edit_team'       => ['owner'=>true,  'admin'=>true,  'coach'=>false, 'viewer'=>false],
        'perm_manage_members'         => ['owner'=>true,  'admin'=>true,  'coach'=>false, 'viewer'=>false],
        'perm_approve_requests'       => ['owner'=>true,  'admin'=>true,  'coach'=>false, 'viewer'=>false],
        'perm_approve_coach_requests' => ['owner'=>true,  'admin'=>true,  'coach'=>false, 'viewer'=>false],
        'perm_create_assessment'      => ['owner'=>true,  'admin'=>true,  'coach'=>false, 'viewer'=>false],
        'perm_save_assessment_result' => ['owner'=>true,  'admin'=>true,  'coach'=>false, 'viewer'=>false],
        'perm_duplicate_session'      => ['owner'=>true,  'admin'=>true,  'coach'=>false, 'viewer'=>false],
        'perm_delete_topic'           => ['owner'=>true,  'admin'=>true,  'coach'=>false, 'viewer'=>false],
        'perm_create_training_session'=> ['owner'=>true,  'admin'=>true,  'coach'=>true,  'viewer'=>false],
        'perm_save_attendance'        => ['owner'=>true,  'admin'=>true,  'coach'=>true,  'viewer'=>false],
        'perm_manage_training_group'  => ['owner'=>true,  'admin'=>true,  'coach'=>true,  'viewer'=>false],
        'perm_create_training_topic'  => ['owner'=>true,  'admin'=>true,  'coach'=>true,  'viewer'=>false],
        'perm_manage_idp'             => ['owner'=>true,  'admin'=>true,  'coach'=>true,  'viewer'=>false],
        'perm_manage_idp_template'    => ['owner'=>true,  'admin'=>true,  'coach'=>true,  'viewer'=>false],
        'perm_enroll_player'          => ['owner'=>true,  'admin'=>true,  'coach'=>true,  'viewer'=>false],
        'perm_view_academy'           => ['owner'=>true,  'admin'=>true,  'coach'=>true,  'viewer'=>true],
        'perm_view_teams'             => ['owner'=>true,  'admin'=>true,  'coach'=>true,  'viewer'=>true],
        'perm_view_players'           => ['owner'=>true,  'admin'=>true,  'coach'=>true,  'viewer'=>true],
        'perm_view_coaches'           => ['owner'=>true,  'admin'=>true,  'coach'=>true,  'viewer'=>true],
        'perm_view_reports'           => ['owner'=>true,  'admin'=>true,  'coach'=>true,  'viewer'=>true],
        'perm_view_training'          => ['owner'=>true,  'admin'=>true,  'coach'=>true,  'viewer'=>true],
        'perm_view_assessments'       => ['owner'=>true,  'admin'=>true,  'coach'=>true,  'viewer'=>true],
        'perm_view_idp'               => ['owner'=>true,  'admin'=>true,  'coach'=>true,  'viewer'=>true],
    ];

    /**
     * Check if current user has a specific permission in the academy.
     * Reads custom overrides from academy_role_permissions, falls back to defaults.
     * Owner always has full access regardless of overrides.
     */
    private function hasPermission($academy_id, string $permission_key): bool
    {
        $role = $this->getCachedRole($academy_id);
        if (!$role) return false;
        if ($role === 'owner') return true;

        // Check custom override first
        $custom = $this->academyModel->getAcademyRolePermissions((int)$academy_id);
        if (isset($custom[$permission_key][$role])) {
            return (bool)$custom[$permission_key][$role];
        }

        // Fall back to default matrix
        return (bool)(self::$defaultPermMatrix[$permission_key][$role] ?? false);
    }

    /**
     * คำนวณสิทธิ์ทั้งหมดของผู้ใช้ปัจจุบันสำหรับ academy นี้
     * คืน array ของ [perm_key => bool] ใช้ส่งไปยัง View ผ่าน $perms
     */
    private function getAllUserPermissions($academy_id): array
    {
        $perms = [];
        foreach (array_keys(self::$defaultPermMatrix) as $key) {
            $perms[$key] = $this->hasPermission($academy_id, $key);
        }
        return $perms;
    }

    /**
     * Check if current user can manage training (owner/admin/coach).
     *
     * @param int $academy_id
     * @return bool
     */
    private function canTrainAcademy($academy_id)
    {
        $role = $this->getCachedRole($academy_id);
        return in_array($role, ['owner', 'admin', 'coach']);
    }

    /**
     * Check if current user can view academy (any approved member).
     *
     * @param int $academy_id
     * @return bool
     */
    private function canViewAcademy($academy_id)
    {
        $role = $this->getCachedRole($academy_id);
        return $role !== null;
    }

    /**
     * Get member role with per-request caching.
     * Prevents duplicate DB queries when canManageAcademy + canViewAcademy
     * are called multiple times in the same request.
     */
    private function getCachedRole($academy_id)
    {
        $key = (int)$academy_id;
        if (!array_key_exists($key, $this->cachedRoles)) {
            $userId = session()->get('user_id');
            $coachUserId = session()->get('coach_user_id');
            $this->cachedRoles[$key] = $this->academyModel->getMemberRole($academy_id, $userId, $coachUserId);
        }
        return $this->cachedRoles[$key];
    }

    /**
     * Get current user's first academy.
     * Checks coach or regular user session and returns the first academy found.
     *
     * @return array|null Academy data or null
     */
    private function getUserAcademy()
    {
        $academies = [];

        // ลองหาจาก coach_user_id ก่อน (ถ้ามี)
        $coachUserId = session()->get('coach_user_id');
        if ($coachUserId) {
            $academies = $this->academyModel->getAcademyByCoach($coachUserId);
        }

        // ถ้าไม่เจอ ลองหาจาก user_id
        if (empty($academies)) {
            $userId = session()->get('user_id');
            if ($userId) {
                $academies = $this->academyModel->getAcademyByUser($userId);
            }
        }

        if (empty($academies)) {
            return null;
        }

        // Priority: owner > admin > other roles
        $rolePriority = ['owner' => 1, 'admin' => 2];
        $best = null;
        $bestPriority = PHP_INT_MAX;

        foreach ($academies as $academy) {
            $role = $academy['role'] ?? '';
            $priority = $rolePriority[$role] ?? 99;
            if ($priority < $bestPriority) {
                $bestPriority = $priority;
                $best = $academy;
            }
        }

        return $best ?? $academies[0];
    }

    /**
     * Get ALL academies the current user belongs to.
     * Useful for academy switcher feature.
     *
     * @return array List of academy data
     */
    public function getUserAllAcademies()
    {
        if (session()->get('user_type') === 'coach') {
            $academies = $this->academyModel->getAcademyByCoach(session()->get('coach_user_id'));
        } else {
            $academies = $this->academyModel->getAcademyByUser(session()->get('user_id'));
        }

        if (empty($academies)) {
            return [];
        }

        // Sort by role priority: owner > admin > other
        $rolePriority = ['owner' => 1, 'admin' => 2, 'coach' => 3, 'viewer' => 4];
        usort($academies, function ($a, $b) use ($rolePriority) {
            $pa = $rolePriority[$a['role'] ?? ''] ?? 99;
            $pb = $rolePriority[$b['role'] ?? ''] ?? 99;
            return $pa - $pb;
        });

        return $academies;
    }

    /**
     * Get all team IDs accessible within the user's academy context.
     * Uses academy_teams mapping instead of old sub-team hierarchy.
     *
     * @param int $academy_id
     * @return array Array of team_id integers
     */
    private function getAcademyTeamIds($academy_id)
    {
        return $this->academyModel->getAcademyTeamIds($academy_id);
    }

    /**
     * Validate that a team belongs to the given academy.
     *
     * @param int $academy_id
     * @param int $team_id
     * @return bool
     */
    private function validateTeamInAcademy($academy_id, $team_id)
    {
        return $this->academyModel->isTeamInAcademy($academy_id, $team_id);
    }

    /**
     * Resolve academy_id: use provided value or find from user's memberships.
     * Returns null if no academy found.
     *
     * @param int|null $academy_id
     * @return int|null
     */
    private function resolveAcademyId($academy_id = null)
    {
        if ($academy_id) {
            // Auto-set current_academy_id + name สำหรับ super (ให้ menu/breadcrumb ตรงกัน)
            if (session()->get('is_super_academy')) {
                $row = $this->db->table('academy')
                    ->select('name')->where('id', (int)$academy_id)
                    ->where('is_active', 1)->limit(1)->get()->getRowArray();
                session()->set('current_academy_id', (int)$academy_id);
                session()->set('current_academy_name', $row['name'] ?? '');
            }
            return (int)$academy_id;
        }

        // Super: ใช้ session.current_academy_id เท่านั้น — ห้าม fallback getUserAcademy() (จะคืน 189 academies)
        if (session()->get('is_super_academy')) {
            $cur = (int)session()->get('current_academy_id');
            if ($cur <= 0) return null;
            // Validate ว่า academy ยัง active อยู่ (กัน race ที่ academy ถูก deactivate ระหว่าง session)
            $exists = $this->db->table('academy')
                ->where('id', $cur)->where('is_active', 1)->countAllResults() > 0;
            if (!$exists) {
                session()->remove('current_academy_id');
                session()->remove('current_academy_name');
                return null;
            }
            return $cur;
        }

        $academy = $this->getUserAcademy();
        return $academy ? (int)$academy['id'] : null;
    }

    /**
     * Helper: ตรวจสอบสิทธิ์ academy + โหลดข้อมูลพื้นฐาน
     * ลด boilerplate ที่ซ้ำกัน 17+ methods
     *
     * @param int|null $academy_id
     * @param bool $needManage  ต้องการสิทธิ์ manage (owner/admin) หรือไม่
     * @return array ['redirect' => Response|null, 'academy_id' => int, 'data' => array]
     */
    private function ensureAcademyAccess($academy_id, $needManage = false)
    {
        $academy_id = $this->resolveAcademyId($academy_id);

        if (!$academy_id) {
            return ['redirect' => redirect()->to(base_url('academy/register')), 'academy_id' => null, 'data' => []];
        }

        if (!$this->canViewAcademy($academy_id)) {
            return ['redirect' => redirect()->to(base_url('academy/join'))->with('error', lang('Common.academy_no_permission')), 'academy_id' => $academy_id, 'data' => []];
        }

        if ($needManage && !$this->canManageAcademy($academy_id)) {
            return ['redirect' => redirect()->to(base_url('academy/info/' . $academy_id))->with('error', lang('Common.academy_no_permission')), 'academy_id' => $academy_id, 'data' => []];
        }

        $data = [];
        $data['academy'] = $this->academyModel->getAcademy($academy_id);

        if (empty($data['academy'])) {
            return ['redirect' => redirect()->to(base_url('academy/register'))->with('error', lang('Common.no_data_found')), 'academy_id' => $academy_id, 'data' => []];
        }

        $data['canManage'] = $this->canManageAcademy($academy_id);
        $data['perms'] = $this->getAllUserPermissions($academy_id);

        return ['redirect' => null, 'academy_id' => $academy_id, 'data' => $data];
    }

    /**
     * Helper: ตรวจสอบสิทธิ์ manage สำหรับ AJAX endpoints
     * ลด boilerplate ที่ซ้ำกัน 30+ methods
     *
     * @param int $academy_id
     * @return \CodeIgniter\HTTP\Response|null  Return JSON error or null (= OK)
     */
    private function ensureAjaxManageAccess($academy_id)
    {
        if (!$this->canManageAcademy($academy_id)) {
            return $this->response->setJSON(['status' => 'error', 'message' => lang('Common.academy_no_permission')]);
        }
        return null;
    }

    /**
     * Helper: ตรวจสอบสิทธิ์ train (owner/admin/coach) สำหรับ AJAX endpoints
     */
    private function ensureAjaxTrainAccess($academy_id)
    {
        if (!$this->canTrainAcademy($academy_id)) {
            return $this->response->setJSON(['status' => 'error', 'message' => lang('Common.academy_no_permission')]);
        }
        return null;
    }

    /**
     * Helper: ตรวจสอบสิทธิ์ train (owner/admin/coach) + โหลดข้อมูลพื้นฐาน
     * เหมือน ensureAcademyAccess($id, true) แต่อนุญาต coach ด้วย
     */
    private function ensureAcademyTrainAccess($academy_id)
    {
        $academy_id = $this->resolveAcademyId($academy_id);

        if (!$academy_id) {
            return ['redirect' => redirect()->to(base_url('academy/register')), 'academy_id' => null, 'data' => []];
        }

        if (!$this->canViewAcademy($academy_id)) {
            return ['redirect' => redirect()->to(base_url('academy/join'))->with('error', lang('Common.academy_no_permission')), 'academy_id' => $academy_id, 'data' => []];
        }

        if (!$this->canTrainAcademy($academy_id)) {
            return ['redirect' => redirect()->to(base_url('academy/info/' . $academy_id))->with('error', lang('Common.academy_no_permission')), 'academy_id' => $academy_id, 'data' => []];
        }

        $data = [];
        $data['academy'] = $this->academyModel->getAcademy($academy_id);

        if (empty($data['academy'])) {
            return ['redirect' => redirect()->to(base_url('academy/register'))->with('error', lang('Common.no_data_found')), 'academy_id' => $academy_id, 'data' => []];
        }

        $data['canManage'] = $this->canTrainAcademy($academy_id);
        $data['perms'] = $this->getAllUserPermissions($academy_id);

        return ['redirect' => null, 'academy_id' => $academy_id, 'data' => $data];
    }

    private function ensureAjaxPermAccess($academy_id, string $perm_key)
    {
        if (!$this->hasPermission($academy_id, $perm_key)) {
            return $this->response->setJSON(['status' => 'error', 'message' => lang('Common.academy_no_permission')]);
        }
        return null;
    }

    private function ensurePermAccess($academy_id, string $perm_key)
    {
        $academy_id = $this->resolveAcademyId($academy_id);
        if (!$academy_id) {
            return ['redirect' => redirect()->to(base_url('academy/register')), 'academy_id' => null, 'data' => []];
        }
        if (!$this->canViewAcademy($academy_id)) {
            return ['redirect' => redirect()->to(base_url('academy/join'))->with('error', lang('Common.academy_no_permission')), 'academy_id' => $academy_id, 'data' => []];
        }
        if (!$this->hasPermission($academy_id, $perm_key)) {
            return ['redirect' => redirect()->to(base_url('academy/info/' . $academy_id))->with('error', lang('Common.academy_no_permission')), 'academy_id' => $academy_id, 'data' => []];
        }
        $data = [];
        $data['academy'] = $this->academyModel->getAcademy($academy_id);
        if (empty($data['academy'])) {
            return ['redirect' => redirect()->to(base_url('academy/register'))->with('error', lang('Common.no_data_found')), 'academy_id' => $academy_id, 'data' => []];
        }
        $data['canManage'] = true;
        $data['perms'] = $this->getAllUserPermissions($academy_id);
        return ['redirect' => null, 'academy_id' => $academy_id, 'data' => $data];
    }

    /**
     * Helper: Resolve team ภายใน academy (ถ้าไม่ระบุ ใช้ทีมแรก)
     * ลด code ที่ซ้ำกัน 3+ methods
     *
     * @param int $academy_id
     * @param int|null $team_id
     * @return array ['redirect' => Response|null, 'team' => array|null, 'team_id' => int|null]
     */
    private function resolveTeamForAcademy($academy_id, $team_id = null)
    {
        if ($team_id) {
            if (!$this->validateTeamInAcademy($academy_id, $team_id)) {
                return [
                    'redirect' => redirect()->to(base_url('academy/teams/' . $academy_id))->with('error', lang('Common.academy_team_not_in_academy')),
                    'team' => null,
                    'team_id' => null
                ];
            }
            $team = $this->academyModel->getTeam($team_id);
            session()->set('team', $team);
            return ['redirect' => null, 'team' => $team, 'team_id' => (int)$team_id];
        }

        // Fallback: ใช้ทีมแรกของ academy
        $teams = $this->academyModel->getAcademyTeams($academy_id);
        if (!empty($teams)) {
            $team = $this->academyModel->getTeam($teams[0]['id']);
            session()->set('team', $team);
            return ['redirect' => null, 'team' => $team, 'team_id' => (int)$teams[0]['id']];
        }

        return ['redirect' => null, 'team' => null, 'team_id' => null];
    }

    // =====================================================
    // Shared-trait hooks (jersey / get_player / chart) + academy-specific overrides
    // =====================================================

    // --- get_player: shared logic ใน App\Traits\PlayerAjaxTrait ---
    protected function playerAjaxMessages(): array
    {
        return [
            'auth_required'      => lang('Common.academy_auth_required'),
            'player_not_found'   => lang('Common.academy_player_not_found'),
            'team_not_found'     => lang('Common.academy_team_not_found'),
            'player_not_in_team' => lang('Common.academy_player_not_in_team'),
        ];
    }

    // =====================================================
    // Chart Selection (save/get player & coach chart)
    // =====================================================


    /**
     * Save coach chart selection via AJAX
     * POST /academy/save-coach-chart-selection
     */
    public function save_coach_chart_selection()
    {
        $teamId = $this->request->getPost('team_id');
        $coachIds = $this->request->getPost('coach_ids');

        if (!$teamId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Team ID required']);
        }

        if (!is_array($coachIds)) {
            $coachIds = [];
        }

        // Validate coaches belong to this team or academy context
        $validCoaches = $this->academyModel->getTeamCoaches($teamId);
        $validCoachIds = array_column($validCoaches, 'id');

        $coachIds = array_filter($coachIds, function ($id) use ($validCoachIds) {
            return in_array($id, $validCoachIds);
        });

        $coachIds = array_values($coachIds);

        $result = $this->academyModel->saveCoachChartSelection($teamId, $coachIds);

        if ($result) {
            return $this->response->setJSON(['status' => 'success']);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to save']);
        }
    }

    // --- chart selection: shared logic ใน App\Traits\ChartSelectionTrait ---
    protected function chartModel()
    {
        return $this->academyModel;
    }

    // =====================================================
    // Jersey Upload Management
    // =====================================================

    // --- Jersey management: shared logic ใน App\Traits\JerseyManagementTrait ---
    protected function jerseyEntityModel()
    {
        return $this->academyModel;
    }

    protected function jerseyMessages(): array
    {
        return [
            'auth_required'    => lang('Common.academy_auth_required'),
            'data_incomplete'  => lang('Common.academy_data_incomplete'),
            'kit_type_invalid' => lang('Common.academy_kit_type_invalid'),
            'image_read_error' => lang('Common.academy_image_read_error'),
            'kit_uploaded'     => lang('Common.academy_kit_uploaded'),
            'kit_deleted'      => lang('Common.academy_kit_deleted'),
        ];
    }

}
