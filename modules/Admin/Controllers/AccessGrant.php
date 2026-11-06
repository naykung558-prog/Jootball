<?php

namespace Modules\Admin\Controllers;

use App\Controllers\BaseController;
use Modules\Admin\Models\AccessGrantModel;

/**
 * ให้/ถอนสิทธิ์แบบกดครั้งเดียวจากหน้า admin/users และ admin/coach
 * ตรรกะการเขียนข้อมูลอยู่ใน AccessGrantModel ทั้งหมด
 */
class AccessGrant extends BaseController
{
    protected $accessGrantModel;

    public function __construct()
    {
        $this->accessGrantModel = new AccessGrantModel();
    }

    // =====================================================
    // ค้นหา
    // =====================================================

    public function searchAcademy()
    {
        $q = trim((string) $this->request->getGet('q'));
        return $this->response->setJSON([
            'status' => true,
            'data'   => $this->accessGrantModel->searchAcademies($q),
        ]);
    }

    public function searchTeam()
    {
        $q = trim((string) $this->request->getGet('q'));
        return $this->response->setJSON([
            'status' => true,
            'data'   => $this->accessGrantModel->searchTeams($q),
        ]);
    }

    // =====================================================
    // สิทธิ์ปัจจุบัน
    // =====================================================

    public function userAcademies($userId)
    {
        return $this->response->setJSON([
            'status' => true,
            'data'   => $this->accessGrantModel->userAcademies((int) $userId),
        ]);
    }

    public function coachTeams($coachId)
    {
        return $this->response->setJSON([
            'status' => true,
            'data'   => $this->accessGrantModel->coachTeams((int) $coachId),
        ]);
    }

    // =====================================================
    // ให้/ถอนสิทธิ์
    // =====================================================

    public function grantAcademy()
    {
        $userId    = (int) $this->request->getPost('user_id');
        $academyId = (int) $this->request->getPost('academy_id');
        $role      = (string) ($this->request->getPost('role') ?: 'admin');

        if (!$userId || !$academyId) {
            return $this->response->setJSON(['status' => false, 'message' => lang('Common.ag_incomplete')]);
        }

        $result = $this->accessGrantModel->grantAcademyToUser($userId, $academyId, $role);
        return $this->response->setJSON(['status' => $result['success'], 'message' => $result['message']]);
    }

    public function revokeAcademy()
    {
        $userId    = (int) $this->request->getPost('user_id');
        $academyId = (int) $this->request->getPost('academy_id');

        if (!$userId || !$academyId) {
            return $this->response->setJSON(['status' => false, 'message' => lang('Common.ag_incomplete')]);
        }

        $result = $this->accessGrantModel->revokeAcademyFromUser($userId, $academyId);
        return $this->response->setJSON(['status' => $result['success'], 'message' => $result['message']]);
    }

    public function grantTeam()
    {
        $coachId = (int) $this->request->getPost('coach_id');
        $teamId  = (int) $this->request->getPost('team_id');

        if (!$coachId || !$teamId) {
            return $this->response->setJSON(['status' => false, 'message' => lang('Common.ag_incomplete')]);
        }

        $result = $this->accessGrantModel->grantTeamToCoach($coachId, $teamId);
        return $this->response->setJSON([
            'status'     => $result['success'],
            'message'    => $result['message'],
            'no_academy' => $result['no_academy'] ?? false,
        ]);
    }

    public function revokeTeam()
    {
        $coachId = (int) $this->request->getPost('coach_id');
        $teamId  = (int) $this->request->getPost('team_id');

        if (!$coachId || !$teamId) {
            return $this->response->setJSON(['status' => false, 'message' => lang('Common.ag_incomplete')]);
        }

        $result = $this->accessGrantModel->revokeTeamFromCoach($coachId, $teamId);
        return $this->response->setJSON(['status' => $result['success'], 'message' => $result['message']]);
    }
}
