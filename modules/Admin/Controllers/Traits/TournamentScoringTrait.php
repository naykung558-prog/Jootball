<?php

namespace Modules\Admin\Controllers\Traits;

use App\Libraries\MatchScheduler;
use App\Controllers\BaseController;
use \Hermawan\DataTables\DataTable;
use Modules\Admin\Models\TeamModel;
use Modules\Admin\Models\TournamentModel;
use Modules\Player\Models\PlayerModel;
use Modules\Admin\Models\PlayerStateModel;
use App\Models\Main_Model;

trait TournamentScoringTrait
{
    /**
     * หน้าจัดการคะแนน (Score Management)
     */
    public function score_management($tour_id = '')
    {
        if (empty($tour_id)) {
            return redirect()->to(base_url('admin/tournament'));
        }

        // รับค่า filter_date จาก query string (ถ้ามี)
        $filterDate = $this->request->getGet('date');
        $filterDate = ($filterDate !== null && $filterDate !== '') ? $filterDate : '';

        // อัปเดตสถานะแมตช์อัตโนมัติตามวันที่
        $this->tournamentModel->autoUpdateMatchStatus($tour_id);

        $data['tour_id'] = $tour_id;
        $data['tournament'] = $this->tournamentModel->get_Tournament($tour_id);

        // ดึงวันที่ที่มีแมตช์ - ใช้ try-catch เพื่อป้องกัน error
        try {
            $data['match_dates'] = $this->tournamentModel->getMatchDatesForScoring($tour_id);
        } catch (\Exception $e) {
            log_message('error', 'getMatchDatesForScoring error: ' . $e->getMessage());
            $data['match_dates'] = [];
        }
        $data['filter_date'] = $filterDate;

        // ดึงแมตช์ (กรองตามวันที่ถ้ามี) - ใช้ try-catch เพื่อป้องกัน error
        try {
            $data['matches_by_round'] = $this->tournamentModel->getMatchesGroupedByRound($tour_id, $filterDate);
        } catch (\Exception $e) {
            log_message('error', 'getMatchesGroupedByRound error: ' . $e->getMessage());
            $data['matches_by_round'] = [];
        }

        return view('Modules\Admin\Views\Tournament\score_management.php', $data);
    }

    /**
     * AJAX: บันทึกคะแนนแมตช์ (ไม่จบการแข่งขัน)
     */
    public function save_match_score()
    {
        $input = $this->request->getPost();

        if (empty($input['match_id'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'msg' => 'ไม่พบรหัสแมตช์'
            ]);
        }

        $result = $this->tournamentModel->saveMatchScore($input);
        return $this->response->setJSON($result);
    }

    /**
     * AJAX: บันทึกคะแนนและจบการแข่งขัน
     */
    public function end_match_with_score()
    {
        $input = $this->request->getPost();

        if (empty($input['match_id'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'msg' => 'ไม่พบรหัสแมตช์'
            ]);
        }

        // Validate scores are provided
        if (!isset($input['team1_score']) || !isset($input['team2_score']) ||
            $input['team1_score'] === '' || $input['team2_score'] === '') {
            return $this->response->setJSON([
                'status' => 'error',
                'msg' => 'กรุณากรอกคะแนนให้ครบทั้งสองทีม'
            ]);
        }

        $result = $this->tournamentModel->endMatchWithScore($input);
        return $this->response->setJSON($result);
    }

    /**
     * AJAX: ดึงแมตช์ทั้งหมดสำหรับหน้าจัดการคะแนน
     */
    public function get_matches_for_scoring($tour_id = '')
    {
        if (empty($tour_id)) {
            return $this->response->setJSON([
                'status' => 'error',
                'data' => []
            ]);
        }

        // รับค่า filter_date จาก query string (ถ้ามี)
        $filterDate = $this->request->getGet('date') ?? '';

        $matches = $this->tournamentModel->getMatchesGroupedByRound($tour_id, $filterDate);

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $matches
        ]);
    }

    // ==========================================
    // Score Management v2 - Lineup & Events
    // ==========================================

    /**
     * AJAX: ดึงรายชื่อตัวจริง/สำรอง สำหรับ Modal
     */
    public function get_match_lineup($match_id = '')
    {
        if (empty($match_id)) {
            return $this->response->setJSON([
                'status' => 'error',
                'msg' => 'ไม่พบรหัสแมตช์'
            ]);
        }

        // Get match data
        $match = $this->tournamentModel->getOnceTournament_Match_Result($match_id);
        if (!$match) {
            return $this->response->setJSON([
                'status' => 'error',
                'msg' => 'ไม่พบข้อมูลแมตช์'
            ]);
        }

        $tour_id = $match['tour_id'];
        $team1_id = $match['team1_id'];
        $team2_id = $match['team2_id'];

        // Get current lineup for both teams
        $team1_starters = $this->playerStateModel->getPlayerToMatch($tour_id, $match_id, $team1_id, 0);
        $team1_subs = $this->playerStateModel->getPlayerToMatch($tour_id, $match_id, $team1_id, 1);
        $team2_starters = $this->playerStateModel->getPlayerToMatch($tour_id, $match_id, $team2_id, 0);
        $team2_subs = $this->playerStateModel->getPlayerToMatch($tour_id, $match_id, $team2_id, 1);

        // คำนวณสถานะปัจจุบันตามเหตุการณ์เปลี่ยนตัว
        $team1_lineup = $this->applySubstitutionEventsToLineup($team1_starters, $team1_subs, $match_id, $team1_id);
        $team2_lineup = $this->applySubstitutionEventsToLineup($team2_starters, $team2_subs, $match_id, $team2_id);

        return $this->response->setJSON([
            'status' => 'success',
            'data' => [
                'match' => $match,
                'team1' => [
                    'team_id' => $team1_id,
                    'team_name' => $match['team1_name'] ?? '',
                    'starters' => $team1_lineup['starters'],
                    'substitutes' => $team1_lineup['substitutes']
                ],
                'team2' => [
                    'team_id' => $team2_id,
                    'team_name' => $match['team2_name'] ?? '',
                    'starters' => $team2_lineup['starters'],
                    'substitutes' => $team2_lineup['substitutes']
                ]
            ]
        ]);
    }

    /**
     * คำนวณสถานะผู้เล่นปัจจุบันตามเหตุการณ์เปลี่ยนตัว (สำหรับ lineup display)
     */
    private function applySubstitutionEventsToLineup($starters, $subs, $match_id, $team_id)
    {
        // ดึงเหตุการณ์เปลี่ยนตัวทั้งหมดของทีมนี้ในแมตช์นี้
        $substitutions = $this->db->table('tournament_match_event')
            ->where('match_id', $match_id)
            ->where('team_id', $team_id)
            ->where('event_id', 2) // เปลี่ยนตัว
            ->get()
            ->getResultArray();

        if (empty($substitutions)) {
            return ['starters' => $starters, 'substitutes' => $subs];
        }

        // สร้าง map ของผู้เล่นที่ถูกเปลี่ยนออก และผู้เล่นที่เข้ามา
        $playersOut = []; // ผู้เล่นที่ออก → กลายเป็นสำรอง
        $playersIn = [];  // ผู้เล่นที่เข้า → กลายเป็นตัวจริง

        foreach ($substitutions as $sub) {
            if (!empty($sub['player_1_id'])) {
                $playersOut[$sub['player_1_id']] = true;
            }
            if (!empty($sub['player_2_id'])) {
                $playersIn[$sub['player_2_id']] = true;
            }
        }

        // รวมผู้เล่นทั้งหมด
        $allPlayers = array_merge($starters, $subs);
        $newStarters = [];
        $newSubs = [];

        foreach ($allPlayers as $player) {
            $playerId = $player['player_id'] ?? $player['id'] ?? null;

            if (isset($playersOut[$playerId])) {
                // ผู้เล่นที่ออก → สำรอง
                $newSubs[] = $player;
            } elseif (isset($playersIn[$playerId])) {
                // ผู้เล่นที่เข้า → ตัวจริง
                $newStarters[] = $player;
            } else {
                // ไม่มีการเปลี่ยนแปลง - ตรวจสอบว่าอยู่ใน starters หรือ subs เดิม
                $isOriginalStarter = false;
                foreach ($starters as $starter) {
                    $starterId = $starter['player_id'] ?? $starter['id'] ?? null;
                    if ($starterId == $playerId) {
                        $isOriginalStarter = true;
                        break;
                    }
                }

                if ($isOriginalStarter) {
                    $newStarters[] = $player;
                } else {
                    $newSubs[] = $player;
                }
            }
        }

        return ['starters' => $newStarters, 'substitutes' => $newSubs];
    }

    /**
     * AJAX: บันทึกรายชื่อตัวจริง/สำรอง
     */
    public function save_match_lineup()
    {
        $input = $this->request->getPost();

        if (empty($input['match_id']) || empty($input['team_id'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'msg' => 'ข้อมูลไม่ครบถ้วน'
            ]);
        }

        $match_id = $input['match_id'];
        $team_id = $input['team_id'];
        $starters = json_decode($input['starters'] ?? '[]', true);
        $substitutes = json_decode($input['substitutes'] ?? '[]', true);

        // Get match data
        $match = $this->tournamentModel->getOnceTournament_Match_Result($match_id);
        if (!$match) {
            return $this->response->setJSON([
                'status' => 'error',
                'msg' => 'ไม่พบข้อมูลแมตช์'
            ]);
        }

        $tour_id = $match['tour_id'];

        $this->db->transBegin();

        try {
            // Delete existing lineup for this team in this match
            $this->db->table('player_to_match')
                ->where('match_id', $match_id)
                ->where('team_id', $team_id)
                ->delete();

            // Insert starters
            foreach ($starters as $player_id) {
                $this->db->table('player_to_match')->insert([
                    'player_id' => $player_id,
                    'team_id' => $team_id,
                    'tournament_id' => $tour_id,
                    'match_id' => $match_id,
                    'is_substitute' => 0
                ]);
            }

            // Insert substitutes
            foreach ($substitutes as $player_id) {
                $this->db->table('player_to_match')->insert([
                    'player_id' => $player_id,
                    'team_id' => $team_id,
                    'tournament_id' => $tour_id,
                    'match_id' => $match_id,
                    'is_substitute' => 1
                ]);
            }

            $this->db->transCommit();

            return $this->response->setJSON([
                'status' => 'success',
                'msg' => 'บันทึกรายชื่อสำเร็จ'
            ]);
        } catch (\Exception $e) {
            $this->db->transRollback();
            return $this->response->setJSON([
                'status' => 'error',
                'msg' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * AJAX: ดึง Events Timeline
     */
    public function get_match_events($match_id = '')
    {
        if (empty($match_id)) {
            return $this->response->setJSON([
                'status' => 'error',
                'msg' => 'ไม่พบรหัสแมตช์'
            ]);
        }

        $events = $this->tournamentModel->getMatchEvent($match_id);
        $event_types = $this->tournamentModel->getEventTypes();

        return $this->response->setJSON([
            'status' => 'success',
            'data' => [
                'events' => $events,
                'event_types' => $event_types
            ]
        ]);
    }

    /**
     * AJAX: เพิ่ม Event ใหม่
     */
    /**
     * AJAX: บันทึกการเปลี่ยนตัวหลายคู่พร้อมกัน
     *
     * รับ subs เป็น JSON array ของ { player_out, player_in, minute }
     * ทำในทรานแซกชันเดียว เพราะแต่ละคู่ไปแก้ player_to_match ของทีมเดียวกัน
     * ถ้าปล่อยให้ client ยิงทีละคู่ แล้วคู่กลางพัง lineup จะค้างครึ่ง ๆ กลาง ๆ
     */
    public function add_match_substitutions()
    {
        $input   = $this->request->getPost();
        $matchId = $input['match_id'] ?? null;
        $teamId  = $input['team_id'] ?? null;
        $subs    = json_decode($input['subs'] ?? '[]', true);

        if (empty($matchId) || empty($teamId) || empty($subs) || !is_array($subs)) {
            return $this->response->setJSON([
                'status' => 'error',
                'msg' => lang('Common.please_fill_complete_info')
            ]);
        }

        $match = $this->tournamentModel->getOnceTournament_Match_Result($matchId);
        if (!$match) {
            return $this->response->setJSON([
                'status' => 'error',
                'msg' => lang('Common.match_not_found')
            ]);
        }

        // กันเลือกคนเดิมซ้ำในชุดเดียวกัน — ฝั่งหน้าจอกันไว้แล้ว แต่ยิงตรงมาได้
        $seen = [];
        foreach ($subs as $s) {
            foreach ([$s['player_out'] ?? null, $s['player_in'] ?? null] as $pid) {
                if (empty($pid)) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'msg' => lang('Common.sub_row_incomplete')
                    ]);
                }
                if (isset($seen[$pid])) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'msg' => lang('Common.sub_player_duplicated')
                    ]);
                }
                $seen[$pid] = true;
            }
        }

        $this->db->transBegin();
        try {
            $saved = 0;
            foreach ($subs as $s) {
                $out    = $s['player_out'];
                $in     = $s['player_in'];
                $minute = (isset($s['minute']) && $s['minute'] !== '') ? $s['minute'] : null;

                $this->db->table('tournament_match_event')->insert([
                    'tour_id'     => $match['tour_id'],
                    'match_id'    => $matchId,
                    'event_id'    => 2, // เปลี่ยนตัว
                    'team_id'     => $teamId,
                    'player_1_id' => $out,
                    'player_2_id' => $in,
                    'minute'      => $minute,
                ]);

                // คนออก → เป็นสำรอง / คนเข้า → เป็นตัวจริง (ตรรกะเดียวกับ add_match_event)
                $this->db->table('player_to_match')
                    ->where('match_id', $matchId)->where('team_id', $teamId)
                    ->where('player_id', $out)
                    ->update(['is_substitute' => 1]);

                $this->db->table('player_to_match')
                    ->where('match_id', $matchId)->where('team_id', $teamId)
                    ->where('player_id', $in)
                    ->update(['is_substitute' => 0]);

                $saved++;
            }

            if ($this->db->transStatus() === false) {
                $this->db->transRollback();
                return $this->response->setJSON([
                    'status' => 'error',
                    'msg' => lang('Common.error_occurred')
                ]);
            }

            $this->db->transCommit();
            return $this->response->setJSON([
                'status' => 'success',
                'saved'  => $saved,
                'msg'    => lang('Common.add_event_success')
            ]);
        } catch (\Exception $e) {
            $this->db->transRollback();
            return $this->response->setJSON([
                'status' => 'error',
                'msg' => lang('Common.error_occurred') . ': ' . $e->getMessage()
            ]);
        }
    }

    public function add_match_event()
    {
        $input = $this->request->getPost();

        if (empty($input['match_id']) || empty($input['team_id']) || empty($input['event_id']) || empty($input['player_1_id'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'msg' => 'กรุณากรอกข้อมูลให้ครบถ้วน'
            ]);
        }

        // Get match data
        $match = $this->tournamentModel->getOnceTournament_Match_Result($input['match_id']);
        if (!$match) {
            return $this->response->setJSON([
                'status' => 'error',
                'msg' => 'ไม่พบข้อมูลแมตช์'
            ]);
        }

        try {
            // แปลง empty string เป็น null สำหรับ int columns
            $player2Id = !empty($input['player_2_id']) ? $input['player_2_id'] : null;
            $minute = !empty($input['minute']) ? $input['minute'] : null;

            $this->db->table('tournament_match_event')->insert([
                'tour_id' => $match['tour_id'],
                'match_id' => $input['match_id'],
                'event_id' => $input['event_id'],
                'team_id' => $input['team_id'],
                'player_1_id' => $input['player_1_id'],
                'player_2_id' => $player2Id,
                'minute' => $minute
            ]);

            // ถ้าเป็นเหตุการณ์เปลี่ยนตัว (event_id = 2) ให้อัปเดต lineup
            if ($input['event_id'] == 2 && $player2Id) {
                // ผู้เล่นที่ออก (player_1_id) → กลายเป็นสำรอง
                $this->db->table('player_to_match')
                    ->where('match_id', $input['match_id'])
                    ->where('team_id', $input['team_id'])
                    ->where('player_id', $input['player_1_id'])
                    ->update(['is_substitute' => 1]);

                // ผู้เล่นสำรองที่เข้า (player_2_id) → กลายเป็นตัวจริง
                $this->db->table('player_to_match')
                    ->where('match_id', $input['match_id'])
                    ->where('team_id', $input['team_id'])
                    ->where('player_id', $player2Id)
                    ->update(['is_substitute' => 0]);
            }

            return $this->response->setJSON([
                'status' => 'success',
                'msg' => 'เพิ่มเหตุการณ์สำเร็จ'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'msg' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * AJAX: ลบ Event
     */
    public function remove_match_event()
    {
        $input = $this->request->getPost();

        if (empty($input['event_id'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'msg' => 'ไม่พบรหัส Event'
            ]);
        }

        try {
            // ดึงข้อมูล event ก่อนลบ
            $event = $this->db->table('tournament_match_event')
                ->where('match_event_id', $input['event_id'])
                ->get()
                ->getRowArray();

            if ($event) {
                // ถ้าเป็นเหตุการณ์เปลี่ยนตัว (event_id = 2) ให้ rollback สถานะผู้เล่น
                if ($event['event_id'] == 2 && !empty($event['player_2_id'])) {
                    // ผู้เล่นที่ออก (player_1_id) → กลับมาเป็นตัวจริง
                    $this->db->table('player_to_match')
                        ->where('match_id', $event['match_id'])
                        ->where('team_id', $event['team_id'])
                        ->where('player_id', $event['player_1_id'])
                        ->update(['is_substitute' => 0]);

                    // ผู้เล่นที่เข้า (player_2_id) → กลับเป็นสำรอง
                    $this->db->table('player_to_match')
                        ->where('match_id', $event['match_id'])
                        ->where('team_id', $event['team_id'])
                        ->where('player_id', $event['player_2_id'])
                        ->update(['is_substitute' => 1]);
                }

                // ลบ event
                $this->db->table('tournament_match_event')
                    ->where('match_event_id', $input['event_id'])
                    ->delete();
            }

            return $this->response->setJSON([
                'status' => 'success',
                'msg' => 'ลบเหตุการณ์สำเร็จ'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'msg' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * AJAX: ดึงรายชื่อนักเตะในทีม (สำหรับ dropdown)
     */
    public function get_team_players($tour_id = '', $team_id = '')
    {
        if (empty($tour_id) || empty($team_id)) {
            return $this->response->setJSON([
                'status' => 'error',
                'msg' => 'ข้อมูลไม่ครบถ้วน'
            ]);
        }

        // Get players submitted for this tournament
        $players = $this->db->table('tournament_submitted_players tsp')
            ->select('p.id, p.first_name, p.last_name, COALESCE(ape_gtp.shirt_number, p.shirt_number) as shirt_number, pos.name_th as position_name', false)
            ->join('tournament_team tt', 'tt.tour_team_id = tsp.tour_team_id', 'inner')
            ->join('player p', 'p.id = tsp.player_id', 'inner')
            ->join('academy_player_enrollments ape_gtp', 'ape_gtp.player_id = p.id AND ape_gtp.team_id = ' . (int)$team_id . ' AND ape_gtp.is_active = 1', 'left', false)
            ->join('tbl_player_position pos', 'pos.id = COALESCE(NULLIF(ape_gtp.position_id,0), p.player_position_id)', 'left', false)
            ->where('tt.tour_id', $tour_id)
            ->where('tt.team_id', $team_id)
            ->orderBy('CAST(COALESCE(ape_gtp.shirt_number, p.shirt_number) AS UNSIGNED)', 'ASC', false)
            ->get()
            ->getResultArray();

        // If no submitted players, fallback to team players
        if (empty($players)) {
            $players = $this->playerModel->get_PlayerList($team_id);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $players
        ]);
    }

    /**
     * AJAX: ดึงรายชื่อนักเตะที่ลงสนามในแมตช์ (สำหรับ dropdown เลือกผู้เล่นใน Event)
     */
    public function get_match_players($match_id = '', $team_id = '')
    {
        if (empty($match_id) || empty($team_id)) {
            return $this->response->setJSON([
                'status' => 'error',
                'msg' => 'ข้อมูลไม่ครบถ้วน'
            ]);
        }

        // Get match data
        $match = $this->tournamentModel->getOnceTournament_Match_Result($match_id);
        if (!$match) {
            return $this->response->setJSON([
                'status' => 'error',
                'msg' => 'ไม่พบข้อมูลแมตช์'
            ]);
        }

        // Get players in lineup (both starters and subs)
        $players = $this->db->table('player_to_match ptm')
            ->select('p.id, p.first_name, p.last_name, COALESCE(ape_gmp.shirt_number, p.shirt_number) as shirt_number, pos.name_th as position_name, ptm.is_substitute', false)
            ->join('player p', 'p.id = ptm.player_id', 'inner')
            ->join('academy_player_enrollments ape_gmp', 'ape_gmp.player_id = p.id AND ape_gmp.team_id = ' . (int)$team_id . ' AND ape_gmp.is_active = 1', 'left', false)
            ->join('tbl_player_position pos', 'pos.id = COALESCE(NULLIF(ape_gmp.position_id,0), p.player_position_id)', 'left', false)
            ->where('ptm.match_id', $match_id)
            ->where('ptm.team_id', $team_id)
            ->orderBy('ptm.is_substitute', 'ASC')
            ->orderBy('CAST(COALESCE(ape_gmp.shirt_number, p.shirt_number) AS UNSIGNED)', 'ASC', false)
            ->get()
            ->getResultArray();

        // คำนวณสถานะปัจจุบันตามเหตุการณ์เปลี่ยนตัว
        $players = $this->applySubstitutionEvents($players, $match_id, $team_id);

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $players
        ]);
    }

    /**
     * คำนวณสถานะผู้เล่นปัจจุบันตามเหตุการณ์เปลี่ยนตัว
     */
    private function applySubstitutionEvents($players, $match_id, $team_id)
    {
        // ดึงเหตุการณ์เปลี่ยนตัวทั้งหมดของทีมนี้ในแมตช์นี้
        $substitutions = $this->db->table('tournament_match_event')
            ->where('match_id', $match_id)
            ->where('team_id', $team_id)
            ->where('event_id', 2) // เปลี่ยนตัว
            ->get()
            ->getResultArray();

        if (empty($substitutions)) {
            return $players;
        }

        // สร้าง map ของผู้เล่นที่ถูกเปลี่ยนออก และผู้เล่นที่เข้ามา
        $playersOut = []; // ผู้เล่นที่ออก → กลายเป็นสำรอง
        $playersIn = [];  // ผู้เล่นที่เข้า → กลายเป็นตัวจริง

        foreach ($substitutions as $sub) {
            if (!empty($sub['player_1_id'])) {
                $playersOut[$sub['player_1_id']] = true;
            }
            if (!empty($sub['player_2_id'])) {
                $playersIn[$sub['player_2_id']] = true;
            }
        }

        // อัปเดตสถานะผู้เล่น
        foreach ($players as &$player) {
            $playerId = $player['id'];

            if (isset($playersOut[$playerId])) {
                // ผู้เล่นที่ออก → สำรอง
                $player['is_substitute'] = 1;
            }
            if (isset($playersIn[$playerId])) {
                // ผู้เล่นที่เข้า → ตัวจริง
                $player['is_substitute'] = 0;
            }
        }

        // เรียงใหม่ตาม is_substitute
        usort($players, function($a, $b) {
            if ($a['is_substitute'] == $b['is_substitute']) {
                return intval($a['shirt_number'] ?? 0) - intval($b['shirt_number'] ?? 0);
            }
            return $a['is_substitute'] - $b['is_substitute'];
        });

        return $players;
    }

    /**
     * Export ผลการแข่งขันเป็น PDF
     */
    public function export_match_result($match_id = '')
    {
        // Security Check: System Admin OR Academy Owner/Staff
        $isAdmin = (bool) session()->get('isAdminLoggedIn');
        $isLoggedIn = (bool) session()->get('isLoggedIn');
        $userAcademyId = getUserAcademyId();

        if (!$isAdmin && !$isLoggedIn) {
            return redirect()->to(base_url('admin/login'))->with('error', 'กรุณาเข้าสู่ระบบก่อนใช้งาน');
        }

        if (!$isAdmin && !isSuperAdmin() && !$userAcademyId) {
            return redirect()->to(base_url())->with('error', 'Unauthorized access');
        }

        if (empty($match_id)) {
            return redirect()->back()->with('error', 'ไม่พบข้อมูลแมตช์');
        }

        // Get match data
        $match = $this->tournamentModel->getOnceTournament_Match_Result($match_id);
        if (!$match) {
            return redirect()->back()->with('error', 'ไม่พบข้อมูลแมตช์');
        }

        // Get tournament data
        $tournament = $this->tournamentModel->get_Tournament($match['tour_id']);

        // Get events for both teams
        $events = $this->db->table('tournament_match_event tme')
            ->select('tme.*, e.name as event_name,
                      p1.first_name as pl1_first_name, p1.last_name as pl1_last_name, COALESCE(ape_e1.shirt_number, p1.shirt_number) as pl1_shirt_number,
                      p2.first_name as pl2_first_name, p2.last_name as pl2_last_name, COALESCE(ape_e2.shirt_number, p2.shirt_number) as pl2_shirt_number', false)
            ->join('tbl_event e', 'e.id = tme.event_id', 'left')
            ->join('player p1', 'p1.id = tme.player_1_id', 'left')
            ->join('academy_player_enrollments ape_e1', 'ape_e1.player_id = p1.id AND ape_e1.team_id = tme.team_id AND ape_e1.is_active = 1', 'left', false)
            ->join('player p2', 'p2.id = tme.player_2_id', 'left')
            ->join('academy_player_enrollments ape_e2', 'ape_e2.player_id = p2.id AND ape_e2.team_id = tme.team_id AND ape_e2.is_active = 1', 'left', false)
            ->where('tme.match_id', $match_id)
            ->orderBy('tme.minute', 'ASC')
            ->get()
            ->getResultArray();

        // Get team 1 lineup (starters)
        $team1Starters = $this->db->table('player_to_match ptm')
            ->select('p.id, p.first_name, p.last_name, COALESCE(ape_t1s.shirt_number, p.shirt_number) as shirt_number, pos.name_th as position_name', false)
            ->join('player p', 'p.id = ptm.player_id', 'inner')
            ->join('academy_player_enrollments ape_t1s', 'ape_t1s.player_id = p.id AND ape_t1s.team_id = ' . (int)$match['team1_id'] . ' AND ape_t1s.is_active = 1', 'left', false)
            ->join('tbl_player_position pos', 'pos.id = COALESCE(NULLIF(ape_t1s.position_id,0), p.player_position_id)', 'left', false)
            ->where('ptm.match_id', $match_id)
            ->where('ptm.team_id', $match['team1_id'])
            ->where('ptm.is_substitute', 0)
            ->orderBy('CAST(COALESCE(ape_t1s.shirt_number, p.shirt_number) AS UNSIGNED)', 'ASC', false)
            ->get()
            ->getResultArray();

        // Get team 1 substitutes
        $team1Substitutes = $this->db->table('player_to_match ptm')
            ->select('p.id, p.first_name, p.last_name, COALESCE(ape_t1b.shirt_number, p.shirt_number) as shirt_number, pos.name_th as position_name', false)
            ->join('player p', 'p.id = ptm.player_id', 'inner')
            ->join('academy_player_enrollments ape_t1b', 'ape_t1b.player_id = p.id AND ape_t1b.team_id = ' . (int)$match['team1_id'] . ' AND ape_t1b.is_active = 1', 'left', false)
            ->join('tbl_player_position pos', 'pos.id = COALESCE(NULLIF(ape_t1b.position_id,0), p.player_position_id)', 'left', false)
            ->where('ptm.match_id', $match_id)
            ->where('ptm.team_id', $match['team1_id'])
            ->where('ptm.is_substitute', 1)
            ->orderBy('CAST(COALESCE(ape_t1b.shirt_number, p.shirt_number) AS UNSIGNED)', 'ASC', false)
            ->get()
            ->getResultArray();

        // Get team 2 lineup (starters)
        $team2Starters = $this->db->table('player_to_match ptm')
            ->select('p.id, p.first_name, p.last_name, COALESCE(ape_t2s.shirt_number, p.shirt_number) as shirt_number, pos.name_th as position_name', false)
            ->join('player p', 'p.id = ptm.player_id', 'inner')
            ->join('academy_player_enrollments ape_t2s', 'ape_t2s.player_id = p.id AND ape_t2s.team_id = ' . (int)$match['team2_id'] . ' AND ape_t2s.is_active = 1', 'left', false)
            ->join('tbl_player_position pos', 'pos.id = COALESCE(NULLIF(ape_t2s.position_id,0), p.player_position_id)', 'left', false)
            ->where('ptm.match_id', $match_id)
            ->where('ptm.team_id', $match['team2_id'])
            ->where('ptm.is_substitute', 0)
            ->orderBy('CAST(COALESCE(ape_t2s.shirt_number, p.shirt_number) AS UNSIGNED)', 'ASC', false)
            ->get()
            ->getResultArray();

        // Get team 2 substitutes
        $team2Substitutes = $this->db->table('player_to_match ptm')
            ->select('p.id, p.first_name, p.last_name, COALESCE(ape_t2b.shirt_number, p.shirt_number) as shirt_number, pos.name_th as position_name', false)
            ->join('player p', 'p.id = ptm.player_id', 'inner')
            ->join('academy_player_enrollments ape_t2b', 'ape_t2b.player_id = p.id AND ape_t2b.team_id = ' . (int)$match['team2_id'] . ' AND ape_t2b.is_active = 1', 'left', false)
            ->join('tbl_player_position pos', 'pos.id = COALESCE(NULLIF(ape_t2b.position_id,0), p.player_position_id)', 'left', false)
            ->where('ptm.match_id', $match_id)
            ->where('ptm.team_id', $match['team2_id'])
            ->where('ptm.is_substitute', 1)
            ->orderBy('CAST(COALESCE(ape_t2b.shirt_number, p.shirt_number) AS UNSIGNED)', 'ASC', false)
            ->get()
            ->getResultArray();

        // Separate events by team and type
        $team1Goals = [];
        $team2Goals = [];
        $team1Cards = [];
        $team2Cards = [];
        $team1Subs = [];
        $team2Subs = [];
        $team1RedCards = [];
        $team2RedCards = [];

        foreach ($events as $event) {
            $eventData = [
                'minute' => $event['minute'] ?? '',
                'shirt_number' => $event['pl1_shirt_number'] ?? '',
                'name' => trim(($event['pl1_first_name'] ?? '') . ' ' . ($event['pl1_last_name'] ?? '')),
                'player2_shirt_number' => $event['pl2_shirt_number'] ?? '',
                'player2_name' => trim(($event['pl2_first_name'] ?? '') . ' ' . ($event['pl2_last_name'] ?? '')),
                'event_id' => $event['event_id'],
            ];

            if ($event['team_id'] == $match['team1_id']) {
                switch ($event['event_id']) {
                    case 1: // Goal
                        $eventData['is_own_goal'] = false;
                        $team1Goals[] = $eventData;
                        break;
                    case 5: // Own Goal
                        $eventData['is_own_goal'] = true;
                        $team1Goals[] = $eventData;
                        break;
                    case 2: // Yellow Card
                        $team1Cards[] = $eventData;
                        break;
                    case 3: // Red Card
                        $team1RedCards[] = $eventData;
                        break;
                    case 4: // Substitution
                        $team1Subs[] = $eventData;
                        break;
                }
            } else {
                switch ($event['event_id']) {
                    case 1: // Goal
                        $eventData['is_own_goal'] = false;
                        $team2Goals[] = $eventData;
                        break;
                    case 5: // Own Goal
                        $eventData['is_own_goal'] = true;
                        $team2Goals[] = $eventData;
                        break;
                    case 2: // Yellow Card
                        $team2Cards[] = $eventData;
                        break;
                    case 3: // Red Card
                        $team2RedCards[] = $eventData;
                        break;
                    case 4: // Substitution
                        $team2Subs[] = $eventData;
                        break;
                }
            }
        }

        $data = [
            'match' => $match,
            'tournament' => $tournament,
            'team1Goals' => $team1Goals,
            'team2Goals' => $team2Goals,
            'team1Cards' => $team1Cards,
            'team2Cards' => $team2Cards,
            'team1Subs' => $team1Subs,
            'team2Subs' => $team2Subs,
            'team1RedCards' => $team1RedCards,
            'team2RedCards' => $team2RedCards,
            'team1Starters' => $team1Starters,
            'team1Substitutes' => $team1Substitutes,
            'team2Starters' => $team2Starters,
            'team2Substitutes' => $team2Substitutes,
        ];

        $html = view('Modules\Admin\Views\Tournament\export\match_result_pdf', $data);

        $this->response->setHeader('Content-Type', 'application/pdf');
        $mpdf = new \Mpdf\Mpdf([
            'default_font' => 'thsarabun',
            'default_font_size' => 14,
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_top' => 10,
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_bottom' => 10,
            'margin_header' => 5,
            'margin_footer' => 5,
            'orientation' => 'P',
        ]);

        $mpdf->WriteHTML($html);
        $mpdf->Output('match_result_' . $match_id . '.pdf', 'I');
    }


    public function update_stage_final_match()
    {
        $stageId = $this->request->getPost('stage_id');
        $finalMatch = $this->request->getPost('final_match');

        if (!$stageId) {
            return $this->response->setJSON([
                'status' => 'error',
                'msg' => 'ไม่พบ ID ของ Stage'
            ]);
        }

        try {
            $db = \Config\Database::connect();

            $db->table('tournament_stages')
                ->where('stage_id', $stageId)
                ->update([
                    'final_match' => (int)$finalMatch
                ]);

            return $this->response->setJSON([
                'status' => 'success'
            ]);

        } catch (\Exception $e) {

            return $this->response->setJSON([
                'status' => 'error',
                'msg' => $e->getMessage()
            ]);
        }
    }
}
