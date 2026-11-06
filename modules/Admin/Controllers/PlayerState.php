<?php

namespace Modules\Admin\Controllers;

use App\Controllers\BaseController;
use Modules\Admin\Models\PlayerStateModel;
use \Hermawan\DataTables\DataTable;


class PlayerState extends BaseController
{
    protected $playerStateModel;
    public function __construct()
    {
        $this->playerStateModel = new PlayerStateModel();
    }

    public function savePlayerToMatch()
    {
        $input = $this->request->getPost();
        if(empty($input['tour_id'])){
            $input['tour_id'] = 0 ; // ของ GLO
        }
        // ตัวจริงทีมเหย้า
        if (!empty($input['real_home_players'])) {
            $this->playerStateModel->savePlayerToMatch($input['real_home_players'], $input['tour_id'],  $input['match_id'], $input['team1_id'], 0);
        }
        // สำรองทีมเหย้า
        if (!empty($input['sub_home_players'])) {
            $this->playerStateModel->savePlayerToMatch($input['sub_home_players'], $input['tour_id'], $input['match_id'], $input['team1_id'],1);
        }
        // ตัวจริงทีมเยือน
        if (!empty($input['real_away_players'])) {
            $this->playerStateModel->savePlayerToMatch($input['real_away_players'], $input['tour_id'], $input['match_id'], $input['team2_id'], 0);
        }
        // สำรองทีมเยือน
        if (!empty($input['sub_away_players'])) {
            $this->playerStateModel->savePlayerToMatch($input['sub_away_players'], $input['tour_id'], $input['match_id'], $input['team2_id'], 1);
        }

        return redirect()->to(base_url('admin/tournament/tournament-match-result-manage/' . $input['tour_id'] . '/' . $input['match_id']));
    }
}
