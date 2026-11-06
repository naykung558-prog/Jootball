<?php

namespace Modules\Admin\Controllers;

use App\Controllers\BaseController;
use Modules\Admin\Models\ReportGuestModel;
use Modules\Player\Models\PlayerModel;
use \Hermawan\DataTables\DataTable;

class ReportGuest extends BaseController
{
    protected $reportModel;
    protected $playerModel;

    public function __construct()
    {
        $this->reportModel = new ReportGuestModel();
        $this->playerModel = new PlayerModel();

        helper('permission');
    }

    public function report_players()
    {
        $data['player_position'] = $this->playerModel->player_position([1,2,3,4]);
        //$data['popular_position'] = $this->reportModel->getPopularPosition();
        //$data['average_age'] = $this->reportModel->getAverageAgePlayer();
        $data['player_position_value'] = $this->reportModel->getPlayerGroupByPosition();
        $data['most_best_avg_player'] = $this->reportModel->getAverageScorePlayer(5);
        //$data['count_players'] = $this->reportModel->getCountPlayer();
        return view("Modules\Admin\Views\ReportGuest\players_report", $data);
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
        
        return view("Modules\Admin\Views\ReportGuest\player_details", $data);
    }
}
