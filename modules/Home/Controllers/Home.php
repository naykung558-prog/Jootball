<?php

namespace Modules\Home\Controllers;

use App\Controllers\BaseController;
use \Hermawan\DataTables\DataTable;
use App\Libraries\Mydate;
use App\Models\Main_Model;
use Modules\Admin\Models\TournamentModel;
use Modules\Home\Models\SponsorsModel;
use Modules\Home\Models\HelpGuideModel;
use Modules\Academy\Models\AcademyModel;

class Home extends BaseController
{
    protected $Main_Model;
    protected $tournamentModel;

    protected $academyModel;

    protected $sponsorsModel;
    protected $helpGuideModel;

    public function __construct()
    {
        $this->Main_Model = new Main_Model();
        $this->tournamentModel = new TournamentModel();
        $this->sponsorsModel = new SponsorsModel();
        $this->helpGuideModel = new HelpGuideModel();
        $this->academyModel = new AcademyModel;
    }

    public function index()
    {
        $data = $this->tournamentModel->getStatTourAll();

        $data['Mydate'] = new Mydate();
        $data['players'] = $this->Main_Model->count_ji('player', ['is_active' => 1]);
        $data['teams'] = $this->Main_Model->count_ji('team', ['is_active' => 1]);
        $data['staffs'] = $this->Main_Model->count_ji('coach', ['is_active' => 1]);
        $data['tournaments'] = $this->Main_Model->count_ji('tournament');
        $data['tour_matchs'] = $this->Main_Model->count_ji('tournament_match_result');

        // ดึงข้อมูล Sponsors จาก Model
        $data['navbar_sponsor'] = $this->sponsorsModel->getNavbarSponsor();
        $data['sponsors'] = $this->sponsorsModel->getActiveSponsors();

        // ไกด์ไลน์การใช้งาน (จัดการในหลังบ้าน)
        $data['help_guides'] = $this->helpGuideModel->getActiveGuides();

        // The football-stats dashboard is heavy (~3s). It is loaded asynchronously
        // via football_stats() below so the page shell/background paints immediately.
        $data['academy_id'] = (int)($this->request->getGet('academy_id') ?? 0);
        $data['tour_id'] = (int)($this->request->getGet('tour_id') ?? 0);

        return view('Modules\Home\Views\index.php', $data);
    }

    /**
     * AJAX endpoint: renders the heavy football-stats section on demand so the
     * home page can display without blocking on this computation.
     */
    public function football_stats()
    {
        $academy_id = (int)($this->request->getGet('academy_id') ?? 0);
        $tourId     = (int)($this->request->getGet('tour_id') ?? 0);

        $stats = $this->academyModel->getFootballStatsDashboard($academy_id, $tourId ?: null, 'home');

        return view('Modules\Academy\Views\_football_stats_content', [
            'stats'        => $stats,
            'academy'      => ['id' => $academy_id],
            'filterAction' => base_url(''),
        ]);
    }

    public function sts_coach() {
        $data = array();
        //$data['Mydate'] = new Mydate();
        return view('Modules\Home\Views\sts_coach.php', $data);
    }

    public function sts_player_position() {
        $data = array();
        //$data['Mydate'] = new Mydate();
        return view('Modules\Home\Views\sts_player_position.php', $data);
    }

    public function sts_map() {
        $data = array();
        //$data['Mydate'] = new Mydate();
        return view('Modules\Home\Views\sts_map.php', $data);
    }

    public function statistics() {
        $data = array();
        //$data['Mydate'] = new Mydate();
        return view('Modules\Home\Views\statistics.php', $data);
    }

    public function statistics_tour($tour_id=0) {
        $data = array();
        $data['tour_id'] = $tour_id;
        $data['tour_info'] = $this->tournamentModel->getOneTournament($tour_id);
        return view('Modules\Home\Views\statistics_tour.php', $data);
    }

    public function team_profile() {
        $data = array();
        $input = $this->request->getGet();
        $data['tour_id'] = isset($input['tour_id'])?$input['tour_id']:'';
        $data['team_id'] = isset($input['team_id'])?$input['team_id']:'';
        
        return view('Modules\Home\Views\team_profile.php', $data);
    }

}