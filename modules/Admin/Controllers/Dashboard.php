<?php

namespace Modules\Admin\Controllers;

use App\Controllers\BaseController;
use \Hermawan\DataTables\DataTable;
use Modules\Admin\Models\ReportGuestModel;
use Modules\Player\Models\PlayerModel;

class Dashboard extends BaseController
{
    protected $reportGuestModel;
    protected $playerModel;

    public function __construct()
    {
        $this->reportGuestModel = new ReportGuestModel();
        $this->playerModel = new PlayerModel();
    }

    public function index()
    {
        $data = [
            'title' => lang('Common.admin_dashboard_title'),
        ];

        // เพิ่มข้อมูล ReportGuest สำหรับ admin role หรือ super admin
        if (hasRole('admin') || isSuperAdmin()) {
            try {
                // ดึงข้อมูลนักเตะที่มีคะแนนเฉลี่ยสูงสุด
                $data['top_players'] = $this->reportGuestModel->getAverageScorePlayer(5);

                // ดึงข้อมูลการกระจายตำแหน่งนักเตะ
                $data['player_positions'] = $this->reportGuestModel->getPlayerGroupByPosition();

                // ดึงข้อมูลตำแหน่งทั้งหมด
                $data['all_positions'] = $this->playerModel->player_position([1,2,3,4]);

            } catch (\Exception $e) {
                // หากเกิดข้อผิดพลาด ให้ส่งข้อมูลเปล่า
                $data['top_players'] = [];
                $data['player_positions'] = [];
                $data['all_positions'] = [];

                log_message('error', 'Dashboard ReportGuest data error: ' . $e->getMessage());
            }
        }

        return view('Modules\Admin\Views\dashboard', $data);
    }

    /**
     * AJAX method for DataTable in dashboard
     */
    public function getPlayersAjax()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        try {
            $result = $this->reportGuestModel->getPlayersList_ajax();
            return $result;
        } catch (\Exception $e) {
            log_message('error', 'Dashboard getPlayersAjax error: ' . $e->getMessage());
            return $this->response->setJSON([
                'draw' => intval($this->request->getPost('draw')),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => []
            ]);
        }
    }
}