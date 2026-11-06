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

trait AcademyAssessmentTrait
{
    // =====================================================
    // Assessments
    // =====================================================

    /**
     * รายการทดสอบ
     */
    public function assessments($academy_id)
    {
        $access = $this->ensureAcademyAccess($academy_id);
        if ($access['redirect']) return $access['redirect'];
        $academy_id = $access['academy_id'];
        $data = $access['data'];

        $filters = [
            'type'     => $this->request->getGet('type'),
            'group_id' => $this->request->getGet('group_id'),
        ];

        $data['assessments'] = $this->academyModel->getAssessments($academy_id, $filters);
        $data['groups'] = $this->academyModel->getTrainingGroups($academy_id);
        $data['filters'] = $filters;
        $data['title'] = lang('Common.assessments');

        return view('Modules\Academy\Views\assessments', $data);
    }

    /**
     * ฟอร์มสร้าง/แก้ไขทดสอบ
     */
    public function assessmentManage($academy_id, $assessment_id = null)
    {
        $access = $this->ensurePermAccess($academy_id, 'perm_create_assessment');
        if ($access['redirect']) return $access['redirect'];
        $academy_id = $access['academy_id'];
        $data = $access['data'];

        $data['assessment'] = $assessment_id ? $this->academyModel->getAssessment($assessment_id) : null;
        $data['criteria'] = $assessment_id ? $this->academyModel->getAssessmentCriteria($assessment_id) : [];
        $data['groups'] = $this->academyModel->getTrainingGroups($academy_id);
        $data['canManage'] = true;
        $data['title'] = $assessment_id ? lang('Common.assessment_edit') : lang('Common.assessment_create');

        return view('Modules\Academy\Views\assessment_manage', $data);
    }

    /**
     * บันทึกทดสอบ + criteria
     */
    public function assessmentSave()
    {
        $academy_id = $this->request->getPost('academy_id');
        if (!$this->hasPermission($academy_id, 'perm_create_assessment')) {
            return redirect()->back()->with('error', lang('Common.academy_no_permission'));
        }

        // ดึง coach_id
        $created_by = null;
        if (session()->get('user_type') === 'coach') {
            $coachData = session()->get('coach_data');
            $created_by = $coachData['id'] ?? null;
        }

        $data = [
            'id'              => $this->request->getPost('id') ?: null,
            'academy_id'      => $academy_id,
            'group_id'        => $this->request->getPost('group_id'),
            'name'            => $this->request->getPost('name'),
            'description'     => $this->request->getPost('description'),
            'assessment_date' => $this->request->getPost('assessment_date'),
            'assessment_type' => $this->request->getPost('assessment_type') ?: 'skill',
            'created_by'      => $created_by,
        ];

        $assessmentId = $this->academyModel->saveAssessment($data);

        // บันทึก criteria
        $criteriaNames = $this->request->getPost('criteria_name') ?: [];
        $criteriaDescs = $this->request->getPost('criteria_description') ?: [];
        $criteriaMaxScores = $this->request->getPost('criteria_max_score') ?: [];

        $criteriaList = [];
        foreach ($criteriaNames as $i => $name) {
            if (trim($name) !== '') {
                $criteriaList[] = [
                    'name'        => $name,
                    'description' => $criteriaDescs[$i] ?? null,
                    'max_score'   => $criteriaMaxScores[$i] ?? 100,
                ];
            }
        }

        if (!empty($criteriaList)) {
            $this->academyModel->saveAssessmentCriteria($assessmentId, $criteriaList);
        }

        return redirect()->to('/academy/assessment/detail/' . $academy_id . '/' . $assessmentId)
            ->with('success', lang('Common.assessment_saved'));
    }

    /**
     * รายละเอียดทดสอบ + ลงคะแนน
     */
    public function assessmentDetail($academy_id, $assessment_id)
    {
        $access = $this->ensureAcademyAccess($academy_id);
        if ($access['redirect']) return $access['redirect'];
        $academy_id = $access['academy_id'];
        $data = $access['data'];

        $assessment = $this->academyModel->getAssessment($assessment_id);
        if (!$assessment) {
            return redirect()->to('/academy/assessments/' . $academy_id)->with('error', lang('Common.academy_not_found'));
        }

        $data['assessment'] = $assessment;
        $data['criteria'] = $this->academyModel->getAssessmentCriteria($assessment_id);
        $data['results'] = $this->academyModel->getAssessmentResults($assessment_id);

        // ดึงนักเตะที่ต้องทดสอบ (จากกลุ่มหรือทั้ง Academy)
        if (!empty($assessment['group_id'])) {
            $data['players'] = $this->academyModel->getGroupPlayers($assessment['group_id']);
        } else {
            $data['players'] = $this->academyModel->getAvailablePlayers($academy_id);
        }

        $data['title'] = $assessment['name'];

        return view('Modules\Academy\Views\assessment_detail', $data);
    }

    /**
     * บันทึกผลทดสอบ (AJAX)
     */
    public function saveAssessmentResults()
    {
        $academy_id = $this->getInput('academy_id');
        $err = $this->ensureAjaxPermAccess($academy_id, 'perm_save_assessment_result');
        if ($err) return $err;

        $assessment_id = $this->getInput('assessment_id');
        $results = $this->getInput('results') ?: [];

        $assessed_by = null;
        if (session()->get('user_type') === 'coach') {
            $coachData = session()->get('coach_data');
            $assessed_by = $coachData['id'] ?? null;
        }

        $this->academyModel->saveAssessmentResults($assessment_id, $results, $assessed_by);

        return $this->response->setJSON(['status' => 'success', 'message' => lang('Common.assessment_results_saved')]);
    }

    /**
     * ลบทดสอบ
     */
    public function assessmentDelete()
    {
        $academy_id = $this->getInput('academy_id');
        $err = $this->ensureAjaxPermAccess($academy_id, 'perm_create_assessment');
        if ($err) return $err;

        $this->academyModel->deleteAssessment($this->getInput('assessment_id'));
        return $this->response->setJSON(['status' => 'success', 'message' => lang('Common.assessment_deleted')]);
    }
}
