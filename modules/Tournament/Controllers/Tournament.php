<?php

namespace Modules\Tournament\Controllers;

use App\Controllers\BaseController;
use Modules\Tournament\Models\TournamentModel;
use \Hermawan\DataTables\DataTable;
use App\Libraries\Mydate;
use App\Models\Main_Model;

class Tournament extends BaseController
{
    protected $TournamentModel;
    protected $Main_Model;

    public function __construct()
    {
        $this->TournamentModel = new TournamentModel;
        $this->Main_Model = new Main_Model();
    }

    public function index($tour_id)
    {
        $data = array();
        $data['Mydate'] = new Mydate();
        $data['data'] = $this->Main_Model->find_ji('tournament', 'tour_id', $tour_id);
        $data['count_teams'] = $this->TournamentModel->CountTeams($tour_id);
        $data['count_teams_approved'] = $this->TournamentModel->CountApprovedTeams($tour_id);
        $data['count_players'] = $this->TournamentModel->CounPlayers($tour_id);
        $data['count_coachs'] = $this->TournamentModel->CounCoachs($tour_id);
        return view('Modules\Tournament\Views\index.php', $data);
    }

    public function team($tour_id)
    {
        $data = array();
        $data['data'] = $this->Main_Model->find_ji('tournament', 'tour_id', $tour_id);
        $data['team'] = $this->Main_Model->findJoin_ji('tournament_team', [['table' => 'team', 'condition' => 'team.id = tournament_team.team_id']], ['team.*', 'tournament_team.player_list_status', 'tournament_team.tour_team_id', 'tournament_team.register_status'], ['tournament_team.tour_id' => $tour_id]);
        $data['tour_id'] = $tour_id;

        return view('Modules\Tournament\Views\team.php', $data);
    }

    public function result($tour_id)
    {
        $data = array();
        $Get = $this->request->getGet();
        $data['tour_id'] = $tour_id;
        $data['data'] = $this->Main_Model->find_ji('tournament', 'tour_id', $tour_id);
        // $data['rounds'] = $this->TournamentModel->getRounds($tour_id);
        // if(empty($Get['round']) && empty($data['rounds'][@$Get['round']]) ){
        //     $data['round'] = array_key_first($data['rounds']);
        // }else{
        //     $data['round'] = $Get['round'];
        // }
        $category = $this->Main_Model->findWhere_ji('tournament_category', array('tour_id' => $tour_id));
        foreach ($category as $key => $value) {
            $data['categories'][$value['category_id']] = $value['category_name'];
        }
        if (empty($Get['cate_id']) && empty($data['categories'][@$Get['cate_id']])) {
            $data['cate_id'] = array_key_first($data['categories']);
        } else {
            $data['cate_id'] = $Get['cate_id'];
        }

        $rounds = $this->Main_Model->findWhere_ji('tournament_round', array('tour_id' => $tour_id));
        foreach ($rounds as $key => $value) {
            $data['rounds'][$value['round_id']] = $value['round_name'];
        }
        if (empty($Get['round']) && empty($data['rounds'][@$Get['round']])) {
            $data['round'] = array_key_last($data['rounds']);
        } else {
            $data['round'] = $Get['round'];
        }


        $data['matches'] = $this->TournamentModel->getMatch($tour_id, $data);

        return view('Modules\Tournament\Views\result.php', $data);
    }

    public function group($tour_id)
    {
        $Get = $this->request->getGet();
        $group_id = $Get['group_id'];
        $data['tour_id'] = $tour_id;
        $data['data'] = $this->Main_Model->find_ji('tournament', 'tour_id', $tour_id);
        $data['group_id'] = $group_id;
        $data['cate_id'] = $Get['cate_id'];
        $data['round'] = $Get['round'];
        $data['matches'] = $this->TournamentModel->getMatch($tour_id, $data);
        return view('Modules\Tournament\Views\group.php', $data);
    }

    function update_tour()
    {
        // $this->TournamentModel->update_tour();
        $this->TournamentModel->update_tour_team();
    }
}
