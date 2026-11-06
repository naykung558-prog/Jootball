<?php

namespace Modules\Admin\Controllers;

use App\Controllers\BaseController;
use \Hermawan\DataTables\DataTable;
use Modules\Admin\Models\AcademyModel;


class Academy extends BaseController
{
    protected $academyModel;

    public function __construct()
    {
        $this->academyModel = new AcademyModel();
    }

    public function index()
    {
        return view('Modules\Admin\Views\Academy\index');
    }

    public function ajax_academies()
    {
        $result = $this->academyModel->getAcademies();
        return $result;
    }

    public function manage($id = '')
    {
        $data = [];
        if ($id) {
            $data['academy'] = $this->academyModel->getAcademy($id);
        }
        return view('Modules\Admin\Views\Academy\manage', $data);
    }

    public function detail($id)
    {
        $data = [];
        $data['academy'] = $this->academyModel->getAcademy($id);
        $data['teams'] = $this->academyModel->getAcademyTeams($id);
        $data['members'] = $this->academyModel->getAcademyMembers($id);
        return view('Modules\Admin\Views\Academy\detail', $data);
    }

    public function save()
    {
        $input = $this->request->getPost();

        // Handle logo file upload
        $file = $this->request->getFile('logo');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            if (!in_array($file->getMimeType(), $validTypes)) {
                return redirect()->back()->withInput()->with('error', lang('Common.invalid_file_type'));
            }

            $uploadPath = FCPATH . 'public/uploads/academy/';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            $newName = 'academy_' . time() . '_' . bin2hex(random_bytes(8)) . '.' . $file->getExtension();
            if ($file->move($uploadPath, $newName)) {
                $input['logo'] = 'public/uploads/academy/' . $newName;
            }
        }

        $this->academyModel->saveAcademy($input);
        return redirect()->to('/admin/academy/')->with('success', lang('Common.save_success'));
    }

    public function toggleActive()
    {
        $id = $this->request->getPost('id');
        $result = $this->academyModel->toggleActive($id);
        return $this->response->setJSON(['success' => true, 'new_status' => $result]);
    }

    public function deleteAcademy()
    {
        $id = $this->request->getPost('academy_id');
        $result = $this->academyModel->deleteAcademy($id);
        return $this->response->setJSON($result);
    }

    // =====================================================
    // Merge Teams into Academy
    // =====================================================

    public function ajaxAvailableTeams()
    {
        return $this->academyModel->getAvailableTeamsForMerge();
    }

    public function ajaxTeamDetail()
    {
        $teamId = $this->request->getPost('team_id');
        $detail = $this->academyModel->getTeamDetailForMerge($teamId);
        if (!$detail) {
            return $this->response->setJSON(['status' => false, 'message' => lang('Common.data_not_found')]);
        }
        return $this->response->setJSON(['status' => true, 'data' => $detail]);
    }

    public function mergeTeams()
    {
        $data['academies'] = $this->academyModel->searchActiveAcademies();
        return view('Modules\Admin\Views\Academy\merge_teams', $data);
    }

    public function ajaxAcademiesList()
    {
        $search = $this->request->getPost('search') ?? '';
        $academies = $this->academyModel->searchActiveAcademies($search);
        return $this->response->setJSON(['results' => $academies]);
    }

    public function mergePreview()
    {
        $teamIds = $this->request->getPost('team_ids');
        $targetType = $this->request->getPost('target_type');
        $academyId = $this->request->getPost('academy_id');

        if (empty($teamIds) || !is_array($teamIds)) {
            return $this->response->setJSON(['status' => false, 'message' => lang('Common.merge_select_min_teams')]);
        }

        $preview = $this->academyModel->getMergePreview($teamIds, $targetType, $academyId);
        return $this->response->setJSON(['status' => true, 'data' => $preview]);
    }

    public function mergeExecute()
    {
        $teamIds = $this->request->getPost('team_ids');
        $targetType = $this->request->getPost('target_type');
        $academyId = $this->request->getPost('academy_id');
        $name = $this->request->getPost('name') ?? '';
        $nameEn = $this->request->getPost('name_en') ?? '';
        $academyData = [
            'name' => $name,
            'name_en' => $nameEn,
            'logo' => $this->request->getPost('logo') ?? '',
            'contact_name' => $this->request->getPost('contact_name') ?? '',
            'contact_phone' => $this->request->getPost('contact_phone') ?? '',
            'contact_email' => $this->request->getPost('contact_email') ?? '',
            'contact_line' => $this->request->getPost('contact_line') ?? '',
            'latitude' => $this->request->getPost('latitude') ?? '',
            'longitude' => $this->request->getPost('longitude') ?? '',
        ];

        if (empty($teamIds) || !is_array($teamIds)) {
            return $this->response->setJSON(['status' => false, 'message' => lang('Common.merge_select_min_teams')]);
        }

        if ($targetType === 'new' && empty($name)) {
            return $this->response->setJSON(['status' => false, 'message' => lang('Common.merge_academy_name_required')]);
        }

        if ($targetType === 'existing' && empty($academyId)) {
            return $this->response->setJSON(['status' => false, 'message' => lang('Common.merge_select_academy')]);
        }

        // ทีมที่อยู่ใน academy อยู่แล้ว เพิ่มเข้าได้เฉพาะอคาเดมีที่มีอยู่ (สร้างใหม่ไม่ได้)
        if ($targetType === 'new' && !empty($this->academyModel->getTeamsAlreadyInAcademy($teamIds))) {
            return $this->response->setJSON(['status' => false, 'message' => lang('Common.merge_in_academy_existing_only')]);
        }

        $result = $this->academyModel->executeMerge($teamIds, $targetType, $academyId, $academyData);
        return $this->response->setJSON($result);
    }
}
