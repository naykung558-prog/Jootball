<?php

namespace Modules\Analytics\Controllers;

use App\Controllers\BaseController;
use Modules\Analytics\Models\AnalysisModel;
use Modules\Analytics\Models\MatchAnalyticsModel;

class AnalysisView extends BaseController
{
    protected $analysisModel;
    protected $matchModel;

    public function __construct()
    {
        $this->analysisModel = new AnalysisModel();
        $this->matchModel    = new MatchAnalyticsModel();
    }

    /**
     * Heatmap ผู้เล่น
     */
    public function heatmap($matchId)
    {
        $match = $this->matchModel->find($matchId);
        if (!$match) {
            return redirect()->to('/admin/analytics/matches')->with('error', 'ไม่พบข้อมูลแมตช์');
        }

        $players  = $this->analysisModel->getMatchPlayers($matchId);
        $heatData = [];

        // โหลด heatmap data ของผู้เล่นคนแรก (default)
        if (!empty($players)) {
            $firstPlayer = $players[0];
            $heatData = $this->analysisModel->getHeatmapData(
                $matchId,
                $firstPlayer['player_id'],
                $firstPlayer['jersey_num'],
                $firstPlayer['team_side']
            );
        }

        return view('template/admin_layout', [
            'title'   => 'Heatmap - ' . $match['match_name'],
            'content' => view('Modules\Analytics\Views\heatmap', [
                'match'    => $match,
                'players'  => $players,
                'heatData' => json_encode($heatData),
            ]),
        ]);
    }

    /**
     * การวิเคราะห์ความเร็ว
     */
    public function speed($matchId)
    {
        $match = $this->matchModel->find($matchId);
        if (!$match) {
            return redirect()->to('/admin/analytics/matches')->with('error', 'ไม่พบข้อมูลแมตช์');
        }

        $players  = $this->analysisModel->getMatchPlayers($matchId);
        $allStats = $this->analysisModel->getAllPlayerStats($matchId);

        return view('template/admin_layout', [
            'title'   => 'วิเคราะห์ความเร็ว - ' . $match['match_name'],
            'content' => view('Modules\Analytics\Views\speed_analysis', [
                'match'    => $match,
                'players'  => $players,
                'allStats' => $allStats,
            ]),
        ]);
    }

    /**
     * เปรียบเทียบผู้เล่น 2 คน
     */
    public function compare()
    {
        $db = \Config\Database::connect();

        // ดึง player ที่มีสถิติทั้งหมด
        $playersWithStats = $db->table('analytics_player_stats aps')
                               ->select('DISTINCT p.id, p.first_name as player_name, p.last_name as player_surname, p.image_path as image_url')
                               ->join('player p', 'p.id = aps.player_id', 'inner')
                               ->orderBy('p.first_name', 'ASC')
                               ->get()->getResultArray();

        // ดึงรายชื่อแมตช์ทั้งหมดที่มีสถิติ (สำหรับ match filter)
        $matches = $db->table('analytics_matches am')
                      ->select('am.id, am.match_name, am.match_date')
                      ->join('analytics_player_stats aps', 'aps.match_id = am.id', 'inner')
                      ->groupBy('am.id')
                      ->orderBy('am.match_date', 'DESC')
                      ->get()->getResultArray();

        return view('template/admin_layout', [
            'title'   => 'เปรียบเทียบผู้เล่น',
            'content' => view('Modules\Analytics\Views\compare', [
                'playersWithStats' => $playersWithStats,
                'matches'          => $matches,
            ]),
        ]);
    }

    /**
     * AJAX — ดึงตำแหน่ง tracking (สำหรับ heatmap + animation)
     */
    public function ajaxPositions()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $body      = $this->request->getJSON(true) ?? $this->request->getPost();
        $matchId   = isset($body['match_id'])   ? (int)$body['match_id']   : 0;
        $playerId  = isset($body['player_id'])  ? (int)$body['player_id']  : null;
        $jerseyNum = isset($body['jersey_num']) ? (int)$body['jersey_num'] : null;
        $teamSide  = isset($body['team_side'])  ? (int)$body['team_side']  : null;
        $half      = isset($body['half'])       ? (int)$body['half']       : null;

        $positions = $this->analysisModel->getTrackingPositions($matchId, $playerId, $jerseyNum, $teamSide, $half);

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $positions,
        ]);
    }
}
