<?php

namespace Modules\Admin\Controllers;

use App\Controllers\BaseController;
use Modules\Admin\Models\ReportModel;
use Modules\Player\Models\PlayerModel;
use \Hermawan\DataTables\DataTable;

class Report extends BaseController
{
    protected $reportModel;
    protected $playerModel;

    public function __construct()
    {
        $this->reportModel = new ReportModel();
        $this->playerModel = new PlayerModel();
    }

    public function report_players()
    {
        $data['player_position'] = $this->playerModel->player_position([1,2,3,4]);
        $data['popular_position'] = $this->reportModel->getPopularPosition();
        $data['average_age'] = $this->reportModel->getAverageAgePlayer();
        $data['player_position_value'] = $this->reportModel->getPlayerGroupByPosition();
        $data['most_best_avg_player'] = $this->reportModel->getAverageScorePlayer(5);
        $data['count_players'] = $this->reportModel->getCountPlayer();
        return view("Modules\Admin\Views\Report\players_report", $data);
    }

    public function ajax_get_players()
    {
        $result = $this->reportModel->getPlayersList_ajax();
        return $result;
    }

    public function report_details_player($id) {
        $data['state_list'] = [
            "shots" => "โอกาสยิงประตูทั้งหมด",
            "shots_on_target" => "ยิงประตูตรงกรอบ",
            "total_passes" => "ส่งบอลทั้งหมด",
            "success_passed" => "ส่งบอลสำเร็จ",
            "tackles" => "เข้าปะทะ",
            "interceptions" => "ตัดบอลสำเร็จ",
            "fouls_committed" => "ฟาวล์",
            "offside" => "ล้ำหน้า",
            "saves" => "ป้องกันการเสียประตู",
        ];
        
        $data['player'] = $this->reportModel->getPlayerData($id);
        return view("Modules\Admin\Views\Report\player_details", $data);
    }

    public function report_logs()
    {
        $db = \Config\Database::connect();
        
        // สถิติจำนวน Log ทั้งหมด
        $data['total_logs'] = $db->table('logs_login')->countAllResults();
        
        // ผู้ใช้งานไม่ซ้ำหน้า (user_id)
        $data['unique_users'] = $db->table('logs_login')
                                   ->selectCount('user_id', 'count')
                                   ->where('user_id >', 0)
                                   ->groupBy('user_id')
                                   ->countAllResults();
        
        // แยกตามประเภท Web / API (สมมติว่าใช้คอลัมน์ type)
        $data['web_percent'] = 0;
        if ($data['total_logs'] > 0) {
            $web_count = $db->table('logs_login')->where('type', 'web')->countAllResults();
            $data['web_percent'] = round(($web_count / $data['total_logs']) * 100);
        }

        // รายการที่ผิดปกติ (สมมติว่าเป็น error response หรือ login failed)
        // ในที่นี้สมมติว่าเป็น response_data ที่มีคำว่า "error" หรือ "failed"
        $data['anomaly_count'] = $db->table('logs_login')
                                    ->like('response_data', 'error')
                                    ->orLike('response_data', 'failed')
                                    ->countAllResults();

        return view("Modules\Admin\Views\Report\logs_report", $data);
    }

    public function ajax_get_logs()
    {
        $result = $this->reportModel->getLogsList_ajax();
        return $result;
    }

}
