<?php

namespace Modules\Admin\Controllers;

use App\Libraries\MatchScheduler;
use App\Controllers\BaseController;
use \Hermawan\DataTables\DataTable;
use Modules\Admin\Models\TeamModel;
use Modules\Admin\Models\TournamentModel;
use Modules\Player\Models\PlayerModel;
use Modules\Admin\Models\PlayerStateModel;
use App\Models\Main_Model;
use Modules\Admin\Controllers\Traits\TournamentScoringTrait;
use Modules\Admin\Controllers\Traits\TournamentExportTrait;
use Modules\Admin\Controllers\Traits\TournamentStagesTrait;
use Modules\Admin\Controllers\Traits\TournamentMatchGenTrait;

class Tournament extends BaseController
{
    use TournamentMatchGenTrait;
    use TournamentStagesTrait;
    use TournamentExportTrait;
    use TournamentScoringTrait;
    protected $db;
    protected $tournamentModel;
    protected $teamModel;
    protected $playerModel;
    protected $playerStateModel;

    public function __construct()
    {
        helper('academy');
        $this->db = db_connect();
        $this->Main_Model = new Main_Model();
        $this->tournamentModel = new TournamentModel();
        $this->teamModel = new TeamModel();
        $this->playerModel = new PlayerModel();
        $this->playerStateModel = new PlayerStateModel();
    }

    public function index()
    {
        return view('Modules\Admin\Views\Tournament\tour_index');
    }

    public function tour_manage($tour_id = '')
    {
        //$data['position'] = $this->tournamentModel->player_position();
        $data = ['mode' => ($tour_id == '' ? 'add' : 'edit')];
        if ($tour_id) {
            $data['data'] = $this->tournamentModel->get_Tournament($tour_id);
        }

        // ดึงรายการทัวร์นาเมนต์ทั้งหมด (สำหรับ multi-select ตรวจสอบนักเตะซ้ำข้ามรายการ)
        $data['tournaments'] = $this->tournamentModel->get_TournamentList(null);
        if ($tour_id) {
            $linkedTours = $this->tournamentModel->getLinkedTournaments($tour_id);
            $data['linked_tournament_ids'] = array_column($linkedTours, 'linked_tour_id');
            $sourceTours = $this->tournamentModel->getSourceTournaments($tour_id);
            $data['source_tournament_ids'] = array_column($sourceTours, 'source_tour_id');
        } else {
            $data['linked_tournament_ids'] = [];
            $data['source_tournament_ids'] = [];
        }

        try {
            $data['banks'] = $this->tournamentModel->getBanks();
            if (empty($data['banks'])) {
                 // Fallback if table is empty or query fails (e.g. migration not run)
                 $data['banks'] = [
                    ['id' => 1, 'name' => 'ธนาคารกสิกรไทย', 'short_name' => 'KBANK', 'code' => '004'],
                    ['id' => 2, 'name' => 'ธนาคารไทยพาณิชย์', 'short_name' => 'SCB', 'code' => '014'],
                    ['id' => 3, 'name' => 'ธนาคารกรุงเทพ', 'short_name' => 'BBL', 'code' => '002'],
                    ['id' => 4, 'name' => 'ธนาคารกรุงไทย', 'short_name' => 'KTB', 'code' => '006'],
                    ['id' => 5, 'name' => 'ธนาคารกรุงศรีอยุธยา', 'short_name' => 'BAY', 'code' => '025'],
                    ['id' => 6, 'name' => 'ธนาคารทหารไทยธนชาต', 'short_name' => 'TTB', 'code' => '011'],
                    ['id' => 7, 'name' => 'ธนาคารออมสิน', 'short_name' => 'GSB', 'code' => '030'],
                    ['id' => 8, 'name' => 'ธนาคารเกียรตินาคินภัทร', 'short_name' => 'KKP', 'code' => '069'],
                    ['id' => 9, 'name' => 'ธนาคารซีไอเอ็มบี ไทย', 'short_name' => 'CIMBT', 'code' => '022'],
                    ['id' => 10, 'name' => 'ธนาคารทิสโก้', 'short_name' => 'TISCO', 'code' => '067'],
                    ['id' => 11, 'name' => 'ธนาคารยูโอบี', 'short_name' => 'UOB', 'code' => '024'],
                    ['id' => 12, 'name' => 'ธนาคารแลนด์ แอนด์ เฮ้าส์', 'short_name' => 'LHBANK', 'code' => '073'],
                    ['id' => 13, 'name' => 'ธนาคารสแตนดาร์ดชาร์เตอร์ด (ไทย)', 'short_name' => 'SCBT', 'code' => '020'],
                    ['id' => 14, 'name' => 'ธนาคารเพื่อการเกษตรและสหกรณ์การเกษตร', 'short_name' => 'BAAC', 'code' => '034'],
                    ['id' => 15, 'name' => 'ธนาคารอาคารสงเคราะห์', 'short_name' => 'GHB', 'code' => '033'],
                 ];
            }
        } catch (\Exception $e) {
            // If getBanks() throws an exception (e.g., table not found), use the full fallback list
             $data['banks'] = [
                ['id' => 1, 'name' => 'ธนาคารกสิกรไทย', 'short_name' => 'KBANK', 'code' => '004'],
                ['id' => 2, 'name' => 'ธนาคารไทยพาณิชย์', 'short_name' => 'SCB', 'code' => '014'],
                ['id' => 3, 'name' => 'ธนาคารกรุงเทพ', 'short_name' => 'BBL', 'code' => '002'],
                ['id' => 4, 'name' => 'ธนาคารกรุงไทย', 'short_name' => 'KTB', 'code' => '006'],
                ['id' => 5, 'name' => 'ธนาคารกรุงศรีอยุธยา', 'short_name' => 'BAY', 'code' => '025'],
                ['id' => 6, 'name' => 'ธนาคารทหารไทยธนชาต', 'short_name' => 'TTB', 'code' => '011'],
                ['id' => 7, 'name' => 'ธนาคารออมสิน', 'short_name' => 'GSB', 'code' => '030'],
                ['id' => 8, 'name' => 'ธนาคารเกียรตินาคินภัทร', 'short_name' => 'KKP', 'code' => '069'],
                ['id' => 9, 'name' => 'ธนาคารซีไอเอ็มบี ไทย', 'short_name' => 'CIMBT', 'code' => '022'],
                ['id' => 10, 'name' => 'ธนาคารทิสโก้', 'short_name' => 'TISCO', 'code' => '067'],
                ['id' => 11, 'name' => 'ธนาคารยูโอบี', 'short_name' => 'UOB', 'code' => '024'],
                ['id' => 12, 'name' => 'ธนาคารแลนด์ แอนด์ เฮ้าส์', 'short_name' => 'LHBANK', 'code' => '073'],
                ['id' => 13, 'name' => 'ธนาคารสแตนดาร์ดชาร์เตอร์ด (ไทย)', 'short_name' => 'SCBT', 'code' => '020'],
                ['id' => 14, 'name' => 'ธนาคารเพื่อการเกษตรและสหกรณ์การเกษตร', 'short_name' => 'BAAC', 'code' => '034'],
                ['id' => 15, 'name' => 'ธนาคารอาคารสงเคราะห์', 'short_name' => 'GHB', 'code' => '033'],
             ];
        }

        return view('Modules\Admin\Views\Tournament\tour_manage.php', $data);
    }

    public function save_tournament()
    {
        try {
            $input = $this->request->getPost();

            // Validate: เปิด require_source_players ต้องเลือก source tournaments อย่างน้อย 1 รายการ
            // (กัน config ไม่สมบูรณ์: เปิด whitelist แต่ไม่มีต้นทาง → submit จะปล่อยผ่านทุกคน)
            if (!empty($input['require_source_players']) && empty($input['source_tournaments'])) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'msg'    => lang('Common.source_require_needs_source')
                ]);
            }

            // Handle logo image upload
            if (!empty($input['base64_image'])) {
                $saveDir = ROOTPATH . 'public/uploads/tournaments/';
                if (!is_dir($saveDir)) {
                    mkdir($saveDir, 0775, true);
                } else if (!is_writable($saveDir)) {
                    chmod($saveDir, 0775);
                }

                $random_name = substr(number_format(time() * rand(), 0, '', ''), 0, 10);
                // $img = $this->replaceUnderscoreWithSlash($input);
                $img = $input['base64_image'];
                $img = str_replace('data:image/png;base64,', '', $img);
                $img = str_replace(' ', '+', $img);
                $data = base64_decode($img);
                $set_name_img = $random_name . '.jpeg';
                $file = $saveDir . $set_name_img;
                file_put_contents($file, $data);
                $input['logo'] = 'public/uploads/tournaments/' . $set_name_img;
            }

            // Handle PDF file upload
            $pdfFile = $this->request->getFile('pdf_file');
            if ($pdfFile && $pdfFile->isValid() && !$pdfFile->hasMoved()) {
                // Validate file type
                $allowedMimes = ['application/pdf'];
                if (!in_array($pdfFile->getMimeType(), $allowedMimes)) {
                    if ($this->request->isAJAX()) {
                        return $this->response->setJSON(['status' => 'error', 'msg' => 'กรุณาอัปโหลดไฟล์ PDF เท่านั้น']);
                    }
                    return redirect()->back()->with('error', 'กรุณาอัปโหลดไฟล์ PDF เท่านั้น');
                }

                // Validate file size (max 10MB)
                if ($pdfFile->getSize() > 10 * 1024 * 1024) {
                     if ($this->request->isAJAX()) {
                        return $this->response->setJSON(['status' => 'error', 'msg' => 'ขนาดไฟล์ PDF ต้องไม่เกิน 10MB']);
                    }
                    return redirect()->back()->with('error', 'ขนาดไฟล์ PDF ต้องไม่เกิน 10MB');
                }

                // Create upload directory
                $pdfDir = ROOTPATH . 'public/uploads/tournaments/pdfs/';
                if (!is_dir($pdfDir)) {
                    mkdir($pdfDir, 0775, true);
                } else if (!is_writable($pdfDir)) {
                    chmod($pdfDir, 0775);
                }

                // Generate unique filename
                $random_name = substr(number_format(time() * rand(), 0, '', ''), 0, 10);
                $newName = 'tournament_' . $random_name . '.pdf';

                // Move file to upload directory
                if ($pdfFile->move($pdfDir, $newName)) {
                    $input['pdf_path'] = 'public/uploads/tournaments/pdfs/' . $newName;
                } else {
                    log_message('error', 'Failed to upload PDF file. Path: ' . $pdfDir . ' Error: ' . $pdfFile->getErrorString());
                    throw new \Exception('Failed to move uploaded PDF file to: ' . $pdfDir);
                }
            }


            // Handle QR Code upload (Legacy File Input)
            $qrFile = $this->request->getFile('qrcode_file');
            if ($qrFile && $qrFile->isValid() && !$qrFile->hasMoved()) {
                // Validate file type
                $allowedMimes = ['image/jpeg', 'image/png', 'image/jpg'];
                if (!in_array($qrFile->getMimeType(), $allowedMimes)) {
                     if ($this->request->isAJAX()) {
                        return $this->response->setJSON(['status' => 'error', 'msg' => 'กรุณาอัปโหลดไฟล์รูปภาพ (JPG, PNG) เท่านั้น']);
                    }
                    return redirect()->back()->with('error', 'กรุณาอัปโหลดไฟล์รูปภาพ (JPG, PNG) เท่านั้น');
                }

                // Create upload directory
                $qrDir = ROOTPATH . 'public/uploads/tournaments/qrcodes/';
                if (!is_dir($qrDir)) {
                    mkdir($qrDir, 0775, true);
                } else if (!is_writable($qrDir)) {
                    chmod($qrDir, 0775);
                }

                // Generate unique filename
                $random_name = substr(number_format(time() * rand(), 0, '', ''), 0, 10);
                $newName = 'qrcode_' . $random_name . '.' . $qrFile->getExtension();

                // Move file to upload directory
                if ($qrFile->move($qrDir, $newName)) {
                    $input['qrcode'] = 'public/uploads/tournaments/qrcodes/' . $newName;
                } else {
                    log_message('error', 'Failed to upload QR Code file. Path: ' . $qrDir . ' Error: ' . $qrFile->getErrorString());
                    throw new \Exception('Failed to move uploaded QR Code file to: ' . $qrDir);
                }
            }

            $this->tournamentModel->saveTournament($input);

            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => 'success', 'msg' => 'บันทึกข้อมูลเรียบร้อยแล้ว']);
            }

            return redirect()->to(base_url('admin/tournament'));

        } catch (\Exception $e) {
            log_message('error', '[save_tournament] Error: ' . $e->getMessage());
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => 'error', 'msg' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
            }
            return redirect()->back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

    public function ajax_tournaments()
    {
        $result = $this->tournamentModel->get_Tournament();
        return $result;
    }

    public function pending_chart_players($tour_id)
    {
        $tourId = (int) $tour_id;
        if ($tourId <= 0) {
            return $this->response->setJSON([
                'status' => 'error',
                'msg' => 'ไม่พบรายการแข่งขัน',
                'data' => [],
            ]);
        }

        $builder = $this->db->table('tournament_submitted_players ts')
            ->select("
                ts.id AS submitted_id,
                ts.player_id AS player_id,
                p.first_name,
                p.last_name,
                p.shirt_number,
                tt.team_id,
                tm.name AS team_name,
                tt.tour_id,
                t.name AS tournament_name,
                t.tournament_start_date,
                pt.player_id AS current_player_id
            ")
            ->join('tournament_team tt', 'ts.tour_team_id = tt.tour_team_id', 'inner')
            ->join('tournament t', 'tt.tour_id = t.tour_id', 'inner')
            ->join('player_teams pt', 'ts.player_id = pt.player_id AND tt.team_id = pt.team_id', 'left')
            ->join('player p', 'p.id = ts.player_id', 'left')
            ->join('team tm', 'tm.id = tt.team_id', 'left')
            ->where('tt.tour_id', $tourId)
            ->where('pt.player_id IS NULL', null, false)
            ->where('t.tournament_start_date > NOW()', null, false)
            ->orderBy('tm.name', 'ASC')
            ->orderBy('CAST(p.shirt_number AS UNSIGNED)', 'ASC', false)
            ->orderBy('p.first_name', 'ASC');

        if (!session()->get('is_super_admin')) {
            $builder->where('t.create_by', session()->get('member_id'));
        }

        $players = $builder->get()->getResultArray();

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $players,
            'count' => count($players),
        ]);
    }

    public function delete_pending_chart_player()
    {
        $submittedId = (int) $this->request->getPost('submitted_id');
        if ($submittedId <= 0) {
            return $this->response->setJSON([
                'status' => 'error',
                'msg' => 'ไม่พบรายการที่ต้องการลบ',
            ]);
        }

        $builder = $this->db->table('tournament_submitted_players ts')
            ->select('ts.id')
            ->join('tournament_team tt', 'ts.tour_team_id = tt.tour_team_id', 'inner')
            ->join('tournament t', 'tt.tour_id = t.tour_id', 'inner')
            ->join('player_teams pt', 'ts.player_id = pt.player_id AND tt.team_id = pt.team_id', 'left')
            ->where('ts.id', $submittedId)
            ->where('pt.player_id IS NULL', null, false)
            ->where('t.tournament_start_date > NOW()', null, false);

        if (!session()->get('is_super_admin')) {
            $builder->where('t.create_by', session()->get('member_id'));
        }

        $row = $builder->get()->getRowArray();
        if (!$row) {
            return $this->response->setJSON([
                'status' => 'error',
                'msg' => 'ไม่พบรายการ หรือไม่มีสิทธิ์ลบข้อมูลนี้',
            ]);
        }

        $deleted = $this->db->table('tournament_submitted_players')
            ->where('id', $submittedId)
            ->delete();

        return $this->response->setJSON([
            'status' => $deleted ? 'success' : 'error',
            'msg' => $deleted ? 'ลบรายการเรียบร้อยแล้ว' : 'เกิดข้อผิดพลาดในการลบรายการ',
        ]);
    }

    public function tournament_team($tour_id = '')
    {
        $data = [];
        if ($tour_id) {
            $data['tour_id'] = $tour_id;
            $data['data'] = $this->tournamentModel->get_Tournament_Team($tour_id);
        }
        return view('Modules\Admin\Views\Tournament\tour_team_index.php', $data);
    }

    public function tournament_team_manage($tour_id = '')
    {
        $data = ['mode' => 'add'];
        $data['team_options'] = $this->tournamentModel->getTeamOptions();
        if ($tour_id) {
            $data['data'] = $this->tournamentModel->getOnceTournament_Team($tour_id);
            $data['team_options_selected'] = $this->tournamentModel->getTournamentTeamSelected($tour_id);
            $data['tour_id'] = $tour_id;
        }

        return view('Modules\Admin\Views\Tournament\tour_team_manage.php', $data);
    }

    // tbl tournament_team
    public function ajax_tournament_teams($tour_id = '')
    {
        $result = $this->tournamentModel->get_Tournament_TeamTable($tour_id);
        return $result;
    }

    /**
     * AJAX: ดึงแมตช์พร้อมชื่อทีม
     */
    public function ajax_tournament_match_result()
    {
        $tour_id = $this->request->getGet('tour_id') ?? '';

        if (empty($tour_id)) {
            return $this->response->setJSON([
                'status' => 'error',
                'msg' => 'Tour ID is required'
            ]);
        }

        $matches = $this->db->table('tournament_match_result')
            ->select('tournament_match_result.*,
                      team1.name as team1_name,
                      team1.logo as team1_logo,
                      team2.name as team2_name,
                      team2.logo as team2_logo')
            ->join('team as team1', 'tournament_match_result.team1_id = team1.id', 'left')
            ->join('team as team2', 'tournament_match_result.team2_id = team2.id', 'left')
            ->where('tournament_match_result.tour_id', $tour_id)
            ->orderBy('tournament_match_result.match_round', 'ASC')
            ->orderBy('tournament_match_result.match_id', 'ASC')
            ->get()
            ->getResultArray();


        // Sort matches: Scheduled (Date/Time set) first, then by Date/Time, then Unscheduled
        usort($matches, function ($a, $b) {
            // 1. Sort by Round (Keep rounds together)
            if ($a['match_round'] !== $b['match_round']) {
                // If round naming is standard, string compare works.
                return strcmp($a['match_round'], $b['match_round']);
            }

            // 2. Check if scheduled
            // Assuming empty date means unscheduled. Adjust condition as needed based on DB defaults.
            $aHasDate = !empty($a['date']) && $a['date'] !== '0000-00-00';
            $bHasDate = !empty($b['date']) && $b['date'] !== '0000-00-00';

            if ($aHasDate && !$bHasDate) return -1; // A comes first
            if (!$aHasDate && $bHasDate) return 1;  // B comes first

            // 3. If both scheduled, compare Date then Time
            if ($aHasDate && $bHasDate) {
                if ($a['date'] !== $b['date']) {
                    return strcmp($a['date'], $b['date']);
                }
                $aTime = $a['time'] ?? '';
                $bTime = $b['time'] ?? '';
                if ($aTime !== $bTime) {
                    return strcmp($aTime, $bTime);
                }
            }

            // 4. Fallback to Match ID
            return $a['match_id'] <=> $b['match_id'];
        });

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $matches
        ]);
    }

    public function save_tournament_team()
    {
        $input = $this->request->getPost();
        // echo "<pre>" ;
        // print_r($input);die;
        $result = $this->tournamentModel->save_tournament_teams($input);
        $tour_id = $input['tour_id'];
        return redirect()->to(base_url('admin/tournament/tournament-team/' . $tour_id));
    }

    function delete_tournament_team()
    {
        $input = $this->request->getPost();
        $result = $this->tournamentModel->delete_tournament_team($input['tour_team_id']);
        if ($result == "success") {
            $response = array(
                'status' => 'success',
                'msg' => 'แก้ไขสถานะสำเร็จ'
            );
        } else {
            $response = array(
                'status' => 'error',
                'msg' => 'เกิดข้อผิดพลาด'
            );
        }
        return $this->response->setJson($response);
    }

    function change_register_status()
    {
        $input = $this->request->getPost();
        $result = $this->tournamentModel->change_register_status($input['tour_team_id'], $input['register_status']);
        if ($result == "success") {
            $response = array(
                'status' => 'success',
                'msg' => 'แก้ไขสถานะสำเร็จ'
            );
        } else {
            $response = array(
                'status' => 'error',
                'msg' => 'เกิดข้อผิดพลาด'
            );
        }
        return $this->response->setJson($response);
    }
    //

    public function ranking($tour_id = '')
    {
        if ($tour_id) {
            $data['tour_id'] = $tour_id;
            $data['data'] = $this->tournamentModel->get_Ranking($tour_id);
        }
        return view('Modules\Admin\Views\Tournament\ranking.php', $data);
    }

    public function tournament_match_result($tour_id = '')
    {
        $round_name = $this->request->getGet('round_name');

        if ($tour_id) {
            $data['tour_id'] = $tour_id;
            $data['allMatches'] = $this->tournamentModel->get_Tournament_Match_Result($tour_id, '', $round_name);
            $data['listBracket'] = $this->tournamentModel->getBracketData($tour_id);

            $data['round_name'] = $this->db->table('tournament_match_result')->select('match_round')->where('tour_id', $tour_id)->groupBy('match_round')->get()->getResultArray();
        }
         //echo '<pre>';
         //print_r($data);
         //die();
        return view('Modules\Admin\Views\Tournament\tournament_match_result_index.php', $data);
    }

    public function tournament_match_result_manage($tour_id = '', $match_id = '')
    {
        $data = ['mode' => ($match_id == '' ? 'add' : 'edit')];
        //if ($tour_id) {
        $data['tour_id'] = $tour_id;
        $data['match_id'] = $match_id;
        $data['data'] = $this->tournamentModel->getOnceTournament_Match_Result($match_id);
        $data['event_types'] = $this->tournamentModel->getEventTypes();
        $data['players_home'] = $this->playerStateModel->getPlayerDataToMatch($tour_id, $match_id, $data['data']['team1_id'], '');
        $data['players_away'] = $this->playerStateModel->getPlayerDataToMatch($tour_id, $match_id, $data['data']['team2_id'], '');
        $data['players_home_list'] = $this->playerModel->get_PlayerList($data['data']['team1_id'], [5, 6, 7]);
        $data['players_away_list'] = $this->playerModel->get_PlayerList($data['data']['team2_id'], [5, 6, 7]);
        $data['events'] = $this->tournamentModel->getMatchEvent($match_id);
        //}

        //option สำหรับผู่เล่นที่ถูกเลือกลงสนาม
        $data['real_home_player'] = $this->playerStateModel->getPlayerToMatch($tour_id, $match_id, $data['data']['team1_id'], 0);
        $data['sub_home_player'] = $this->playerStateModel->getPlayerToMatch($tour_id, $match_id, $data['data']['team1_id'], 1);
        $data['real_away_player'] = $this->playerStateModel->getPlayerToMatch($tour_id, $match_id, $data['data']['team2_id'], 0);
        $data['sub_away_player'] = $this->playerStateModel->getPlayerToMatch($tour_id, $match_id, $data['data']['team2_id'], 1);
        return view('Modules\Admin\Views\Tournament\tournament_match_result_manage.php', $data);
    }

    public function ajax_tournament_match_for_prog_rank_result()
    {
        $tour_id = $this->request->getGet('tour_id') ?? '';

        $data = $this->db->table('tournament_match_result tmr')
            ->select('
                tmr.*,
                team1.name as team1_name,
                team1.name_en as team1_name_en,
                team1.logo as team1_logo,
                team2.name as team2_name,
                team2.name_en as team2_name_en,
                team2.logo AS team2_logo,
                ts.stage_name,
                ts.format,
                ts.teams_json,
                ts.final_match
            ')
            ->join(
                'tournament_stages ts',
                'tmr.stage_id = ts.stage_id',
                'inner'
            )
            ->join('team AS team1', 'tmr.team1_id = team1.id', 'left')
            ->join('team AS team2', 'tmr.team2_id = team2.id', 'left')
            ->where('tmr.tour_id', $tour_id)

            // ===== stage =====
            ->orderBy('ts.order_num', 'ASC')

            // ===== date (ไม่ใช่ NULL ขึ้นก่อน) =====
            ->orderBy('tmr.date IS NULL', 'ASC', false)
            ->orderBy('tmr.date', 'ASC')
            ->orderBy('tmr.time', 'ASC')

            // ===== group_id (สำหรับ league) =====
            ->orderBy('tmr.group_id', 'ASC')
            
            // ===== match_id (เพื่อเรียงลำดับแมตช์ให้ถูกต้อง) =====
            ->orderBy('tmr.match_id', 'ASC')

            ->get()
            ->getResultArray();

        // ===== Group matches by group_id and stage_id =====
        $group_matches = [];
        $knockout_matches = [];
        foreach($data as $kk => $dd) {
            if($dd['format'] === 'league_single' && !empty($dd['group_id'])) {
                if(!isset($group_matches[$dd['stage_id']])) {
                    $group_matches[$dd['stage_id']] = [];
                }
                if(!isset($group_matches[$dd['stage_id']][$dd['group_id']])) {
                    $group_matches[$dd['stage_id']][$dd['group_id']] = [];
                }
                $group_matches[$dd['stage_id']][$dd['group_id']][] = $kk;
            }
            elseif($dd['format'] !== 'league_single') {
                if(!isset($knockout_matches[$dd['stage_id']])) {
                    $knockout_matches[$dd['stage_id']] = [];
                }
                $knockout_matches[$dd['stage_id']][] = $kk;
            }
        }

        // ===== Sort group (league) matches by match_id ให้ตรงลำดับสร้างคู่ round-robin =====
        // สำคัญ: $data เรียงตาม date/time ก่อน ถ้าไม่ sort ใหม่ match_index จะอิงเวลา
        // ทำให้ชื่อคู่ (A1vA2, A1vA3...) ไปแปะผิด match → เวลา/ชื่อไม่ตรงกัน
        foreach($group_matches as $stage_id => $groups) {
            foreach($groups as $group_id => $matches) {
                usort($matches, function($a, $b) use ($data) {
                    return $data[$a]['match_id'] - $data[$b]['match_id'];
                });
                $group_matches[$stage_id][$group_id] = $matches;
            }
        }

        // ===== Sort knockout matches by match_id to ensure correct order =====
        foreach($knockout_matches as $stage_id => $matches) {
            usort($matches, function($a, $b) use ($data) {
                return $data[$a]['match_id'] - $data[$b]['match_id'];
            });
            $knockout_matches[$stage_id] = $matches;
        }

        // ===== Handle auto_ teams =====
        // ชื่อของฝั่งที่ยังไม่ได้เลือกทีมมาจาก slot ใน teams_json ที่บันทึกไว้กับแถวนั้นโดยตรง
        // (team1_slot / team2_slot) ไม่ใช่การเดาจากลำดับแถว — ลำดับแถวเลื่อนทันทีที่มีการ
        // ลบคู่ทิ้ง และสูตรจับคู่ที่เดาไว้ตรงนี้ก็ไม่ตรงกับสูตรที่ MatchScheduler ใช้สร้างคู่จริง
        foreach($data as $kk => $dd) {
            if(empty($dd['teams_json'])) continue;

            $stage_teams = json_decode($dd['teams_json'], true);
            if(empty($stage_teams)) continue;

            $team1_slot = ($dd['team1_slot'] ?? null) !== null ? (int) $dd['team1_slot'] : null;
            $team2_slot = ($dd['team2_slot'] ?? null) !== null ? (int) $dd['team2_slot'] : null;

            $slot_team = function($slot) use ($stage_teams) {
                if($slot === null || !isset($stage_teams[$slot])) return null;
                return $stage_teams[$slot];
            };

            $slot_group = function($slot) use ($slot_team) {
                $t = $slot_team($slot);
                $group = $t['group'] ?? null;
                return $group !== null && $group !== '' ? (string) $group : null;
            };

            // เติม group_id จาก teams_json เฉพาะรอบลีกที่แถว match ไม่ได้เก็บ group_id ไว้
            if($dd['format'] === 'league_single' && empty($dd['group_id'])) {
                $team1_group = $slot_group($team1_slot);
                $team2_group = $slot_group($team2_slot);

                if($team1_group && $team2_group && $team1_group === $team2_group) {
                    $data[$kk]['group_id'] = $team1_group;
                    $dd['group_id'] = $team1_group;
                } elseif($team1_group || $team2_group) {
                    $fallback_group = $team1_group ?: $team2_group;
                    $data[$kk]['group_id'] = $fallback_group;
                    $dd['group_id'] = $fallback_group;
                }
            }

            $slot_label = function($slot) use ($stage_teams) {
                if($slot === null || !isset($stage_teams[$slot])) return null;
                $t = $stage_teams[$slot];
                // ป้ายที่ผู้ใช้ตั้งเองมาก่อนชื่อที่ระบบสร้างให้ (A1, A2, ...)
                $name = $t['custom_slot_label'] ?? ($t['name'] ?? null);
                $nameEn = $t['custom_slot_label_en'] ?? ($t['name_en'] ?? $name);
                return [
                    'name' => $name,
                    'name_en' => $nameEn,
                ];
            };

            // ข้ามการเติมชื่อถ้าทั้ง team1 และ team2 มี ID แล้ว (ใช้ != 0 เพื่อเช็คค่า 0 จริงๆ)
            if($dd['team1_id'] != 0 && $dd['team2_id'] != 0) continue;

            // ----- Legacy: แถวที่สร้างก่อนมีคอลัมน์ slot ยังต้องเดาจากตำแหน่งเหมือนเดิม -----
            if($team1_slot === null || $team2_slot === null) {
                if($dd['format'] === 'league_single' && !empty($dd['group_id'])) {
                    // array_filter คงคีย์เดิมไว้ คีย์นั้นคือ slot index ใน teams_json
                    $group_teams = array_filter($stage_teams, function($t) use ($dd) {
                        return ($t['group'] ?? null) === $dd['group_id'];
                    });
                    $group_slots = array_keys($group_teams);

                    $match_index = array_search($kk, $group_matches[$dd['stage_id']][$dd['group_id']] ?? []);

                    $matchups = [];
                    for ($i = 0; $i < count($group_slots); $i++) {
                        for ($j = $i + 1; $j < count($group_slots); $j++) {
                            $matchups[] = [$i, $j];
                        }
                    }

                    if($match_index !== false && isset($matchups[$match_index])) {
                        $team1_slot ??= $group_slots[$matchups[$match_index][0]] ?? null;
                        $team2_slot ??= $group_slots[$matchups[$match_index][1]] ?? null;
                    }
                } elseif($dd['format'] !== 'league_single') {
                    // คู่ knockout ที่ n ใช้ slot [2n, 2n+1]
                    $match_idx = array_search($kk, $knockout_matches[$dd['stage_id']] ?? []);
                    if($match_idx !== false) {
                        $team1_slot ??= $match_idx * 2;
                        $team2_slot ??= $match_idx * 2 + 1;
                    }
                }
            }

            // ใช้ชื่อจาก JSON เฉพาะฝั่งที่ยังไม่มี ID
            if($dd['team1_id'] == 0 && ($label1 = $slot_label($team1_slot)) !== null) {
                $data[$kk]['team1_name'] = $label1['name'];
                $data[$kk]['team1_name_en'] = $label1['name_en'];
            }
            if($dd['team2_id'] == 0 && ($label2 = $slot_label($team2_slot)) !== null) {
                $data[$kk]['team2_name'] = $label2['name'];
                $data[$kk]['team2_name_en'] = $label2['name_en'];
            }
        }

        $event_arr = $this->db->table('tournament_match_event tm')->select(' tm.*,team.logo,team.name as team_name,team.name_en as team_name_en, te.name AS event_name, te.name_en AS event_name_en, p1.first_name as p1_first_name,p1.last_name as p1_last_name,p1.image_path as p1_image_path, p2.first_name as p2_first_name,p2.last_name as p2_last_name,p2.image_path as p2_image_path, ') ->join('tbl_event te', 'tm.event_id = te.id', 'left') ->join('team', 'team.id = tm.team_id', 'left') ->join('player p1', 'p1.id = tm.player_1_id', 'left') ->join('player p2', 'p2.id = tm.player_2_id', 'left') ->whereIn( 'tm.match_id', function ($builder) use ($tour_id) { return $builder->select('match_id') ->from('tournament_match_result') ->where('tour_id', $tour_id); } ) ->orderBy('tm.match_id', 'ASC') ->orderBy('CAST(tm.minute AS UNSIGNED)', 'ASC', false) ->get() ->getResultArray();
        //die(print_r($event_arr));
        $tmp_event = array();
        foreach($event_arr as $kk1=>$dd1) {
            if($dd1['tour_id']!=0) {
                $tmp_event[$dd1['match_id']][] = $dd1;
            }
        }
        //die(print_r($tmp_event));
        foreach($data as $kk1=>$dd1) {
            if(isset($tmp_event[$dd1['match_id']])){
                $dd1['event'] = $tmp_event[$dd1['match_id']];
            }
            // แทน match_round ด้วย stage_name
            $dd1['match_round'] = $dd1['stage_name'];
            $data[$kk1] = $dd1;
        }

        //die(print_r($data));

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $data
        ]);
    }

    //Tour Program
    public function program($tour_id = '')
    {
        $round_name = $this->request->getGet('round_name');

        if ($tour_id) {
            $data["tournament"] = $this->tournamentModel->get_Tournament($tour_id);
            $data['tour_id'] = $tour_id;
            $data['allMatches'] = $this->tournamentModel->get_Tournament_Match_Result($tour_id, '', $round_name);
            $data['listBracket'] = $this->tournamentModel->getBracketData($tour_id);

            $data['round_name'] = $this->db->table('tournament_match_result')->select('match_round')->where('tour_id', $tour_id)->groupBy('match_round')->get()->getResultArray();
        }
         //echo '<pre>';
         //print_r($data);
         //die();
        return view('Modules\Admin\Views\Tournament\tournament_program_index.php', $data);
    }


    public function ajax_tournament_mvp()
    {
        $tour_id = $this->request->getGet('tour_id');
        $tournament_mvp = $this->db->table('tournament_mvp')
            ->select("*")
            ->where('tour_id',$tour_id)
            ->get()
            ->getRowArray();
        $num = !isset($tournament_mvp['num'])?'7':$tournament_mvp['num'];

        $result = $this->db->table('tournament_mvp_players')
            ->select("*")
            ->join('tournament_mvp mvp','mvp.tour_mvp_id=tournament_mvp_players.tour_mvp_id')
            ->where('mvp.tour_id',$tour_id)
            ->get()
            ->getResultArray();

        return $this->response->setJSON([
            'status' => 'success',
            'num'=> $num,
            'data'   => $result
        ]);
    }

    public function save_tournament_mvp()
    {
        $input = $this->request->getPost();

        if (empty($input['tour_id']) || empty($input['num'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'msg' => 'ข้อมูลไม่ครบถ้วน'
            ]);
        }

        $tour_id = $input['tour_id'];
        $num = $input['num'];
        $tournament_mvp_players = json_decode($input['tournament_mvp_players'] ?? '[]', true);


        // Get match data
        $tour_mvp = $this->db->table('tournament_mvp')
            ->select("*")
            ->where('tour_id',$tour_id)
            ->get()
            ->getRowArray();
        if (!$tour_mvp) {
            $this->db->table('tournament_mvp')->insert([
                'tour_id' => $tour_id,
                'num' => $num,
                'created_at' => date("Y-m-d H:i:s")
            ]);
            $tour_mvp_id = $this->db->insertID();
        }else {
            $tour_mvp_id = $tour_mvp['tour_mvp_id'];
            $this->db->table('tournament_mvp')->update([
                'num' => $num
            ],['tour_mvp_id'=>$tour_mvp['tour_mvp_id']]);
        }

        $this->db->transBegin();

        try {
            // Delete existing lineup for this team in this match
            $this->db->table('tournament_mvp_players')
                ->where('tour_mvp_id', $tour_mvp_id)
                ->delete();

            // Insert starters
            foreach ($tournament_mvp_players as $player) {
                //die(print_r($player));
                $this->db->table('tournament_mvp_players')->insert([
                    'tour_mvp_id' => $tour_mvp_id,
                    'player_id' => $player['id'],
                    'zone' => $player['zone'],
                    'created_at' => date("Y-m-d H:i:s")
                ]);
            }

            $this->db->transCommit();

            return $this->response->setJSON([
                'status' => 'success',
                'msg' => 'บันทึกสำเร็จ'
            ]);
        } catch (\Exception $e) {
            $this->db->transRollback();
            return $this->response->setJSON([
                'status' => 'error',
                'msg' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ]);
        }
    }
    public function get_tournament_ranking()
    {
        $tour_id = $this->request->getGet('tour_id');
        if (empty($tour_id)) {
            return $this->response->setJSON([
                'status' => 'error',
                'msg' => 'ไม่พบรหัสรายการ'
            ]);
        }

        $tournament = $this->db->table('tournament')
            ->select('number_ranking')
            ->where('tour_id', $tour_id)
            ->get()
            ->getRowArray();

        $rankings = $this->db->table('tournament_rankings')
            ->select('tournament_rankings.*, team.name as team_name, team.name_en as team_name_en, team.logo as team_logo')
            ->join('team', 'team.id = tournament_rankings.team_id', 'left')
            ->where('tournament_rankings.tour_id', $tour_id)
            ->orderBy('rank_no', 'ASC')
            ->get()
            ->getResultArray();
        
        return $this->response->setJSON([
            'status' => 'success',
            'number_ranking' => (int) ($tournament['number_ranking'] ?? 4),
            'data' => $rankings
        ]);
    }

    public function save_tournament_ranking()
    {
        $input = $this->request->getPost();
        if (empty($input['tour_id'])) {
            return $this->response->setJSON(['status' => 'error', 'msg' => 'ไม่พบรหัสรายการ']);
        }
        
        $tour_id = $input['tour_id'];
        $rankings = json_decode($input['rankings'] ?? '[]', true);
        if (!is_array($rankings)) {
            return $this->response->setJSON(['status' => 'error', 'msg' => 'รูปแบบข้อมูล Ranking ไม่ถูกต้อง']);
        }

        $number_ranking = max(1, min(32, (int) ($input['number_ranking'] ?? 4)));
        $matchTeams = $this->db->table('tournament_match_result')
            ->select('team1_id, team2_id')
            ->where('tour_id', $tour_id)
            ->get()
            ->getResultArray();
        $allowedTeamIds = [];
        foreach ($matchTeams as $matchTeam) {
            if (!empty($matchTeam['team1_id'])) {
                $allowedTeamIds[] = (int) $matchTeam['team1_id'];
            }
            if (!empty($matchTeam['team2_id'])) {
                $allowedTeamIds[] = (int) $matchTeam['team2_id'];
            }
        }

        $stageTeams = $this->db->table('tournament_stages')
            ->select('teams_json')
            ->where('tour_id', $tour_id)
            ->get()
            ->getResultArray();
        foreach ($stageTeams as $stageTeam) {
            $teams = json_decode($stageTeam['teams_json'] ?? '[]', true);
            if (!is_array($teams)) {
                continue;
            }

            foreach ($teams as $team) {
                $teamId = $team['id'] ?? 0;
                if (is_string($teamId) && strpos($teamId, 'auto_') === 0) {
                    continue;
                }
                if ((int) $teamId > 0) {
                    $allowedTeamIds[] = (int) $teamId;
                }
            }
        }
        $allowedTeamIds = array_values(array_unique($allowedTeamIds));
        $selectedTeamIds = [];
        $normalizedRankings = [];

        foreach ($rankings as $r) {
            $teamId = (int) ($r['team_id'] ?? 0);
            $rankNo = (int) ($r['rank_no'] ?? 0);

            if ($teamId <= 0) {
                continue;
            }
            if ($rankNo < 1 || $rankNo > $number_ranking) {
                return $this->response->setJSON(['status' => 'error', 'msg' => 'ลำดับ Ranking ไม่ถูกต้อง']);
            }
            if (!in_array($teamId, $allowedTeamIds, true)) {
                return $this->response->setJSON(['status' => 'error', 'msg' => 'พบทีมที่ไม่ได้เข้าร่วมรายการนี้']);
            }
            if (in_array($teamId, $selectedTeamIds, true)) {
                return $this->response->setJSON(['status' => 'error', 'msg' => 'ไม่สามารถระบุทีมซ้ำใน Ranking ได้']);
            }

            $selectedTeamIds[] = $teamId;
            $normalizedRankings[] = [
                'team_id' => $teamId,
                'rank_no' => $rankNo
            ];
        }
        
        $this->db->transStart();
        $this->db->table('tournament_rankings')->where('tour_id', $tour_id)->delete();
        
        if (!empty($normalizedRankings)) {
            foreach ($normalizedRankings as $r) {
                $this->db->table('tournament_rankings')->insert([
                    'tour_id' => $tour_id,
                    'team_id' => $r['team_id'],
                    'rank_no' => $r['rank_no']
                ]);
            }
        }
        
        $this->db->table('tournament')
            ->where('tour_id', $tour_id)
            ->update(['number_ranking' => $number_ranking]);
        
        $this->db->transComplete();
        
        if ($this->db->transStatus() === false) {
            return $this->response->setJSON(['status' => 'error', 'msg' => 'ไม่สามารถบันทึกข้อมูลได้']);
        }
        
        return $this->response->setJSON(['status' => 'success', 'msg' => 'บันทึกสำเร็จ']);
    }

    public function ajax_tournament_players()
    {
        $tour_id = $this->request->getGet('tour_id');
        $team_id = $this->request->getGet('team_id');

        if (empty($tour_id) && empty($team_id)) {
            return $this->response->setJSON([
                'status' => 'error',
                'msg'    => 'tour_id | team_id is required'
            ]);
        }

        $players = array();
        if($tour_id!='') {
            $players = $this->db->table('tournament_submitted_players tsp')
                ->select("
                    tsp.*,
                    p.id AS player_id,
                    p.first_name,
                    p.last_name,
                    p.image_path,
                    COALESCE(NULLIF(pos.parent_id,0), pos.id, p.player_position_id) AS player_position_id,
                    pos.name_th AS position_name_th,
                    pos.name_en AS position_name_en,
                    pos.parent_id,
                    tt.tour_team_id,
                    team.name AS team_name,
                    team.name_en AS team_name_en,
                    team.logo AS team_logo,
                    COALESCE(ape_tsl.shirt_number, p.shirt_number) AS shirt_number
                ", false)
                ->join('tournament_team tt', 'tt.tour_team_id = tsp.tour_team_id', 'inner')
                ->join('player p', 'p.id = tsp.player_id', 'inner')
                ->join('academy_player_enrollments ape_tsl', 'ape_tsl.player_id = p.id AND ape_tsl.team_id = tt.team_id AND ape_tsl.is_active = 1', 'left', false)
                ->join('tbl_player_position pos', 'pos.id = COALESCE(NULLIF(ape_tsl.position_id,0), p.player_position_id)', 'left', false)
                ->join('team team', 'tt.team_id = team.id', 'left')
                ->where('tt.tour_id', $tour_id)
                ->groupBy('p.id')
                ->orderBy('tt.tour_team_id', 'ASC')
                ->orderBy('player_position_id', 'ASC')
                ->orderBy('p.first_name', 'ASC')
                ->get()
                ->getResultArray();
        }

        if($team_id!='') {
            $players = $this->db->table('player')
                ->select("
                    player.*,
                    player.id AS player_id,
                    player.first_name,
                    player.last_name,
                    player.image_path,
                    COALESCE(NULLIF(pos.parent_id,0), pos.id) AS player_position_id,
                    coarse_tour.name_th AS position_name_th,
                    coarse_tour.name_en AS position_name_en,
                    COALESCE(NULLIF(pos.parent_id,0), pos.id) AS parent_id,
                    team.name AS team_name,
                    team.name_en AS team_name_en,
                    team.logo AS team_logo,
                    COALESCE(pt_tour.shirt_number, player.shirt_number) AS shirt_number
                ", false)
                ->join('academy_player_enrollments pt_tour', 'pt_tour.player_id = player.id AND pt_tour.team_id = ' . (int)$team_id . ' AND pt_tour.is_active = 1', 'LEFT', false)
                ->join('tbl_player_position pos', 'pos.id = COALESCE(NULLIF(pt_tour.position_id,0), player.player_position_id)', 'left', false)
                ->join('tbl_player_position coarse_tour', 'coarse_tour.id = COALESCE(NULLIF(pos.parent_id,0), pos.id)', 'left')
                ->join('team team', 'team.id = pt_tour.team_id', 'left')
                ->where('pt_tour.team_id', $team_id)
                ->groupBy('player.id')
                ->orderBy('COALESCE(NULLIF(pos.parent_id,0), pos.id)', 'ASC')
                ->orderBy('player.first_name', 'ASC')
                ->get()
                ->getResultArray();
        }

        //replace postion หลัก
        $positions = $this->db->table('tbl_player_position')
            ->select("*")
            ->get()
            ->getResultArray();
        $positions_arr = [];
        foreach($positions as $kk=>$dd) {
            $positions_arr[$dd['id']] = $dd['name_en'];
        }
        //
        //die(print_r($positions_arr));
        foreach($players as $kk=>$dd) {
            //die(print_r($dd));
            if(isset($positions_arr[$dd['parent_id']])) {
                $players[$kk]['pos'] = $positions_arr[$dd['parent_id']];
            }else {
                $players[$kk]['pos'] = array(0=>'N/A');
            }

            $encrypter = \Config\Services::encrypter();
            $link_report_people = base_url('player_ex/report_people/' . bin2hex($encrypter->encrypt($dd['id'])));
            $players[$kk]['link_report_people'] = $link_report_people;

        }

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $players
        ]);
    }
    public function ajax_tournament_list()
    {
        $page = $this->request->getGet('page');
        $data = $this->tournamentModel->get_TournamentList($page=='statistic'?0:1);
        if($page=='statistic') { //ที่ผ่านไปแล้ว
            $tmp_data = [];
            foreach($data as $key=>$value) {
                if($value['tournament_end_date']!='' && $value['tournament_end_date']< date('Y-m-d')) {
                    $tmp_data[] = $value;
                }
            }
            $data = $tmp_data;
        }else {
            $tmp_data = [];
            foreach($data as $key=>$value) {
                $today = strtotime(date('Y-m-d'));
                $start = strtotime($value['tournament_start_date'] . ' -7 days');
                $end   = strtotime($value['tournament_end_date']);

                if ($today >= $start && $today <= $end) {
                    $tmp_data[] = $value;
                }
            }
            $data = $tmp_data;
        }
        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $data
        ]);
    }
    public function ajax_tour_teams()
    {
        $tour_id = $this->request->getGet('tour_id');

        $this->table = "tournament_team";
        $builder = $this->db->table($this->table);
        $builder->select("{$this->table}.tour_team_id,{$this->table}.tour_id,{$this->table}.team_id, {$this->table}.register_status, {$this->table}.payment_slip, team.name, team.logo");
        $builder->join("team", "{$this->table}.team_id = team.id", "LEFT");

        $result = $this->db->table('tournament_team tt')
            ->select('
                tt.*,
                t.name AS team_name,
                t.name_en AS team_name_en,
                t.logo AS team_logo
            ')
            ->join(
                'tournament_match_result tmr',
                '(tt.team_id = tmr.team1_id OR tt.team_id = tmr.team2_id)',
                'LEFT'
            )
            ->join('team t', 'tt.team_id = t.id', 'LEFT');

        if($tour_id!='') {
            $result->where('tt.tour_id',$tour_id);
        }

            //->where('tmr.match_status !=', 0)
        $result = $result->groupBy('tt.team_id')   // ⭐ สำคัญ ป้องกันข้อมูลซ้ำ
            ->orderBy('tt.team_id', 'DESC')
            ->get()
            ->getResultArray();


        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $result
        ]);
    }

    public function ajax_team_statistics()
    {
        $team_id = $this->request->getGet('team_id');
        $tour_id = $this->request->getGet('tour_id');
        $page = $this->request->getGet('page');
        $result = array();

        if($tour_id!='') {
            $result = $this->tournamentModel->getStatTourAll($tour_id);
        }else if($team_id!='') {
            $result = $this->tournamentModel->getStatTeamAll($team_id);
            $result['team_info'] = $this->tournamentModel->get_TeamInfo($team_id);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $result
        ]);
    }

    public function ajax_dashboard_statistic() {
        $coach = $this->db->table('coach co')
            ->select('
                IFNULL(l.id, 0) AS id,
                IFNULL(l.name, "ไม่ระบุข้อมูล") AS level_name,
                COUNT(DISTINCT co.id) AS total_coach
            ')
            ->join(
                'coach_certs c',
                'c.coach_id = co.id AND c.is_active = 1',
                'left'
            )
            ->join(
                'tbl_certs_level l',
                'l.id = c.cert_level_id',
                'left'
            )
            ->groupBy('IFNULL(l.id, 0)')
            ->orderBy('CASE WHEN l.id IS NULL THEN 1 ELSE 0 END', '', false)
            ->orderBy('l.id', 'ASC')
            ->get()
            ->getResultArray();
        //

        $coach_arr = $this->db->table('coach co')
            ->select("
                co.first_name, co.last_name,co.image_path,
                c.cert_level_id,
                COALESCE(l.name, 'N/A') AS cert_name
            ", false) // ใส่ false เพื่อหยุดการ escape query ของ CI ไม่ให้เติม backticks ผิดที่
            ->join(
                'coach_certs c',
                'c.coach_id = co.id AND c.is_active = 1',
                'left'
            )
            ->join(
                'tbl_certs_level l',
                'l.id = c.cert_level_id',
                'left'
            )
            ->get()
            ->getResultArray();

        $builder = $builder = $this->db->table('player p');

        $builder->select('
            parent.id,
            parent.name_en,
            COUNT(p.id) AS total_player
        ');

        // JOIN sub (ใช้ Raw SQL เพราะมี JSON)
        $builder = $this->db->table('player p');

        $builder->select("
            parent.id,
            COALESCE(parent.name_en, 'ไม่ระบุข้อมูล') AS name_en,
            COUNT(p.id) AS total_player
        ", false); // false เพื่อไม่ให้ CI escape COALESCE

        // ใช้ตำแหน่งจาก enrollment เป็นหลัก (primary) fallback เป็น sub-position เดิมใน player
        $builder->join(
            'academy_player_enrollments ape_pp',
            'ape_pp.player_id = p.id AND ape_pp.is_active = 1 AND ape_pp.is_primary = 1',
            'left',
            false
        );

        $builder->join(
            'tbl_player_position sub',
            "sub.id = COALESCE(
                NULLIF(ape_pp.position_id, 0),
                CASE
                    WHEN JSON_VALID(p.player_sub_position_id)
                    THEN JSON_UNQUOTE(JSON_EXTRACT(p.player_sub_position_id, '$[0]'))
                    ELSE NULL
                END
            )",
            'left',
            false
        );

        $builder->join(
            'tbl_player_position parent',
            'parent.id = sub.parent_id AND parent.parent_id = 0',
            'left'
        );

        $builder->where('p.is_active', 1);

        /* ✅ เอา NULL มาด้วย */
        $builder->groupStart()
                ->whereIn('parent.id', [1,2,3,4])
                ->orWhere('parent.id IS NULL', null, false)
                ->groupEnd();

        $builder->groupBy(['parent.id', 'parent.name_en']);

        /* ✅ ให้ NULL ไปอยู่ล่างสุด */
        $builder->orderBy('parent.id IS NULL', '', false);
        $builder->orderBy('parent.id', 'ASC');

        $player_position = $builder->get()->getResultArray();

        $result['coach'] = $coach;
        $result['coach_arr'] = $coach_arr;
        $result['player_position'] = $player_position;

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $result
        ]);
    }

    public function ajax_map_statistic() {

        $coachSub = $this->db->table('coach')
            ->select('team_id, COUNT(*) total_coach')
            ->groupBy('team_id');

        $playerSub = $this->db->table('academy_player_enrollments')
            ->select('team_id, COUNT(*) total_player')
            ->where('is_active', 1)
            ->groupBy('team_id');

        // ✅ เพิ่ม match (team1 + team2 และ match_status != 0)
        $matchSub = $this->db->table('tournament_match_result')
            ->select('team_id, COUNT(*) total_match')
            ->from('(
                SELECT team1_id AS team_id
                FROM tournament_match_result
                WHERE match_status != 0

                UNION ALL

                SELECT team2_id AS team_id
                FROM tournament_match_result
                WHERE match_status != 0
            ) tm', false)
            ->groupBy('team_id');


        $team = $this->db->table('team t')
            ->select('
                t.id,
                t.name,
                t.name_en,
                t.latitude,
                t.longitude,
                t.logo,
                IFNULL(c.total_coach,0) AS total_coach,
                IFNULL(p.total_player,0) AS total_player,
                IFNULL(m.total_match,0) AS total_match
            ')
            // เพิ่ม INNER JOIN กับ academy_teams
            ->join('academy_teams act', 't.id = act.team_id', 'inner')

            // Join Sub-queries เดิมของคุณ
            ->join('('.$coachSub->getCompiledSelect().') c','c.team_id = t.id','left',false)
            ->join('('.$playerSub->getCompiledSelect().') p','p.team_id = t.id','left',false)
            ->join('('.$matchSub->getCompiledSelect().') m','m.team_id = t.id','left',false)

            // เงื่อนไข Where จาก SQL ใหม่
            ->where('t.parent_team_id IS NULL', null, false)

            // เงื่อนไข Where เดิมของคุณ
            ->where('t.province_id IS NOT NULL', null, false)
            ->where('t.latitude IS NOT NULL', null, false)
            ->where('t.longitude IS NOT NULL', null, false)

            // จัดเรียงลำดับตาม act.sort_order
            ->orderBy('act.sort_order', 'ASC')

            ->get()
            ->getResultArray();

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $team
        ]);
    }

    //End Tour Program

    public function getInputEventCard()
    {
        $data['data'] = $this->request->getPost();
        $data['event_types'] = $this->tournamentModel->getEventTypes();
        $data['player'] = $this->db->table('player')
            ->select('player.*, COALESCE(NULLIF(tbl_player_position.parent_id,0), tbl_player_position.id) as player_position_id, coarse_ec.name_th as position_name')->where('player.id', $this->request->getPost('player_id'))
            ->join("academy_player_enrollments pt_ec", "pt_ec.player_id = player.id AND pt_ec.team_id = " . (int)$this->request->getPost('team_id') . " AND pt_ec.is_active = 1", "LEFT")
            ->join("tbl_player_position", "tbl_player_position.id = COALESCE(NULLIF(pt_ec.position_id,0), player.player_position_id)", "left", false)
            ->join("tbl_player_position coarse_ec", "coarse_ec.id = COALESCE(NULLIF(tbl_player_position.parent_id,0), tbl_player_position.id)", "left")
            ->get()->getRowArray();

        $data['real_player'] = $this->playerStateModel->getPlayerToMatch($data['data']['tour_id'], $data['data']['match_id'], $data['data']['team_id'], 0);
        $data['sub_player'] = $this->playerStateModel->getPlayerToMatch($data['data']['tour_id'], $data['data']['match_id'], $data['data']['team_id'], 1);
        return view('Modules\Admin\Views\Tournament\add_event_card.php', $data);
    }

    public function getEventsTable($match_id)
    {
        $data['data'] = $this->tournamentModel->getOnceTournament_Match_Result($match_id);
        $data['events'] = $this->tournamentModel->getMatchEvent($match_id);
        return view('Modules\Admin\Views\Tournament\table_events.php', $data);
    }

    public function deleteEvent()
    {
        $id = $this->request->getPost('id');
        $match_id = $this->request->getPost('match_id');
        $result['score'] = $this->tournamentModel->deleteEvent($id, $match_id);
        $result['table'] = $this->getEventsTable($match_id);
        return $this->response->setJSON($result);
    }

    public function delete_tournament()
    {
        $input = $this->request->getPost();
        $this->tournamentModel->deleteTournament($input['id']);
        return $this->response->setJSON(['status' => 'success', 'msg' => 'ลบข้อมูลเรียบร้อยแล้ว']);
    }

    public function toggle_tournament_field()
    {
        $id = $this->request->getPost('id');
        $field = $this->request->getPost('field');
        $value = (int) $this->request->getPost('value');

        $allowedFields = ['is_active', 'is_registration_open'];
        if (!$id || !in_array($field, $allowedFields)) {
            return $this->response->setJSON(['status' => 'error', 'msg' => 'ข้อมูลไม่ถูกต้อง']);
        }

        // ถ้าจะเปิดรับสมัคร ต้องเช็คว่ายังไม่เลยวันสิ้นสุดรับสมัคร
        if ($field === 'is_registration_open' && $value === 1) {
            $tour = $this->db->table('tournament')->select('end_date')->where('tour_id', $id)->get()->getRow();
            if ($tour && !empty($tour->end_date) && $tour->end_date !== '0000-00-00' && date('Y-m-d') > $tour->end_date) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'msg' => lang('Common.registration_expired_cannot_open')
                ]);
            }
        }

        $this->db->table('tournament')
            ->where('tour_id', $id)
            ->update([$field => $value]);

        return $this->response->setJSON(['status' => 'success', 'new_value' => $value]);
    }

    public function clone_tournament($tour_id = null)
    {
        if (!$tour_id) {
            return redirect()->to(base_url('admin/tournament'))->with('error', 'ไม่พบรหัสทัวร์นาเมนต์');
        }

        // Clone the tournament
        $newTourId = $this->tournamentModel->cloneTournament($tour_id);

        if ($newTourId) {
            return redirect()->to(base_url('admin/tournament/tournament-manage/' . $newTourId))->with('success', 'คัดลอกทัวร์นาเมนต์เรียบร้อยแล้ว');
        } else {
            return redirect()->to(base_url('admin/tournament'))->with('error', 'ไม่สามารถคัดลอกทัวร์นาเมนต์ได้');
        }
    }

    public function create_match_tournament()
    {
        $input = $this->request->getPost();
        $tour_id = @$input['tour_id'];

        $data['team_options_selected'] = $this->tournamentModel->getTournamentTeamSelected($tour_id);
        // ตัวอย่างการใช้งาน
        // echo "<pre>" ;
        $startDate = "2025-04-20";
        $matchesPerDay = 4;
        $matchTimes = ["16:00", "18:00", "20:00", "22:00"];
        $fieldsPerDay = 3;

        // ตารางแบบลีก เหย้า-เยือน
        // $leagueSchedule = $this->generateLeagueSchedule(array_column($data['team_options_selected'], 'team_id'), true, $startDate, $matchesPerDay, $matchTimes, $fieldsPerDay);
        // echo "\nลีก เหย้า-เยือน:\n";
        // ตารางแบบบอลถ้วย นัดเดียว

        //ลบข้อมูลที่มีการจัดการแข่ขันก่อนหน้านี้ให้หมด
        $this->tournamentModel->clearMatchTournament($tour_id);
        $result = $this->generateCupSchedule(array_column($data['team_options_selected'], 'team_id'), true, $startDate, $matchesPerDay, $matchTimes, $fieldsPerDay, $tour_id);
        // return $cupSchedule;
        return $result;
    }

    public function swap_match_teams()
    {
        $matchId = $this->request->getPost('match_id');
        $result = $this->tournamentModel->swapMatchTeams($matchId);
        return $this->response->setJSON($result);
    }

    public function save_tournament_match_result()
    {
        $input = $this->request->getPost();

        if (empty($input['tour_id'])) {
            $input['tour_id'] = 0;
        }
        // save event
        // echo '<pre>';
        // print_r($input);
        // die();
        $this->tournamentModel->saveTournamentMatchEvent($input['tour_id'], $input['match_id'], @$input['events']);
        $result = $this->tournamentModel->manageTournamentMatch($input);
        return $this->response->setJSON($result);
    }

    public function save_tournament_match_result_multi()
    {
        $input = $this->request->getPost();

        $allowedFields = ['date', 'time', 'stadium', 'referee'];
        $results = [];
        $successCount = 0;
        $errorCount = 0;
        $tourId = isset($input['tour_id']) ? (int) $input['tour_id'] : 0;
        $matchIdsInput = $this->request->getPost('match_ids');

        if (!empty($matchIdsInput) && !empty($input['field']) && in_array($input['field'], $allowedFields, true)) {
            $field = $input['field'];
            $value = $input['value'] ?? null;
            $matchIds = is_array($matchIdsInput) ? $matchIdsInput : [$matchIdsInput];
            $matchIds = array_values(array_unique(array_filter(array_map('intval', $matchIds))));

            if ($tourId <= 0) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'msg' => 'เกิดข้อผิดพลาด: ไม่พบ tour_id'
                ]);
            }

            if (empty($matchIds)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'msg' => 'เกิดข้อผิดพลาด: ไม่พบ match_id'
                ]);
            }

            foreach ($matchIds as $matchId) {
                $inputItem = [
                    'tour_id' => $tourId,
                    'match_id' => $matchId,
                    $field => $value,
                ];

                $itemResult = $this->tournamentModel->manageTournamentMatch($inputItem);
                $results[] = $itemResult;

                if (($itemResult['status'] ?? '') === 'success') {
                    $successCount++;
                } else {
                    $errorCount++;
                }
            }

            return $this->response->setJSON([
                'status' => $errorCount === 0 ? 'success' : 'warning',
                'msg' => $errorCount === 0 ? 'บันทึกข้อมูลสำเร็จ' : 'บันทึกบางรายการไม่สำเร็จ',
                'success_count' => $successCount,
                'error_count' => $errorCount,
                'multi' => $results
            ]);
        }

        if (empty($input['match_id'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'msg' => 'เกิดข้อผิดพลาด: ไม่พบ match_id'
            ]);
        }

        if ($tourId <= 0) {
            return $this->response->setJSON([
                'status' => 'error',
                'msg' => 'เกิดข้อผิดพลาด: ไม่พบ tour_id'
            ]);
        }

        foreach ($input['match_id'] as $dd) {
            $matchId = isset($dd['match_id']) ? (int) $dd['match_id'] : 0;
            if ($matchId <= 0) {
                $results[] = [
                    'status' => 'error',
                    'msg' => 'เกิดข้อผิดพลาด: ไม่พบ match_id'
                ];
                $errorCount++;
                continue;
            }

            $inputItem = [
                'tour_id' => $tourId,
                'match_id' => $matchId
            ];

            foreach ($allowedFields as $field) {
                if (array_key_exists($field, $dd)) {
                    $inputItem[$field] = $dd[$field];
                }
            }

            if (count($inputItem) <= 2) {
                $results[] = [
                    'status' => 'error',
                    'msg' => 'เกิดข้อผิดพลาด: ไม่พบข้อมูลที่ต้องการอัปเดต'
                ];
                $errorCount++;
                continue;
            }

            $itemResult = $this->tournamentModel->manageTournamentMatch($inputItem);
            $results[] = $itemResult;

            if (($itemResult['status'] ?? '') === 'success') {
                $successCount++;
            } else {
                $errorCount++;
            }
        }

        return $this->response->setJSON([
            'status' => $errorCount === 0 ? 'success' : 'warning',
            'msg' => $errorCount === 0 ? 'บันทึกข้อมูลสำเร็จ' : 'บันทึกบางรายการไม่สำเร็จ',
            'success_count' => $successCount,
            'error_count' => $errorCount,
            'multi' => $results
        ]);
    }


    public function saveMatchEvent()
    {
        $input = $this->request->getPost();
        $result['score'] = $this->tournamentModel->saveMatchEvent($input);
        $result['table'] = $this->getEventsTable($input['match_id']);
        return $this->response->setJSON($result);
    }

    public function updateMatchStatus()
    {
        $input = $this->request->getPost();
        $result['status'] = $this->tournamentModel->manageMatchStatus($input);
        return $this->response->setJSON($result);
    }

    public function create_match_event_form()
    {
        $input = $this->request->getPost();
        $data['index'] = $input['index'];
        $data['data'] = $this->tournamentModel->getOnceTournament_Match_Result($input['match_id']);
        $data['event_types'] = $this->tournamentModel->getEventTypes();
        return view('Modules\Admin\Views\Tournament\tournament_match_event_form', $data);
    }

    public function create_player_option()
    {
        $input = $this->request->getPost();
        // $data['players'] = $this->playerModel->get_PlayerList($input['team_id']);
        $data['players'] = $this->playerStateModel->getPlayerDataToMatch($input['tour_id'], $input['match_id'], $input['team_id'], '');

        return view('Modules\Admin\Views\Tournament\options_players', $data);
    }

    public function tournament_match_detail_manage($tour_id = '', $match_id = '')
    {
        $data = ['mode' => ($match_id == '' ? 'add' : 'edit')];

        //if ($tour_id) {
        $data['tour_id'] = $tour_id;
        $data['match_id'] = $match_id;
        $matchData = $this->tournamentModel->getOnceTournament_Match_Result($match_id);
        $data['data'] = $matchData;

        // ส่งข้อมูลทีมไปให้ view ใช้งานได้โดยตรง
        $data['team1_id'] = $matchData['team1_id'] ?? '';
        $data['team2_id'] = $matchData['team2_id'] ?? '';
        $data['date'] = $matchData['date'] ?? '';
        $data['time'] = $matchData['time'] ?? '';
        $data['stadium'] = $matchData['stadium'] ?? '';
        $data['referee'] = $matchData['referee'] ?? '';
        $data['team1_score'] = $matchData['team1_score'] ?? '';
        $data['team2_score'] = $matchData['team2_score'] ?? '';
        $data['group_id'] = $matchData['group_id'] ?? '';

        // ดึงทีมทั้งหมดในทัวร์นาเมนต์ (รวมทีมที่ตกรอบด้วย) เพื่อให้เลือกได้
        $data['match_options'] = $this->tournamentModel->getTeamOptions($tour_id, false);

        // ดึงรายการสนามสำหรับ dropdown
        $stadiumModel = new \Modules\Admin\Models\StadiumModel();
        $data['stadiums'] = $stadiumModel->getActiveStadiums();

        //}
        return view('Modules\Admin\Views\Tournament\tournament_match_detail_manage.php', $data);
    }

    function isPowerOfTwo($number)
    {
        return ($number & ($number - 1)) == 0 && $number > 0;
    }

    function generateLeagueSchedule($teams, $startDate, $matchesPerDay, $matchTimes, $fieldsPerDay, $tour_id, $homeAway = true)
    {
        /**
         * สร้างตารางแข่งขันแบบลีก พร้อมกำหนดวันที่และเวลา รองรับหลายสนาม
         * @param array $teams รายชื่อทีม
         * @param bool $homeAway True = เหย้าเยือน, False = นัดเดียว
         * @param string $startDate วันที่เริ่มต้นการแข่งขัน (YYYY-MM-DD)
         * @param int $matchesPerDay จำนวนการแข่งขันต่อวัน
         * @param array $matchTimes เวลาที่กำหนดให้แต่ละแมตช์
         * @param int $fieldsPerDay จำนวนสนามที่ใช้ต่อวัน
         * @return array ตารางการแข่งขัน
         */
        $matches = [];
        foreach (array_keys($teams) as $home) {
            foreach (array_keys($teams) as $away) {
                if ($home !== $away) {
                    $matches[] = [$home, $away];
                    if (!$homeAway) {
                        break;
                    }
                }
            }
        }
        return $this->scheduleMatches($matches, $teams, $startDate, $matchesPerDay, $matchTimes, $fieldsPerDay);
    }

    function generateCupSchedule($teams, $singleMatch = true, $startDate, $matchesPerDay, $matchTimes, $fieldsPerDay, $tour_id)
    {
        /**
         * สร้างตารางแข่งขันแบบบอลถ้วย (Knockout) พร้อมกำหนดวันที่และเวลา รองรับหลายสนาม
         * @param array $teams รายชื่อทีม
         * @param bool $singleMatch True = นัดเดียว, False = เหย้าเยือน
         * @param string $startDate วันที่เริ่มต้นการแข่งขัน (YYYY-MM-DD)
         * @param int $matchesPerDay จำนวนการแข่งขันต่อวัน
         * @param array $matchTimes เวลาที่กำหนดให้แต่ละแมตช์
         * @param int $fieldsPerDay จำนวนสนามที่ใช้ต่อวัน
         * @return array ตารางการแข่งขันทุกรอบ
         */
        // if (count($teams) % 2 != 0) {
        //     throw new Exception("จำนวนทีมต้องเป็นเลขคู่สำหรับบอลถ้วย");
        // }
        if (!$this->isPowerOfTwo(count($teams))) {
            // throw new Exception("จำนวนทีมต้องเป็นเลข 2^n (2, 4, 8, 16, ...) สำหรับบอลถ้วย");
            $response = array(
                'status' => 'error',
                'msg' => 'จำนวนทีมไม่ครบ ควรต้องมีจำนวนทีมตั้งแต่ (2, 4, 8, 16, ...) สำหรับบอลถ้วย'
            );
            return $this->response->setJson($response);
        }

        $rounds = [];
        // $currentRoundTeams = array_keys($teams);
        $currentRoundTeams = $teams;
        while (count($currentRoundTeams) > 1) {
            $matches = [];
            shuffle($currentRoundTeams);
            for ($i = 0; $i < count($currentRoundTeams); $i += 2) {
                $matches[] = [$currentRoundTeams[$i], $currentRoundTeams[$i + 1]];
                if (!$singleMatch) {
                    $matches[] = [$currentRoundTeams[$i + 1], $currentRoundTeams[$i]];
                }
            }

            $rounds[] = $this->scheduleMatches($matches, $teams, $startDate, $matchesPerDay, $matchTimes, $fieldsPerDay, $tour_id);

            // คัดเลือกทีมที่ผ่านเข้ารอบถัดไป (สมมติว่าทีมแรกในแต่ละคู่ชนะ)
            $currentRoundTeams = [];
            foreach ($matches as $match) {
                // $currentRoundTeams[] = $match[0];
                $currentRoundTeams[] = array(0 => 0);
            }
        }

        // return $rounds;
        $response = array(
            'status' => 'success',
            'msg' => 'จัดรายการแข่งขันสำเร็จ'
        );
        return $this->response->setJson($response);
    }

    function scheduleMatches($matches, $teams, $startDate, $matchesPerDay, $matchTimes, $fieldsPerDay, $tour_id = '')
    {
        /**
         * กำหนดวันที่และเวลาสำหรับการแข่งขัน รองรับหลายสนาม และแข่งพร้อมกันได้
         * @param array $matches รายชื่อคู่แข่งขัน
         * @param array $teams รายชื่อทีมที่มี ID
         * @param string $startDate วันที่เริ่มต้นการแข่งขัน (YYYY-MM-DD)
         * @param int $matchesPerDay จำนวนการแข่งขันต่อวัน
         * @param array $matchTimes เวลาที่กำหนดให้แต่ละแมตช์
         * @param int $fieldsPerDay จำนวนสนามที่ใช้ต่อวัน
         * @return array ตารางการแข่งขันพร้อมวันที่และเวลา
         */
        $schedule = [];
        $currentDate = strtotime($startDate);
        $matchIndex = 0;

        while ($matchIndex < count($matches)) {
            $dateKey = date('Y-m-d', $currentDate);
            $fieldIndex = 1;
            $timeIndex = 0;

            for ($i = 0; $i < $matchesPerDay && $matchIndex < count($matches); $i++) {
                if ($fieldIndex > $fieldsPerDay) {
                    $currentDate = strtotime("+1 days", $currentDate);
                    $dateKey = date('Y-m-d', $currentDate);
                    $fieldIndex = 1;
                    $timeIndex = 0;
                }

                $schedule[$dateKey][] = [
                    'teams' => [
                        'home' => ['id' => $matches[$matchIndex][0]],
                        'away' => ['id' => $matches[$matchIndex][1]]
                    ],
                    'time' => $matchTimes[$timeIndex % count($matchTimes)],
                    'field' => $fieldIndex
                ];

                $data_input = [
                    'tour_id' => $tour_id,
                    'team1_id' => @$matches[$matchIndex][0], //home team
                    'team1_score' => '', //home score
                    'team2_id' => @$matches[$matchIndex][1], //alway team
                    'team2_score' => '', //alway score
                    'result' => '',
                    'referee' => '',
                    'stadium' => $fieldIndex,
                    'date' => $dateKey,
                    'time' => $matchTimes[$timeIndex % count($matchTimes)],
                    'create_by' => session()->get('member_id'),
                    'create_date' => date('Y-m-d H:i:s'),
                    'update_by' => session()->get('member_id'),
                    'update_date' => date('Y-m-d H:i:s')
                ];
                // print_r($schedule[$dateKey]);
                $this->tournamentModel->manageTournamentMatch($data_input);

                $matchIndex++;
                if ($fieldIndex >= $fieldsPerDay) {
                    $timeIndex++; // เปลี่ยนเวลาในรอบใหม่เมื่อสนามเต็ม
                }
                $fieldIndex = ($fieldIndex % $fieldsPerDay) + 1;
            }
        }
        return $schedule;
    }





}
 
