<?php

namespace Modules\Academy\Controllers\Traits;

trait AcademyAiTrait
{
    /**
     * POST: วิเคราะห์ IDP ด้วย Claude AI
     */
    public function idpAIAnalyze()
    {
        $academy_id = (int)$this->request->getPost('academy_id');
        $player_id = (int)$this->request->getPost('player_id');

        $access = $this->ensureAcademyAccess($academy_id);
        if ($access['redirect']) {
            return $this->response->setJSON(['status' => 'error', 'message' => lang('Common.academy_no_permission')]);
        }

        $claudeAI = new \App\Libraries\ClaudeAI();
        if (!$claudeAI->isConfigured()) {
            return $this->response->setJSON(['status' => 'error', 'message' => lang('Common.idp_ai_not_configured')]);
        }

        // ตรวจสอบ limit: เดือนละ 1 ครั้ง/นักเตะ
        $existingAnalysis = $this->academyModel->getAIAnalysisThisMonth($player_id, $academy_id);
        if ($existingAnalysis) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => lang('Common.idp_ai_limit_reached'),
            ]);
        }

        $player = $this->playerModel->get_Player($player_id);
        if (!$player) {
            return $this->response->setJSON(['status' => 'error', 'message' => lang('Common.no_data_found')]);
        }

        // Build input data
        $inputData = $this->buildAIInputData($player_id, $academy_id, $player);
        $systemPrompt = $this->getAISystemPrompt();
        $userMessage = $this->formatAIUserMessage($inputData);

        $startTime = microtime(true);
        $result = $claudeAI->analyze($systemPrompt, $userMessage);
        $processingTime = (int)((microtime(true) - $startTime) * 1000);

        if (!$result['success']) {
            log_message('error', 'IDP AI Analysis failed: ' . $result['error']);
            return $this->response->setJSON(['status' => 'error', 'message' => lang('Common.idp_ai_error') . ': ' . $result['error']]);
        }

        // Save to DB
        $activePlan = $this->academyModel->getActiveIDPPlan($player_id, $academy_id);
        $analysisId = $this->academyModel->saveAIAnalysis([
            'player_id' => $player_id,
            'academy_id' => $academy_id,
            'plan_id' => $activePlan ? $activePlan['id'] : null,
            'input_data' => json_encode($inputData, JSON_UNESCAPED_UNICODE),
            'result_text' => $result['text'],
            'result_summary' => $this->extractSummary($result['text']),
            'model_used' => $result['model'],
            'tokens_input' => $result['tokens_input'],
            'tokens_output' => $result['tokens_output'],
            'processing_time_ms' => $processingTime,
            'created_by' => session()->get('id'),
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return $this->response->setJSON([
            'status' => 'success',
            'data' => [
                'id' => $analysisId,
                'result_text' => $result['text'],
                'result_summary' => $this->extractSummary($result['text']),
                'model_used' => $result['model'],
                'tokens_input' => $result['tokens_input'],
                'tokens_output' => $result['tokens_output'],
                'processing_time_ms' => $processingTime,
                'created_at' => date('Y-m-d H:i:s'),
            ],
        ]);
    }

    /**
     * GET: ดึงประวัติวิเคราะห์ AI
     */
    public function getAIAnalysisHistory($academy_id, $player_id)
    {
        $access = $this->ensureAcademyAccess((int)$academy_id);
        if ($access['redirect']) {
            return $this->response->setJSON(['status' => 'error', 'message' => lang('Common.academy_no_permission')]);
        }

        $history = $this->academyModel->getAIAnalysisHistory((int)$player_id, (int)$academy_id);

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $history,
        ]);
    }

    /**
     * GET: ดึงผลวิเคราะห์ AI เต็ม
     */
    public function getAIAnalysisDetail($id)
    {
        $analysis = $this->academyModel->getAIAnalysis((int)$id);
        if (!$analysis) {
            return $this->response->setJSON(['status' => 'error', 'message' => lang('Common.no_data_found')]);
        }

        // Verify access
        $access = $this->ensureAcademyAccess((int)$analysis['academy_id']);
        if ($access['redirect']) {
            return $this->response->setJSON(['status' => 'error', 'message' => lang('Common.academy_no_permission')]);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $analysis,
        ]);
    }

    /**
     * รวบรวมข้อมูล IDP สำหรับส่งให้ AI วิเคราะห์
     */
    private function buildAIInputData($player_id, $academy_id, $player)
    {
        // Physical data
        $bmi = null;
        if (!empty($player['height']) && !empty($player['weight'])) {
            $h = (float)$player['height'] / 100;
            $bmi = $h > 0 ? round((float)$player['weight'] / ($h * $h), 1) : null;
        }
        $footMap = ['left' => 'ซ้าย', 'right' => 'ขวา', 'both' => 'สองเท้า'];

        // Enrollment for position/shirt
        $enrollment = $this->academyModel->getPlayerEnrollment($player_id, $academy_id);

        // Attendance stats
        $attendance = $this->academyModel->getPlayerAttendanceStats($player_id, $academy_id);

        // Academy assessment history
        $academyAssessments = $this->academyModel->getPlayerAssessmentHistory($player_id, $academy_id);

        $data = [
            'player' => [
                'name'         => trim(($player['first_name'] ?? '') . ' ' . ($player['last_name'] ?? '')),
                'nickname'     => $player['nick_name'] ?? '',
                'age'          => !empty($player['birth_date']) ? $this->calculateAge($player['birth_date']) : null,
                'birth_date'   => $player['birth_date'] ?? '',
                'height_cm'    => !empty($player['height']) ? (float)$player['height'] : null,
                'weight_kg'    => !empty($player['weight']) ? (float)$player['weight'] : null,
                'bmi'          => $bmi,
                'dominant_foot'=> !empty($player['dominant_foot']) ? ($footMap[$player['dominant_foot']] ?? $player['dominant_foot']) : null,
                'position'     => $enrollment['position_name'] ?? ($player['position'] ?? ''),
                'shirt_number' => $enrollment['shirt_number'] ?? '',
            ],
            'attendance' => [
                'total'           => (int)($attendance['total'] ?? 0),
                'present'         => (int)($attendance['present'] ?? 0),
                'absent'          => (int)($attendance['absent'] ?? 0),
                'late'            => (int)($attendance['late'] ?? 0),
                'attendance_rate' => round((float)($attendance['attendance_rate'] ?? 0), 1),
            ],
            'academy_assessments' => [],
            'plans'            => [],
            'goals'            => [],
            'assessments'      => [],
            'feedbacks'        => [],
            'dimension_scores' => [],
        ];

        // Academy assessment history (grouped by type)
        $asmByType = [];
        foreach ($academyAssessments as $a) {
            $key = $a['assessment_name'] ?? 'ไม่ระบุ';
            if (!isset($asmByType[$key])) $asmByType[$key] = ['name' => $key, 'count' => 0, 'scores' => []];
            $asmByType[$key]['count']++;
            if (!empty($a['score']) && !empty($a['max_score'])) {
                $asmByType[$key]['scores'][] = round(($a['score'] / $a['max_score']) * 100, 1);
            }
        }
        foreach ($asmByType as $grp) {
            $avg = count($grp['scores']) > 0 ? round(array_sum($grp['scores']) / count($grp['scores']), 1) : null;
            $data['academy_assessments'][] = ['name' => $grp['name'], 'count' => $grp['count'], 'avg_percent' => $avg];
        }

        $plans = $this->academyModel->getPlayerIDPPlans($player_id, $academy_id);
        foreach ($plans as $plan) {
            $data['plans'][] = [
                'title' => $plan['title'],
                'status' => $plan['status'],
                'start_date' => isset($plan['start_date']) ? $plan['start_date'] : '',
                'end_date' => isset($plan['end_date']) ? $plan['end_date'] : '',
                'template_name' => isset($plan['template_title']) ? $plan['template_title'] : '',
            ];

            // Goals
            $goals = $this->academyModel->getIDPGoals($plan['id']);
            foreach ($goals as $goal) {
                $data['goals'][] = [
                    'dimension' => isset($goal['dimension_name']) ? $goal['dimension_name'] : '',
                    'indicator' => isset($goal['indicator_name']) ? $goal['indicator_name'] : '',
                    'title' => isset($goal['title']) ? $goal['title'] : '',
                    'baseline_value' => isset($goal['baseline_value']) ? (float)$goal['baseline_value'] : 0,
                    'target_value' => isset($goal['target_value']) ? (float)$goal['target_value'] : 0,
                    'current_value' => isset($goal['current_value']) ? (float)$goal['current_value'] : 0,
                ];
            }

            // Assessments (dimension-level)
            $assessments = $this->academyModel->getIDPAssessments($plan['id']);
            foreach ($assessments as $assess) {
                $data['assessments'][] = [
                    'dimension' => isset($assess['dimension_name']) ? $assess['dimension_name'] : '',
                    'score' => isset($assess['score']) ? (float)$assess['score'] : 0,
                    'max_score' => isset($assess['max_score']) ? (float)$assess['max_score'] : 5,
                    'assessment_type' => isset($assess['assessment_type']) ? $assess['assessment_type'] : '',
                    'assessed_at' => isset($assess['assessment_date']) ? $assess['assessment_date'] : '',
                ];
            }

            // Feedbacks
            $feedbacks = $this->academyModel->getIDPFeedbacks($plan['id']);
            foreach ($feedbacks as $fb) {
                $data['feedbacks'][] = [
                    'type'             => $fb['feedback_type'] ?? '',
                    'content'          => $fb['content'] ?? '',
                    'strengths'        => $fb['strengths'] ?? '',
                    'areas_to_improve' => $fb['areas_to_improve'] ?? '',
                    'action_items'     => $fb['action_items'] ?? '',
                    'goal'             => $fb['goal_title'] ?? '',
                    'feedback_date'    => $fb['feedback_date'] ?? '',
                ];
            }
        }

        // Training session type breakdown (3 เดือนล่าสุด)
        $threeMonthsAgo = date('Y-m-d', strtotime('-3 months'));
        $sessionTypes = $this->db->query(
            "SELECT s.training_type, COUNT(*) as count
             FROM academy_training_attendance a
             JOIN academy_training_sessions s ON s.id = a.session_id
             WHERE a.player_id = ? AND s.academy_id = ?
               AND s.training_date >= ?
             GROUP BY s.training_type
             ORDER BY count DESC",
            [$player_id, $academy_id, $threeMonthsAgo]
        )->getResultArray();
        $data['session_types'] = $sessionTypes;

        // Dimension scores from template comparison
        $templates = $this->academyModel->getPlayerDistinctTemplates($player_id, $academy_id);
        if (!empty($templates)) {
            $templateId = $templates[0]['template_id'];
            $compareData = $this->academyModel->getPlayerDimensionScoresByTemplate($player_id, $academy_id, $templateId);
            foreach ($compareData as $dim) {
                $data['dimension_scores'][] = [
                    'dimension' => isset($dim['dimension_name']) ? $dim['dimension_name'] : '',
                    'baseline' => isset($dim['baseline_score']) ? (float)$dim['baseline_score'] : 0,
                    'current' => isset($dim['current_score']) ? (float)$dim['current_score'] : 0,
                ];
            }
        }

        return $data;
    }

    /**
     * System prompt สำหรับ AI วิเคราะห์ IDP
     */
    private function getAISystemPrompt()
    {
        // return 'คุณคือผู้เชี่ยวชาญด้านการพัฒนานักฟุตบอลเยาวชน (Youth Football Development Specialist) '
        //     . 'ที่มีความรู้ลึกซึ้งด้านเทคนิค แท็คติก กายภาพ จิตใจ และพัฒนาการตามช่วงอายุ (Long-Term Athlete Development — LTAD) '
        //     . 'ต้องอ้างอิงช่วง LTAD ของนักเตะทุกครั้งที่ให้คำแนะนำ '
        //     . 'วิเคราะห์ข้อมูลนักเตะและให้คำแนะนำเชิงปฏิบัติที่โค้ชสามารถนำไปใช้ได้จริง '
        //     . 'ตอบเป็นภาษาไทย ในรูปแบบ Markdown ตามหัวข้อต่อไปนี้เท่านั้น:' . "\n\n"
        //     . '## 1. สรุปภาพรวม' . "\n"
        //     . 'สรุปสถานะการพัฒนา 3-4 ประโยค ระบุระดับ (ดีมาก/ดี/ปานกลาง/ต้องพัฒนา) พร้อมเหตุผล และระบุช่วง LTAD พร้อมความหมายของช่วงอายุนี้' . "\n\n"
        //     . '## 2. จุดแข็ง' . "\n"
        //     . 'ระบุ 2-3 ด้านที่โดดเด่น อ้างอิงคะแนนจริง (baseline → current) และเปรียบเทียบกับมาตรฐานที่เหมาะสมสำหรับช่วงอายุนี้' . "\n\n"
        //     . '## 3. จุดที่ต้องพัฒนา' . "\n"
        //     . 'ระบุ 2-3 ด้านที่ต้องปรับปรุงมากที่สุด อ้างอิงคะแนนและ gap จากเป้าหมาย พร้อมอธิบายว่าสำคัญอย่างไรสำหรับช่วงอายุนี้' . "\n\n"
        //     . '## 4. แนวโน้มการพัฒนา' . "\n"
        //     . 'วิเคราะห์ว่า baseline → current เปลี่ยนแปลงอย่างไร อัตราเข้าฝึกและประเภทการฝึก (ปกติ/ฟิตเนส/แมตช์/พิเศษ) ส่งผลต่อพัฒนาการหรือไม่' . "\n\n"
        //     . '## 5. แผนฝึกซ้อมที่แนะนำ' . "\n"
        //     . 'ให้ drill/exercise เฉพาะทาง 3-5 รายการ ระบุชื่อ วัตถุประสงค์ และความถี่ที่แนะนำ '
        //     . 'เหมาะกับอายุ ตำแหน่ง และเน้นชดเชยประเภทการฝึกที่ยังน้อยหรือจุดอ่อนที่พบ' . "\n\n"
        //     . '## 6. เป้าหมาย 3 เดือนถัดไป' . "\n"
        //     . 'แนะนำ 2-3 เป้าหมายที่วัดได้ (SMART) พร้อมค่าตัวเลขเป้าหมายที่เหมาะสมกับช่วงอายุนี้' . "\n\n"
        //     . 'กฎ: ใช้เฉพาะข้อมูลที่ได้รับ ห้ามสมมติ ถ้าข้อมูลไม่เพียงพอให้ระบุว่า "ข้อมูลไม่เพียงพอ" '
        //     . 'คำแนะนำต้องเฉพาะเจาะจงกับอายุ ตำแหน่ง และข้อมูลจริงของนักเตะคนนี้เท่านั้น';

        return 'คุณคือหัวหน้าฝ่ายพัฒนาเยาวชน (Head of Youth Development) '
            . 'ผู้เชี่ยวชาญด้าน Long-Term Athlete Development (LTAD), Football Data Analytics, Talent Identification และ Performance Optimization '
            . 'มีความสามารถวิเคราะห์เชิงลึกทั้งด้าน Technical, Tactical, Physical, Psychological ตามช่วงวัย '
            . 'ทุกคำแนะนำต้องอ้างอิง LTAD Stage และ Window of Opportunity อย่างชัดเจน '
            . 'ใช้หลัก Evidence-Based และ Causal Analysis เท่านั้น '
            . 'ห้ามคาดเดา ห้ามสมมติข้อมูลที่ไม่มีโดยเด็ดขาด '
            . 'คำตอบต้อง actionable และโค้ชสามารถนำไปใช้ได้ทันที '
            . 'ตอบเป็นภาษาไทย ในรูปแบบ Markdown ตามหัวข้อต่อไปนี้เท่านั้น:' . "\n\n"

            . '## 1. การวิเคราะห์สถานะการพัฒนา (Developmental Status)' . "\n"
            . '- Executive Summary 3-4 ประโยค' . "\n"
            . '- จัดระดับ: Elite / Competitive / Developmental / Emerging (ต้องมีเหตุผลจากข้อมูล)' . "\n"
            . '- ระบุ LTAD Stage + อธิบายความสำคัญของช่วงวัย (เช่น PHV, Neuroplasticity, Motivation)' . "\n"
            . '- หากข้อมูลไม่ครบ ให้ระบุ: "ข้อมูลไม่เพียงพอสำหรับการประเมินเชิงลึก"' . "\n\n"

            . '## 2. ดัชนีความพร้อมโดยรวม (Performance Readiness Score)' . "\n"
            . '- ประเมินความพร้อม 4 ด้าน: Technical / Tactical / Physical / Psychological' . "\n"
            . '- ให้คะแนนเป็นช่วง (Low / Moderate / High) เท่านั้น (ห้ามสร้างตัวเลข)' . "\n"
            . '- อธิบายเหตุผลของแต่ละด้านจากข้อมูลที่มี' . "\n"
            . '- สรุป Overall Readiness พร้อม Insight หลัก' . "\n\n"

            . '## 3. การวิเคราะห์จุดแข็งเชิงเปรียบเทียบ (Comparative Strengths)' . "\n"
            . '- ระบุ 2-3 จุดแข็งหลัก' . "\n"
            . '- อ้างอิง Baseline → Current' . "\n"
            . '- เทียบ Benchmark ตามตำแหน่งและอายุ' . "\n"
            . '- อธิบาย Impact ต่อเกม (Match Performance)' . "\n\n"

            . '## 4. ดัชนีช่องว่างสมรรถภาพ (Critical Gaps & Severity)' . "\n"
            . '- ระบุ 2-3 Gap เท่านั้น' . "\n"
            . '- วิเคราะห์ Current vs Positional Standard' . "\n"
            . '- ให้ระดับความรุนแรง: High / Medium / Low' . "\n"
            . '- เชื่อมกับ LTAD Window of Opportunity ว่าทำไมต้องรีบพัฒนา' . "\n"
            . '- ระบุความเสี่ยงหากไม่แก้ (Performance / Injury / Development Plateau)' . "\n\n"

            . '## 5. ลำดับความสำคัญในการพัฒนา (Priority Development Roadmap)' . "\n"
            . '- จัดลำดับ Gap จากสำคัญที่สุด → รองลงมา' . "\n"
            . '- อธิบายเหตุผลเชิงกลยุทธ์ (Why now?)' . "\n"
            . '- ระบุ Quick Win vs Long-term Development' . "\n\n"

            . '## 6. วิเคราะห์ความสัมพันธ์ของการฝึก (Training Correlation Analysis)' . "\n"
            . '- วิเคราะห์ Trend ของ Performance' . "\n"
            . '- เชื่อมโยง Training Type (Team / Individual / Fitness / Match) → ผลลัพธ์' . "\n"
            . '- วิเคราะห์ Attendance / Consistency' . "\n"
            . '- ระบุ Key Driver (ปัจจัยหลัก) vs Supporting Factors' . "\n\n"

            . '## 7. แผนการฝึกซ้อมเชิงยุทธศาสตร์ (Strategic Training Prescriptions)' . "\n"
            . '- เสนอ 3-5 Drills เท่านั้น' . "\n"
            . '- รูปแบบ: [ชื่อ Drill] | [วัตถุประสงค์] | [วิธีฝึกย่อ] | [ความถี่/Volume]' . "\n"
            . '- ต้องแก้ Critical Gaps โดยตรง' . "\n"
            . '- เหมาะกับอายุ + ตำแหน่ง + LTAD Stage' . "\n"
            . '- หลีกเลี่ยงการฝึกนอกช่วงพัฒนาการ' . "\n\n"

            . '## 8. เป้าหมายระยะสั้น (3-Month SMART Goals)' . "\n"
            . '- ระบุ 2-3 เป้าหมายเท่านั้น' . "\n"
            . '- ต้องเป็น SMART Goal' . "\n"
            . '- ใช้ Baseline → Target จากข้อมูลจริงเท่านั้น' . "\n"
            . '- หากไม่มีข้อมูล ห้ามสร้าง และให้ระบุว่า "ข้อมูลไม่เพียงพอ"' . "\n\n"

            . 'กฎสำคัญ:' . "\n"
            . '- ห้ามสมมติข้อมูลหรือสร้างตัวเลขเองโดยเด็ดขาด' . "\n"
            . '- ใช้เฉพาะข้อมูลที่ได้รับ' . "\n"
            . '- ทุก Insight ต้องมีเหตุผลรองรับ (Evidence + Logic)' . "\n"
            . '- ทุกคำแนะนำต้องเชื่อมโยงกับ LTAD และช่วงวัย' . "\n"
            . '- เน้นคุณภาพของ Insight มากกว่าปริมาณ';
    }

    /**
     * จัดรูปแบบข้อมูลเป็น user message แบบ structured text (ดีกว่า raw JSON)
     */
    private function formatAIUserMessage($inputData)
    {
        $p = $inputData['player'];
        $att = $inputData['attendance'];

        $msg = "=== ข้อมูลนักเตะ ===" . "\n";
        $msg .= "ชื่อ: " . ($p['name'] ?: 'ไม่ระบุ') . ($p['nickname'] ? " (\"" . $p['nickname'] . "\")" : '') . "\n";
        $msg .= "อายุ: " . ($p['age'] ? $p['age'] . ' ปี' : 'ไม่ระบุ') . "\n";
        $msg .= "ตำแหน่ง: " . ($p['position'] ?: 'ไม่ระบุ') . "\n";
        $msg .= "ส่วนสูง: " . ($p['height_cm'] ? $p['height_cm'] . ' ซม.' : 'ไม่ระบุ') . "\n";
        $msg .= "น้ำหนัก: " . ($p['weight_kg'] ? $p['weight_kg'] . ' กก.' : 'ไม่ระบุ') . "\n";
        $msg .= "BMI: " . ($p['bmi'] ?: 'ไม่ระบุ') . "\n";
        $msg .= "เท้าถนัด: " . ($p['dominant_foot'] ?: 'ไม่ระบุ') . "\n";

        $msg .= "\n=== การเข้าร่วมฝึกซ้อม ===" . "\n";
        $msg .= "ครั้งทั้งหมด: {$att['total']} | มา: {$att['present']} | ขาด: {$att['absent']} | สาย: {$att['late']} | อัตราเข้าร่วม: {$att['attendance_rate']}%\n";

        if (!empty($inputData['academy_assessments'])) {
            $msg .= "\n=== ผลการทดสอบทักษะ ===" . "\n";
            foreach ($inputData['academy_assessments'] as $a) {
                $msg .= "- {$a['name']}: {$a['count']} ครั้ง";
                if ($a['avg_percent'] !== null) $msg .= " (เฉลี่ย {$a['avg_percent']}%)";
                $msg .= "\n";
            }
        }

        if (!empty($inputData['dimension_scores'])) {
            $msg .= "\n=== คะแนนรายด้าน (Baseline → ปัจจุบัน) ===" . "\n";
            foreach ($inputData['dimension_scores'] as $d) {
                $diff = round($d['current'] - $d['baseline'], 2);
                $arrow = $diff > 0 ? "↑+{$diff}" : ($diff < 0 ? "↓{$diff}" : "→ คงที่");
                $msg .= "- {$d['dimension']}: {$d['baseline']} → {$d['current']} ({$arrow})\n";
            }
        }

        if (!empty($inputData['goals'])) {
            $msg .= "\n=== เป้าหมาย IDP ===" . "\n";
            foreach ($inputData['goals'] as $g) {
                $pct = $g['target_value'] > 0 ? round(($g['current_value'] / $g['target_value']) * 100) : 0;
                $msg .= "- [{$g['dimension']}] {$g['title']}: ";
                $msg .= "baseline={$g['baseline_value']}, เป้า={$g['target_value']}, ปัจจุบัน={$g['current_value']} ({$pct}%)\n";
            }
        }

        if (!empty($inputData['session_types'])) {
            $typeLabels = ['regular' => 'ปกติ', 'special' => 'พิเศษ', 'fitness' => 'ฟิตเนส', 'match' => 'แมตช์'];
            $msg .= "\n=== ประเภทการฝึกซ้อม (3 เดือนล่าสุด) ===" . "\n";
            foreach ($inputData['session_types'] as $t) {
                $label = $typeLabels[$t['training_type']] ?? $t['training_type'];
                $msg .= "- {$label}: {$t['count']} ครั้ง\n";
            }
        }

        if (!empty($inputData['feedbacks'])) {
            $msg .= "\n=== Feedback จากโค้ช ===" . "\n";
            foreach (array_slice($inputData['feedbacks'], 0, 5) as $fb) {
                $date = !empty($fb['feedback_date']) ? " ({$fb['feedback_date']})" : '';
                $goal = !empty($fb['goal']) ? " [เป้าหมาย: {$fb['goal']}]" : '';
                $msg .= "- [{$fb['type']}]{$goal}{$date}\n";
                if (!empty($fb['content']))          $msg .= "  เนื้อหา: {$fb['content']}\n";
                if (!empty($fb['strengths']))        $msg .= "  จุดแข็ง: {$fb['strengths']}\n";
                if (!empty($fb['areas_to_improve'])) $msg .= "  พัฒนา: {$fb['areas_to_improve']}\n";
                if (!empty($fb['action_items']))     $msg .= "  Action: {$fb['action_items']}\n";
            }
        }

        if (!empty($inputData['plans'])) {
            $msg .= "\n=== แผน IDP ===" . "\n";
            foreach ($inputData['plans'] as $plan) {
                $msg .= "- {$plan['title']} ({$plan['status']}) template: {$plan['template_name']}\n";
            }
        }

        return $msg;
    }

    /**
     * ดึงสรุปสั้นจากผลวิเคราะห์ (หัวข้อ "สรุปภาพรวม" ย่อหน้าแรก)
     */
    private function extractSummary($text)
    {
        // Try to find content after "## 1. สรุปภาพรวม"
        $pattern = '/##\s*1\.\s*สรุปภาพรวม\s*\n+(.*?)(\n##|\z)/su';
        if (preg_match($pattern, $text, $matches)) {
            $summary = trim($matches[1]);
            if (strlen($summary) > 490) {
                $summary = mb_substr($summary, 0, 490) . '...';
            }
            return $summary;
        }
        // Fallback: first 490 chars
        $summary = trim(preg_replace('/^#.*$/m', '', $text));
        if (strlen($summary) > 490) {
            $summary = mb_substr($summary, 0, 490) . '...';
        }
        return $summary;
    }

    /**
     * คำนวณอายุจากวันเกิด
     */
    private function calculateAge($birthday)
    {
        if (empty($birthday)) return null;
        $birthDate = new \DateTime($birthday);
        $now = new \DateTime();
        return $now->diff($birthDate)->y;
    }
}
