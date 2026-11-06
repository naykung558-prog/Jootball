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

trait AcademyReportTrait
{
    // =====================================================
    // Reports
    // =====================================================

    /**
     * หน้ารายงานหลัก
     */
    public function reports($academy_id)
    {
        $access = $this->ensureAcademyAccess($academy_id);
        if ($access['redirect']) return $access['redirect'];
        $academy_id = $access['academy_id'];
        $data = $access['data'];

        $month = $this->request->getGet('month') ?: date('m');
        $year = $this->request->getGet('year') ?: date('Y');

        $data['groups'] = $this->academyModel->getTrainingGroups($academy_id);
        $data['monthlySummary'] = $this->academyModel->getMonthlySummary($academy_id, $month, $year);
        $data['assessmentSummary'] = $this->academyModel->getAssessmentSummary($academy_id);
        $data['month'] = $month;
        $data['year'] = $year;
        $data['title'] = lang('Common.reports');

        return view('Modules\Academy\Views\reports', $data);
    }

    /**
     * รายงานรายวัน
     */
    public function reportDaily($academy_id)
    {
        $access = $this->ensureAcademyAccess($academy_id);
        if ($access['redirect']) return $access['redirect'];
        $academy_id = $access['academy_id'];
        $data = $access['data'];

        $date = $this->request->getGet('date') ?: date('Y-m-d');
        $data['date'] = $date;
        $data['sessions'] = $this->academyModel->getDailySummary($academy_id, $date);
        $data['title'] = lang('Common.report_daily');

        return view('Modules\Academy\Views\report_daily', $data);
    }

    /**
     * รายงานรายเดือน
     */
    public function reportMonthly($academy_id)
    {
        $access = $this->ensureAcademyAccess($academy_id);
        if ($access['redirect']) return $access['redirect'];
        $academy_id = $access['academy_id'];
        $data = $access['data'];

        $month = $this->request->getGet('month') ?: date('m');
        $year = $this->request->getGet('year') ?: date('Y');
        $groupId = (int)($this->request->getGet('group_id') ?? 0);

        $data['month'] = $month;
        $data['year'] = $year;
        $data['selectedGroupId'] = $groupId;
        $data['summary'] = $this->academyModel->getMonthlySummary($academy_id, $month, $year, $groupId ?: null);

        // รายชื่อกลุ่มทั้งหมด — ใช้เติม dropdown filter (เสมอ)
        $allGroups = $this->academyModel->getTrainingGroups($academy_id);
        $data['allGroups'] = $allGroups;

        // ตาราง/กราฟรายกลุ่ม: ถ้าเลือกกลุ่ม → แสดงเฉพาะกลุ่มนั้น, ไม่เลือก → ทุกกลุ่ม
        $groups = $groupId
            ? array_values(array_filter($allGroups, fn($g) => (int)$g['id'] === $groupId))
            : $allGroups;
        foreach ($groups as &$group) {
            $group['stats'] = $this->academyModel->getGroupTrainingStats($group['id'], [
                'month' => $month, 'year' => $year
            ]);
        }
        unset($group);
        $data['groups'] = $groups;

        $data['title'] = lang('Common.report_monthly');

        return view('Modules\Academy\Views\report_monthly', $data);
    }

    /**
     * รายงานรายนักเตะ
     */
    public function reportPlayer($academy_id, $player_id)
    {
        $access = $this->ensureAcademyAccess($academy_id);
        if ($access['redirect']) return $access['redirect'];
        $academy_id = $access['academy_id'];
        $data = $access['data'];

        $player = $this->playerModel->get_Player($player_id);
        if (!$player) {
            return redirect()->to('/academy/reports/' . $academy_id)->with('error', lang('Common.academy_not_found'));
        }

        // Year filter for attendance
        $selectedYear = (int)($this->request->getGet('year') ?? 0);

        // Available years from training sessions
        $yearRows = $this->db->query("
            SELECT DISTINCT YEAR(s.training_date) as yr
            FROM academy_training_attendance a
            JOIN academy_training_sessions s ON s.id = a.session_id
            WHERE a.player_id = ? AND s.academy_id = ? AND s.status = 'completed'
            ORDER BY yr DESC
        ", [$player_id, $academy_id])->getResultArray();
        $availableYears = array_column($yearRows, 'yr');

        $attendanceFilters = $selectedYear > 0 ? ['year' => $selectedYear] : [];
        $attendance = $this->academyModel->getPlayerAttendanceStats($player_id, $academy_id, $attendanceFilters);
        $assessmentHistory = $this->academyModel->getPlayerAssessmentHistory($player_id, $academy_id);

        // Group assessments by assessment name for summary
        $assessmentGroups = [];
        foreach ($assessmentHistory as $a) {
            $key = $a['assessment_name'] ?? 'ไม่ระบุ';
            if (!isset($assessmentGroups[$key])) {
                $assessmentGroups[$key] = ['name' => $key, 'count' => 0, 'avg' => 0, 'total' => 0, 'items' => []];
            }
            $assessmentGroups[$key]['count']++;
            if (!empty($a['score']) && !empty($a['max_score'])) {
                $assessmentGroups[$key]['total'] += ($a['score'] / $a['max_score']) * 100;
            }
            $assessmentGroups[$key]['items'][] = $a;
        }
        foreach ($assessmentGroups as &$g) {
            $g['avg'] = $g['count'] > 0 ? round($g['total'] / $g['count'], 1) : 0;
        }
        unset($g);

        $data['player']           = $player;
        $data['attendance']       = $attendance;
        $data['available_years']  = $availableYears;
        $data['selected_year']    = $selectedYear;
        $data['assessments']      = $assessmentHistory;
        $data['assessment_groups']= array_values($assessmentGroups);
        $data['stats']            = ['attendance' => $attendance, 'assessments' => $assessmentHistory, 'avg_assessment_score' => 0, 'assessment_count' => count($assessmentHistory)];
        $data['title']            = lang('Common.report_player');

        // Enrollment data
        $data['enrollment']        = $this->academyModel->getPlayerEnrollment((int)$player_id, (int)$academy_id);
        $data['all_academies']     = $this->academyModel->getPlayerAcademies((int)$player_id);
        $data['enrollment_history']= $this->academyModel->getPlayerEnrollmentHistory((int)$player_id);
        $data['physical_history']  = $this->academyModel->getPlayerPhysicalHistory((int)$player_id, 10);

        // IDP data - initialize defaults to prevent undefined variable warnings
        $data['idp_compare'] = [];
        $data['idp_goals'] = [];
        $data['idp_templates'] = [];
        $data['selected_template_id'] = null;
        $data['idp_peer_max'] = [];

        $idpSummary = $this->academyModel->getPlayerIDPSummary($player_id, $academy_id);
        $data['idp_summary'] = $idpSummary;
        $data['idp_plans'] = $this->academyModel->getPlayerIDPPlans($player_id, $academy_id);

        // Get distinct templates for this player
        $templates = $this->academyModel->getPlayerDistinctTemplates($player_id, $academy_id);
        $data['idp_templates'] = $templates;

        // Determine selected template: from query param or default to active plan's template
        $selectedTemplateId = $this->request->getGet('template_id');
        if (empty($selectedTemplateId) && !empty($idpSummary['active_plan']['template_id'])) {
            $selectedTemplateId = $idpSummary['active_plan']['template_id'];
        }
        if (empty($selectedTemplateId) && !empty($templates)) {
            $selectedTemplateId = $templates[0]['template_id'];
        }
        $data['selected_template_id'] = $selectedTemplateId;

        // Get dimension scores and goals for the selected template
        if (!empty($selectedTemplateId)) {
            $data['idp_compare'] = $this->academyModel->getPlayerDimensionScoresByTemplate(
                $player_id, $academy_id, $selectedTemplateId
            );

            $data['idp_peer_max'] = $this->academyModel->getPeerMaxScoresForPlayer(
                $player_id, $academy_id, $selectedTemplateId
            );

            // Get goals from plans matching the selected template
            foreach ($data['idp_plans'] as $plan) {
                if (isset($plan['template_id']) && $plan['template_id'] == $selectedTemplateId && $plan['status'] === 'active') {
                    $data['idp_goals'] = $this->academyModel->getGoalsByDimension($plan['id']);
                    break;
                }
            }
        }

        // AI Analysis: ตรวจสอบว่าเดือนนี้วิเคราะห์แล้วหรือยัง
        $aiThisMonth = $this->academyModel->getAIAnalysisThisMonth($player_id, $academy_id);
        $data['ai_analyzed_this_month'] = !empty($aiThisMonth);
        $data['ai_last_analysis_date'] = $aiThisMonth ? $aiThisMonth['created_at'] : null;

        // Permissions
        $currentUserId = session()->get('user_id');
        $data['can_edit_all']      = $this->academyModel->canEditAllPlayerFields((int)$player_id, $currentUserId);
        $data['can_edit_physical'] = $this->academyModel->canEditPlayerPhysical((int)$player_id, $currentUserId);

        return view('Modules\Academy\Views\report_player', $data);
    }

    /**
     * AJAX endpoint: IDP radar chart data + goals for a given template
     */
    public function getIDPRadarData($academy_id, $player_id)
    {
        $access = $this->ensureAcademyAccess($academy_id);
        if ($access['redirect']) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Access denied']);
        }
        $academy_id = $access['academy_id'];

        $templateId = $this->request->getGet('template_id');
        if (empty($templateId)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Missing template_id']);
        }

        $compare = $this->academyModel->getPlayerDimensionScoresByTemplate($player_id, $academy_id, $templateId);
        $peerMax = $this->academyModel->getPeerMaxScoresForPlayer($player_id, $academy_id, $templateId);

        // Goals for this template's active plan
        $goals = [];
        $plans = $this->academyModel->getPlayerIDPPlans($player_id, $academy_id);
        foreach ($plans as $plan) {
            if (isset($plan['template_id']) && $plan['template_id'] == $templateId && $plan['status'] === 'active') {
                $goals = $this->academyModel->getGoalsByDimension($plan['id']);
                break;
            }
        }

        // Template name
        $templates = $this->academyModel->getPlayerDistinctTemplates($player_id, $academy_id);
        $templateName = '';
        foreach ($templates as $tpl) {
            if ($tpl['template_id'] == $templateId) { $templateName = $tpl['title']; break; }
        }

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => [
                'compare'       => $compare,
                'peer_max'      => $peerMax,
                'goals'         => $goals,
                'template_name' => $templateName,
            ]
        ]);
    }

    /**
     * รายงานรายกลุ่ม
     */
    public function reportGroup($academy_id, $group_id)
    {
        $access = $this->ensureAcademyAccess($academy_id);
        if ($access['redirect']) return $access['redirect'];
        $academy_id = $access['academy_id'];
        $data = $access['data'];

        $group = $this->academyModel->getTrainingGroup($group_id);
        if (!$group) {
            return redirect()->to('/academy/reports/' . $academy_id)->with('error', lang('Common.academy_not_found'));
        }

        $month = $this->request->getGet('month') ?: date('m');
        $year = $this->request->getGet('year') ?: date('Y');

        $data['group'] = $group;
        $data['coaches'] = $this->academyModel->getGroupCoaches($group_id);
        $data['players'] = $this->academyModel->getGroupPlayers($group_id);
        $data['stats'] = $this->academyModel->getGroupTrainingStats($group_id, [
            'month' => $month, 'year' => $year
        ]);
        $data['sessions'] = $this->academyModel->getTrainingSessions($group_id, [
            'month' => $month, 'year' => $year
        ]);
        $sessionIds = array_column($data['sessions'] ?? [], 'id');
        $data['attachmentCounts'] = $this->academyModel->getAttachmentsForSessions($sessionIds);
        $data['month'] = $month;
        $data['year'] = $year;
        $data['title'] = lang('Common.report_group');

        return view('Modules\Academy\Views\report_group', $data);
    }
}
