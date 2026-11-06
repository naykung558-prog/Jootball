<?php

namespace Modules\Admin\Controllers;

use App\Controllers\BaseController;
use \Hermawan\DataTables\DataTable;
use Modules\Admin\Models\TeamModel;
use Modules\Admin\Models\TournamentModel;
use Modules\Admin\Controllers\PlayerState;
use Modules\Admin\Models\PlayerStateModel;

class Scout extends BaseController
{
    protected $teamModel;
    protected $tournamentModel;
    protected $playerStateModel;
    public function __construct()
    {
        $this->teamModel = new TeamModel();
        $this->tournamentModel = new TournamentModel();
        $this->playerStateModel = new PlayerStateModel();
    }

    public function index()
    {
        $data['search'] = $this->request->getGet();
        if (empty($data['search']['match_date'])) {
            $data['search']['match_date'] = date('Y-m-d');
        } else {
            $format_match_date = date('Y-m-d', strtotime(str_replace('/', '-', $data['search']['match_date'])));
            $data['search']['match_date'] = $format_match_date;
        }
        $data['matches'] = $this->tournamentModel->get_Tournament_Match_Result('', $data['search']['match_date']);
        return view('Modules\Admin\Views\Scout\index', $data);
    }

    public function player_state_list($tour_id = '', $match_id = '')
    {
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
        $data['data'] = $this->tournamentModel->getOnceTournament_Match_Result($match_id);
        $data['players_home'] = $this->playerStateModel->getPlayerDataToMatch($tour_id, $match_id, $data['data']['team1_id'], '');
        $data['players_away'] = $this->playerStateModel->getPlayerDataToMatch($tour_id, $match_id, $data['data']['team2_id'], '');
        return view('Modules\Admin\Views\Scout\player_state_list', $data);
    }

    public function getPlayerState() {
        $input = $this->request->getPost();
        $player_id = $input['player_id'];
        $tour_id = $input['tour_id'];
        $match_id = $input['match_id'];
        $result = $this->playerStateModel->getPlayerState($player_id, $tour_id, $match_id);
        return $this->response->setJSON($result);
    }

    public function savePlayerState(){
        $input = $this->request->getPost();
        $result = $this->playerStateModel->savePlayerState($input);
        return $this->response->setJSON($input);
    }
}
