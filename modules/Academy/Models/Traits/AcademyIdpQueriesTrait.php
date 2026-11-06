<?php

namespace Modules\Academy\Models\Traits;

use CodeIgniter\Model;
use \Hermawan\DataTables\DataTable;
use Modules\Academy\Models\Traits\AcademyMemberQueriesTrait;

trait AcademyIdpQueriesTrait
{
    // =====================================================
    // IDP (Individual Development Plan)
    // =====================================================

    // --- Dimensions & Indicators ---

    /**
     * ดึงมิติการพัฒนาทั้งหมด (ตาม locale)
     */
    function getIDPDimensions($template_id = null)
    {
        $lang = function_exists('get_current_language') ? get_current_language() : 'th';
        $builder = $this->db->table('idp_dimensions');
        $builder->select("id, template_id, name_{$lang} as name, name_th, name_en, description_{$lang} as description, icon, color, sort_order", false);
        if ($template_id) {
            $builder->where('template_id', $template_id);
        }
        $builder->where('is_active', 1);
        $builder->orderBy('sort_order', 'ASC');
        return $builder->get()->getResultArray();
    }

    /**
     * ดึงตัวชี้วัดตามมิติ
     */
    function getIDPIndicators($dimension_id = null)
    {
        $lang = function_exists('get_current_language') ? get_current_language() : 'th';
        $builder = $this->db->table('idp_indicators')
            ->select("id, dimension_id, name_{$lang} as name, description_{$lang} as description, measurement_type, unit, min_value, max_value, is_lower_better, sort_order", false)
            ->where('is_active', 1);

        if ($dimension_id) {
            $builder->where('dimension_id', $dimension_id);
        }

        return $builder->orderBy('sort_order', 'ASC')
            ->get()->getResultArray();
    }

    /**
     * ดึงตัวชี้วัดทั้งหมดจัดกลุ่มตามมิติ
     */
    function getAllIDPIndicatorsGrouped()
    {
        $dimensions = $this->getIDPDimensions();
        $result = [];
        foreach ($dimensions as $dim) {
            $dim['indicators'] = $this->getIDPIndicators($dim['id']);
            $result[] = $dim;
        }
        return $result;
    }

    // --- IDP Plans ---

    /**
     * ดึงแผน IDP ทั้งหมดของนักเตะ
     */
    function getPlayerIDPPlans($player_id, $academy_id)
    {
        return $this->db->table('idp_plans p')
            ->select("p.*, CONCAT(c.first_name, ' ', c.last_name) as coach_name", false)
            ->join('coach c', 'c.id = p.coach_id', 'left')
            ->where('p.player_id', $player_id)
            ->where('p.academy_id', $academy_id)
            ->orderBy('p.created_at', 'DESC')
            ->get()->getResultArray();
    }

    /**
     * ดึงแผน IDP เดี่ยว พร้อม coach info + player info
     */
    function getIDPPlan($plan_id)
    {
        return $this->db->table('idp_plans p')
            ->select("p.*, CONCAT(c.first_name, ' ', c.last_name) as coach_name,
                      CONCAT(pl.first_name, ' ', pl.last_name) as player_name,
                      pl.nick_name as player_nickname, pl.image_path as player_image,
                      pl.shirt_number as player_number", false)
            ->join('coach c', 'c.id = p.coach_id', 'left')
            ->join('player pl', 'pl.id = p.player_id', 'left')
            ->where('p.id', $plan_id)
            ->get()->getRowArray();
    }

    /**
     * บันทึกแผน IDP (INSERT/UPDATE)
     */
    function saveIDPPlan($data)
    {
        $fields = ['academy_id', 'player_id', 'coach_id', 'group_id', 'template_id', 'title', 'season',
                    'start_date', 'end_date', 'overall_notes', 'status', 'created_by'];
        $save = [];
        foreach ($fields as $f) {
            if (isset($data[$f])) {
                $save[$f] = $data[$f];
            }
        }
        // coach_id empty → null
        if (isset($save['coach_id']) && $save['coach_id'] === '') {
            $save['coach_id'] = null;
        }
        // group_id empty → null
        if (isset($save['group_id']) && $save['group_id'] === '') {
            $save['group_id'] = null;
        }
        // template_id empty → null
        if (isset($save['template_id']) && $save['template_id'] === '') {
            $save['template_id'] = null;
        }

        if (!empty($data['id'])) {
            $this->db->table('idp_plans')
                ->where('id', $data['id'])
                ->update($save);
            return $data['id'];
        } else {
            $this->db->table('idp_plans')->insert($save);
            return $this->db->insertID();
        }
    }

    /**
     * ลบแผน IDP (CASCADE ลบ goals/progress/assessments/feedback)
     */
    function deleteIDPPlan($plan_id)
    {
        return $this->db->table('idp_plans')
            ->where('id', $plan_id)
            ->delete();
    }

    /**
     * ดึงแผน IDP ที่ active อยู่ของนักเตะ
     */
    function getActiveIDPPlan($player_id, $academy_id)
    {
        return $this->db->table('idp_plans p')
            ->select("p.*, CONCAT(c.first_name, ' ', c.last_name) as coach_name", false)
            ->join('coach c', 'c.id = p.coach_id', 'left')
            ->where('p.player_id', $player_id)
            ->where('p.academy_id', $academy_id)
            ->where('p.status', 'active')
            ->orderBy('p.created_at', 'DESC')
            ->get()->getRowArray();
    }

    /**
     * สรุปภาพรวมแผน IDP
     */
    function getIDPPlanSummary($plan_id)
    {
        $goals = $this->getIDPGoals($plan_id);
        $goalCount = count($goals);
        $achievedCount = 0;
        $totalPercent = 0;

        foreach ($goals as $g) {
            $totalPercent += (int)$g['achievement_percent'];
            if ($g['status'] === 'achieved') $achievedCount++;
        }

        $latestScores = $this->getLatestDimensionScores($plan_id);
        $feedbackCount = $this->db->table('idp_feedback')
            ->where('plan_id', $plan_id)
            ->countAllResults();

        return [
            'goal_count'      => $goalCount,
            'achieved_count'  => $achievedCount,
            'avg_achievement' => $goalCount > 0 ? round($totalPercent / $goalCount, 1) : 0,
            'dimension_scores'=> $latestScores,
            'feedback_count'  => $feedbackCount,
        ];
    }

    // --- IDP Goals ---

    /**
     * ดึงเป้าหมายทั้งหมดของแผน (JOIN dimension)
     */
    function getIDPGoals($plan_id)
    {
        $lang = function_exists('get_current_language') ? get_current_language() : 'th';
        return $this->db->table('idp_goals g')
            ->select("g.*, d.name_{$lang} as dimension_name, d.icon as dimension_icon, d.color as dimension_color,
                      i.name_{$lang} as indicator_name", false)
            ->join('idp_dimensions d', 'd.id = g.dimension_id', 'left')
            ->join('idp_indicators i', 'i.id = g.indicator_id', 'left')
            ->where('g.plan_id', $plan_id)
            ->orderBy('g.sort_order', 'ASC')
            ->orderBy('g.dimension_id', 'ASC')
            ->get()->getResultArray();
    }

    /**
     * ดึงเป้าหมายเดี่ยว + progress history
     */
    function getIDPGoal($goal_id)
    {
        $lang = function_exists('get_current_language') ? get_current_language() : 'th';
        $goal = $this->db->table('idp_goals g')
            ->select("g.*, d.name_{$lang} as dimension_name, d.icon as dimension_icon, d.color as dimension_color,
                      i.name_{$lang} as indicator_name", false)
            ->join('idp_dimensions d', 'd.id = g.dimension_id', 'left')
            ->join('idp_indicators i', 'i.id = g.indicator_id', 'left')
            ->where('g.id', $goal_id)
            ->get()->getRowArray();

        if ($goal) {
            $goal['progress_history'] = $this->getGoalProgressHistory($goal_id);
        }
        return $goal;
    }

    /**
     * บันทึกเป้าหมาย (INSERT/UPDATE)
     */
    function saveIDPGoal($data)
    {
        $fields = ['plan_id', 'dimension_id', 'indicator_id', 'title', 'description',
                    'target_value', 'baseline_value', 'current_value', 'target_date',
                    'goal_type', 'priority', 'training_recommendation', 'status',
                    'achievement_percent', 'sort_order'];
        $save = [];
        foreach ($fields as $f) {
            if (isset($data[$f])) {
                $save[$f] = $data[$f];
            }
        }
        // indicator_id empty → null
        if (isset($save['indicator_id']) && $save['indicator_id'] === '') {
            $save['indicator_id'] = null;
        }

        if (!empty($data['id'])) {
            $this->db->table('idp_goals')
                ->where('id', $data['id'])
                ->update($save);
            return $data['id'];
        } else {
            $this->db->table('idp_goals')->insert($save);
            return $this->db->insertID();
        }
    }

    /**
     * ลบเป้าหมาย
     */
    function deleteIDPGoal($goal_id)
    {
        return $this->db->table('idp_goals')
            ->where('id', $goal_id)
            ->delete();
    }

    /**
     * บันทึกความก้าวหน้า (INSERT progress + UPDATE goal.current_value)
     */
    function updateGoalProgress($goal_id, $data)
    {
        // Sanitize inputs — empty strings to proper types for INT columns
        $value = isset($data['value']) && $data['value'] !== '' && $data['value'] !== null
            ? $data['value'] : null;
        $percent = isset($data['achievement_percent']) && $data['achievement_percent'] !== '' && $data['achievement_percent'] !== null
            ? (int)$data['achievement_percent'] : null;
        $recordedDate = !empty($data['recorded_date']) ? $data['recorded_date'] : date('Y-m-d');
        $notes = isset($data['notes']) && $data['notes'] !== '' ? $data['notes'] : null;
        $recordedBy = isset($data['recorded_by']) ? $data['recorded_by'] : null;

        $this->db->transBegin();

        // Insert progress record
        $this->db->table('idp_progress')->insert([
            'goal_id'            => $goal_id,
            'recorded_date'      => $recordedDate,
            'value'              => $value,
            'achievement_percent'=> $percent !== null ? $percent : 0,
            'notes'              => $notes,
            'recorded_by'        => $recordedBy,
        ]);

        // Update goal current_value + achievement_percent
        $update = [];
        if ($value !== null) {
            $update['current_value'] = $value;
        }
        if ($percent !== null) {
            $update['achievement_percent'] = $percent;
            // Auto update status
            if ($percent >= 100) {
                $update['status'] = 'achieved';
            } elseif ($percent > 0) {
                $update['status'] = 'in_progress';
            }
        }
        if (!empty($update)) {
            $this->db->table('idp_goals')
                ->where('id', $goal_id)
                ->update($update);
        }

        if ($this->db->transStatus() === false) {
            $this->db->transRollback();
            return false;
        }
        $this->db->transCommit();
        return true;
    }

    /**
     * ดึง timeline ความก้าวหน้าของเป้าหมาย
     */
    function getGoalProgressHistory($goal_id)
    {
        return $this->db->table('idp_progress')
            ->where('goal_id', $goal_id)
            ->orderBy('recorded_date', 'ASC')
            ->get()->getResultArray();
    }

    /**
     * จัดกลุ่มเป้าหมายตามมิติ
     */
    function getGoalsByDimension($plan_id)
    {
        $goals = $this->getIDPGoals($plan_id);
        $grouped = [];
        foreach ($goals as $goal) {
            $dimId = $goal['dimension_id'];
            if (!isset($grouped[$dimId])) {
                $grouped[$dimId] = [
                    'dimension_id'    => $dimId,
                    'dimension_name'  => $goal['dimension_name'],
                    'dimension_icon'  => $goal['dimension_icon'],
                    'dimension_color' => $goal['dimension_color'],
                    'goals'           => []
                ];
            }
            $grouped[$dimId]['goals'][] = $goal;
        }
        return array_values($grouped);
    }

    // --- IDP Assessment (Dimension-based) ---

    /**
     * บันทึกคะแนนประเมินรายมิติ
     */
    function saveIDPAssessment($data)
    {
        $fields = ['plan_id', 'dimension_id', 'indicator_id', 'score', 'rating',
                    'assessment_date', 'assessment_type', 'notes', 'assessed_by'];
        $save = [];
        foreach ($fields as $f) {
            if (isset($data[$f])) {
                $save[$f] = $data[$f];
            }
        }
        if (isset($save['indicator_id']) && $save['indicator_id'] === '') {
            $save['indicator_id'] = null;
        }

        if (!empty($data['id'])) {
            $this->db->table('idp_assessments')
                ->where('id', $data['id'])
                ->update($save);
            return $data['id'];
        } else {
            $this->db->table('idp_assessments')->insert($save);
            return $this->db->insertID();
        }
    }

    /**
     * ดึงผลประเมินตาม type (baseline/midterm/final/adhoc)
     */
    function getIDPAssessments($plan_id, $type = null)
    {
        $lang = function_exists('get_current_language') ? get_current_language() : 'th';
        $builder = $this->db->table('idp_assessments a')
            ->select("a.*, d.name_{$lang} as dimension_name, d.icon as dimension_icon, d.color as dimension_color,
                      i.name_{$lang} as indicator_name", false)
            ->join('idp_dimensions d', 'd.id = a.dimension_id', 'left')
            ->join('idp_indicators i', 'i.id = a.indicator_id', 'left')
            ->where('a.plan_id', $plan_id);

        if ($type) {
            $builder->where('a.assessment_type', $type);
        }

        return $builder->orderBy('a.assessment_date', 'DESC')
            ->orderBy('d.sort_order', 'ASC')
            ->get()->getResultArray();
    }

    /**
     * ดึงคะแนนล่าสุดทุกมิติ (สำหรับ radar chart)
     */
    function getLatestDimensionScores($plan_id)
    {
        $lang = function_exists('get_current_language') ? get_current_language() : 'th';
        // Subquery: latest assessment per dimension
        $sql = "SELECT a.dimension_id, d.name_{$lang} as dimension_name, d.icon, d.color, d.sort_order,
                       COALESCE(a.score, a.rating) as score
                FROM idp_assessments a
                JOIN idp_dimensions d ON d.id = a.dimension_id
                WHERE a.plan_id = ?
                  AND a.indicator_id IS NULL
                  AND a.id = (
                    SELECT a2.id FROM idp_assessments a2
                    WHERE a2.plan_id = a.plan_id
                      AND a2.dimension_id = a.dimension_id
                      AND a2.indicator_id IS NULL
                    ORDER BY a2.assessment_date DESC, a2.id DESC
                    LIMIT 1
                  )
                ORDER BY d.sort_order ASC";
        return $this->db->query($sql, [$plan_id])->getResultArray();
    }

    /**
     * ดึงคะแนนตามเวลาของมิติเดียว (สำหรับ line chart)
     */
    /**
     * บันทึกคะแนนทุกมิติพร้อมกัน (bulk)
     */
    function bulkSaveIDPAssessments($plan_id, $scores, $assessment_type, $assessment_date, $assessed_by)
    {
        $this->db->transBegin();

        foreach ($scores as $score) {
            $data = [
                'plan_id'         => $plan_id,
                'dimension_id'    => $score['dimension_id'],
                'indicator_id'    => !empty($score['indicator_id']) ? $score['indicator_id'] : null,
                'score'           => $score['score'] ?? null,
                'rating'          => $score['rating'] ?? null,
                'assessment_date' => $assessment_date,
                'assessment_type' => $assessment_type,
                'notes'           => $score['notes'] ?? null,
                'assessed_by'     => $assessed_by,
            ];
            $this->db->table('idp_assessments')->insert($data);
        }

        if ($this->db->transStatus() === false) {
            $this->db->transRollback();
            return false;
        }
        $this->db->transCommit();
        return true;
    }

    // --- IDP Feedback ---

    /**
     * บันทึก feedback
     */
    function saveIDPFeedback($data)
    {
        $fields = ['plan_id', 'goal_id', 'feedback_date', 'feedback_type',
                    'content', 'strengths', 'areas_to_improve', 'action_items', 'created_by'];
        $save = [];
        foreach ($fields as $f) {
            if (isset($data[$f])) {
                $save[$f] = $data[$f];
            }
        }
        if (isset($save['goal_id']) && $save['goal_id'] === '') {
            $save['goal_id'] = null;
        }

        if (!empty($data['id'])) {
            $this->db->table('idp_feedback')
                ->where('id', $data['id'])
                ->update($save);
            return $data['id'];
        } else {
            $this->db->table('idp_feedback')->insert($save);
            return $this->db->insertID();
        }
    }

    /**
     * ดึง feedback ทั้งหมดของแผน
     */
    function getIDPFeedbacks($plan_id)
    {
        $lang = function_exists('get_current_language') ? get_current_language() : 'th';
        return $this->db->table('idp_feedback f')
            ->select("f.*, u.name as created_by_name,
                      g.title as goal_title", false)
            ->join('user u', 'u.id = f.created_by', 'left')
            ->join('idp_goals g', 'g.id = f.goal_id', 'left')
            ->where('f.plan_id', $plan_id)
            ->orderBy('f.feedback_date', 'DESC')
            ->get()->getResultArray();
    }

    /**
     * ลบ feedback
     */
    function deleteIDPFeedback($id)
    {
        return $this->db->table('idp_feedback')
            ->where('id', $id)
            ->delete();
    }

    /**
     * ดึง feedback ล่าสุดของแผน
     */
    function getLatestFeedback($plan_id)
    {
        return $this->db->table('idp_feedback f')
            ->select("f.*, u.name as created_by_name", false)
            ->join('user u', 'u.id = f.created_by', 'left')
            ->where('f.plan_id', $plan_id)
            ->orderBy('f.feedback_date', 'DESC')
            ->get()->getRowArray();
    }

    // --- IDP Reports ---

    /**
     * ภาพรวม IDP ทั้ง Academy — จำนวนแผน, active, completed + players with IDP
     */
    function getIDPOverview($academy_id)
    {
        $totalPlans = $this->db->table('idp_plans')
            ->where('academy_id', $academy_id)
            ->countAllResults();

        $activePlans = $this->db->table('idp_plans')
            ->where('academy_id', $academy_id)
            ->where('status', 'active')
            ->countAllResults();

        $completedPlans = $this->db->table('idp_plans')
            ->where('academy_id', $academy_id)
            ->where('status', 'completed')
            ->countAllResults();

        $idpCountResult = $this->db->query(
            "SELECT COUNT(DISTINCT player_id) as cnt FROM idp_plans WHERE academy_id = ?",
            [$academy_id]
        )->getRowArray();
        $playersWithIDP = (int)($idpCountResult['cnt'] ?? 0);

        return [
            'total_plans'      => $totalPlans,
            'active_plans'     => $activePlans,
            'completed_plans'  => $completedPlans,
            'draft_plans'      => $totalPlans - $activePlans - $completedPlans,
            'players_with_idp' => $playersWithIDP,
        ];
    }

    /**
     * สรุป IDP นักเตะ — latest plan status + latest scores + attendance
     */
    function getPlayerIDPSummary($player_id, $academy_id)
    {
        $activePlan = $this->getActiveIDPPlan($player_id, $academy_id);
        $allPlans = $this->getPlayerIDPPlans($player_id, $academy_id);
        $attendanceStats = $this->getPlayerAttendanceStats($player_id, $academy_id);

        $summary = [
            'active_plan'    => $activePlan,
            'plan_count'     => count($allPlans),
            'attendance'     => $attendanceStats,
            'latest_scores'  => [],
        ];

        if ($activePlan) {
            $summary['latest_scores'] = $this->getLatestDimensionScores($activePlan['id']);
            $summary['plan_summary'] = $this->getIDPPlanSummary($activePlan['id']);
        }

        return $summary;
    }

    /**
     * baseline vs current comparison data สำหรับ before/after chart
     */
    function getIDPCompareData($plan_id)
    {
        $lang = function_exists('get_current_language') ? get_current_language() : 'th';
        $dimensions = $this->getIDPDimensions();

        $result = [];
        foreach ($dimensions as $dim) {
            // Baseline score (first assessment)
            $baseline = $this->db->table('idp_assessments')
                ->select('COALESCE(score, rating) as score', false)
                ->where('plan_id', $plan_id)
                ->where('dimension_id', $dim['id'])
                ->where('indicator_id IS NULL')
                ->where('assessment_type', 'baseline')
                ->orderBy('assessment_date', 'ASC')
                ->get()->getRowArray();

            // Latest score
            $latest = $this->db->table('idp_assessments')
                ->select('COALESCE(score, rating) as score', false)
                ->where('plan_id', $plan_id)
                ->where('dimension_id', $dim['id'])
                ->where('indicator_id IS NULL')
                ->orderBy('assessment_date', 'DESC')
                ->orderBy('id', 'DESC')
                ->get()->getRowArray();

            $result[] = [
                'dimension_id'   => $dim['id'],
                'dimension_name' => $dim['name'],
                'icon'           => $dim['icon'],
                'color'          => $dim['color'],
                'baseline_score' => $baseline ? (float)$baseline['score'] : 0,
                'current_score'  => $latest ? (float)$latest['score'] : 0,
            ];
        }

        return $result;
    }

    /**
     * ดึง template ที่ไม่ซ้ำกันของนักเตะ (จาก plans ที่มี)
     */
    function getPlayerDistinctTemplates($player_id, $academy_id)
    {
        return $this->db->table('idp_plans p')
            ->select('p.template_id, t.title', false)
            ->join('idp_templates t', 't.id = p.template_id')
            ->where('p.player_id', $player_id)
            ->where('p.academy_id', $academy_id)
            ->where('p.template_id IS NOT NULL')
            ->groupBy(['p.template_id', 't.title'])
            ->orderBy('t.title', 'ASC')
            ->get()->getResultArray();
    }

    /**
     * ดึงคะแนนระดับ dimension ตาม template สำหรับ radar chart
     * ใช้ idp_assessment_results (indicator-level) เป็นหลัก, fallback ไป idp_assessments
     */
    function getPlayerDimensionScoresByTemplate($player_id, $academy_id, $template_id)
    {
        $dimensions = $this->getIDPDimensions($template_id);

        // Get all plan IDs for this player/academy/template
        $plans = $this->db->table('idp_plans')
            ->select('id')
            ->where('player_id', $player_id)
            ->where('academy_id', $academy_id)
            ->where('template_id', $template_id)
            ->get()->getResultArray();

        $planIds = array_column($plans, 'id');
        if (empty($planIds) || empty($dimensions)) {
            return [];
        }

        $result = [];
        foreach ($dimensions as $dim) {
            $baselineScore = 0;
            $currentScore  = 0;
            $hasResults    = false;

            // --- Try idp_assessment_results first (indicator-level) ---

            // Latest: most recent assessed_at for this dimension
            $latestRow = $this->db->table('idp_assessment_results')
                ->select('MAX(assessed_at) as max_at', false)
                ->whereIn('plan_id', $planIds)
                ->where('dimension_id', $dim['id'])
                ->get()->getRowArray();

            if (!empty($latestRow['max_at'])) {
                $latestDate = substr($latestRow['max_at'], 0, 10); // YYYY-MM-DD
                $latestScores = $this->db->table('idp_assessment_results ar')
                    ->select('ar.score, i.min_value, i.max_value, i.is_lower_better')
                    ->join('idp_indicators i', 'i.id = ar.indicator_id')
                    ->whereIn('ar.plan_id', $planIds)
                    ->where('ar.dimension_id', $dim['id'])
                    ->where('ar.assessed_at >=', $latestDate . ' 00:00:00')
                    ->where('ar.assessed_at <=', $latestDate . ' 23:59:59')
                    ->get()->getResultArray();

                if (!empty($latestScores)) {
                    $currentScore = $this->normalizeIndicatorScores($latestScores);
                    $hasResults = true;
                }
            }

            // Baseline: earliest baseline-type assessment
            $baselineRow = $this->db->table('idp_assessment_results')
                ->select('MIN(assessed_at) as min_at', false)
                ->whereIn('plan_id', $planIds)
                ->where('dimension_id', $dim['id'])
                ->where('assessment_type', 'baseline')
                ->get()->getRowArray();

            if (!empty($baselineRow['min_at'])) {
                $baselineDate = substr($baselineRow['min_at'], 0, 10);
                $baselineScores = $this->db->table('idp_assessment_results ar')
                    ->select('ar.score, i.min_value, i.max_value, i.is_lower_better')
                    ->join('idp_indicators i', 'i.id = ar.indicator_id')
                    ->whereIn('ar.plan_id', $planIds)
                    ->where('ar.dimension_id', $dim['id'])
                    ->where('ar.assessment_type', 'baseline')
                    ->where('ar.assessed_at >=', $baselineDate . ' 00:00:00')
                    ->where('ar.assessed_at <=', $baselineDate . ' 23:59:59')
                    ->get()->getResultArray();

                if (!empty($baselineScores)) {
                    $baselineScore = $this->normalizeIndicatorScores($baselineScores);
                }
            }

            // --- Fallback to idp_assessments (dimension-level) ---
            if (!$hasResults) {
                $fbBaseline = $this->db->table('idp_assessments')
                    ->select('COALESCE(score, rating) as score', false)
                    ->whereIn('plan_id', $planIds)
                    ->where('dimension_id', $dim['id'])
                    ->where('indicator_id IS NULL')
                    ->where('assessment_type', 'baseline')
                    ->orderBy('assessment_date', 'ASC')
                    ->get()->getRowArray();

                $fbLatest = $this->db->table('idp_assessments')
                    ->select('COALESCE(score, rating) as score', false)
                    ->whereIn('plan_id', $planIds)
                    ->where('dimension_id', $dim['id'])
                    ->where('indicator_id IS NULL')
                    ->orderBy('assessment_date', 'DESC')
                    ->orderBy('id', 'DESC')
                    ->get()->getRowArray();

                $baselineScore = $fbBaseline ? (float)$fbBaseline['score'] : 0;
                $currentScore  = $fbLatest ? (float)$fbLatest['score'] : 0;
            }

            $result[] = [
                'dimension_id'   => $dim['id'],
                'dimension_name' => $dim['name'],
                'icon'           => $dim['icon'],
                'color'          => $dim['color'],
                'baseline_score' => round($baselineScore, 2),
                'current_score'  => round($currentScore, 2),
            ];
        }

        return $result;
    }

    /**
     * คำนวณคะแนนสูงสุดต่อ dimension ของกลุ่มที่ player คนนี้สังกัด (ใน template เดียวกัน)
     * ใช้เป็น peer baseline สำหรับกราฟ — scope = idp_group_plan_id หรือ group_id ของ plan ที่ active
     * Return: [dimension_id => max_score (0-5)]  (empty ถ้าไม่มี group context)
     */
    function getPeerMaxScoresForPlayer($player_id, $academy_id, $template_id)
    {
        // Find player's plan for this template (active first, fallback latest)
        $plan = $this->db->table('idp_plans')
            ->select('id, group_id, idp_group_plan_id')
            ->where('player_id', (int)$player_id)
            ->where('academy_id', (int)$academy_id)
            ->where('template_id', (int)$template_id)
            ->orderBy("CASE status WHEN 'active' THEN 0 WHEN 'completed' THEN 1 ELSE 2 END", '', false)
            ->orderBy('updated_at', 'DESC')
            ->limit(1)
            ->get()->getRowArray();

        if (empty($plan)) return [];

        $groupId      = !empty($plan['group_id']) ? (int)$plan['group_id'] : null;
        $groupPlanId  = !empty($plan['idp_group_plan_id']) ? (int)$plan['idp_group_plan_id'] : null;

        if ($groupId === null && $groupPlanId === null) return [];

        // Reuse the group comparison query — pass either group_id or group_plan_id
        $scopeId = $groupId !== null ? $groupId : $groupPlanId;
        $groupData = $this->getGroupNormalizedDimensionScores($scopeId, (int)$academy_id, (int)$template_id, $groupPlanId);

        if (empty($groupData)) return [];

        // Compute max per dimension
        $maxes = [];
        foreach ($groupData as $playerRow) {
            foreach ($playerRow['dimensions'] as $ds) {
                $dimId = (int)$ds['dimension_id'];
                $score = (float)$ds['score'];
                if (!isset($maxes[$dimId]) || $score > $maxes[$dimId]) {
                    $maxes[$dimId] = $score;
                }
            }
        }
        return $maxes;
    }

    /**
     * Normalize indicator scores to 0-5 scale and return average
     */
    private function normalizeIndicatorScores($scores)
    {
        $normalized = [];
        foreach ($scores as $s) {
            $min   = (float)($s['min_value'] ?? 0);
            $max   = (float)($s['max_value'] ?? 5);
            $range = $max - $min;
            if ($range <= 0) continue;

            $score = (float)$s['score'];
            $norm  = ($score - $min) / $range;
            if (!empty($s['is_lower_better'])) {
                $norm = 1.0 - $norm;
            }
            $norm = max(0.0, min(1.0, $norm));
            $normalized[] = $norm * 5.0;
        }

        return count($normalized) > 0
            ? array_sum($normalized) / count($normalized)
            : 0;
    }

    /**
     * ดึงรายละเอียดการประเมิน indicator-level ของ plan + dimension
     * Returns:
     *   ['plan' => [...], 'dimension' => [...], 'latest' => [...]|null, 'baseline' => [...]|null]
     *   หรือ null ถ้าไม่พบ plan
     */
    function getPlanDimensionDetail($plan_id, $dimension_id)
    {
        $lang = function_exists('get_current_language') ? get_current_language() : 'th';

        // 1) Plan + Player + Template
        $plan = $this->db->table('idp_plans p')
            ->select('p.id, p.academy_id, p.player_id, p.template_id,
                      pl.first_name, pl.last_name, pl.nick_name, pl.image_path, pl.shirt_number,
                      t.title as template_title', false)
            ->join('player pl', 'pl.id = p.player_id', 'left')
            ->join('idp_templates t', 't.id = p.template_id', 'left')
            ->where('p.id', (int)$plan_id)
            ->get()->getRowArray();

        if (empty($plan)) {
            return null;
        }

        // 2) Dimension info
        $dimension = $this->db->table('idp_dimensions')
            ->select("id, name_{$lang} as name, icon, color", false)
            ->where('id', (int)$dimension_id)
            ->get()->getRowArray();

        if (empty($dimension)) {
            return null;
        }

        $playerName = trim(($plan['first_name'] ?? '') . ' ' . ($plan['last_name'] ?? ''));
        if (!empty($plan['nick_name'])) {
            $playerName = $plan['nick_name'] . ' ' . $playerName;
        }

        $result = [
            'plan' => [
                'id'             => (int)$plan['id'],
                'academy_id'     => (int)$plan['academy_id'],
                'player_id'      => (int)$plan['player_id'],
                'player_name'    => trim($playerName),
                'player_image'   => $plan['image_path'] ?? '',
                'shirt_number'   => $plan['shirt_number'] ?? '',
                'template_id'    => (int)$plan['template_id'],
                'template_title' => $plan['template_title'] ?? '',
            ],
            'dimension' => [
                'id'    => (int)$dimension['id'],
                'name'  => $dimension['name'],
                'icon'  => $dimension['icon'] ?? 'fa-star',
                'color' => $dimension['color'] ?? '#0d6efd',
            ],
            'latest'   => null,
            'baseline' => null,
        ];

        // 3) Latest indicator-level
        $latestRow = $this->db->table('idp_assessment_results')
            ->select('MAX(assessed_at) as max_at', false)
            ->where('plan_id', (int)$plan_id)
            ->where('dimension_id', (int)$dimension_id)
            ->get()->getRowArray();

        if (!empty($latestRow['max_at'])) {
            $latestDate = substr($latestRow['max_at'], 0, 10);
            $rows = $this->db->table('idp_assessment_results ar')
                ->select("ar.id, ar.indicator_id, ar.score, ar.notes, ar.assessed_at,
                          i.name_{$lang} as name, i.min_value, i.max_value, i.is_lower_better, i.unit", false)
                ->join('idp_indicators i', 'i.id = ar.indicator_id')
                ->where('ar.plan_id', (int)$plan_id)
                ->where('ar.dimension_id', (int)$dimension_id)
                ->where('ar.assessed_at >=', $latestDate . ' 00:00:00')
                ->where('ar.assessed_at <=', $latestDate . ' 23:59:59')
                ->orderBy('i.sort_order', 'ASC')
                ->get()->getResultArray();

            $result['latest'] = $this->buildAssessmentBlock($rows, $latestRow['max_at']);
        }

        // 4) Baseline indicator-level
        $baselineRow = $this->db->table('idp_assessment_results')
            ->select('MIN(assessed_at) as min_at', false)
            ->where('plan_id', (int)$plan_id)
            ->where('dimension_id', (int)$dimension_id)
            ->where('assessment_type', 'baseline')
            ->get()->getRowArray();

        if (!empty($baselineRow['min_at'])) {
            $baselineDate = substr($baselineRow['min_at'], 0, 10);
            $rows = $this->db->table('idp_assessment_results ar')
                ->select("ar.id, ar.indicator_id, ar.score, ar.notes, ar.assessed_at,
                          i.name_{$lang} as name, i.min_value, i.max_value, i.is_lower_better, i.unit", false)
                ->join('idp_indicators i', 'i.id = ar.indicator_id')
                ->where('ar.plan_id', (int)$plan_id)
                ->where('ar.dimension_id', (int)$dimension_id)
                ->where('ar.assessment_type', 'baseline')
                ->where('ar.assessed_at >=', $baselineDate . ' 00:00:00')
                ->where('ar.assessed_at <=', $baselineDate . ' 23:59:59')
                ->orderBy('i.sort_order', 'ASC')
                ->get()->getResultArray();

            $result['baseline'] = $this->buildAssessmentBlock($rows, $baselineRow['min_at']);
        }

        // 5) Fallback: ถ้าไม่มี indicator-level เลย → ใช้ idp_assessments (dimension-level)
        if ($result['latest'] === null) {
            $fbLatest = $this->db->table('idp_assessments')
                ->select('COALESCE(score, rating) as score, assessment_date', false)
                ->where('plan_id', (int)$plan_id)
                ->where('dimension_id', (int)$dimension_id)
                ->where('indicator_id IS NULL')
                ->orderBy('assessment_date', 'DESC')
                ->orderBy('id', 'DESC')
                ->get()->getRowArray();
            if ($fbLatest) {
                $result['latest'] = [
                    'assessed_at'      => $fbLatest['assessment_date'] ?? '',
                    'normalized_score' => (float)$fbLatest['score'],
                    'indicators'       => [],
                    'is_dimension_level' => true,
                ];
            }
        }
        if ($result['baseline'] === null) {
            $fbBaseline = $this->db->table('idp_assessments')
                ->select('COALESCE(score, rating) as score, assessment_date', false)
                ->where('plan_id', (int)$plan_id)
                ->where('dimension_id', (int)$dimension_id)
                ->where('indicator_id IS NULL')
                ->where('assessment_type', 'baseline')
                ->orderBy('assessment_date', 'ASC')
                ->get()->getRowArray();
            if ($fbBaseline) {
                $result['baseline'] = [
                    'assessed_at'      => $fbBaseline['assessment_date'] ?? '',
                    'normalized_score' => (float)$fbBaseline['score'],
                    'indicators'       => [],
                    'is_dimension_level' => true,
                ];
            }
        }

        return $result;
    }

    /**
     * Helper: build assessment block จาก rows ของ idp_assessment_results
     */
    private function buildAssessmentBlock(array $rows, $assessedAt)
    {
        if (empty($rows)) return null;

        $indicators = [];
        $normVals = [];
        foreach ($rows as $r) {
            $min = (float)($r['min_value'] ?? 0);
            $max = (float)($r['max_value'] ?? 5);
            $range = $max - $min;
            $score = (float)$r['score'];
            $norm = 0.0;
            if ($range > 0) {
                $norm = ($score - $min) / $range;
                if (!empty($r['is_lower_better'])) {
                    $norm = 1.0 - $norm;
                }
                $norm = max(0.0, min(1.0, $norm)) * 5.0;
                $normVals[] = $norm;
            }
            $indicators[] = [
                'id'              => (int)$r['indicator_id'],
                'name'            => $r['name'] ?? '',
                'score'           => $score,
                'min_value'       => $min,
                'max_value'       => $max,
                'is_lower_better' => (int)($r['is_lower_better'] ?? 0),
                'unit'            => $r['unit'] ?? '',
                'normalized'      => round($norm, 2),
                'notes'           => $r['notes'] ?? '',
            ];
        }

        $avg = count($normVals) > 0 ? array_sum($normVals) / count($normVals) : 0;

        return [
            'assessed_at'        => $assessedAt,
            'normalized_score'   => round($avg, 2),
            'indicators'         => $indicators,
            'is_dimension_level' => false,
        ];
    }

    /**
     * ดึงรายชื่อนักเตะทั้งหมดของ Academy พร้อมสถานะ IDP
     */
    function getAcademyPlayersWithIDPStatus($academy_id)
    {
        $lang = function_exists('get_current_language') ? get_current_language() : 'th';
        $posCol = ($lang === 'en') ? 'pp.name_en' : 'pp.name_th';
        $subPosCol = ($lang === 'en') ? 'name_en' : 'name_th';

        $sql = "SELECT p.id, p.first_name, p.last_name, p.nick_name, p.image_path,
                       COALESCE(ape.shirt_number, p.shirt_number) as shirt_number,
                       p.birth_date,
                       {$posCol} as position_name,
                       COALESCE(ape.sub_position_ids, p.player_sub_position_id) as sub_position_ids,
                       (SELECT COUNT(*) FROM idp_plans ip WHERE ip.player_id = p.id AND ip.academy_id = ?) as plan_count,
                       (SELECT ip2.status FROM idp_plans ip2 WHERE ip2.player_id = p.id AND ip2.academy_id = ? ORDER BY ip2.created_at DESC LIMIT 1) as latest_plan_status,
                       (SELECT ip3.id FROM idp_plans ip3 WHERE ip3.player_id = p.id AND ip3.academy_id = ? AND ip3.status = 'active' ORDER BY ip3.created_at DESC LIMIT 1) as active_plan_id
                FROM player p
                JOIN academy_player_enrollments ape ON ape.player_id = p.id
                    AND ape.academy_id = ?
                    AND ape.is_active = 1
                LEFT JOIN tbl_player_position pp ON pp.id = COALESCE(ape.position_id, p.player_position_id)
                WHERE (
                      ape.team_id = 0
                      OR ape.team_id IN (SELECT team_id FROM academy_teams WHERE academy_id = ?)
                  )
                ORDER BY p.first_name ASC";

        $players = $this->db->query($sql, [$academy_id, $academy_id, $academy_id, $academy_id, $academy_id])->getResultArray();
        $positionRows = $this->db->table('tbl_player_position')->select("id, {$subPosCol} as name")->get()->getResultArray();
        $positionMap = [];
        foreach ($positionRows as $position) {
            $positionMap[(string)$position['id']] = $position['name'];
        }

        foreach ($players as &$player) {
            $mainPosition = trim((string)($player['position_name'] ?? ''));
            $subPosition = '';

            if ($mainPosition === '' && !empty($player['sub_position_ids'])) {
                $subIds = json_decode(html_entity_decode($player['sub_position_ids']), true);
                if (!is_array($subIds)) {
                    $subIds = array_filter(explode(',', html_entity_decode($player['sub_position_ids'])));
                }

                $subNames = [];
                foreach ($subIds as $subId) {
                    $subId = trim((string)$subId, " \t\n\r\0\x0B\"[]");
                    if ($subId !== '' && isset($positionMap[$subId])) {
                        $subNames[] = $positionMap[$subId];
                    }
                }
                $subPosition = implode(', ', $subNames);
            }

            $displayPosition = $mainPosition !== '' ? $mainPosition : $subPosition;
            $textClass = $mainPosition !== '' ? 'text-success' : 'text-warning';

            $player['primary_position_name'] = $mainPosition;
            $player['sub_position_name'] = $subPosition;
            $player['position_name'] = $displayPosition;
            $player['position_text_class'] = $displayPosition !== '' ? $textClass : '';
            $player['position_display_html'] = $displayPosition !== ''
                ? '<span class="' . $textClass . '">' . esc($displayPosition) . '</span>'
                : '';
        }
        unset($player);

        return $players;
    }

    // ============================================================
    // IDP Group (Batch) Methods
    // ============================================================

    /**
     * @deprecated Use getIDPGroupPlans() for V2 group plans. Kept for backward compatibility.
     * สร้างแผน IDP แบบกลุ่ม (batch insert) สำหรับหลายนักเตะพร้อมกัน
     * ดึงสรุป IDP แยกตามกลุ่ม (แผน+แม่แบบ+จำนวนนักเตะ+โค้ช)
     * @param int $academy_id
     * @return array  keyed by group_id
     */
    function getGroupIDPSummary($academy_id)
    {
        // Get all group IDP plans with template info
        $rows = $this->db->table('idp_plans p')
            ->select("p.group_id, p.template_id, p.title as plan_title, p.status,
                      t.title as template_title,
                      COUNT(DISTINCT p.player_id) as player_count,
                      CONCAT(c.first_name, ' ', c.last_name) as coach_name", false)
            ->join('idp_templates t', 't.id = p.template_id', 'left')
            ->join('coach c', 'c.id = p.coach_id', 'left')
            ->where('p.academy_id', $academy_id)
            ->where('p.status', 'active')
            ->where('p.group_id IS NOT NULL')
            ->groupBy('p.group_id, p.template_id, p.coach_id')
            ->orderBy('p.group_id', 'ASC')
            ->get()->getResultArray();

        $summary = [];
        // Track unique players per group
        $groupPlayerIds = [];
        foreach ($rows as $row) {
            $gid = (int)$row['group_id'];
            if (!isset($summary[$gid])) {
                $summary[$gid] = [
                    'templates' => [],
                    'coaches'   => [],
                    'unique_players' => 0,
                ];
                $groupPlayerIds[$gid] = [];
            }
            // Track templates
            $tid = $row['template_id'] ? (int)$row['template_id'] : 0;
            if (!isset($summary[$gid]['templates'][$tid])) {
                $summary[$gid]['templates'][$tid] = [
                    'title' => $row['template_title'] ?: $row['plan_title'],
                    'player_count' => 0,
                ];
            }
            $summary[$gid]['templates'][$tid]['player_count'] += (int)$row['player_count'];

            // Track coaches
            if (!empty($row['coach_name']) && !in_array($row['coach_name'], $summary[$gid]['coaches'])) {
                $summary[$gid]['coaches'][] = $row['coach_name'];
            }
        }

        // Count unique players per group
        foreach (array_keys($summary) as $gid) {
            $cnt = $this->db->query(
                "SELECT COUNT(DISTINCT player_id) as cnt FROM idp_plans WHERE group_id = ? AND academy_id = ? AND status = 'active'",
                [$gid, $academy_id]
            )->getRow();
            $summary[$gid]['unique_players'] = $cnt ? (int)$cnt->cnt : 0;
        }

        return $summary;
    }

    /**
     * ลบแผน IDP ทั้ง batch (group_id + template_id)
     * ลบทั้งแผน + assessments + assessment_results
     */
    function deleteGroupIDPBatch($group_id, $academy_id, $template_id)
    {
        $this->db->transBegin();

        // Get plan IDs to delete
        $builder = $this->db->table('idp_plans')
            ->where('group_id', $group_id)
            ->where('academy_id', $academy_id)
            ->where('status', 'active');
        if ($template_id !== null) {
            $builder->where('template_id', $template_id);
        }
        $plans = $builder->get()->getResultArray();
        $planIds = array_column($plans, 'id');

        if (empty($planIds)) {
            $this->db->transRollback();
            return ['success' => false, 'count' => 0];
        }

        // Delete assessment results
        $this->db->table('idp_assessment_results')
            ->whereIn('plan_id', $planIds)
            ->delete();

        // Delete assessments
        $this->db->table('idp_assessments')
            ->whereIn('plan_id', $planIds)
            ->delete();

        // Delete goals & progress if exist
        $goalIds = $this->db->table('idp_goals')
            ->select('id')
            ->whereIn('plan_id', $planIds)
            ->get()->getResultArray();
        if (!empty($goalIds)) {
            $goalIdList = array_column($goalIds, 'id');
            $this->db->table('idp_progress')->whereIn('goal_id', $goalIdList)->delete();
            $this->db->table('idp_goals')->whereIn('plan_id', $planIds)->delete();
        }

        // Delete feedback
        $this->db->table('idp_feedback')
            ->whereIn('plan_id', $planIds)
            ->delete();

        // Delete plans
        $this->db->table('idp_plans')
            ->whereIn('id', $planIds)
            ->delete();

        if ($this->db->transStatus() === false) {
            $this->db->transRollback();
            return ['success' => false, 'count' => 0];
        }
        $this->db->transCommit();
        return ['success' => true, 'count' => count($planIds)];
    }

    /**
     * @param array $planTemplate  ข้อมูลแผนแม่แบบ (title, season, start_date, end_date, academy_id, coach_id, status, created_by)
     * @param array $playerIds     รายชื่อ player_id ที่ต้องสร้างแผน
     * @param int   $group_id      ID ของ training group
     * @return array ['success' => bool, 'plan_ids' => [player_id => plan_id], 'count' => int]
     */
    function batchCreateIDPPlans($planTemplate, $playerIds, $group_id)
    {
        $this->db->transBegin();

        $planIds = [];
        foreach ($playerIds as $playerId) {
            $data = [
                'academy_id'  => $planTemplate['academy_id'],
                'player_id'   => $playerId,
                'coach_id'    => !empty($planTemplate['coach_id']) ? $planTemplate['coach_id'] : null,
                'group_id'    => $group_id,
                'template_id' => !empty($planTemplate['template_id']) ? $planTemplate['template_id'] : null,
                'title'       => $planTemplate['title'],
                'season'      => $planTemplate['season'] ?? null,
                'start_date'  => $planTemplate['start_date'] ?? null,
                'end_date'    => $planTemplate['end_date'] ?? null,
                'status'      => $planTemplate['status'] ?? 'active',
                'created_by'  => $planTemplate['created_by'] ?? null,
            ];
            $this->db->table('idp_plans')->insert($data);
            $planIds[$playerId] = $this->db->insertID();
        }

        if ($this->db->transStatus() === false) {
            $this->db->transRollback();
            return ['success' => false, 'plan_ids' => [], 'count' => 0];
        }
        $this->db->transCommit();
        return ['success' => true, 'plan_ids' => $planIds, 'count' => count($planIds)];
    }

    /**
     * ดึงแผน IDP ทั้งหมดที่สร้างจากกลุ่มนี้ พร้อมข้อมูลนักเตะ+โค้ช
     * @param int $group_id    ID ของ training group
     * @param int $academy_id  ID ของ academy
     * @return array
     */
    function getGroupIDPPlans($group_id, $academy_id, $template_id = null, $group_plan_id = null)
    {
        $builder = $this->db->table('idp_plans p')
            ->select("p.*,
                      CONCAT(pl.first_name, ' ', pl.last_name) as player_name,
                      pl.nick_name as player_nick_name,
                      pl.shirt_number, pl.image_path as player_image,
                      CONCAT(c.first_name, ' ', c.last_name) as coach_name", false)
            ->join('player pl', 'pl.id = p.player_id')
            ->join('coach c', 'c.id = p.coach_id', 'left');

        if ($group_plan_id) {
            $builder->where('p.idp_group_plan_id', $group_plan_id);
        } else {
            $builder->where('p.group_id', $group_id);
        }

        $builder->where('p.academy_id', $academy_id)
            ->where('p.status', 'active');

        if ($template_id) {
            $builder->where('p.template_id', $template_id);
        }

        return $builder->orderBy('pl.first_name', 'ASC')
            ->get()->getResultArray();
    }

    /**
     * ดึงนักเตะในกลุ่มฝึกซ้อม พร้อมสถานะแผน IDP (active plan หรือไม่)
     * @param int $group_id    ID ของ training group
     * @param int $academy_id  ID ของ academy
     * @return array
     */
    function getGroupPlayersWithPlanStatus($group_id, $academy_id)
    {
        $lang = function_exists('get_current_language') ? get_current_language() : 'th';
        $posCol = ($lang === 'en') ? 'pp.name_en' : 'pp.name_th';

        $sql = "SELECT p.id, p.first_name, p.last_name, p.nick_name, p.image_path,
                       COALESCE(ape_gps.shirt_number, p.shirt_number) as shirt_number, p.birth_date,
                       {$posCol} as position_name,
                       (SELECT COUNT(*) FROM idp_plans ip WHERE ip.player_id = p.id AND ip.academy_id = ? AND ip.group_id = ?) as group_plan_count,
                       (SELECT ip2.id FROM idp_plans ip2 WHERE ip2.player_id = p.id AND ip2.academy_id = ? AND ip2.group_id = ? AND ip2.status = 'active' ORDER BY ip2.created_at DESC LIMIT 1) as active_plan_id,
                       (SELECT ip3.status FROM idp_plans ip3 WHERE ip3.player_id = p.id AND ip3.academy_id = ? AND ip3.group_id = ? ORDER BY ip3.created_at DESC LIMIT 1) as latest_plan_status
                FROM player p
                JOIN academy_training_group_players gp ON gp.player_id = p.id AND gp.group_id = ?
                LEFT JOIN academy_player_enrollments ape_gps ON ape_gps.player_id = p.id AND ape_gps.academy_id = ? AND ape_gps.is_active = 1 AND ape_gps.is_primary = 1
                LEFT JOIN tbl_player_position pp ON pp.id = COALESCE(NULLIF(ape_gps.position_id,0), p.player_position_id)
                WHERE gp.status = 'active'
                ORDER BY p.first_name ASC";

        return $this->db->query($sql, [$academy_id, $group_id, $academy_id, $group_id, $academy_id, $group_id, $group_id, $academy_id])->getResultArray();
    }

    /**
     * บันทึกคะแนนประเมินกลุ่ม (bulk) สำหรับหลายนักเตะ x หลายมิติ
     * @param array  $assessments   [ ['plan_id' => X, 'scores' => [['dimension_id' => D, 'score' => S], ...]], ... ]
     * @param string $type          assessment_type: baseline/midterm/final/adhoc
     * @param string $date          assessment_date (Y-m-d)
     * @param int    $assessed_by   user_id ของผู้ประเมิน
     * @return array ['success' => bool, 'count' => int]
     */
    function bulkSaveGroupAssessments($assessments, $type, $date, $assessed_by)
    {
        $this->db->transBegin();
        $count = 0;

        foreach ($assessments as $item) {
            $planId = $item['plan_id'];
            if (empty($item['scores']) || !is_array($item['scores'])) {
                continue;
            }
            foreach ($item['scores'] as $score) {
                if (empty($score['dimension_id']) || !isset($score['score'])) {
                    continue;
                }
                $data = [
                    'plan_id'         => $planId,
                    'dimension_id'    => $score['dimension_id'],
                    'indicator_id'    => null,
                    'score'           => $score['score'],
                    'rating'          => $score['score'],
                    'assessment_date' => $date,
                    'assessment_type' => $type,
                    'notes'           => $score['notes'] ?? null,
                    'assessed_by'     => $assessed_by,
                ];
                $this->db->table('idp_assessments')->insert($data);
                $count++;
            }
        }

        if ($this->db->transStatus() === false) {
            $this->db->transRollback();
            return ['success' => false, 'count' => 0];
        }
        $this->db->transCommit();
        return ['success' => true, 'count' => $count];
    }

    // ============================================================
    // IDP Group Plans V2 — Independent Group Plan Entity
    // ============================================================

    /**
     * ดึง group plans ทั้งหมดของ academy + template info + player count
     */
    function getIDPGroupPlans($academy_id)
    {
        $sql = "SELECT gp.*,
                       t.title as template_title,
                       CONCAT(c.first_name, ' ', c.last_name) as coach_name,
                       (SELECT COUNT(DISTINCT p.player_id) FROM idp_plans p WHERE p.idp_group_plan_id = gp.id) as player_count
                FROM idp_group_plans gp
                LEFT JOIN idp_templates t ON t.id = gp.template_id
                LEFT JOIN coach c ON c.id = gp.coach_id
                WHERE gp.academy_id = ?
                ORDER BY gp.status ASC, gp.created_at DESC";

        return $this->db->query($sql, [$academy_id])->getResultArray();
    }

    /**
     * ดึง group plan 1 รายการ
     */
    function getIDPGroupPlan($id)
    {
        return $this->db->table('idp_group_plans')
            ->where('id', $id)
            ->get()->getRowArray();
    }

    /**
     * INSERT/UPDATE group plan
     */
    function saveIDPGroupPlan($data)
    {
        if (!empty($data['id'])) {
            $id = $data['id'];
            unset($data['id']);
            $data['updated_at'] = date('Y-m-d H:i:s');
            $this->db->table('idp_group_plans')->where('id', $id)->update($data);
            return $id;
        }
        $this->db->table('idp_group_plans')->insert($data);
        return $this->db->insertID();
    }

    /**
     * ลบ group plan + cascade ลบ plans ข้างใน (assessments, goals, progress, feedback)
     */
    function deleteIDPGroupPlan($id, $academy_id)
    {
        $this->db->transBegin();

        // Get plan IDs
        $plans = $this->db->table('idp_plans')
            ->where('idp_group_plan_id', $id)
            ->where('academy_id', $academy_id)
            ->get()->getResultArray();
        $planIds = array_column($plans, 'id');

        if (!empty($planIds)) {
            // Delete assessment results
            $this->db->table('idp_assessment_results')
                ->whereIn('plan_id', $planIds)->delete();

            // Delete assessments
            $this->db->table('idp_assessments')
                ->whereIn('plan_id', $planIds)->delete();

            // Delete goals & progress
            $goalIds = $this->db->table('idp_goals')
                ->select('id')
                ->whereIn('plan_id', $planIds)
                ->get()->getResultArray();
            if (!empty($goalIds)) {
                $goalIdList = array_column($goalIds, 'id');
                $this->db->table('idp_progress')->whereIn('goal_id', $goalIdList)->delete();
                $this->db->table('idp_goals')->whereIn('plan_id', $planIds)->delete();
            }

            // Delete feedback
            $this->db->table('idp_feedback')
                ->whereIn('plan_id', $planIds)->delete();

            // Delete plans
            $this->db->table('idp_plans')
                ->whereIn('id', $planIds)->delete();
        }

        // Delete group plan itself
        $this->db->table('idp_group_plans')
            ->where('id', $id)
            ->where('academy_id', $academy_id)
            ->delete();

        if ($this->db->transStatus() === false) {
            $this->db->transRollback();
            return ['success' => false, 'count' => 0];
        }
        $this->db->transCommit();
        return ['success' => true, 'count' => count($planIds)];
    }

    /**
     * ดึงนักเตะในแผนกลุ่ม
     */
    function getIDPGroupPlanPlayers($group_plan_id)
    {
        $lang = function_exists('get_current_language') ? get_current_language() : 'th';
        $posCol = ($lang === 'en') ? 'pp.name_en' : 'pp.name_th';

        $sql = "SELECT DISTINCT p.id, p.first_name, p.last_name, p.nick_name, p.image_path,
                       COALESCE(ape_gpp.shirt_number, p.shirt_number) as shirt_number, p.birth_date,
                       {$posCol} as position_name,
                       ip.id as plan_id, ip.status as plan_status
                FROM idp_plans ip
                JOIN player p ON p.id = ip.player_id
                LEFT JOIN academy_player_enrollments ape_gpp ON ape_gpp.player_id = p.id AND ape_gpp.academy_id = ip.academy_id AND ape_gpp.is_active = 1 AND ape_gpp.is_primary = 1
                LEFT JOIN tbl_player_position pp ON pp.id = COALESCE(NULLIF(ape_gpp.position_id,0), p.player_position_id)
                WHERE ip.idp_group_plan_id = ?
                ORDER BY p.first_name ASC";

        return $this->db->query($sql, [$group_plan_id])->getResultArray();
    }

    /**
     * เพิ่มนักเตะลงแผนกลุ่ม (สร้าง idp_plans ใหม่)
     */
    function addPlayersToGroupPlan($group_plan_id, $playerIds, $planTemplate)
    {
        $this->db->transBegin();
        $planIds = [];
        foreach ($playerIds as $playerId) {
            $data = [
                'academy_id'        => $planTemplate['academy_id'],
                'player_id'         => $playerId,
                'coach_id'          => !empty($planTemplate['coach_id']) ? $planTemplate['coach_id'] : null,
                'group_id'          => !empty($planTemplate['group_id']) ? $planTemplate['group_id'] : null,
                'idp_group_plan_id' => $group_plan_id,
                'template_id'       => !empty($planTemplate['template_id']) ? $planTemplate['template_id'] : null,
                'title'             => $planTemplate['title'],
                'season'            => isset($planTemplate['season']) ? $planTemplate['season'] : null,
                'start_date'        => isset($planTemplate['start_date']) ? $planTemplate['start_date'] : null,
                'end_date'          => isset($planTemplate['end_date']) ? $planTemplate['end_date'] : null,
                'status'            => isset($planTemplate['status']) ? $planTemplate['status'] : 'active',
                'created_by'        => isset($planTemplate['created_by']) ? $planTemplate['created_by'] : null,
            ];
            $this->db->table('idp_plans')->insert($data);
            $planIds[$playerId] = $this->db->insertID();
        }

        if ($this->db->transStatus() === false) {
            $this->db->transRollback();
            return ['success' => false, 'plan_ids' => [], 'count' => 0];
        }
        $this->db->transCommit();
        return ['success' => true, 'plan_ids' => $planIds, 'count' => count($planIds)];
    }

    /**
     * ลบนักเตะจากแผนกลุ่ม
     */
    function removePlayerFromGroupPlan($group_plan_id, $player_id)
    {
        return $this->db->table('idp_plans')
            ->where('idp_group_plan_id', $group_plan_id)
            ->where('player_id', $player_id)
            ->delete();
    }

    // =====================================================
    // IDP Template Selection (for plan creation)
    // =====================================================

    /**
     * ดึงแม่แบบ IDP ที่ใช้ได้สำหรับ Academy นี้: global public + academy-owned
     */
    function getAvailableIDPTemplates($academy_id)
    {
        $builder = $this->db->table('idp_templates');
        $builder->select('id, title, description, owner_type, owner_id, is_active');
        $builder->groupStart()
            ->where('owner_type', 'global')
            ->where('is_public', 1)
            ->where('is_active', 1)
        ->groupEnd()
        ->orGroupStart()
            ->where('owner_type', 'academy')
            ->where('owner_id', $academy_id)
            ->where('is_active', 1)
        ->groupEnd();
        $builder->orderBy('owner_type', 'ASC');
        $builder->orderBy('title', 'ASC');
        return $builder->get()->getResultArray();
    }

    /**
     * ดึงมิติสำหรับแม่แบบเฉพาะ (ใช้ตอนดู/ประเมินแผน)
     */
    function getIDPTemplateDimensionsForPlan($template_id)
    {
        $lang = function_exists('get_current_language') ? get_current_language() : 'th';
        $builder = $this->db->table('idp_dimensions');
        $builder->select("id, name_{$lang} as name, name_th, name_en, icon, color, sort_order", false);
        $builder->where('template_id', $template_id);
        $builder->where('is_active', 1);
        $builder->orderBy('sort_order', 'ASC');
        return $builder->get()->getResultArray();
    }

    // =====================================================
    // IDP Library — Template Management
    // =====================================================

    /**
     * Get template library: global public templates + academy's own templates
     */
    function getIDPTemplateLibrary($academy_id)
    {
        $lang = function_exists('get_current_language') ? get_current_language() : 'th';

        // Global public templates
        $global = $this->db->table('idp_templates t')
            ->select("t.*, (SELECT COUNT(*) FROM idp_dimensions d WHERE d.template_id = t.id AND d.is_active = 1) as dimension_count, (SELECT COUNT(*) FROM idp_indicators i JOIN idp_dimensions d2 ON i.dimension_id = d2.id WHERE d2.template_id = t.id AND i.is_active = 1) as indicator_count", false)
            ->where('t.owner_type', 'global')
            ->where('t.is_public', 1)
            ->where('t.is_active', 1)
            ->orderBy('t.id', 'ASC')
            ->get()->getResultArray();

        // Academy's own templates
        $academy = $this->db->table('idp_templates t')
            ->select("t.*, (SELECT COUNT(*) FROM idp_dimensions d WHERE d.template_id = t.id AND d.is_active = 1) as dimension_count, (SELECT COUNT(*) FROM idp_indicators i JOIN idp_dimensions d2 ON i.dimension_id = d2.id WHERE d2.template_id = t.id AND i.is_active = 1) as indicator_count", false)
            ->where('t.owner_type', 'academy')
            ->where('t.owner_id', $academy_id)
            ->orderBy('t.id', 'DESC')
            ->get()->getResultArray();

        return [
            'global' => $global,
            'academy' => $academy,
        ];
    }

    /**
     * Get single template by ID
     */
    function getIDPTemplate($template_id)
    {
        return $this->db->table('idp_templates')
            ->where('id', $template_id)
            ->get()->getRowArray();
    }

    /**
     * Insert or update a template (enforce owner_type='academy')
     */
    function saveIDPTemplate($data)
    {
        if (!empty($data['id'])) {
            $id = $data['id'];
            unset($data['id']);
            $data['updated_at'] = date('Y-m-d H:i:s');
            $this->db->table('idp_templates')->where('id', $id)->update($data);
            return $id;
        } else {
            unset($data['id']);
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['updated_at'] = date('Y-m-d H:i:s');
            $this->db->table('idp_templates')->insert($data);
            return $this->db->insertID();
        }
    }

    /**
     * Delete template only if academy owns it
     */
    function deleteIDPTemplate($template_id, $academy_id)
    {
        $template = $this->getIDPTemplate($template_id);
        if (!$template || $template['owner_type'] !== 'academy' || (int)$template['owner_id'] !== (int)$academy_id) {
            return false;
        }

        $this->db->transBegin();

        // Delete indicators of dimensions belonging to this template
        $dimensions = $this->db->table('idp_dimensions')
            ->select('id')
            ->where('template_id', $template_id)
            ->get()->getResultArray();

        $dimIds = array_column($dimensions, 'id');
        if (!empty($dimIds)) {
            $this->db->table('idp_indicators')->whereIn('dimension_id', $dimIds)->delete();
        }

        // Delete dimensions
        $this->db->table('idp_dimensions')->where('template_id', $template_id)->delete();

        // Delete template
        $this->db->table('idp_templates')->where('id', $template_id)->delete();

        if ($this->db->transStatus() === false) {
            $this->db->transRollback();
            return false;
        }
        $this->db->transCommit();
        return true;
    }

    /**
     * Clone (deep copy) a template: template + dimensions + indicators
     */
    function cloneIDPTemplate($source_template_id, $academy_id, $new_title, $created_by = null)
    {
        $source = $this->getIDPTemplate($source_template_id);
        if (!$source) {
            return false;
        }

        $this->db->transBegin();

        // 1. Insert new template
        $newTemplate = [
            'title'       => $new_title,
            'description' => $source['description'],
            'owner_type'  => 'academy',
            'owner_id'    => $academy_id,
            'is_public'   => 0,
            'is_active'   => 1,
            'created_by'  => $created_by,
            'created_at'  => date('Y-m-d H:i:s'),
            'updated_at'  => date('Y-m-d H:i:s'),
        ];
        $this->db->table('idp_templates')->insert($newTemplate);
        $newTemplateId = $this->db->insertID();

        // 2. Get source dimensions
        $sourceDimensions = $this->db->table('idp_dimensions')
            ->where('template_id', $source_template_id)
            ->orderBy('sort_order', 'ASC')
            ->get()->getResultArray();

        // 3. Copy dimensions and build ID mapping
        $dimIdMap = []; // old_dim_id => new_dim_id
        foreach ($sourceDimensions as $dim) {
            $oldDimId = $dim['id'];
            $newDim = [
                'template_id'    => $newTemplateId,
                'name_th'        => $dim['name_th'],
                'name_en'        => $dim['name_en'],
                'description_th' => $dim['description_th'],
                'description_en' => $dim['description_en'],
                'icon'           => $dim['icon'],
                'color'          => $dim['color'],
                'sort_order'     => $dim['sort_order'],
                'is_active'      => $dim['is_active'],
                'owner_type'     => 'academy',
                'owner_id'       => $academy_id,
                'is_public'      => 0,
            ];
            $this->db->table('idp_dimensions')->insert($newDim);
            $dimIdMap[$oldDimId] = $this->db->insertID();
        }

        // 4. Copy indicators for each dimension
        foreach ($dimIdMap as $oldDimId => $newDimId) {
            $sourceIndicators = $this->db->table('idp_indicators')
                ->where('dimension_id', $oldDimId)
                ->orderBy('sort_order', 'ASC')
                ->get()->getResultArray();

            foreach ($sourceIndicators as $ind) {
                $newInd = [
                    'dimension_id'    => $newDimId,
                    'name_th'         => $ind['name_th'],
                    'name_en'         => $ind['name_en'],
                    'description_th'  => $ind['description_th'],
                    'description_en'  => $ind['description_en'],
                    'measurement_type' => $ind['measurement_type'],
                    'unit'            => $ind['unit'],
                    'min_value'       => $ind['min_value'],
                    'max_value'       => $ind['max_value'],
                    'is_lower_better' => $ind['is_lower_better'],
                    'sort_order'      => $ind['sort_order'],
                    'is_active'       => $ind['is_active'],
                    'owner_type'      => 'academy',
                    'owner_id'        => $academy_id,
                ];
                $this->db->table('idp_indicators')->insert($newInd);
            }
        }

        if ($this->db->transStatus() === false) {
            $this->db->transRollback();
            return false;
        }
        $this->db->transCommit();
        return $newTemplateId;
    }

    /**
     * Get dimensions for a template (language aware)
     */
    function getIDPTemplateDimensions($template_id)
    {
        $lang = function_exists('get_current_language') ? get_current_language() : 'th';
        return $this->db->table('idp_dimensions')
            ->select("id, template_id, name_th, name_en, name_{$lang} as name, description_th, description_en, description_{$lang} as description, icon, color, sort_order, is_active, owner_type, owner_id, is_public", false)
            ->where('template_id', $template_id)
            ->orderBy('sort_order', 'ASC')
            ->get()->getResultArray();
    }

    /**
     * Insert or update a dimension
     */
    function saveIDPTemplateDimension($data)
    {
        if (!empty($data['id'])) {
            $id = $data['id'];
            unset($data['id']);
            $this->db->table('idp_dimensions')->where('id', $id)->update($data);
            return $id;
        } else {
            unset($data['id']);
            $this->db->table('idp_dimensions')->insert($data);
            return $this->db->insertID();
        }
    }

    /**
     * Delete dimension only if academy owns it
     */
    function deleteIDPTemplateDimension($dimension_id, $academy_id)
    {
        $dim = $this->db->table('idp_dimensions')
            ->where('id', $dimension_id)
            ->get()->getRowArray();

        if (!$dim || $dim['owner_type'] !== 'academy' || (int)$dim['owner_id'] !== (int)$academy_id) {
            return false;
        }

        $this->db->transBegin();

        // Delete indicators first
        $this->db->table('idp_indicators')->where('dimension_id', $dimension_id)->delete();
        // Delete dimension
        $this->db->table('idp_dimensions')->where('id', $dimension_id)->delete();

        if ($this->db->transStatus() === false) {
            $this->db->transRollback();
            return false;
        }
        $this->db->transCommit();
        return true;
    }

    /**
     * Get indicators for a dimension (language aware)
     */
    function getIDPDimensionIndicators($dimension_id)
    {
        $lang = function_exists('get_current_language') ? get_current_language() : 'th';
        return $this->db->table('idp_indicators')
            ->select("id, dimension_id, name_th, name_en, name_{$lang} as name, description_th, description_en, description_{$lang} as description, measurement_type, unit, min_value, max_value, is_lower_better, sort_order, is_active, owner_type, owner_id", false)
            ->where('dimension_id', $dimension_id)
            ->orderBy('sort_order', 'ASC')
            ->get()->getResultArray();
    }

    /**
     * Insert or update an indicator
     */
    function saveIDPTemplateIndicator($data)
    {
        if (!empty($data['id'])) {
            $id = $data['id'];
            unset($data['id']);
            $this->db->table('idp_indicators')->where('id', $id)->update($data);
            return $id;
        } else {
            unset($data['id']);
            $this->db->table('idp_indicators')->insert($data);
            return $this->db->insertID();
        }
    }

    /**
     * Delete indicator only if academy owns it
     */
    function deleteIDPTemplateIndicator($indicator_id, $academy_id)
    {
        $ind = $this->db->table('idp_indicators')
            ->where('id', $indicator_id)
            ->get()->getRowArray();

        if (!$ind || $ind['owner_type'] !== 'academy' || (int)$ind['owner_id'] !== (int)$academy_id) {
            return false;
        }

        $this->db->table('idp_indicators')->where('id', $indicator_id)->delete();
        return true;
    }

    /**
     * Get single dimension by ID
     */
    function getIDPTemplateDimensionById($dimension_id)
    {
        return $this->db->table('idp_dimensions')
            ->where('id', $dimension_id)
            ->get()->getRowArray();
    }

    /**
     * Get single indicator by ID
     */
    function getIDPTemplateIndicatorById($indicator_id)
    {
        return $this->db->table('idp_indicators')
            ->where('id', $indicator_id)
            ->get()->getRowArray();
    }

    // =====================================================
    // IDP Report — Indicator-Level & Group Comparison
    // =====================================================

    /**
     * Normalize an indicator score to a 0-5 scale based on measurement_type.
     * - rating: already 1-5, use as-is
     * - percentage: score/100*5
     * - score/time/distance: (score-min)/(max-min)*5, inverted if is_lower_better
     * Clamps result to 0-5.
     *
     * @param float  $score
     * @param string $measurementType  rating|percentage|score|time|distance
     * @param float  $maxValue
     * @param float  $minValue
     * @param bool   $isLowerBetter
     * @return float  normalized score 0-5
     */
    private function normalizeIndicatorScore($score, $measurementType, $maxValue = 5, $minValue = 0, $isLowerBetter = false)
    {
        $score = (float)$score;
        $maxValue = (float)$maxValue;
        $minValue = (float)$minValue;

        if ($measurementType === 'rating') {
            // Already on 1-5 scale
            $normalized = $score;
        } elseif ($measurementType === 'percentage') {
            // 0-100 → 0-5
            $normalized = ($score / 100.0) * 5.0;
        } else {
            // score, time, distance: linear mapping using min/max
            $range = $maxValue - $minValue;
            if ($range <= 0) {
                $normalized = $score;
            } else {
                $ratio = ($score - $minValue) / $range;
                if ($isLowerBetter) {
                    $ratio = 1.0 - $ratio;
                }
                $normalized = $ratio * 5.0;
            }
        }

        // Clamp to 0-5
        return max(0, min(5, round($normalized, 2)));
    }

    /**
     * Get all indicator-level scores for a plan, grouped by dimension.
     * Uses idp_assessment_results table (indicator-level scores).
     * Returns latest score per indicator.
     *
     * @param int $plan_id
     * @return array  [ dimension_id => ['dimension' => [...], 'indicators' => [...]] ]
     */
    function getPlayerIndicatorScores($plan_id)
    {
        $lang = function_exists('get_current_language') ? get_current_language() : 'th';

        // Get latest score per indicator for this plan
        $sql = "SELECT ar.dimension_id, ar.indicator_id, ar.score, ar.assessment_type, ar.assessed_at,
                       d.name_{$lang} as dimension_name, d.icon, d.color, d.sort_order as dim_sort,
                       i.name_{$lang} as indicator_name, i.sort_order as ind_sort,
                       i.measurement_type, i.max_value, i.min_value, i.is_lower_better
                FROM idp_assessment_results ar
                JOIN idp_dimensions d ON d.id = ar.dimension_id
                JOIN idp_indicators i ON i.id = ar.indicator_id
                WHERE ar.plan_id = ?
                  AND ar.id = (
                    SELECT ar2.id FROM idp_assessment_results ar2
                    WHERE ar2.plan_id = ar.plan_id
                      AND ar2.indicator_id = ar.indicator_id
                    ORDER BY ar2.assessed_at DESC, ar2.id DESC
                    LIMIT 1
                  )
                ORDER BY d.sort_order ASC, i.sort_order ASC";

        $rows = $this->db->query($sql, [$plan_id])->getResultArray();

        // Group by dimension
        $result = [];
        foreach ($rows as $row) {
            $dimId = $row['dimension_id'];
            if (!isset($result[$dimId])) {
                $result[$dimId] = [
                    'dimension' => [
                        'id'   => $dimId,
                        'name' => $row['dimension_name'],
                        'icon' => $row['icon'],
                        'color'=> $row['color'],
                    ],
                    'indicators' => [],
                ];
            }
            $result[$dimId]['indicators'][] = [
                'id'               => $row['indicator_id'],
                'name'             => $row['indicator_name'],
                'score'            => (float)$row['score'],
                'max_value'        => (float)($row['max_value'] ?: 5),
                'min_value'        => (float)($row['min_value'] ?: 0),
                'is_lower_better'  => !empty($row['is_lower_better']),
                'measurement_type' => $row['measurement_type'],
                'assessment_type'  => $row['assessment_type'],
            ];
        }

        return $result;
    }

    /**
     * Get dimension-level scores for ALL plans in a training group.
     * Used for group radar chart overlay.
     *
     * @param int $group_id
     * @param int $academy_id
     * @return array [ { player_name, player_image, shirt_number, dimensions: [{name, score}] } ]
     */
    function getGroupComparisonData($group_id, $academy_id, $template_id = null)
    {
        // Get all plans in this group
        $plans = $this->getGroupIDPPlans($group_id, $academy_id);
        $dimensions = $this->getIDPDimensions($template_id);

        $result = [];
        foreach ($plans as $plan) {
            $latestScores = $this->getLatestDimensionScores($plan['id']);

            // Build a map of dimension_id => score
            $scoreMap = [];
            foreach ($latestScores as $ls) {
                $scoreMap[(int)$ls['dimension_id']] = (float)($ls['score'] ?: 0);
            }

            $dimScores = [];
            foreach ($dimensions as $dim) {
                $dimScores[] = [
                    'dimension_id'   => $dim['id'],
                    'dimension_name' => $dim['name'],
                    'score'          => isset($scoreMap[(int)$dim['id']]) ? $scoreMap[(int)$dim['id']] : 0,
                ];
            }

            $result[] = [
                'plan_id'      => $plan['id'],
                'player_id'    => $plan['player_id'],
                'player_name'  => $plan['player_name'],
                'player_image' => isset($plan['player_image']) ? $plan['player_image'] : '',
                'shirt_number' => isset($plan['shirt_number']) ? $plan['shirt_number'] : '',
                'dimensions'   => $dimScores,
            ];
        }

        return $result;
    }

    /**
     * Get dimension-level NORMALIZED scores for ALL plans in a group.
     * Each indicator is normalized to 0-5 scale before averaging per dimension.
     * Drop-in replacement for getGroupComparisonData() for radar chart.
     *
     * @param int      $group_id
     * @param int      $academy_id
     * @param int|null $template_id
     * @return array [ { player_name, player_image, shirt_number, dimensions: [{name, score}] } ]
     */
    function getGroupNormalizedDimensionScores($group_id, $academy_id, $template_id = null, $group_plan_id = null)
    {
        $plans = $this->getGroupIDPPlans($group_id, $academy_id, null, $group_plan_id);
        $dimensions = $this->getIDPDimensions($template_id);

        $result = [];
        foreach ($plans as $plan) {
            $indicatorScores = $this->getPlayerIndicatorScores($plan['id']);

            // Normalize each indicator → average per dimension
            $normalizedDimMap = []; // dim_id => normalized score
            foreach ($indicatorScores as $dimId => $dimData) {
                $normScores = [];
                foreach ($dimData['indicators'] as $ind) {
                    $normScores[] = $this->normalizeIndicatorScore(
                        $ind['score'],
                        $ind['measurement_type'],
                        $ind['max_value'],
                        $ind['min_value'],
                        $ind['is_lower_better']
                    );
                }
                if (count($normScores) > 0) {
                    $normalizedDimMap[(int)$dimId] = round(array_sum($normScores) / count($normScores), 2);
                }
            }

            $dimScores = [];
            foreach ($dimensions as $dim) {
                $dimScores[] = [
                    'dimension_id'   => $dim['id'],
                    'dimension_name' => $dim['name'],
                    'score'          => isset($normalizedDimMap[(int)$dim['id']]) ? $normalizedDimMap[(int)$dim['id']] : 0,
                ];
            }

            $result[] = [
                'plan_id'      => $plan['id'],
                'player_id'    => $plan['player_id'],
                'player_name'  => $plan['player_name'],
                'player_image' => isset($plan['player_image']) ? $plan['player_image'] : '',
                'shirt_number' => isset($plan['shirt_number']) ? $plan['shirt_number'] : '',
                'dimensions'   => $dimScores,
            ];
        }

        return $result;
    }

    /**
     * Get group dimension trends over time (normalized 0-5).
     * Returns per-dimension daily group averages for Chart.js line chart.
     *
     * @param int      $group_id
     * @param int      $academy_id
     * @param int|null $template_id
     * @param int      $days   number of days to look back (default 30)
     * @return array ['dates' => [...], 'dimensions' => [{id, name, color, icon, scores: [...]}]]
     */
    function getGroupDimensionTrends($group_id, $academy_id, $template_id = null, $days = 30, $group_plan_id = null)
    {
        $lang = function_exists('get_current_language') ? get_current_language() : 'th';

        // Get all indicator data grouped by date, dimension, indicator
        $groupFilter = $group_plan_id ? "p.idp_group_plan_id = ?" : "p.group_id = ?";
        $groupFilterVal = $group_plan_id ? $group_plan_id : $group_id;
        $sql = "SELECT DATE(ar.assessed_at) as assess_date,
                       ar.dimension_id, ar.indicator_id, ar.player_id,
                       ar.score,
                       i.measurement_type, i.max_value, i.min_value, i.is_lower_better
                FROM idp_assessment_results ar
                JOIN idp_plans p ON p.id = ar.plan_id
                JOIN idp_indicators i ON i.id = ar.indicator_id
                WHERE {$groupFilter} AND p.academy_id = ? AND p.status = 'active'
                  AND ar.assessed_at >= DATE_SUB(CURDATE(), INTERVAL ? DAY)";
        $params = [$groupFilterVal, $academy_id, $days];

        if ($template_id !== null) {
            $sql .= " AND p.template_id = ?";
            $params[] = $template_id;
        }

        $sql .= " ORDER BY assess_date ASC, ar.dimension_id ASC";

        $rows = $this->db->query($sql, $params)->getResultArray();

        if (empty($rows)) {
            return ['dates' => [], 'dimensions' => []];
        }

        // Structure: date -> dim_id -> player_id -> indicator_id -> score
        $dateData = [];
        foreach ($rows as $row) {
            $date = $row['assess_date'];
            $dimId = (int)$row['dimension_id'];
            $playerId = (int)$row['player_id'];
            $indId = (int)$row['indicator_id'];

            $normalized = $this->normalizeIndicatorScore(
                $row['score'],
                $row['measurement_type'],
                $row['max_value'],
                $row['min_value'],
                !empty($row['is_lower_better'])
            );

            if (!isset($dateData[$date])) $dateData[$date] = [];
            if (!isset($dateData[$date][$dimId])) $dateData[$date][$dimId] = [];
            if (!isset($dateData[$date][$dimId][$playerId])) $dateData[$date][$dimId][$playerId] = [];
            // Keep latest per indicator per player per date (overwrite is fine, already ordered)
            $dateData[$date][$dimId][$playerId][$indId] = $normalized;
        }

        // Get dimensions info
        $dimensions = $this->getIDPDimensions($template_id);
        $dates = array_keys($dateData);
        sort($dates);

        // Build per-dimension score arrays aligned with dates
        $dimResults = [];
        foreach ($dimensions as $dim) {
            $dimId = (int)$dim['id'];
            $scores = [];
            foreach ($dates as $date) {
                if (!isset($dateData[$date][$dimId])) {
                    $scores[] = null; // no data for this dimension on this date
                    continue;
                }
                // Average: per player → avg indicators → then avg across players
                $playerAvgs = [];
                foreach ($dateData[$date][$dimId] as $playerId => $indScores) {
                    if (!empty($indScores)) {
                        $playerAvgs[] = array_sum($indScores) / count($indScores);
                    }
                }
                if (!empty($playerAvgs)) {
                    $scores[] = round(array_sum($playerAvgs) / count($playerAvgs), 2);
                } else {
                    $scores[] = null;
                }
            }
            $dimResults[] = [
                'id'     => $dim['id'],
                'name'   => $dim['name'],
                'color'  => $dim['color'],
                'icon'   => $dim['icon'],
                'scores' => $scores,
            ];
        }

        return [
            'dates'      => $dates,
            'dimensions' => $dimResults,
        ];
    }

    /**
     * Get assessment summary stats for a group (for summary cards).
     *
     * @param int      $group_id
     * @param int      $academy_id
     * @param int|null $template_id
     * @return array ['total_sessions', 'date_from', 'date_to', 'dims_covered', 'players_assessed', 'dim_last_assessed' => [...]]
     */
    function getGroupAssessmentSummary($group_id, $academy_id, $template_id = null, $group_plan_id = null)
    {
        $lang = function_exists('get_current_language') ? get_current_language() : 'th';

        $groupFilter = $group_plan_id ? "p.idp_group_plan_id = ?" : "p.group_id = ?";
        $groupFilterVal = $group_plan_id ? $group_plan_id : $group_id;

        // Main aggregate query
        $sql = "SELECT COUNT(DISTINCT CONCAT(DATE(ar.assessed_at), '-', ar.assessment_type, '-', ar.dimension_id)) as total_sessions,
                       MIN(DATE(ar.assessed_at)) as date_from,
                       MAX(DATE(ar.assessed_at)) as date_to,
                       COUNT(DISTINCT ar.dimension_id) as dims_covered,
                       COUNT(DISTINCT ar.player_id) as players_assessed
                FROM idp_assessment_results ar
                JOIN idp_plans p ON p.id = ar.plan_id
                WHERE {$groupFilter} AND p.academy_id = ? AND p.status = 'active'";
        $params = [$groupFilterVal, $academy_id];

        if ($template_id !== null) {
            $sql .= " AND p.template_id = ?";
            $params[] = $template_id;
        }

        $summary = $this->db->query($sql, $params)->getRowArray();

        // Per-dimension last assessed date
        $sql2 = "SELECT ar.dimension_id,
                        d.name_{$lang} as dimension_name,
                        MAX(DATE(ar.assessed_at)) as last_assessed
                 FROM idp_assessment_results ar
                 JOIN idp_plans p ON p.id = ar.plan_id
                 JOIN idp_dimensions d ON d.id = ar.dimension_id
                 WHERE {$groupFilter} AND p.academy_id = ? AND p.status = 'active'";
        $params2 = [$groupFilterVal, $academy_id];

        if ($template_id !== null) {
            $sql2 .= " AND p.template_id = ?";
            $params2[] = $template_id;
        }
        $sql2 .= " GROUP BY ar.dimension_id, d.name_{$lang}";

        $dimDates = $this->db->query($sql2, $params2)->getResultArray();

        $dimLastAssessed = [];
        foreach ($dimDates as $row) {
            $dimLastAssessed[(int)$row['dimension_id']] = $row['last_assessed'];
        }

        return [
            'total_sessions'    => (int)($summary['total_sessions'] ?: 0),
            'date_from'         => $summary['date_from'] ?: null,
            'date_to'           => $summary['date_to'] ?: null,
            'dims_covered'      => (int)($summary['dims_covered'] ?: 0),
            'players_assessed'  => (int)($summary['players_assessed'] ?: 0),
            'dim_last_assessed' => $dimLastAssessed,
        ];
    }

    /**
     * Get indicator-level scores for ALL plans in a group (heatmap).
     * Returns structure: plans[], indicators[], scores[plan_id][indicator_id]
     *
     * @param int $group_id
     * @param int $academy_id
     * @return array ['plans' => [...], 'dimensions' => [...], 'scores' => [...]]
     */
    function getGroupIndicatorHeatmap($group_id, $academy_id, $template_id = null, $group_plan_id = null)
    {
        $lang = function_exists('get_current_language') ? get_current_language() : 'th';
        $plans = $this->getGroupIDPPlans($group_id, $academy_id, null, $group_plan_id);

        // Get all dimensions with indicators (filtered by template)
        $grouped = $this->getAllIDPIndicatorsGroupedByDimension($template_id);

        // Normalize to flat array: [{name, icon, color, indicators: [...]}, ...]
        $dimensionsWithIndicators = [];
        foreach ($grouped as $dimData) {
            $dim = $dimData['dimension'];
            $dim['indicators'] = $dimData['indicators'];
            $dimensionsWithIndicators[] = $dim;
        }

        // For each plan, get indicator scores
        $scores = [];
        foreach ($plans as $plan) {
            $indicatorScores = $this->getPlayerIndicatorScores($plan['id']);
            $planScores = [];
            foreach ($indicatorScores as $dimId => $dimData2) {
                foreach ($dimData2['indicators'] as $ind) {
                    $planScores[(int)$ind['id']] = (float)$ind['score'];
                }
            }
            $scores[(int)$plan['id']] = $planScores;
        }

        return [
            'plans'      => $plans,
            'dimensions' => $dimensionsWithIndicators,
            'scores'     => $scores,
        ];
    }

    /**
     * For each dimension, get top N players by latest dimension score.
     *
     * @param int $group_id
     * @param int $academy_id
     * @param int $limit
     * @return array  dimension_id => [ {player_name, player_image, shirt_number, score} ]
     */
    // =====================================================
    // IDP Indicator-Level Assessment (Phase 5)
    // =====================================================

    /**
     * ดึง indicators ทั้งหมดจัดกลุ่มตาม dimension
     * @param int|null $template_id  filter by template (null = all active)
     * @return array  keyed by dimension_id => ['dimension' => {...}, 'indicators' => [{...}, ...]]
     */
    function getAllIDPIndicatorsGroupedByDimension($template_id = null)
    {
        $lang = function_exists('get_current_language') ? get_current_language() : 'th';

        // Get dimensions
        $dimBuilder = $this->db->table('idp_dimensions')
            ->select("id, name_{$lang} as name, description_{$lang} as description, icon, color, sort_order", false)
            ->where('is_active', 1);

        if ($template_id !== null) {
            $dimBuilder->where('template_id', $template_id);
        }

        $dimensions = $dimBuilder->orderBy('sort_order', 'ASC')->get()->getResultArray();

        // Get all indicators
        $indBuilder = $this->db->table('idp_indicators')
            ->select("id, dimension_id, name_{$lang} as name, description_{$lang} as description, measurement_type, unit, min_value, max_value, is_lower_better, sort_order", false)
            ->where('is_active', 1);

        $indicators = $indBuilder->orderBy('dimension_id', 'ASC')
            ->orderBy('sort_order', 'ASC')
            ->get()->getResultArray();

        // Group indicators by dimension_id
        $indByDim = [];
        foreach ($indicators as $ind) {
            $dimId = $ind['dimension_id'];
            if (!isset($indByDim[$dimId])) {
                $indByDim[$dimId] = [];
            }
            $indByDim[$dimId][] = $ind;
        }

        // Build result keyed by dimension_id
        $result = [];
        foreach ($dimensions as $dim) {
            $dimId = $dim['id'];
            $result[$dimId] = [
                'dimension' => $dim,
                'indicators' => isset($indByDim[$dimId]) ? $indByDim[$dimId] : [],
            ];
        }

        return $result;
    }

    /**
     * ดึงคะแนนระดับตัวชี้วัดทั้งหมดสำหรับกลุ่ม
     * @param int $group_id    ID ของ training group
     * @param int $academy_id  ID ของ academy
     * @return array  keyed by plan_id -> indicator_id -> score data
     */
    function getGroupIndicatorAssessments($group_id, $academy_id, $template_id = null, $group_plan_id = null)
    {
        $groupFilter = $group_plan_id ? "p.idp_group_plan_id = ?" : "p.group_id = ?";
        $groupFilterVal = $group_plan_id ? $group_plan_id : $group_id;
        $sql = "SELECT ar.*, p.player_id
                FROM idp_assessment_results ar
                JOIN idp_plans p ON p.id = ar.plan_id
                WHERE {$groupFilter}
                  AND p.academy_id = ?
                  AND p.status = 'active'";
        $params = [$groupFilterVal, $academy_id];

        if ($template_id) {
            $sql .= " AND p.template_id = ?";
            $params[] = $template_id;
        }

        $sql .= " ORDER BY ar.assessed_at DESC";

        $rows = $this->db->query($sql, $params)->getResultArray();

        // Group by plan_id -> indicator_id (keep latest only)
        $result = [];
        foreach ($rows as $row) {
            $planId = $row['plan_id'];
            $indId = $row['indicator_id'];
            if (!isset($result[$planId])) {
                $result[$planId] = [];
            }
            // Keep only the latest score per indicator (query ordered DESC, so first hit is latest)
            if (!isset($result[$planId][$indId])) {
                $result[$planId][$indId] = $row;
            }
        }

        return $result;
    }

    /**
     * ดึงประวัติการประเมินกลุ่ม (assessment log) จัดกลุ่มตามวันที่+ประเภท
     * @param int $group_id
     * @param int $academy_id
     * @param int|null $template_id
     * @return array [ ['assessed_at' => '2026-03-02', 'assessment_type' => 'adhoc', 'score_count' => 154, 'player_count' => 7], ... ]
     */
    function getGroupAssessmentHistory($group_id, $academy_id, $template_id = null, $group_plan_id = null)
    {
        $lang = function_exists('get_current_language') ? get_current_language() : 'th';
        $nameCol = ($lang === 'en') ? 'd.name_en' : 'd.name_th';

        $groupFilter = $group_plan_id ? "p.idp_group_plan_id = ?" : "p.group_id = ?";
        $groupFilterVal = $group_plan_id ? $group_plan_id : $group_id;

        $sql = "SELECT DATE(ar.assessed_at) as assess_date,
                       ar.assessment_type,
                       ar.dimension_id,
                       {$nameCol} as dimension_name,
                       d.icon as dimension_icon,
                       d.color as dimension_color,
                       COUNT(*) as score_count,
                       COUNT(DISTINCT ar.player_id) as player_count,
                       MIN(ar.assessed_at) as first_at,
                       MAX(ar.assessed_at) as last_at
                FROM idp_assessment_results ar
                JOIN idp_plans p ON p.id = ar.plan_id
                JOIN idp_dimensions d ON d.id = ar.dimension_id
                WHERE {$groupFilter}
                  AND p.academy_id = ?
                  AND p.status = 'active'";
        $params = [$groupFilterVal, $academy_id];

        if ($template_id) {
            $sql .= " AND p.template_id = ?";
            $params[] = $template_id;
        }

        $sql .= " GROUP BY DATE(ar.assessed_at), ar.assessment_type, ar.dimension_id
                   ORDER BY assess_date DESC, d.id ASC";

        return $this->db->query($sql, $params)->getResultArray();
    }

    /**
     * ดึงคะแนนประเมินของรอบที่เจาะจง (ตามวันที่+ประเภท)
     * @param int $group_id
     * @param int $academy_id
     * @param string $date  Y-m-d
     * @param string $type  assessment_type
     * @param int|null $template_id
     * @return array  keyed by plan_id -> indicator_id -> row
     */
    function getGroupIndicatorAssessmentsBySession($group_id, $academy_id, $date, $type, $template_id = null, $dimensionId = null, $group_plan_id = null)
    {
        $groupFilter = $group_plan_id ? "p.idp_group_plan_id = ?" : "p.group_id = ?";
        $groupFilterVal = $group_plan_id ? $group_plan_id : $group_id;
        $sql = "SELECT ar.*, p.player_id
                FROM idp_assessment_results ar
                JOIN idp_plans p ON p.id = ar.plan_id
                WHERE {$groupFilter}
                  AND p.academy_id = ?
                  AND p.status = 'active'
                  AND DATE(ar.assessed_at) = ?
                  AND ar.assessment_type = ?";
        $params = [$groupFilterVal, $academy_id, $date, $type];

        if ($template_id) {
            $sql .= " AND p.template_id = ?";
            $params[] = $template_id;
        }

        if ($dimensionId) {
            $sql .= " AND ar.dimension_id = ?";
            $params[] = $dimensionId;
        }

        $sql .= " ORDER BY ar.assessed_at DESC";

        $rows = $this->db->query($sql, $params)->getResultArray();

        $result = [];
        foreach ($rows as $row) {
            $planId = $row['plan_id'];
            $indId = $row['indicator_id'];
            if (!isset($result[$planId])) {
                $result[$planId] = [];
            }
            if (!isset($result[$planId][$indId])) {
                $result[$planId][$indId] = $row;
            }
        }

        return $result;
    }

    /**
     * บันทึกคะแนนระดับตัวชี้วัด (bulk) สำหรับหลายนักเตะ x หลายตัวชี้วัด
     * + คำนวณ dimension-level averages แล้ว upsert ลง idp_assessments ด้วย
     *
     * @param array  $results      [ ['plan_id' => X, 'player_id' => Y, 'scores' => [['dimension_id' => D, 'indicator_id' => I, 'score' => S], ...]], ... ]
     * @param string $type         assessment_type
     * @param string $date         assessment_date (Y-m-d)
     * @param int    $assessed_by  user_id ของผู้ประเมิน
     * @return array ['success' => bool, 'count' => int]
     */
    function bulkSaveIndicatorAssessments($results, $type, $date, $assessed_by, $deletePlanIds = [], $dimensionId = null)
    {
        $this->db->transBegin();
        $count = 0;

        // Overwrite mode: ลบข้อมูลเดิมของรอบนี้ก่อน INSERT ใหม่ (scoped by dimension if provided)
        if (!empty($deletePlanIds)) {
            $this->deleteAssessmentSession($deletePlanIds, $date, $type, $dimensionId);
        }

        // Pre-load indicator metadata for normalization
        $indicatorMeta = []; // indicator_id => {measurement_type, max_value, min_value, is_lower_better}
        $allIndicatorIds = [];
        foreach ($results as $item) {
            if (empty($item['scores']) || !is_array($item['scores'])) continue;
            foreach ($item['scores'] as $score) {
                $indId = isset($score['indicator_id']) ? (int)$score['indicator_id'] : 0;
                if ($indId > 0) $allIndicatorIds[$indId] = true;
            }
        }
        if (!empty($allIndicatorIds)) {
            $metaRows = $this->db->table('idp_indicators')
                ->select('id, measurement_type, max_value, min_value, is_lower_better')
                ->whereIn('id', array_keys($allIndicatorIds))
                ->get()->getResultArray();
            foreach ($metaRows as $m) {
                $indicatorMeta[(int)$m['id']] = $m;
            }
        }

        // Track dimension scores per plan for normalized averaging
        $dimScores = []; // plan_id -> dimension_id -> [normalized_scores]

        foreach ($results as $item) {
            $planId = isset($item['plan_id']) ? (int)$item['plan_id'] : 0;
            $playerId = isset($item['player_id']) ? (int)$item['player_id'] : 0;

            if (empty($planId) || empty($item['scores']) || !is_array($item['scores'])) {
                continue;
            }

            foreach ($item['scores'] as $score) {
                $dimId = isset($score['dimension_id']) ? (int)$score['dimension_id'] : 0;
                $indId = isset($score['indicator_id']) ? (int)$score['indicator_id'] : 0;
                $val = isset($score['score']) ? $score['score'] : null;

                if (empty($dimId) || empty($indId) || $val === null || $val === '') {
                    continue;
                }

                $data = [
                    'plan_id'         => $planId,
                    'dimension_id'    => $dimId,
                    'indicator_id'    => $indId,
                    'player_id'       => $playerId,
                    'session_id'      => null,
                    'score'           => (float)$val,
                    'assessment_type' => $type,
                    'notes'           => isset($score['notes']) ? $score['notes'] : null,
                    'assessed_by'     => $assessed_by,
                    'assessed_at'     => $date . ' ' . date('H:i:s'),
                ];
                $this->db->table('idp_assessment_results')->insert($data);
                $count++;

                // Normalize score for dimension averaging
                $meta = isset($indicatorMeta[$indId]) ? $indicatorMeta[$indId] : null;
                if ($meta) {
                    $normalizedVal = $this->normalizeIndicatorScore(
                        (float)$val,
                        $meta['measurement_type'],
                        (float)($meta['max_value'] ?: 5),
                        (float)($meta['min_value'] ?: 0),
                        !empty($meta['is_lower_better'])
                    );
                } else {
                    $normalizedVal = (float)$val; // fallback: use raw
                }

                if (!isset($dimScores[$planId])) {
                    $dimScores[$planId] = [];
                }
                if (!isset($dimScores[$planId][$dimId])) {
                    $dimScores[$planId][$dimId] = [];
                }
                $dimScores[$planId][$dimId][] = $normalizedVal;
            }
        }

        // Insert dimension-level normalized averages into idp_assessments (legacy table)
        foreach ($dimScores as $planId => $dims) {
            foreach ($dims as $dimId => $scores) {
                if (count($scores) > 0) {
                    $avg = round(array_sum($scores) / count($scores), 2);
                    $dimData = [
                        'plan_id'         => $planId,
                        'dimension_id'    => $dimId,
                        'indicator_id'    => null,
                        'score'           => $avg,
                        'rating'          => round($avg),
                        'assessment_date' => $date,
                        'assessment_type' => $type,
                        'notes'           => 'Normalized avg from indicator scores (0-5)',
                        'assessed_by'     => $assessed_by,
                    ];
                    $this->db->table('idp_assessments')->insert($dimData);
                }
            }
        }

        if ($this->db->transStatus() === false) {
            $this->db->transRollback();
            return ['success' => false, 'count' => 0];
        }
        $this->db->transCommit();
        return ['success' => true, 'count' => $count];
    }

    /**
     * ลบคะแนนประเมินทั้งรอบ (date + type) สำหรับ plan IDs ที่ระบุ
     * ลบทั้ง idp_assessment_results (indicator-level) และ idp_assessments (dimension-level legacy)
     *
     * @param array  $planIds
     * @param string $date  Y-m-d
     * @param string $type  assessment_type
     * @return int   จำนวน rows ที่ลบจาก idp_assessment_results
     */
    function deleteAssessmentSession($planIds, $date, $type, $dimensionId = null)
    {
        if (empty($planIds)) {
            return 0;
        }

        // Delete from idp_assessment_results (indicator-level)
        $builder = $this->db->table('idp_assessment_results')
            ->whereIn('plan_id', $planIds)
            ->where("DATE(assessed_at) = " . $this->db->escape($date), null, false)
            ->where('assessment_type', $type);
        if ($dimensionId) {
            $builder->where('dimension_id', $dimensionId);
        }
        $builder->delete();

        $affectedCount = $this->db->affectedRows();

        // Delete from idp_assessments (dimension-level legacy)
        $builder2 = $this->db->table('idp_assessments')
            ->whereIn('plan_id', $planIds)
            ->where('assessment_date', $date)
            ->where('assessment_type', $type);
        if ($dimensionId) {
            $builder2->where('dimension_id', $dimensionId);
        }
        $builder2->delete();

        return $affectedCount;
    }

    /**
     * ดึงคะแนนระดับตัวชี้วัดล่าสุดสำหรับแผน IDP เดี่ยว
     * @param int $plan_id
     * @return array  keyed by indicator_id
     */
    function getLatestIndicatorScores($plan_id)
    {
        $sql = "SELECT ar.*
                FROM idp_assessment_results ar
                WHERE ar.plan_id = ?
                ORDER BY ar.assessed_at DESC";

        $rows = $this->db->query($sql, [$plan_id])->getResultArray();

        $result = [];
        foreach ($rows as $row) {
            $indId = $row['indicator_id'];
            if (!isset($result[$indId])) {
                $result[$indId] = $row;
            }
        }

        return $result;
    }

    // --- IDP AI Analysis ---

    /**
     * บันทึกผลวิเคราะห์ AI
     */
    function saveAIAnalysis($data)
    {
        $this->db->table('idp_ai_analyses')->insert($data);
        return $this->db->insertID();
    }

    /**
     * ดึงประวัติวิเคราะห์ AI ของนักเตะ
     */
    function getAIAnalysisHistory($player_id, $academy_id, $limit = 20)
    {
        return $this->db->table('idp_ai_analyses a')
            ->select('a.id, a.plan_id, a.result_summary, a.model_used, a.tokens_input, a.tokens_output, a.processing_time_ms, a.created_at, u.name as created_by_name, p.title as plan_title')
            ->join('user u', 'u.id = a.created_by', 'left')
            ->join('idp_plans p', 'p.id = a.plan_id', 'left')
            ->where('a.player_id', $player_id)
            ->where('a.academy_id', $academy_id)
            ->orderBy('a.created_at', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }

    /**
     * ดึงผลวิเคราะห์ AI เดี่ยว (full result_text)
     */
    function getAIAnalysis($id)
    {
        return $this->db->table('idp_ai_analyses a')
            ->select('a.*, u.name as created_by_name, p.title as plan_title')
            ->join('user u', 'u.id = a.created_by', 'left')
            ->join('idp_plans p', 'p.id = a.plan_id', 'left')
            ->where('a.id', $id)
            ->get()
            ->getRowArray();
    }

    /**
     * ตรวจสอบว่านักเตะถูกวิเคราะห์ AI ในเดือนนี้แล้วหรือยัง
     * @return array|null  row ถ้าเคยวิเคราะห์แล้ว, null ถ้ายังไม่เคย
     */
    function getAIAnalysisThisMonth($player_id, $academy_id)
    {
        $firstDay = date('Y-m-01 00:00:00');
        return $this->db->table('idp_ai_analyses')
            ->where('player_id', $player_id)
            ->where('academy_id', $academy_id)
            ->where('created_at >=', $firstDay)
            ->orderBy('created_at', 'DESC')
            ->get()
            ->getRowArray();
    }
}
