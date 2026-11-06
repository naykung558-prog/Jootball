<?php

namespace Modules\Admin\Models\Traits;

use CodeIgniter\Model;
use \Hermawan\DataTables\DataTable;

trait TournamentScoringQueriesTrait
{
    public function deleteMatchById($matchId)
    {
        return $this->db->table('tournament_match_result')
            ->where('match_id', $matchId)
            ->delete();
    }

    // For Tournament List Page with Search & Pagination
    function getTournamentList($keyword = '', $perPage = 25, $page = 1)
    {
        $builder = $this->db->table($this->table);
        $builder->select('tournament.*, tbl_bank.name as bank_name, tbl_bank.short_name as bank_short_name, tbl_bank.logo as bank_icon');
        $builder->join('tbl_bank', 'tournament.bank_id = tbl_bank.id', 'LEFT');

        $builder->where($this->table . '.is_active', 1);
        $builder->where($this->table . '.is_registration_open', 1);

        if (!empty($keyword)) {
            $builder->like($this->table . '.name', $keyword);
        }

        $builder->orderBy($this->table . '.tour_id', 'DESC');

        // Get paginated results using the builder
        $page = (int) $page;
        $offset = ($page - 1) * $perPage;

        // Get total count for pagination
        $totalBuilder = clone $builder;
        $total = $totalBuilder->countAllResults(false);

        // Get paginated data
        $tournaments = $builder->limit($perPage, $offset)->get()->getResultArray();

        // Create pager manually
        $pager = \Config\Services::pager();
        $pager->store('default', $page, $perPage, $total);

        return [
            'tournaments' => $tournaments,
            'pager' => $pager,
        ];
    }

    // Clone Tournament
    public function cloneTournament($tour_id)
    {
        // Get original tournament data
        $original = $this->get_Tournament($tour_id);

        if (!$original) {
            return false;
        }

        // Prepare data for new tournament
        $newTournament = [
            'name' => ($original['name'] ?? '') . ' (Copy)',
            'name_en' => $original['name_en'] ?? '',
            'detail' => $original['detail'] ?? '',
            'generation' => $original['generation'] ?? '',
            'player_count' => $original['player_count'] ?? 0,
            'start_date' => $original['start_date'] ?? null,
            'end_date' => $original['end_date'] ?? null,
            'tournament_start_date' => $original['tournament_start_date'] ?? null,
            'tournament_end_date' => $original['tournament_end_date'] ?? null,
            'bank_id' => $original['bank_id'] ?? null,
            'account_no' => $original['account_no'] ?? '',
            'account_name' => $original['account_name'] ?? '',
            'promptpay' => $original['promptpay'] ?? '',
            'qrcode' => $original['qrcode'] ?? '',
            'logo' => $original['logo'] ?? '',
            'pdf_path' => $original['pdf_path'] ?? '',
            'is_active' => 0, // Set to inactive initially
            'create_by' => session()->get('member_id'), // Use member_id as per saveTournament
        ];

        $this->db->transBegin();

        try {
            // Insert new tournament
            $builder = $this->db->table($this->table);
            $builder->insert($newTournament);
            $newTourId = $this->db->insertID();

            if (!$newTourId) {
                $this->db->transRollback();
                return false;
            }

            // Clone tournament stages (match-setup configuration)
            $stages = $this->db->table('tournament_stages')
                ->where('tour_id', $tour_id)
                ->orderBy('order_num', 'ASC')
                ->get()
                ->getResultArray();

            $stageIdMap = []; // old stage_id => new stage_id
            foreach ($stages as $stage) {
                $this->db->table('tournament_stages')->insert([
                    'tour_id' => $newTourId,
                    'stage_name' => $stage['stage_name'],
                    'format' => $stage['format'],
                    'team_count' => $stage['team_count'],
                    'group_count' => $stage['group_count'],
                    'order_num' => $stage['order_num'],
                ]);
                $stageIdMap[$stage['stage_id']] = $this->db->insertID();
            }

            // Clone matches (team pairings) with scores/status reset
            $userId = session()->get('member_id') ?? 0;
            $now = date('Y-m-d H:i:s');

            $matches = $this->db->table('tournament_match_result')
                ->where('tour_id', $tour_id)
                ->get()
                ->getResultArray();

            foreach ($matches as $match) {
                $this->db->table('tournament_match_result')->insert([
                    'tour_id' => $newTourId,
                    'stage_id' => isset($stageIdMap[$match['stage_id']]) ? $stageIdMap[$match['stage_id']] : null,
                    'match_round' => $match['match_round'],
                    'group_id' => $match['group_id'],
                    'team1_id' => $match['team1_id'],
                    'team2_id' => $match['team2_id'],
                    'team1_score' => null,
                    'team2_score' => null,
                    'result' => null,
                    'match_status' => 0,
                    'referee' => $match['referee'],
                    'stadium' => $match['stadium'],
                    'date' => $match['date'],
                    'time' => $match['time'],
                    'create_by' => $userId,
                    'create_date' => $now,
                    'update_by' => $userId,
                    'update_date' => $now,
                ]);
            }

            $this->db->transCommit();
            return $newTourId;
        } catch (\Exception $e) {
            $this->db->transRollback();
            return false;
        }
    }

    /**
     * ดึงวันที่ทั้งหมดที่มีแมตช์ในทัวร์นาเมนต์ (สำหรับ Score Management)
     * @param int $tour_id
     * @return array
     */
    public function getMatchDatesForScoring($tour_id)
    {
        try {
            // ใช้ raw query เพื่อความแน่นอนในการกรอง date
            $sql = "SELECT DISTINCT date as match_date
                    FROM tournament_match_result
                    WHERE tour_id = ?
                    AND team1_id > 0
                    AND team2_id > 0
                    AND match_status IN (1, 3)
                    AND date IS NOT NULL
                    AND date > '1970-01-01'
                    ORDER BY date DESC";

            $result = $this->db->query($sql, [$tour_id])->getResultArray();

            $dates = [];
            foreach ($result as $row) {
                $date = $row['match_date'] ?? null;
                // ตรวจสอบว่าเป็นวันที่ที่ถูกต้อง
                if (!empty($date) && $date !== '0000-00-00') {
                    // Format to Y-m-d if it's a valid date
                    $dateStr = (string)$date;
                    if (strlen($dateStr) >= 10) {
                        $dates[] = substr($dateStr, 0, 10);
                    }
                }
            }

            return array_values(array_unique($dates));
        } catch (\Exception $e) {
            // Log error และ return empty array เพื่อให้หน้าทำงานต่อได้
            log_message('error', 'getMatchDatesForScoring error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * ดึงแมตช์ทั้งหมดจัดกลุ่มตามรอบ (สำหรับ Score Management)
     * แสดงเฉพาะแมตช์ที่มีทีมครบและสถานะ "กำลังแข่งขัน" (match_status = 1)
     * @param int $tour_id
     * @param string $filterDate วันที่ที่ต้องการกรอง (format: Y-m-d) ถ้าว่างคือแสดงทั้งหมด
     * @return array
     */
    public function getMatchesGroupedByRound($tour_id, $filterDate = '')
    {
        try {
            $builder = $this->db->table('tournament_match_result');
            $builder->select('tournament_match_result.*,
                team1.name as team1_name, team1.logo as team1_logo,
                team2.name as team2_name, team2.logo as team2_logo,
                ts.stage_name, ts.format as stage_format');
            $builder->join('team as team1', 'tournament_match_result.team1_id = team1.id', 'left');
            $builder->join('team as team2', 'tournament_match_result.team2_id = team2.id', 'left');
            $builder->join('tournament_stages as ts', 'tournament_match_result.stage_id = ts.stage_id', 'left');
            $builder->where('tournament_match_result.tour_id', $tour_id);

            // แสดงเฉพาะแมตช์ที่มีทีมครบทั้งสองฝั่ง
            $builder->where('tournament_match_result.team1_id >', 0);
            $builder->where('tournament_match_result.team2_id >', 0);

            // แสดงแมตช์ที่สถานะ "กำลังแข่งขัน" (1) หรือ "จบการแข่งขัน" (3)
            $builder->whereIn('tournament_match_result.match_status', [1, 3]);

            // กรองตามวันที่ (ถ้าระบุ)
            if (!empty($filterDate) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $filterDate)) {
                $builder->where('tournament_match_result.date', $filterDate);
            }

            $builder->orderBy('ts.order_num', 'ASC');
            $builder->orderBy('tournament_match_result.match_round', 'ASC');
            $builder->orderBy('tournament_match_result.group_id', 'ASC');
            $builder->orderBy('tournament_match_result.match_id', 'ASC');

            $builder->groupBy('tournament_match_result.match_id');

            $matches = $builder->get()->getResultArray();

            // Group by stage_name or match_round
            $grouped = [];
            $seenMatchIds = [];
            foreach ($matches as $match) {
                // ป้องกันแมตช์ซ้ำ (deduplicate by match_id)
                $matchId = $match['match_id'];
                if (isset($seenMatchIds[$matchId])) {
                    continue;
                }
                $seenMatchIds[$matchId] = true;

                $roundKey = !empty($match['stage_name']) ? $match['stage_name'] : ($match['match_round'] ?? 'ไม่ระบุรอบ');

                // Add group info if available
                if (!empty($match['group_id'])) {
                    $roundKey .= ' - สาย ' . $match['group_id'];
                }

                if (!isset($grouped[$roundKey])) {
                    $grouped[$roundKey] = [
                        'round_name' => $roundKey,
                        'stage_format' => $match['stage_format'] ?? 'knockout',
                        'matches' => []
                    ];
                }
                $grouped[$roundKey]['matches'][] = $match;
            }

            return array_values($grouped);
        } catch (\Exception $e) {
            log_message('error', 'getMatchesGroupedByRound error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * บันทึกคะแนนแมตช์ (ไม่จบการแข่งขัน)
     * คงสถานะ match_status = 1 (กำลังแข่งขัน) ไว้
     * @param array $input
     * @return array
     */
    public function saveMatchScore($input)
    {
        try {
            $builder = $this->db->table('tournament_match_result');

            $updateData = [
                'update_by' => session()->get('member_id') ?? 0,
                'update_date' => date('Y-m-d H:i:s')
            ];

            // Update scores
            if (isset($input['team1_score'])) {
                $updateData['team1_score'] = $input['team1_score'] !== '' ? (int)$input['team1_score'] : null;
            }
            if (isset($input['team2_score'])) {
                $updateData['team2_score'] = $input['team2_score'] !== '' ? (int)$input['team2_score'] : null;
            }

            // Update penalty scores if provided
            if (isset($input['team1_penalty'])) {
                $updateData['team1_penalty'] = $input['team1_penalty'] !== '' ? (int)$input['team1_penalty'] : null;
            }
            if (isset($input['team2_penalty'])) {
                $updateData['team2_penalty'] = $input['team2_penalty'] !== '' ? (int)$input['team2_penalty'] : null;
            }

            // Update winner if manually selected
            if (isset($input['winner_team_id'])) {
                $updateData['winner_team_id'] = $input['winner_team_id'] !== '' ? (int)$input['winner_team_id'] : null;
            }

            // ไม่เปลี่ยน match_status - คงสถานะ "กำลังแข่งขัน" (1) ไว้

            $builder->where('match_id', $input['match_id']);
            $builder->update($updateData);

            return [
                'status' => 'success',
                'msg' => 'บันทึกคะแนนเรียบร้อย'
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'msg' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ];
        }
    }

    /**
     * บันทึกคะแนนและจบการแข่งขัน
     * @param array $input
     * @return array
     */
    public function endMatchWithScore($input)
    {
        $this->db->transBegin();

        try {
            $match_id = $input['match_id'];
            $team1_score = (int)$input['team1_score'];
            $team2_score = (int)$input['team2_score'];
            $team1_penalty = isset($input['team1_penalty']) && $input['team1_penalty'] !== '' ? (int)$input['team1_penalty'] : null;
            $team2_penalty = isset($input['team2_penalty']) && $input['team2_penalty'] !== '' ? (int)$input['team2_penalty'] : null;
            $manual_winner = isset($input['winner_team_id']) && $input['winner_team_id'] !== '' ? (int)$input['winner_team_id'] : null;

            // Get match data
            $match = $this->db->table('tournament_match_result')
                ->where('match_id', $match_id)
                ->get()
                ->getRowArray();

            if (!$match) {
                $this->db->transRollback();
                return ['status' => 'error', 'msg' => 'ไม่พบข้อมูลแมตช์'];
            }

            $team1_id = $match['team1_id'];
            $team2_id = $match['team2_id'];
            $tour_id = $match['tour_id'];

            // Determine winner
            $result = 0; // 0 = draw, 1 = team1 wins, 2 = team2 wins
            $winner_team_id = null;
            $is_penalty_used = 0;

            if ($team1_score > $team2_score) {
                // Team 1 wins by regular score
                $result = 1;
                $winner_team_id = $team1_id;
            } elseif ($team2_score > $team1_score) {
                // Team 2 wins by regular score
                $result = 2;
                $winner_team_id = $team2_id;
            } else {
                // Scores are tied, check penalties
                if ($team1_penalty !== null && $team2_penalty !== null) {
                    $is_penalty_used = 1;

                    if ($team1_penalty > $team2_penalty) {
                        $result = 1;
                        $winner_team_id = $team1_id;
                    } elseif ($team2_penalty > $team1_penalty) {
                        $result = 2;
                        $winner_team_id = $team2_id;
                    } else {
                        // Penalty also tied, use manual selection
                        if ($manual_winner) {
                            $winner_team_id = $manual_winner;
                            $result = ($manual_winner == $team1_id) ? 1 : 2;
                        } else {
                            // No winner determined yet
                            $result = 0;
                        }
                    }
                } else {
                    // No penalties, use manual selection if provided
                    if ($manual_winner) {
                        $winner_team_id = $manual_winner;
                        $result = ($manual_winner == $team1_id) ? 1 : 2;
                    } else {
                        $result = 0; // Draw
                    }
                }
            }

            // Update match
            $updateData = [
                'team1_score' => $team1_score,
                'team2_score' => $team2_score,
                'team1_penalty' => $team1_penalty,
                'team2_penalty' => $team2_penalty,
                'winner_team_id' => $winner_team_id,
                'is_penalty_used' => $is_penalty_used,
                'result' => $result,
                'match_status' => 3, // Match ended
                'update_by' => session()->get('member_id') ?? 0,
                'update_date' => date('Y-m-d H:i:s')
            ];

            $this->db->table('tournament_match_result')
                ->where('match_id', $match_id)
                ->update($updateData);

            // Handle elimination for knockout format
            $stage = $this->db->table('tournament_stages')
                ->select('format')
                ->where('stage_id', $match['stage_id'])
                ->get()
                ->getRow();

            $isKnockout = ($stage && $stage->format === 'knockout');

            if ($isKnockout && $winner_team_id && $team1_id && $team2_id) {
                $loser_team_id = ($winner_team_id == $team1_id) ? $team2_id : $team1_id;

                // Mark loser as eliminated
                $this->db->table('tournament_team')
                    ->set('is_eliminated', 1)
                    ->where('team_id', $loser_team_id)
                    ->where('tour_id', $tour_id)
                    ->update();

                // Mark winner as not eliminated
                $this->db->table('tournament_team')
                    ->set('is_eliminated', 0)
                    ->where('team_id', $winner_team_id)
                    ->where('tour_id', $tour_id)
                    ->update();
            }

            $this->db->transCommit();

            return [
                'status' => 'success',
                'msg' => 'จบการแข่งขันเรียบร้อย',
                'winner_team_id' => $winner_team_id,
                'result' => $result
            ];
        } catch (\Exception $e) {
            $this->db->transRollback();
            return [
                'status' => 'error',
                'msg' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ];
        }
    }

    function get_TeamInfo($id="")
    {
        if($id==''){
            return [];
        }
        $builder = $this->db->table("team");
        $builder->select("*");
        /*
        if ($is_active !== null) {
            $builder->where("is_active", $is_active);
        }
        */
        if ($id !== null) {
            $builder->where("id", $id);
        }
        $result = $builder->get()->getRowArray();
        return $result;
    }
}
