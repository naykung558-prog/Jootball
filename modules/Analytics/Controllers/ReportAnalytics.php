<?php

namespace Modules\Analytics\Controllers;

use App\Controllers\BaseController;
use Modules\Analytics\Models\MatchAnalyticsModel;
use Modules\Analytics\Models\PlayerAnalyticsModel;

class ReportAnalytics extends BaseController
{
    protected $matchModel;
    protected $playerModel;
    protected $db;

    public function __construct()
    {
        $this->matchModel  = new MatchAnalyticsModel();
        $this->playerModel = new PlayerAnalyticsModel();
        $this->db          = \Config\Database::connect();
    }

    /**
     * รายงานแมตช์
     */
    public function matchReport($matchId)
    {
        $match = $this->matchModel->getMatchWithStats($matchId);
        if (!$match) {
            return redirect()->to('/admin/analytics/matches')->with('error', 'ไม่พบข้อมูล');
        }

        $playerStats = $this->db->table('analytics_player_stats aps')
                                ->select('aps.*, p.first_name as player_name, p.last_name as player_surname')
                                ->join('player p', 'p.id = aps.player_id', 'left')
                                ->where('aps.match_id', $matchId)
                                ->orderBy('aps.total_distance', 'DESC')
                                ->get()->getResultArray();

        return view('template/admin_layout', [
            'title'   => 'รายงานแมตช์ - ' . $match['match_name'],
            'content' => view('Modules\Analytics\Views\report_match', [
                'match'       => $match,
                'playerStats' => $playerStats,
            ]),
        ]);
    }

    /**
     * รายงานผู้เล่น (ภาพรวมทุกแมตช์)
     */
    public function playerReport($playerId)
    {
        $player = $this->db->table('player')
                           ->where('id', $playerId)
                           ->get()->getRowArray();
        if (!$player) {
            return redirect()->to('/admin/analytics/players')->with('error', 'ไม่พบข้อมูลผู้เล่น');
        }

        $allStats  = $this->playerModel->getPlayerAllMatchStats($playerId);
        $totalStat = $this->playerModel->getPlayerTotalStats($playerId);

        return view('template/admin_layout', [
            'title'   => 'รายงานผู้เล่น - ' . $player['first_name'] . ' ' . $player['last_name'],
            'content' => view('Modules\Analytics\Views\report_player', [
                'player'    => $player,
                'allStats'  => $allStats,
                'totalStat' => $totalStat,
            ]),
        ]);
    }

    /**
     * Export PDF รายงานแมตช์ (ใช้ mPDF)
     */
    public function exportPdf($matchId)
    {
        $match = $this->matchModel->getMatchWithStats($matchId);
        if (!$match) {
            return redirect()->to('/admin/analytics/matches')->with('error', 'ไม่พบข้อมูล');
        }

        $playerStats = $this->db->table('analytics_player_stats aps')
                                ->select('aps.*, p.first_name as player_name, p.last_name as player_surname')
                                ->join('player p', 'p.id = aps.player_id', 'left')
                                ->where('aps.match_id', $matchId)
                                ->orderBy('aps.total_distance', 'DESC')
                                ->get()->getResultArray();

        // สร้าง HTML สำหรับ PDF
        $html = view('Modules\Analytics\Views\pdf_match', [
            'match'       => $match,
            'playerStats' => $playerStats,
        ]);

        try {
            $mpdf = new \Mpdf\Mpdf([
                'mode'        => 'utf-8',
                'format'      => 'A4',
                'orientation' => 'L',
                'margin_left' => 15,
                'margin_right'=> 15,
                'margin_top'  => 20,
                'margin_bottom'=> 20,
            ]);

            $mpdf->SetTitle('รายงานวิเคราะห์แมตช์ - ' . $match['match_name']);
            $mpdf->WriteHTML($html);

            $filename = 'analytics_match_' . $matchId . '_' . date('Ymd') . '.pdf';
            $mpdf->Output($filename, 'D');
        } catch (\Exception $e) {
            log_message('error', 'Analytics PDF Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'ไม่สามารถสร้าง PDF ได้: ' . $e->getMessage());
        }
    }
}
