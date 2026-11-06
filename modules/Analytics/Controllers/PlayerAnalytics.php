<?php

namespace Modules\Analytics\Controllers;

use App\Controllers\BaseController;
use Modules\Analytics\Models\PlayerAnalyticsModel;

class PlayerAnalytics extends BaseController
{
    protected $playerModel;
    protected $db;

    public function __construct()
    {
        $this->playerModel = new PlayerAnalyticsModel();
        $this->db          = \Config\Database::connect();
    }

    /**
     * หน้าสถิติผู้เล่น (DataTable)
     */
    public function stats()
    {
        $teams = $this->db->table('team')
                          ->select('id, name as team_name')
                          ->orderBy('name', 'ASC')
                          ->get()->getResultArray();

        return view('template/admin_layout', [
            'title'   => 'สถิติผู้เล่น',
            'content' => view('Modules\Analytics\Views\player_stats', [
                'teams' => $teams,
            ]),
        ]);
    }

    /**
     * AJAX JSON — สถิติรวมของผู้เล่น 1 คน (สำหรับหน้า compare)
     */
    public function statsJson($playerId)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $totalStat = $this->playerModel->getPlayerTotalStats((int)$playerId);
        if (!$totalStat) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ไม่พบข้อมูล']);
        }

        return $this->response->setJSON(['status' => 'success', 'data' => $totalStat]);
    }

    /**
     * AJAX JSON — รายละเอียดผู้เล่น + สถิติ + 5 แมตช์ล่าสุด (สำหรับ modal)
     */
    public function detailJson($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }
        $detail = $this->playerModel->getPlayerDetail((int)$id);
        if (!$detail) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ไม่พบข้อมูลผู้เล่น']);
        }
        return $this->response->setJSON(['status' => 'success', 'data' => $detail]);
    }

    /**
     * AJAX — DataTable ข้อมูลผู้เล่น
     */
    public function ajaxData()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $post   = $this->request->getPost();
        $matchId = isset($post['match_id']) ? (int)$post['match_id'] : null;

        $data = $this->playerModel->getPlayerTable($post, $matchId);
        return $this->response->setJSON($data);
    }
}
