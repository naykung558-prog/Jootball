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

trait AcademyIdpTrait
{
    // =====================================================
    // IDP (Individual Development Plan)
    // =====================================================

    /**
     * หน้ารวม IDP — รายชื่อนักเตะ + สถานะ IDP
     */
    public function idpOverview($academy_id)
    {
        $access = $this->ensureAcademyAccess($academy_id);
        if ($access['redirect']) return $access['redirect'];
        $academy_id = $access['academy_id'];
        $data = $access['data'];

        // โค้ชมีสิทธิ์สร้าง/แก้ไขแผน IDP
        $data['canManage'] = $this->hasPermission($academy_id, 'perm_manage_idp');

        $data['overview'] = $this->academyModel->getIDPOverview($academy_id);
        $data['players'] = $this->academyModel->getAcademyPlayersWithIDPStatus($academy_id);
        $data['idpGroupPlans'] = $this->academyModel->getIDPGroupPlans($academy_id);
        $data['title'] = lang('Common.idp_title');

        return view('Modules\Academy\Views\idp_overview', $data);
    }

    /**
     * ดูแผน IDP — goals, scores, progress, feedback
     */
    public function idpPlanView($academy_id, $plan_id)
    {
        $access = $this->ensureAcademyAccess($academy_id);
        if ($access['redirect']) return $access['redirect'];
        $academy_id = $access['academy_id'];
        $data = $access['data'];

        // โค้ชมีสิทธิ์แก้ไขแผนและบันทึกผลการประเมิน
        $data['canManage'] = $this->hasPermission($academy_id, 'perm_manage_idp');

        $plan = $this->academyModel->getIDPPlan($plan_id);

        if (!$plan || (int)$plan['academy_id'] !== (int)$academy_id) {
            return redirect()->to('/academy/idp/' . $academy_id)->with('error', lang('Common.academy_not_found'));
        }

        $template_id = isset($plan['template_id']) ? $plan['template_id'] : null;
        $data['plan'] = $plan;
        $data['goals'] = $this->academyModel->getGoalsByDimension($plan_id);
        $data['dimensions'] = $this->academyModel->getIDPDimensions($template_id);
        $data['allIndicators'] = $this->academyModel->getAllIDPIndicatorsGrouped();
        $data['summary'] = $this->academyModel->getIDPPlanSummary($plan_id);
        $data['compareData'] = $this->academyModel->getIDPCompareData($plan_id);
        $data['feedbacks'] = $this->academyModel->getIDPFeedbacks($plan_id);
        $data['latestFeedback'] = $this->academyModel->getLatestFeedback($plan_id);
        $data['title'] = lang('Common.idp_plan_view');

        return view('Modules\Academy\Views\idp_plan_view', $data);
    }

    /**
     * ฟอร์มสร้าง/แก้ไขแผน IDP
     */
    public function idpPlanManage($academy_id, $player_id, $plan_id = null)
    {
        $access = $this->ensurePermAccess($academy_id, 'perm_manage_idp');
        if ($access['redirect']) return $access['redirect'];
        $academy_id = $access['academy_id'];
        $data = $access['data'];

        // Get player info
        $player = $this->db->table('player')
            ->where('id', $player_id)
            ->get()->getRowArray();
        if (!$player) {
            return redirect()->to('/academy/idp/' . $academy_id)->with('error', lang('Common.academy_player_not_found'));
        }

        $plan = null;
        if ($plan_id) {
            $plan = $this->academyModel->getIDPPlan($plan_id);
            if (!$plan || (int)$plan['academy_id'] !== (int)$academy_id) {
                return redirect()->to('/academy/idp/' . $academy_id)->with('error', lang('Common.academy_not_found'));
            }
        }

        // Get coaches list for dropdown
        $coaches = $this->academyModel->getAcademyCoaches($academy_id);

        $data['player'] = $player;
        $data['plan'] = $plan;
        $data['coaches'] = $coaches;
        $data['templates'] = $this->academyModel->getAvailableIDPTemplates($academy_id);
        $data['dimensions'] = $this->academyModel->getIDPDimensions();
        $data['indicatorsGrouped'] = $this->academyModel->getAllIDPIndicatorsGrouped();
        $data['title'] = $plan ? lang('Common.idp_plan_edit') : lang('Common.idp_plan_create');

        if ($plan) {
            $data['goals'] = $this->academyModel->getIDPGoals($plan_id);
        }

        return view('Modules\Academy\Views\idp_plan_manage', $data);
    }

    /**
     * บันทึกแผน IDP (form submit)
     */
    public function idpPlanSave()
    {
        $academy_id = $this->request->getPost('academy_id');
        if (!$this->hasPermission($academy_id, 'perm_manage_idp')) {
            return redirect()->to('/academy/info')->with('error', lang('Common.academy_no_permission'));
        }

        $data = [
            'id'             => $this->request->getPost('id'),
            'academy_id'     => $academy_id,
            'player_id'      => $this->request->getPost('player_id'),
            'coach_id'       => $this->request->getPost('coach_id'),
            'template_id'    => $this->request->getPost('template_id'),
            'title'          => $this->request->getPost('title'),
            'season'         => $this->request->getPost('season'),
            'start_date'     => $this->request->getPost('start_date'),
            'end_date'       => $this->request->getPost('end_date'),
            'overall_notes'  => $this->request->getPost('overall_notes'),
            'status'         => $this->request->getPost('status') ?: 'draft',
            'created_by'     => session()->get('user_id') ?: session()->get('coach_user_id'),
        ];

        $plan_id = $this->academyModel->saveIDPPlan($data);

        return redirect()->to('/academy/idp/plan/view/' . $academy_id . '/' . $plan_id)
            ->with('success', lang('Common.idp_plan_saved'));
    }

    /**
     * ลบแผน IDP (AJAX)
     */
    public function idpPlanDelete()
    {
        $academy_id = $this->getInput('academy_id');
        $err = $this->ensureAjaxPermAccess($academy_id, 'perm_manage_idp');
        if ($err) return $err;

        $plan_id = $this->getInput('plan_id');
        $this->academyModel->deleteIDPPlan($plan_id);

        return $this->response->setJSON(['status' => 'success', 'message' => lang('Common.idp_plan_deleted')]);
    }

    /**
     * บันทึกเป้าหมาย (AJAX JSON)
     */
    public function idpGoalSave()
    {
        $data = $this->getInput();
        $plan = $this->academyModel->getIDPPlan($data['plan_id'] ?? 0);
        if (!$plan || !$this->hasPermission($plan['academy_id'], 'perm_manage_idp')) {
            return $this->response->setJSON(['status' => 'error', 'message' => lang('Common.academy_no_permission')]);
        }

        $goal_id = $this->academyModel->saveIDPGoal($data);

        return $this->response->setJSON(['status' => 'success', 'message' => lang('Common.idp_goal_saved'), 'goal_id' => $goal_id]);
    }

    /**
     * ลบเป้าหมาย (AJAX JSON)
     */
    public function idpGoalDelete()
    {
        $goal_id = $this->getInput('goal_id');
        $goal = $this->academyModel->getIDPGoal($goal_id);
        if (!$goal) {
            return $this->response->setJSON(['status' => 'error', 'message' => lang('Common.not_found')]);
        }
        $plan = $this->academyModel->getIDPPlan($goal['plan_id']);
        if (!$plan || !$this->hasPermission($plan['academy_id'], 'perm_manage_idp')) {
            return $this->response->setJSON(['status' => 'error', 'message' => lang('Common.academy_no_permission')]);
        }

        $this->academyModel->deleteIDPGoal($goal_id);

        return $this->response->setJSON(['status' => 'success', 'message' => lang('Common.idp_goal_deleted')]);
    }

    /**
     * บันทึกความก้าวหน้า (AJAX JSON)
     */
    public function idpProgressSave()
    {
        $data = $this->getInput();
        $goal = $this->academyModel->getIDPGoal($data['goal_id'] ?? 0);
        if (!$goal) {
            return $this->response->setJSON(['status' => 'error', 'message' => lang('Common.not_found')]);
        }
        $plan = $this->academyModel->getIDPPlan($goal['plan_id']);
        if (!$plan || !$this->hasPermission($plan['academy_id'], 'perm_manage_idp')) {
            return $this->response->setJSON(['status' => 'error', 'message' => lang('Common.academy_no_permission')]);
        }

        $data['recorded_by'] = session()->get('user_id') ?: session()->get('coach_user_id');
        $result = $this->academyModel->updateGoalProgress($data['goal_id'], $data);

        if ($result) {
            return $this->response->setJSON(['status' => 'success', 'message' => lang('Common.idp_progress_saved')]);
        }
        return $this->response->setJSON(['status' => 'error', 'message' => lang('Common.save_failed')]);
    }

    /**
     * หน้าประเมินรายมิติ (7 มิติ + indicators)
     */
    public function idpAssessmentView($academy_id, $plan_id)
    {
        $access = $this->ensureAcademyAccess($academy_id);
        if ($access['redirect']) return $access['redirect'];
        $academy_id = $access['academy_id'];
        $data = $access['data'];

        $plan = $this->academyModel->getIDPPlan($plan_id);

        if (!$plan || (int)$plan['academy_id'] !== (int)$academy_id) {
            return redirect()->to('/academy/idp/' . $academy_id)->with('error', lang('Common.academy_not_found'));
        }

        $template_id = isset($plan['template_id']) ? $plan['template_id'] : null;
        $data['plan'] = $plan;
        $data['dimensions'] = $this->academyModel->getIDPDimensions($template_id);
        $data['dimensionsWithIndicators'] = $this->academyModel->getAllIDPIndicatorsGrouped();
        $data['existingAssessments'] = $this->academyModel->getIDPAssessments($plan_id);
        $data['title'] = lang('Common.idp_assessment_title');

        return view('Modules\Academy\Views\idp_assessment', $data);
    }

    /**
     * บันทึกคะแนนประเมิน (AJAX JSON) — bulk save
     */
    public function idpAssessmentSave()
    {
        $data = $this->getInput();
        $plan = $this->academyModel->getIDPPlan($data['plan_id'] ?? 0);
        if (!$plan || !$this->hasPermission($plan['academy_id'], 'perm_manage_idp')) {
            return $this->response->setJSON(['status' => 'error', 'message' => lang('Common.academy_no_permission')]);
        }

        $assessed_by = session()->get('user_id') ?: session()->get('coach_user_id');
        $result = $this->academyModel->bulkSaveIDPAssessments(
            $data['plan_id'],
            $data['scores'] ?? [],
            $data['assessment_type'] ?? 'adhoc',
            $data['assessment_date'] ?? date('Y-m-d'),
            $assessed_by
        );

        if ($result) {
            return $this->response->setJSON(['status' => 'success', 'message' => lang('Common.idp_assessment_saved')]);
        }
        return $this->response->setJSON(['status' => 'error', 'message' => lang('Common.save_failed')]);
    }

    /**
     * บันทึก feedback (AJAX JSON)
     */
    public function idpFeedbackSave()
    {
        $data = $this->getInput();
        $plan = $this->academyModel->getIDPPlan($data['plan_id'] ?? 0);
        if (!$plan || !$this->hasPermission($plan['academy_id'], 'perm_manage_idp')) {
            return $this->response->setJSON(['status' => 'error', 'message' => lang('Common.academy_no_permission')]);
        }

        $data['created_by'] = session()->get('user_id') ?: session()->get('coach_user_id');
        $this->academyModel->saveIDPFeedback($data);

        return $this->response->setJSON(['status' => 'success', 'message' => lang('Common.idp_feedback_saved')]);
    }

    /**
     * ลบ feedback (AJAX JSON)
     */
    public function idpFeedbackDelete()
    {
        $feedback_id = $this->getInput('feedback_id');
        $feedback = $this->db->table('idp_feedback')->where('id', $feedback_id)->get()->getRowArray();
        if (!$feedback) {
            return $this->response->setJSON(['status' => 'error', 'message' => lang('Common.not_found')]);
        }
        $plan = $this->academyModel->getIDPPlan($feedback['plan_id']);
        if (!$plan || !$this->hasPermission($plan['academy_id'], 'perm_manage_idp')) {
            return $this->response->setJSON(['status' => 'error', 'message' => lang('Common.academy_no_permission')]);
        }

        $this->academyModel->deleteIDPFeedback($feedback_id);

        return $this->response->setJSON(['status' => 'success', 'message' => lang('Common.idp_feedback_deleted')]);
    }

    /**
     * รายงาน IDP รายนักเตะ (radar + line charts + timeline)
     */
    public function idpReport($academy_id, $player_id)
    {
        $access = $this->ensureAcademyAccess($academy_id);
        if ($access['redirect']) return $access['redirect'];
        $academy_id = $access['academy_id'];
        $data = $access['data'];

        $player = $this->db->table('player')->where('id', $player_id)->get()->getRowArray();

        if (!$player) {
            return redirect()->to('/academy/idp/' . $academy_id)->with('error', lang('Common.academy_not_found'));
        }

        $data['player'] = $player;
        $data['plans'] = $this->academyModel->getPlayerIDPPlans($player_id, $academy_id);
        $data['idpSummary'] = $this->academyModel->getPlayerIDPSummary($player_id, $academy_id);
        $data['dimensions'] = $this->academyModel->getIDPDimensions();

        // Initialize defaults to prevent undefined variable warnings
        $data['compareData'] = [];
        $data['activePlan'] = null;
        $data['goals'] = [];
        $data['feedbacks'] = [];

        // If there's an active plan, get comparison data
        $activePlan = $this->academyModel->getActiveIDPPlan($player_id, $academy_id);
        if ($activePlan) {
            $data['compareData'] = $this->academyModel->getIDPCompareData($activePlan['id']);
            $data['activePlan'] = $activePlan;
            $data['goals'] = $this->academyModel->getGoalsByDimension($activePlan['id']);
            $data['feedbacks'] = $this->academyModel->getIDPFeedbacks($activePlan['id']);
            // Indicator-level breakdown (Phase 6)
            $data['indicatorScores'] = $this->academyModel->getPlayerIndicatorScores($activePlan['id']);
        }

        $data['title'] = lang('Common.idp_report_title');

        return view('Modules\Academy\Views\idp_report', $data);
    }

    /**
     * Group IDP Report — radar comparison, top performers, indicator heatmap
     */
    public function idpGroupReport($academy_id, $group_id)
    {
        $access = $this->ensureAcademyAccess($academy_id);
        if ($access['redirect']) return $access['redirect'];
        $academy_id = $access['academy_id'];
        $data = $access['data'];

        // Detect V2: check if $group_id is actually an idp_group_plan_id
        $groupPlan = $this->academyModel->getIDPGroupPlan($group_id);
        $isV2 = !empty($groupPlan);
        $group_plan_id = $isV2 ? (int)$group_id : null;

        if ($isV2) {
            $group = [
                'id' => $groupPlan['id'],
                'name' => $groupPlan['name'],
                'academy_id' => $groupPlan['academy_id'],
            ];
            if ((int)$groupPlan['academy_id'] !== (int)$academy_id) {
                return redirect()->to('/academy/idp/' . $academy_id)->with('error', lang('Common.academy_not_found'));
            }
        } else {
            $group = $this->academyModel->getTrainingGroup($group_id);
            if (!$group || (int)$group['academy_id'] !== (int)$academy_id) {
                return redirect()->to('/academy/idp/' . $academy_id)->with('error', lang('Common.academy_not_found'));
            }
        }

        // Resolve template_id
        $templates = $this->academyModel->getAvailableIDPTemplates($academy_id);

        $template_id = null;
        if ($isV2 && !empty($groupPlan['template_id'])) {
            $template_id = $groupPlan['template_id'];
        }
        if ($template_id === null) {
            $plans = $this->academyModel->getGroupIDPPlans($group_id, $academy_id, null, $group_plan_id);
            if (!empty($plans)) {
                $template_id = isset($plans[0]['template_id']) ? $plans[0]['template_id'] : null;
            }
        }
        if ($template_id === null && !empty($templates)) {
            $template_id = $templates[0]['id'];
        }

        // Allow URL override: ?template_id=X
        $reqTemplateId = $this->request->getGet('template_id');
        if ($reqTemplateId !== null && $reqTemplateId !== '') {
            $template_id = (int)$reqTemplateId;
        }

        $plans = $this->academyModel->getGroupIDPPlans($group_id, $academy_id, $template_id, $group_plan_id);

        $dimensions = $this->academyModel->getIDPDimensions($template_id);
        $comparisonData = $this->academyModel->getGroupNormalizedDimensionScores($group_id, $academy_id, $template_id, $group_plan_id);
        $heatmapData = $this->academyModel->getGroupIndicatorHeatmap($group_id, $academy_id, $template_id, $group_plan_id);
        $trendData = $this->academyModel->getGroupDimensionTrends($group_id, $academy_id, $template_id, 30, $group_plan_id);
        $summaryData = $this->academyModel->getGroupAssessmentSummary($group_id, $academy_id, $template_id, $group_plan_id);

        // Calculate top performers from normalized comparison data (no separate DB call)
        $topPerformers = [];
        foreach ($dimensions as $dim) {
            $dimId = (int)$dim['id'];
            $playerScores = [];
            foreach ($comparisonData as $player) {
                foreach ($player['dimensions'] as $ds) {
                    if ((int)$ds['dimension_id'] === $dimId && (float)$ds['score'] > 0) {
                        $playerScores[] = [
                            'player_name'  => $player['player_name'],
                            'player_image' => $player['player_image'],
                            'shirt_number' => $player['shirt_number'],
                            'plan_id'      => $player['plan_id'],
                            'score'        => (float)$ds['score'],
                        ];
                    }
                }
            }
            usort($playerScores, function($a, $b) {
                if ($a['score'] == $b['score']) return 0;
                return ($a['score'] > $b['score']) ? -1 : 1;
            });
            $topPerformers[$dimId] = [
                'dimension' => $dim,
                'players'   => array_slice($playerScores, 0, 3),
            ];
        }

        // Get plan info for header display (template title, coach, created date)
        $planInfo = [];
        if (!empty($plans)) {
            $firstPlan = $plans[0];
            $planInfo['template_title'] = '';
            foreach ($templates as $tpl) {
                if ((int)$tpl['id'] === (int)$template_id) {
                    $planInfo['template_title'] = $tpl['title'];
                    break;
                }
            }
            $planInfo['coach_name'] = isset($firstPlan['coach_name']) ? $firstPlan['coach_name'] : '';
            $planInfo['player_count'] = count($plans);
            $planInfo['created_at'] = isset($firstPlan['created_at']) ? $firstPlan['created_at'] : '';
        }

        $data['groupPlan']      = $groupPlan;
        $data['isV2']           = $isV2;
        $data['group_plan_id']  = $group_plan_id;
        $data['group']          = $group;
        $data['templates']      = $templates;
        $data['template_id']    = $template_id;
        $data['dimensions']     = $dimensions;
        $data['comparisonData'] = $comparisonData;
        $data['heatmapData']    = $heatmapData;
        $data['topPerformers']  = $topPerformers;
        $data['trendData']      = $trendData;
        $data['summaryData']    = $summaryData;
        $data['planInfo']       = $planInfo;
        $data['title']          = lang('Common.idp_group_report');

        return view('Modules\Academy\Views\idp_group_report', $data);
    }

    /**
     * AJAX: ดึงรายละเอียดการประเมิน indicator-level ของ plan + dimension
     * GET /academy/idp/dimension-detail?plan_id=X&dimension_id=Y
     */
    public function idpDimensionDetail()
    {
        $planId      = (int)$this->request->getGet('plan_id');
        $dimensionId = (int)$this->request->getGet('dimension_id');

        if ($planId <= 0 || $dimensionId <= 0) {
            return $this->response->setStatusCode(400)
                ->setJSON(['ok' => false, 'error' => 'invalid params']);
        }

        $detail = $this->academyModel->getPlanDimensionDetail($planId, $dimensionId);
        if ($detail === null) {
            return $this->response->setStatusCode(404)
                ->setJSON(['ok' => false, 'error' => 'plan or dimension not found']);
        }

        $academyId = (int)$detail['plan']['academy_id'];
        if (!$this->canViewAcademy($academyId)) {
            return $this->response->setStatusCode(403)
                ->setJSON(['ok' => false, 'error' => 'forbidden']);
        }

        return $this->response->setJSON(array_merge(['ok' => true], $detail));
    }

    // ============================================================
    // IDP Group (Batch) Methods
    // ============================================================

    /**
     * หน้าสร้าง/แก้ไขแผน IDP กลุ่ม (V2: idp_group_plans entity)
     */
    public function idpGroupCreate($academy_id, $group_plan_id = null)
    {
        $access = $this->ensurePermAccess($academy_id, 'perm_manage_idp');
        if ($access['redirect']) return $access['redirect'];
        $academy_id = $access['academy_id'];
        $data = $access['data'];

        $data['groups'] = $this->academyModel->getTrainingGroups($academy_id);
        $data['coaches'] = $this->academyModel->getAvailableCoaches($academy_id);
        $data['templates'] = $this->academyModel->getAvailableIDPTemplates($academy_id);
        $data['dimensions'] = $this->academyModel->getIDPDimensions();
        $data['title'] = lang('Common.idp_create_group_plan');

        // Edit mode V2: load existing group plan by ID
        $editGroupPlanId = $group_plan_id ?: $this->request->getGet('group_plan_id');
        $data['editData'] = null;
        if ($editGroupPlanId) {
            $groupPlan = $this->academyModel->getIDPGroupPlan((int)$editGroupPlanId);
            if (!empty($groupPlan) && (int)$groupPlan['academy_id'] === (int)$academy_id) {
                $players = $this->academyModel->getIDPGroupPlanPlayers((int)$editGroupPlanId);
                $data['editData'] = [
                    'id'          => $groupPlan['id'],
                    'name'        => $groupPlan['name'],
                    'template_id' => $groupPlan['template_id'],
                    'coach_id'    => $groupPlan['coach_id'],
                    'season'      => $groupPlan['season'],
                    'start_date'  => $groupPlan['start_date'],
                    'end_date'    => $groupPlan['end_date'],
                    'status'      => $groupPlan['status'],
                    'notes'       => $groupPlan['notes'],
                    'player_ids'  => array_column($players, 'id'),
                ];
                $data['title'] = lang('Common.idp_edit_group_plan');
            }
        }

        // Backward compat: old group_id edit mode
        $editGroupId = $this->request->getGet('group_id');
        if (!$data['editData'] && $editGroupId) {
            $plans = $this->academyModel->getGroupIDPPlans((int)$editGroupId, $academy_id);
            if (!empty($plans)) {
                $first = $plans[0];
                $data['editData'] = [
                    'group_id'    => (int)$editGroupId,
                    'template_id' => $first['template_id'],
                    'title'       => $first['title'],
                    'name'        => $first['title'],
                    'season'      => $first['season'],
                    'status'      => $first['status'],
                    'start_date'  => $first['start_date'],
                    'end_date'    => $first['end_date'],
                    'coach_id'    => $first['coach_id'],
                    'player_ids'  => array_column($plans, 'player_id'),
                ];
                $data['title'] = lang('Common.idp_edit_group_plan');
            }
        }

        return view('Modules\Academy\Views\idp_group_create', $data);
    }

    /**
     * บันทึกแผน IDP กลุ่ม V2 (form POST) — สร้าง/แก้ไข idp_group_plans entity
     */
    public function idpGroupSave()
    {
        $academy_id = $this->request->getPost('academy_id');
        if (!$this->hasPermission($academy_id, 'perm_manage_idp')) {
            return redirect()->to('/academy/info')->with('error', lang('Common.academy_no_permission'));
        }

        $playerIds = $this->request->getPost('player_ids');
        $name = $this->request->getPost('name');
        $template_id = $this->request->getPost('template_id');

        if (empty($name) || empty($template_id) || empty($playerIds)) {
            return redirect()->back()->with('error', lang('Common.fill_required'));
        }

        $groupPlanData = [
            'academy_id'  => $academy_id,
            'name'        => $name,
            'template_id' => $template_id,
            'coach_id'    => $this->request->getPost('coach_id') ?: null,
            'season'      => $this->request->getPost('season') ?: null,
            'start_date'  => $this->request->getPost('start_date') ?: null,
            'end_date'    => $this->request->getPost('end_date') ?: null,
            'status'      => $this->request->getPost('status') ?: 'active',
            'notes'       => $this->request->getPost('notes') ?: null,
            'created_by'  => session()->get('user_id') ?: session()->get('coach_user_id'),
        ];

        // Check if editing existing group plan
        $editId = $this->request->getPost('group_plan_id');
        if ($editId) {
            $groupPlanData['id'] = $editId;
            $groupPlanId = $this->academyModel->saveIDPGroupPlan($groupPlanData);

            // Get existing player IDs
            $existingPlayers = $this->academyModel->getIDPGroupPlanPlayers($groupPlanId);
            $existingPlayerIds = array_column($existingPlayers, 'id');

            // Remove players no longer in list
            $toRemove = array_diff($existingPlayerIds, $playerIds);
            foreach ($toRemove as $pid) {
                $this->academyModel->removePlayerFromGroupPlan($groupPlanId, $pid);
            }

            // Add new players
            $toAdd = array_diff($playerIds, $existingPlayerIds);
            if (!empty($toAdd)) {
                $planTemplate = [
                    'academy_id'  => $academy_id,
                    'coach_id'    => $groupPlanData['coach_id'],
                    'template_id' => $template_id,
                    'title'       => $name,
                    'season'      => $groupPlanData['season'],
                    'start_date'  => $groupPlanData['start_date'],
                    'end_date'    => $groupPlanData['end_date'],
                    'status'      => $groupPlanData['status'],
                    'created_by'  => $groupPlanData['created_by'],
                ];
                $this->academyModel->addPlayersToGroupPlan($groupPlanId, $toAdd, $planTemplate);
            }

            return redirect()->to('/academy/idp/' . $academy_id)
                ->with('success', lang('Common.save_success'));
        }

        // Create new group plan
        $groupPlanId = $this->academyModel->saveIDPGroupPlan($groupPlanData);

        if ($groupPlanId) {
            $planTemplate = [
                'academy_id'  => $academy_id,
                'coach_id'    => $groupPlanData['coach_id'],
                'template_id' => $template_id,
                'title'       => $name,
                'season'      => $groupPlanData['season'],
                'start_date'  => $groupPlanData['start_date'],
                'end_date'    => $groupPlanData['end_date'],
                'status'      => $groupPlanData['status'],
                'created_by'  => $groupPlanData['created_by'],
            ];
            $result = $this->academyModel->addPlayersToGroupPlan($groupPlanId, $playerIds, $planTemplate);

            return redirect()->to('/academy/idp/' . $academy_id)
                ->with('success', sprintf(lang('Common.idp_plans_created_count'), $result['count']));
        }

        return redirect()->back()->with('error', lang('Common.save_failed'));
    }

    /**
     * ลบแผน IDP กลุ่ม (AJAX POST) — V2 + backward compat
     */
    public function idpGroupPlanDelete()
    {
        $input = $this->getInput();
        $academy_id = isset($input['academy_id']) ? (int)$input['academy_id'] : 0;

        $err = $this->ensureAjaxPermAccess($academy_id, 'perm_manage_idp');
        if ($err) return $err;

        // V2: Delete by idp_group_plan_id
        $group_plan_id = isset($input['group_plan_id']) ? (int)$input['group_plan_id'] : 0;
        if ($group_plan_id) {
            $result = $this->academyModel->deleteIDPGroupPlan($group_plan_id, $academy_id);
            if ($result['success']) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => lang('Common.idp_group_plan_deleted'),
                    'count' => $result['count'],
                ]);
            }
            return $this->response->setJSON(['status' => 'error', 'message' => lang('Common.delete_failed')]);
        }

        // V1 fallback: Delete by group_id + template_id
        $group_id = isset($input['group_id']) ? (int)$input['group_id'] : 0;
        $template_id = isset($input['template_id']) ? (int)$input['template_id'] : null;

        if (empty($group_id)) {
            return $this->response->setJSON(['status' => 'error', 'message' => lang('Common.fill_required')]);
        }

        $result = $this->academyModel->deleteGroupIDPBatch($group_id, $academy_id, $template_id);

        if ($result['success']) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => sprintf(lang('Common.idp_plans_deleted_count'), $result['count']),
                'count' => $result['count'],
            ]);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => lang('Common.delete_failed')]);
    }

    /**
     * หน้าประเมินกลุ่ม — template → dimension → indicator scoring
     */
    public function idpGroupAssess($academy_id, $group_id)
    {
        $access = $this->ensureAcademyAccess($academy_id);
        if ($access['redirect']) return $access['redirect'];
        $academy_id = $access['academy_id'];
        $data = $access['data'];

        // โค้ชมีสิทธิ์บันทึกผลการประเมินกลุ่ม
        $data['canManage'] = $this->hasPermission($academy_id, 'perm_manage_idp');

        // Detect V2: check if $group_id is actually an idp_group_plan_id
        $groupPlan = $this->academyModel->getIDPGroupPlan($group_id);
        $isV2 = !empty($groupPlan);
        $group_plan_id = $isV2 ? (int)$group_id : null;

        // Resolve template_id from URL param or from group plan / plans
        $template_id = $this->request->getGet('template_id');
        if (!$template_id) {
            if ($isV2 && !empty($groupPlan['template_id'])) {
                $template_id = $groupPlan['template_id'];
            } else {
                // Fallback: get from first active plan in this group
                $builder = $this->db->table('idp_plans')
                    ->where('academy_id', $academy_id)
                    ->where('status', 'active')
                    ->orderBy('created_at', 'DESC')
                    ->limit(1);
                if ($isV2) {
                    $builder->where('idp_group_plan_id', $group_plan_id);
                } else {
                    $builder->where('group_id', $group_id);
                }
                $firstPlan = $builder->get()->getRowArray();
                $template_id = !empty($firstPlan['template_id']) ? $firstPlan['template_id'] : null;
            }
        }

        // Get template info
        $templateInfo = null;
        if ($template_id) {
            $templateInfo = $this->db->table('idp_templates')->where('id', $template_id)->get()->getRowArray();
        }

        // Get group plans filtered by template
        $plans = $this->academyModel->getGroupIDPPlans($group_id, $academy_id, $template_id, $group_plan_id);

        $dimensions = $this->academyModel->getIDPDimensions($template_id);

        // Get indicators grouped by dimension
        $indicatorsByDimension = $this->academyModel->getAllIDPIndicatorsGroupedByDimension($template_id);

        // Get assessment history log
        $assessmentHistory = $this->academyModel->getGroupAssessmentHistory($group_id, $academy_id, $template_id, $group_plan_id);

        // Get group info (training group for V1, group plan for V2)
        $group = null;
        if ($isV2) {
            // Use group plan data as group info
            $group = [
                'id' => $groupPlan['id'],
                'name' => $groupPlan['name'],
                'academy_id' => $groupPlan['academy_id'],
            ];
        } else {
            $group = $this->db->table('academy_training_groups')
                ->where('id', $group_id)
                ->get()->getRowArray();
        }

        // Get coaches: for V2 use coach from group plan, for V1 use training group coaches
        $coaches = [];
        if (!$isV2) {
            $coaches = $this->academyModel->getGroupCoaches($group_id);
        }

        $data['groupPlan']             = $groupPlan;
        $data['isV2']                  = $isV2;
        $data['group_plan_id']         = $group_plan_id;
        $data['group']                 = $group;
        $data['group_id']              = $group_id;
        $data['template_id']           = $template_id;
        $data['templateInfo']          = $templateInfo;
        $data['plans']                 = $plans;
        $data['dimensions']            = $dimensions;
        $data['indicatorsByDimension'] = $indicatorsByDimension;
        $data['assessmentHistory']     = $assessmentHistory;
        $data['coaches']               = $coaches;
        $data['title']                 = lang('Common.idp_batch_assess_title');

        return view('Modules\Academy\Views\idp_group_assess', $data);
    }

    /**
     * บันทึกคะแนนประเมินกลุ่ม (AJAX JSON)
     */
    /**
     * API: ดึงคะแนนประเมินรอบเก่า (AJAX GET)
     * URL: /academy/idp/group/assess/history?group_id=X&academy_id=Y&template_id=Z&date=YYYY-MM-DD&type=adhoc
     */
    public function idpGroupAssessHistory()
    {
        $group_id = $this->request->getGet('group_id');
        $academy_id = $this->request->getGet('academy_id');
        $template_id = $this->request->getGet('template_id');
        $date = $this->request->getGet('date');
        $type = $this->request->getGet('type');
        $dimension_id = $this->request->getGet('dimension_id');

        if (!$academy_id || !$this->canViewAcademy($academy_id)) {
            return $this->response->setJSON(['status' => 'error', 'message' => lang('Common.academy_no_permission')]);
        }

        if (!$group_id || !$date || !$type) {
            return $this->response->setJSON(['status' => 'error', 'message' => lang('Common.fill_required')]);
        }

        // Detect V2: check if group_id is actually an idp_group_plan_id
        $groupPlan = $this->academyModel->getIDPGroupPlan($group_id);
        $group_plan_id = !empty($groupPlan) ? (int)$group_id : null;

        $scores = $this->academyModel->getGroupIndicatorAssessmentsBySession(
            $group_id, $academy_id, $date, $type, $template_id, $dimension_id, $group_plan_id
        );

        return $this->response->setJSON([
            'status' => 'success',
            'scores' => $scores
        ]);
    }

    /**
     * API: ดึงนักเตะในกลุ่ม พร้อมสถานะ IDP (AJAX JSON)
     */
    public function getGroupPlayersJSON($group_id)
    {
        // Special case: group_id=0 means "all academy players"
        if ((int)$group_id === 0) {
            $academy_id = $this->request->getGet('academy_id');
            if (!$academy_id || !$this->canViewAcademy($academy_id)) {
                return $this->response->setJSON(['status' => 'error', 'message' => lang('Common.academy_no_permission')]);
            }
            $allPlayers = $this->academyModel->getAcademyPlayersWithIDPStatus($academy_id);
            // Normalize field names to match group player format
            $players = [];
            foreach ($allPlayers as $p) {
                $players[] = [
                    'id' => $p['id'],
                    'first_name' => isset($p['first_name']) ? $p['first_name'] : '',
                    'last_name' => isset($p['last_name']) ? $p['last_name'] : '',
                    'nick_name' => isset($p['nick_name']) ? $p['nick_name'] : '',
                    'image_path' => isset($p['image_path']) ? $p['image_path'] : '',
                    'shirt_number' => isset($p['shirt_number']) ? $p['shirt_number'] : '',
                    'position_name' => isset($p['position_name']) ? $p['position_name'] : '',
                    'position_display_html' => isset($p['position_display_html']) ? $p['position_display_html'] : '',
                    'position_text_class' => isset($p['position_text_class']) ? $p['position_text_class'] : '',
                    'active_plan_id' => isset($p['active_plan_id']) ? $p['active_plan_id'] : null,
                ];
            }
            $coaches = $this->academyModel->getAvailableCoaches($academy_id);
            return $this->response->setJSON([
                'status' => 'success',
                'players' => $players,
                'coaches' => $coaches,
            ]);
        }

        // Find academy_id from group
        $group = $this->db->table('academy_training_groups')
            ->where('id', $group_id)
            ->get()->getRowArray();

        if (!$group || !$this->canViewAcademy($group['academy_id'])) {
            return $this->response->setJSON(['status' => 'error', 'message' => lang('Common.academy_no_permission')]);
        }

        $players = $this->academyModel->getGroupPlayersWithPlanStatus($group_id, $group['academy_id']);
        $coaches = $this->academyModel->getGroupCoaches($group_id);

        return $this->response->setJSON([
            'status' => 'success',
            'players' => $players,
            'coaches' => $coaches
        ]);
    }

    // =====================================================
    // IDP Library — Template Management
    // =====================================================

    /**
     * IDP Library: overview page showing all available templates
     */
    public function idpLibrary($academy_id)
    {
        $access = $this->ensureAcademyAccess($academy_id);
        if ($access['redirect']) return $access['redirect'];
        $academy_id = $access['academy_id'];
        $data = $access['data'];

        $templates = $this->academyModel->getIDPTemplateLibrary($academy_id);

        $data['templates'] = $templates;
        $data['title']     = lang('Common.idp_library_title');

        return view('Modules\Academy\Views\idp_library', $data);
    }

    /**
     * Create/edit academy template form
     */
    public function idpTemplateManage($academy_id, $template_id = null)
    {
        $access = $this->ensurePermAccess($academy_id, 'perm_manage_idp_template');
        if ($access['redirect']) return $access['redirect'];
        $academy_id = $access['academy_id'];
        $data = $access['data'];

        $template = null;
        if ($template_id) {
            $template = $this->academyModel->getIDPTemplate($template_id);
            if (!$template || $template['owner_type'] !== 'academy' || (int)$template['owner_id'] !== (int)$academy_id) {
                return redirect()->to('/academy/idp/library/' . $academy_id)->with('error', lang('Common.idp_template_cannot_edit_global'));
            }
        }

        $data['template'] = $template;
        $data['title']    = $template ? lang('Common.idp_template_edit') : lang('Common.idp_template_create');

        return view('Modules\Academy\Views\idp_template_manage', $data);
    }

    /**
     * Save academy template (form POST)
     */
    public function idpTemplateSave()
    {
        $academy_id = $this->request->getPost('academy_id');
        if (!$this->hasPermission($academy_id, 'perm_manage_idp_template')) {
            return redirect()->to('/academy/info')->with('error', lang('Common.academy_no_permission'));
        }

        $data = [
            'id'          => $this->request->getPost('id'),
            'title'       => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'owner_type'  => 'academy',
            'owner_id'    => $academy_id,
            'is_active'   => $this->request->getPost('is_active') ? 1 : 0,
            'is_public'   => 0,
            'created_by'  => session()->get('user_id') ?: session()->get('coach_user_id'),
        ];

        $template_id = $this->academyModel->saveIDPTemplate($data);

        return redirect()->to('/academy/idp/template/detail/' . $academy_id . '/' . $template_id)
            ->with('success', lang('Common.idp_template_save_success'));
    }

    /**
     * Delete academy template (AJAX JSON)
     */
    public function idpTemplateDelete()
    {
        $academy_id = $this->getInput('academy_id');
        $template_id = $this->getInput('template_id');

        $err = $this->ensureAjaxPermAccess($academy_id, 'perm_manage_idp_template');
        if ($err) return $err;

        $result = $this->academyModel->deleteIDPTemplate($template_id, $academy_id);

        if ($result) {
            return $this->response->setJSON(['status' => 'success', 'message' => lang('Common.idp_template_delete_success')]);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => lang('Common.idp_template_cannot_delete_global')]);
    }

    /**
     * Clone global template (AJAX JSON)
     */
    public function idpTemplateClone()
    {
        $academy_id = $this->getInput('academy_id');
        $source_template_id = $this->getInput('template_id');
        $new_title = $this->getInput('title');

        $err = $this->ensureAjaxPermAccess($academy_id, 'perm_manage_idp_template');
        if ($err) return $err;

        if (empty($new_title)) {
            return $this->response->setJSON(['status' => 'error', 'message' => lang('Common.fill_required')]);
        }

        $created_by = session()->get('user_id') ?: session()->get('coach_user_id');
        $newId = $this->academyModel->cloneIDPTemplate($source_template_id, $academy_id, $new_title, $created_by);

        if ($newId) {
            return $this->response->setJSON([
                'status'      => 'success',
                'message'     => lang('Common.idp_template_clone_success'),
                'template_id' => $newId,
            ]);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => lang('Common.save_failed')]);
    }

    /**
     * Template detail with dimensions list
     */
    public function idpTemplateDetail($academy_id, $template_id)
    {
        $access = $this->ensureAcademyAccess($academy_id);
        if ($access['redirect']) return $access['redirect'];
        $academy_id = $access['academy_id'];
        $data = $access['data'];

        $template = $this->academyModel->getIDPTemplate($template_id);
        if (!$template) {
            return redirect()->to('/academy/idp/library/' . $academy_id)->with('error', lang('Common.not_found'));
        }

        $isOwner = ($template['owner_type'] === 'academy' && (int)$template['owner_id'] === (int)$academy_id);

        $dimensions = $this->academyModel->getIDPTemplateDimensions($template_id);
        // Add indicators + count to each dimension
        foreach ($dimensions as &$dim) {
            $dim['indicators'] = $this->academyModel->getIDPDimensionIndicators($dim['id']);
            $dim['indicator_count'] = count($dim['indicators']);
        }
        unset($dim);

        $data['template']   = $template;
        $data['dimensions'] = $dimensions;
        $data['isOwner']    = $isOwner;
        $data['title']      = lang('Common.idp_template_detail');

        return view('Modules\Academy\Views\idp_template_detail', $data);
    }

    /**
     * Create/edit dimension within template
     */
    public function idpDimensionManage($academy_id, $template_id, $dimension_id = null)
    {
        $access = $this->ensurePermAccess($academy_id, 'perm_manage_idp_template');
        if ($access['redirect']) return $access['redirect'];
        $academy_id = $access['academy_id'];
        $data = $access['data'];

        $template = $this->academyModel->getIDPTemplate($template_id);
        if (!$template || $template['owner_type'] !== 'academy' || (int)$template['owner_id'] !== (int)$academy_id) {
            return redirect()->to('/academy/idp/library/' . $academy_id)->with('error', lang('Common.idp_template_cannot_edit_global'));
        }

        $dimension = null;
        if ($dimension_id) {
            $dimension = $this->academyModel->getIDPTemplateDimensionById($dimension_id);
            if (!$dimension || (int)$dimension['template_id'] !== (int)$template_id) {
                return redirect()->to('/academy/idp/template/detail/' . $academy_id . '/' . $template_id)->with('error', lang('Common.not_found'));
            }
        }

        $data['template']  = $template;
        $data['dimension'] = $dimension;
        $data['title']     = $dimension ? lang('Common.idp_dimension_edit') : lang('Common.idp_dimension_create');

        return view('Modules\Academy\Views\idp_dimension_manage', $data);
    }

    /**
     * Save dimension (form POST)
     */
    public function idpDimensionSave()
    {
        $academy_id = $this->request->getPost('academy_id');
        $template_id = $this->request->getPost('template_id');

        if (!$this->hasPermission($academy_id, 'perm_manage_idp_template')) {
            return redirect()->to('/academy/info')->with('error', lang('Common.academy_no_permission'));
        }

        $data = [
            'id'             => $this->request->getPost('id'),
            'template_id'    => $template_id,
            'name_th'        => $this->request->getPost('name_th'),
            'name_en'        => $this->request->getPost('name_en'),
            'description_th' => $this->request->getPost('description_th'),
            'description_en' => $this->request->getPost('description_en'),
            'icon'           => $this->request->getPost('icon'),
            'color'          => $this->request->getPost('color') ?: '#0d6efd',
            'sort_order'     => (int)$this->request->getPost('sort_order'),
            'is_active'      => $this->request->getPost('is_active') ? 1 : 0,
            'owner_type'     => 'academy',
            'owner_id'       => $academy_id,
            'is_public'      => 0,
        ];

        $this->academyModel->saveIDPTemplateDimension($data);

        return redirect()->to('/academy/idp/template/detail/' . $academy_id . '/' . $template_id)
            ->with('success', lang('Common.idp_dimension_save_success'));
    }

    /**
     * Delete dimension (AJAX JSON)
     */
    public function idpDimensionDelete()
    {
        $academy_id = $this->getInput('academy_id');
        $dimension_id = $this->getInput('dimension_id');

        $err = $this->ensureAjaxPermAccess($academy_id, 'perm_manage_idp_template');
        if ($err) return $err;

        $result = $this->academyModel->deleteIDPTemplateDimension($dimension_id, $academy_id);

        if ($result) {
            return $this->response->setJSON(['status' => 'success', 'message' => lang('Common.idp_dimension_delete_success')]);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => lang('Common.save_failed')]);
    }

    /**
     * Create/edit indicator within dimension
     */
    public function idpIndicatorManage($academy_id, $dimension_id, $indicator_id = null)
    {
        $access = $this->ensurePermAccess($academy_id, 'perm_manage_idp_template');
        if ($access['redirect']) return $access['redirect'];
        $academy_id = $access['academy_id'];
        $data = $access['data'];

        $dimension = $this->academyModel->getIDPTemplateDimensionById($dimension_id);
        if (!$dimension || $dimension['owner_type'] !== 'academy' || (int)$dimension['owner_id'] !== (int)$academy_id) {
            return redirect()->to('/academy/idp/library/' . $academy_id)->with('error', lang('Common.idp_template_cannot_edit_global'));
        }

        $template = $this->academyModel->getIDPTemplate($dimension['template_id']);

        $indicator = null;
        if ($indicator_id) {
            $indicator = $this->academyModel->getIDPTemplateIndicatorById($indicator_id);
            if (!$indicator || (int)$indicator['dimension_id'] !== (int)$dimension_id) {
                return redirect()->to('/academy/idp/template/detail/' . $academy_id . '/' . $dimension['template_id'])->with('error', lang('Common.not_found'));
            }
        }

        $data['template']  = $template;
        $data['dimension'] = $dimension;
        $data['indicator'] = $indicator;
        $data['title']     = $indicator ? lang('Common.idp_indicator_edit') : lang('Common.idp_indicator_create');

        return view('Modules\Academy\Views\idp_indicator_manage', $data);
    }

    /**
     * Save indicator (form POST)
     */
    public function idpIndicatorSave()
    {
        $academy_id = $this->request->getPost('academy_id');
        $dimension_id = $this->request->getPost('dimension_id');

        if (!$this->hasPermission($academy_id, 'perm_manage_idp_template')) {
            return redirect()->to('/academy/info')->with('error', lang('Common.academy_no_permission'));
        }

        // Get dimension to find template_id for redirect
        $dimension = $this->academyModel->getIDPTemplateDimensionById($dimension_id);
        $template_id = $dimension ? $dimension['template_id'] : '';

        $data = [
            'id'               => $this->request->getPost('id'),
            'dimension_id'     => $dimension_id,
            'name_th'          => $this->request->getPost('name_th'),
            'name_en'          => $this->request->getPost('name_en'),
            'description_th'   => $this->request->getPost('description_th'),
            'description_en'   => $this->request->getPost('description_en'),
            'measurement_type' => $this->request->getPost('measurement_type') ?: 'score',
            'unit'             => $this->request->getPost('unit'),
            'min_value'        => $this->request->getPost('min_value') !== '' ? $this->request->getPost('min_value') : 0,
            'max_value'        => $this->request->getPost('max_value') !== '' ? $this->request->getPost('max_value') : 100,
            'is_lower_better'  => $this->request->getPost('is_lower_better') ? 1 : 0,
            'sort_order'       => (int)$this->request->getPost('sort_order'),
            'is_active'        => $this->request->getPost('is_active') ? 1 : 0,
            'owner_type'       => 'academy',
            'owner_id'         => $academy_id,
        ];

        $this->academyModel->saveIDPTemplateIndicator($data);

        return redirect()->to('/academy/idp/template/detail/' . $academy_id . '/' . $template_id)
            ->with('success', lang('Common.idp_indicator_save_success'));
    }

    /**
     * Delete indicator (AJAX JSON)
     */
    public function idpIndicatorDelete()
    {
        $academy_id = $this->getInput('academy_id');
        $indicator_id = $this->getInput('indicator_id');

        $err = $this->ensureAjaxPermAccess($academy_id, 'perm_manage_idp_template');
        if ($err) return $err;

        $result = $this->academyModel->deleteIDPTemplateIndicator($indicator_id, $academy_id);

        if ($result) {
            return $this->response->setJSON(['status' => 'success', 'message' => lang('Common.idp_indicator_delete_success')]);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => lang('Common.save_failed')]);
    }

    // =====================================================
    // IDP Indicator-Level Assessment (Phase 5)
    // =====================================================

    /**
     * หน้าประเมินระดับตัวชี้วัดรายกลุ่ม (players x indicators per dimension)
     */
    public function idpGroupAssessIndicators($academy_id, $group_id)
    {
        $access = $this->ensureAcademyAccess($academy_id);
        if ($access['redirect']) return $access['redirect'];
        $academy_id = $access['academy_id'];
        $data = $access['data'];

        // โค้ชมีสิทธิ์บันทึกผลการประเมิน
        $data['canManage'] = $this->hasPermission($academy_id, 'perm_manage_idp');

        // Detect V2: check if $group_id is actually an idp_group_plan_id
        $groupPlan = $this->academyModel->getIDPGroupPlan($group_id);
        $isV2 = !empty($groupPlan);
        $group_plan_id = $isV2 ? (int)$group_id : null;

        // Get group info
        $group = null;
        if ($isV2) {
            $group = [
                'id' => $groupPlan['id'],
                'name' => $groupPlan['name'],
                'academy_id' => $groupPlan['academy_id'],
            ];
        } else {
            $group = $this->db->table('academy_training_groups')
                ->where('id', $group_id)
                ->get()->getRowArray();
        }

        // Get plans for this group
        $plans = $this->academyModel->getGroupIDPPlans($group_id, $academy_id, null, $group_plan_id);

        // Resolve template_id
        $template_id = null;
        if ($isV2 && !empty($groupPlan['template_id'])) {
            $template_id = $groupPlan['template_id'];
        }
        if ($template_id === null && !empty($plans)) {
            $template_id = isset($plans[0]['template_id']) ? $plans[0]['template_id'] : null;
        }
        if ($template_id === null) {
            $templates = $this->academyModel->getAvailableIDPTemplates($academy_id);
            if (!empty($templates)) {
                $template_id = $templates[0]['id'];
            }
        }

        // Get dimensions and their indicators
        $dimensions = $this->academyModel->getIDPDimensions($template_id);
        $indicatorsByDimension = $this->academyModel->getAllIDPIndicatorsGroupedByDimension($template_id);

        // Get existing indicator scores
        $existingScores = $this->academyModel->getGroupIndicatorAssessments($group_id, $academy_id, $template_id, $group_plan_id);

        // Get coaches
        $coaches = [];
        if (!$isV2) {
            $coaches = $this->academyModel->getGroupCoaches($group_id);
        }

        $data['groupPlan'] = $groupPlan;
        $data['isV2'] = $isV2;
        $data['group_plan_id'] = $group_plan_id;
        $data['group'] = $group;
        $data['plans'] = $plans;
        $data['dimensions'] = $dimensions;
        $data['indicatorsByDimension'] = $indicatorsByDimension;
        $data['existingScores'] = $existingScores;
        $data['coaches'] = $coaches;
        $data['title'] = lang('Common.idp_indicator_assessment_title');
        return view('Modules\Academy\Views\idp_group_assess_indicators', $data);
    }

    /**
     * บันทึกคะแนนระดับตัวชี้วัดรายกลุ่ม (AJAX JSON)
     */
    public function idpGroupAssessIndicatorsSave()
    {
        $input = $this->getInput();
        if (empty($input) || empty($input['results'])) {
            return $this->response->setJSON(['status' => 'error', 'message' => lang('Common.fill_required')]);
        }

        // Permission check via first plan
        $firstPlanId = $input['results'][0]['plan_id'] ?? 0;
        $plan = $this->academyModel->getIDPPlan($firstPlanId);
        if (!$plan || !$this->hasPermission($plan['academy_id'], 'perm_manage_idp')) {
            return $this->response->setJSON(['status' => 'error', 'message' => lang('Common.academy_no_permission')]);
        }

        $assessed_by = session()->get('user_id') ?: session()->get('coach_user_id');

        // รวบรวม plan_id ทั้งหมดเพื่อลบข้อมูลเดิมก่อน INSERT (overwrite mode)
        $planIds = [];
        foreach ($input['results'] as $item) {
            if (!empty($item['plan_id'])) {
                $planIds[] = (int)$item['plan_id'];
            }
        }
        $planIds = array_values(array_unique($planIds));

        $dimensionId = isset($input['dimension_id']) ? (int)$input['dimension_id'] : null;

        $result = $this->academyModel->bulkSaveIndicatorAssessments(
            $input['results'],
            isset($input['assessment_type']) ? $input['assessment_type'] : 'adhoc',
            isset($input['assessment_date']) ? $input['assessment_date'] : date('Y-m-d'),
            $assessed_by,
            $planIds,
            $dimensionId
        );

        if ($result['success']) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => lang('Common.idp_scores_saved'),
                'count' => $result['count']
            ]);
        }
        return $this->response->setJSON(['status' => 'error', 'message' => lang('Common.error')]);
    }
}
