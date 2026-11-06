<?php

namespace Modules\Admin\Controllers;

use App\Controllers\BaseController;
use \Hermawan\DataTables\DataTable;
use Modules\Admin\Models\TeamModel;


class Team extends BaseController
{
    protected $teamModel;
    public function __construct()
    {
        $this->teamModel = new TeamModel();
    }

    public function index()
    {
        return view('Modules\Admin\Views\Team\index');
    }

    public function ajax_teams()
    {
        $result = $this->teamModel->get_Teams();
        return $result;
    }

    public function manage($id = '')
    {
        $data = [];
        if ($id) {
            $data['team'] = $this->teamModel->get_Teams($id);
        }
        return view('Modules\Admin\Views\Team\manage', $data);
    }

    public function saveTeam() {
        $input = $this->request->getPost();
        // print_r($input);
        // die;
        $this->teamModel->saveTeam($input);
        return redirect()->to('/admin/team/')->with('success', 'บันทึกข้อมูลสำเร็จ');
    }

    function deletedTeam() {
        $id = $this->request->getPost('team_id');
        $result = $this->teamModel->deleteTeam($id);
        return $result;
    }

    /**
     * Team Requests Management Page
     */
    public function teamRequests()
    {
        return view('Modules\Admin\Views\Team\requests');
    }

    /**
     * AJAX - Get all pending team join requests
     */
    public function ajax_team_requests()
    {
        $result = $this->teamModel->getAllPendingRequests();
        return $result;
    }

    /**
     * Approve a team join request
     */
    public function approveRequest($id)
    {
        $result = $this->teamModel->updateRequestStatus($id, 'approved');
        if ($result) {
            return $this->response->setJSON(['status' => true, 'message' => 'อนุมัติคำขอเรียบร้อยแล้ว']);
        }
        return $this->response->setJSON(['status' => false, 'message' => 'เกิดข้อผิดพลาด']);
    }

    /**
     * Reject a team join request
     */
    public function rejectRequest($id)
    {
        $result = $this->teamModel->updateRequestStatus($id, 'rejected');
        if ($result) {
            return $this->response->setJSON(['status' => true, 'message' => 'ปฏิเสธคำขอเรียบร้อยแล้ว']);
        }
        return $this->response->setJSON(['status' => false, 'message' => 'เกิดข้อผิดพลาด']);
    }

    /**
     * Team Verify Management Page (ตรวจสอบทีม)
     */
    public function teamVerify()
    {
        return view('Modules\Admin\Views\Team\verify');
    }

    /**
     * AJAX - Get all teams for verification (include inactive)
     */
    public function ajax_teams_verify()
    {
        $result = $this->teamModel->get_Teams_Verify();
        return $result;
    }

    /**
     * View team details for verification
     */
    public function manageVerify($id)
    {
        $data = [];
        $data['team'] = $this->teamModel->get_Teams($id);
        $data['mode'] = 'verify';
        return view('Modules\Admin\Views\Team\manage', $data);
    }

    /**
     * Permanently delete team (set related player/coach team_id = 0)
     */
    public function permanentDeleteTeam()
    {
        $id = $this->request->getPost('team_id');
        $result = $this->teamModel->permanentDeleteTeam($id);
        return $this->response->setJSON($result);
    }
}
