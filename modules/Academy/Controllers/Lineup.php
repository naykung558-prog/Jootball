<?php

namespace Modules\Academy\Controllers;

use App\Controllers\BaseController;
use Modules\Academy\Models\LineupModel;
use Modules\Player\Models\PlayerModel;

/**
 * LineupController
 * 
 * Main controller for Lineup and Player Document management.
 * Main flow: Lineup (tournament & match selection) -> Player Document Form
 */
class Lineup extends BaseController
{
    protected $lineupModel;
    protected $playerModel;

    public function __construct()
    {
        $this->lineupModel = new LineupModel();
        $this->playerModel = new PlayerModel();
    }

    /**
     * MAIN PAGE: Show lineup management page (tournament selection + today's matches)
     * GET /academy/lineup/{academy_id?}
     * 
     * @param int|null $academy_id
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function index($academy_id = null)
    {
        // Resolve academy_id
        if (!$academy_id) {
            $academy = $this->getUserAcademy();
            if (!$academy) {
                return redirect()->to(base_url('academy/register'))
                    ->with('error', 'ไม่พบข้อมูล Academy');
            }
            $academy_id = $academy['id'];
        }

        // Check access
        if (!$this->canViewAcademy($academy_id)) {
            return redirect()->to(base_url('academy/join'))
                ->with('error', lang('Common.academy_no_permission'));
        }

        $academy = $this->lineupModel->getAcademy($academy_id);
        if (!$academy) {
            return redirect()->to(base_url('academy/register'))
                ->with('error', lang('Common.academy_not_found'));
        }

        // Get teams for academy
        $teams = $this->lineupModel->getAcademyTeams($academy_id);

        $data = [
            'academy' => $academy,
            'teams' => $teams,
            'canManage' => $this->canManageAcademy($academy_id)
        ];

        return view('Modules\Academy\Views\lineup.php', $data);
    }

    /**
     * AJAX: Get tournaments for selected team
     * GET /lineup/get-tournaments?team_id={id}
     */
    public function getTournaments()
    {
        $team_id = $this->request->getGet('team_id');
        
        if (!$team_id) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'No team_id']);
        }

        $tournaments = $this->lineupModel->getActiveTournaments($team_id);
        
        // Add base_url to logo
        foreach ($tournaments as &$tournament) {
            if (!empty($tournament['logo'])) {
                $tournament['logo_url'] = base_url($tournament['logo']);
            }
        }
        
        return $this->response->setJSON(['status' => 'success', 'data' => $tournaments]);
    }

    /**
     * AJAX: Get matches for selected team and tournament
     * GET /lineup/get-matches?team_id={id}&tournament_id={id}
     */
    public function getMatches()
    {
        $team_id = $this->request->getGet('team_id');
        $tournament_id = $this->request->getGet('tournament_id');
        
        if (!$team_id) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'No team_id']);
        }

        // Get all matches for this team
        $teams = [['id' => $team_id]];
        $allMatches = $this->lineupModel->getTeamMatches($teams);
        
        // Filter by tournament if provided
        if ($tournament_id) {
            $allMatches = array_filter($allMatches, function($m) use ($tournament_id) {
                return $m['tour_id'] == $tournament_id;
            });
        }
        
        return $this->response->setJSON(['status' => 'success', 'data' => array_values($allMatches)]);
    }

    /**
     * Alias for index() - keep lineup route working
     */
    public function lineup($academy_id = null)
    {
        return $this->index($academy_id);
    }

    /**
     * Show player document form (lineup form for a specific match)
     * GET /lineup/form?team_id={id}&tournament_id={id}&match_id={id}
     *
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function form()
    {
        $team_id = $this->request->getGet('team_id');
        $tournament_id = $this->request->getGet('tournament_id');
        $match_id = $this->request->getGet('match_id');

        // Validate required parameters
        if (!$team_id) {
            return redirect()->to(base_url('lineup'))
                ->with('error', 'กรุณาเลือกทีม');
        }
        if (!$tournament_id) {
            return redirect()->to(base_url('lineup'))
                ->with('error', 'กรุณาเลือกทัวร์นาเมนต์');
        }

        // Get team data
        $teamData = $this->lineupModel->getTeam($team_id);
        if (!$teamData) {
            return redirect()->to(base_url('lineup'))
                ->with('error', 'ไม่พบข้อมูลทีม');
        }

        // Get tournament data
        $tournament = $this->lineupModel->getTournament($tournament_id);
        if (!$tournament) {
            return redirect()->to(base_url('lineup'))
                ->with('error', 'ไม่พบข้อมูลทัวร์นาเมนต์');
        }

        // Verify team is registered in tournament (skip for mockup)
        // TODO: Enable this when real data is implemented
        // $tourTeam = $this->lineupModel->getTournamentTeam($tournament_id, $team_id);
        // if (!$tourTeam) {
        //     return redirect()->to(base_url('lineup'))
        //         ->with('error', 'ทีมนี้ไม่ได้ลงทะเบียนในทัวร์นาเมนต์');
        // }

        // Find academy_id from team
        $academy = $this->lineupModel->db
            ->table('academy_teams')
            ->where('team_id', (int)$team_id)
            ->get()
            ->getRowArray();
        
        $academy_id = $academy['academy_id'] ?? null;

        // Get players from tournament_submitted_players (same as ajax_tournament_players)
        $players = $this->lineupModel->getPlayersByTeam($team_id, $tournament_id);

        $data = [
            'team' => $teamData,
            'tournament' => $tournament,
            'tournament_id' => $tournament_id,
            'match_id' => $match_id,
            'academy_id' => $academy_id,
            'players' => $players
        ];

        return view('Modules\Academy\Views\export\player_document_form', $data);
    }

    /**
     * Save player line-up data (AJAX POST)
     * POST /lineup/save
     *
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function save()
    {
        // Get JSON input
        $input = json_decode(file_get_contents('php://input'));
        
        // Debug: log received data
        log_message('debug', 'Save lineup input: ' . json_encode($input));

        if (empty($input)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'No input data received'
            ]);
        }

        if (empty($input->tournament_id)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'กรุณาเลือกทัวร์นาเมนต์ (tournament_id: ' . ($input->tournament_id ?? 'empty') . ')'
            ]);
        }

        if (empty($input->team_id)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'กรุณาเลือกทีม (team_id: ' . ($input->team_id ?? 'empty') . ')'
            ]);
        }

        $tourId = (int)$input->tournament_id;
        $teamId = (int)$input->team_id;
        $matchId = isset($input->match_id) ? (int)$input->match_id : 0;

        // Skip tournament-team verification for now (mockup data)
        // TODO: Enable this when real tournament_team data is available
        // $tourTeam = $this->lineupModel->getTournamentTeam($tourId, $teamId);
        // if (!$tourTeam) {
        //     return $this->response->setJSON([
        //         'status' => 'error',
        //         'message' => 'ไม่พบข้อมูลทีมในทัวร์นาเมนต์'
        //     ]);
        // }

        // Save line-up data
        $players = $input->players ?? [];

        try {
            log_message('debug', 'Saving lineup data: tourId=' . $tourId . ', teamId=' . $teamId . ', matchId=' . $matchId . ', players=' . json_encode($players));
            
            $result = $this->lineupModel->saveLineUpData($tourId, $teamId, $matchId, $players);
            
            log_message('debug', 'Save result: ' . json_encode($result));

            return $this->response->setJSON([
                'status' => 'success',
                'message' => lang('Common.save_success')
            ]);
        } catch (\Exception $e) {
            log_message('error', 'PlayerDocument save error: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Load saved line-up data (AJAX GET)
     * GET /lineup/load?team_id={id}&tournament_id={id}&match_id={id}
     *
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function load()
    {
        $teamId = $this->request->getGet('team_id');
        $tournamentId = $this->request->getGet('tournament_id');
        $matchId = $this->request->getGet('match_id');

        if (!$teamId) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'ไม่มี team_id'
            ]);
        }

        if (!$tournamentId) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'ไม่มี tournament_id'
            ]);
        }

        if (!$matchId) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'ไม่มี match_id'
            ]);
        }

        try {
            $result = $this->lineupModel->getLineUpData($tournamentId, $teamId, $matchId);

            return $this->response->setJSON([
                'status' => 'success',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            log_message('error', 'PlayerDocument load error: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'เกิดข้อผิดพลาดในการโหลดข้อมูล'
            ]);
        }
    }

    /**
     * Get current user's first academy.
     */
    private function getUserAcademy()
    {
        $academies = [];

        $coachUserId = session()->get('coach_user_id');
        if ($coachUserId) {
            $academies = $this->lineupModel->getAcademyByCoach($coachUserId);
        }

        if (empty($academies)) {
            $userId = session()->get('user_id');
            if ($userId) {
                $academies = $this->lineupModel->getAcademyByUser($userId);
            }
        }

        if (empty($academies)) {
            return null;
        }

        $rolePriority = ['owner' => 1, 'admin' => 2];
        $best = null;
        $bestPriority = PHP_INT_MAX;

        foreach ($academies as $academy) {
            $role = $academy['role'] ?? '';
            $priority = $rolePriority[$role] ?? 99;
            if ($priority < $bestPriority) {
                $bestPriority = $priority;
                $best = $academy;
            }
        }

        return $best ?? $academies[0];
    }

    /**
     * Check if current user can view academy.
     */
    private function canViewAcademy($academy_id)
    {
        $userId = session()->get('user_id');
        $coachUserId = session()->get('coach_user_id');
        $role = $this->lineupModel->getMemberRole($academy_id, $userId, $coachUserId);
        return $role !== null;
    }

    /**
     * Check if current user can manage academy.
     */
    private function canManageAcademy($academy_id)
    {
        $userId = session()->get('user_id');
        $coachUserId = session()->get('coach_user_id');
        $role = $this->lineupModel->getMemberRole($academy_id, $userId, $coachUserId);
        return in_array($role, ['owner', 'admin']);
    }
}
