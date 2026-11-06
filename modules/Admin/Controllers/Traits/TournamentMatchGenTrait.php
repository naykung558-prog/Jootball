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
use Modules\Admin\Controllers\Traits\TournamentScoringTrait;
use Modules\Admin\Controllers\Traits\TournamentExportTrait;
use Modules\Admin\Controllers\Traits\TournamentStagesTrait;

trait TournamentMatchGenTrait
{
    // Google Ai Algorithms
    public function generate()
    {
        // 1. Validation
        $rules = [
            'tour_id' => 'required|is_natural_no_zero',
            'teams'   => 'required',
            'format'  => 'required|in_list[round_robin,knockout,group_stage]',
            'num_groups' => 'permit_empty|is_natural_no_zero',
            'match_round_name' => 'required|string|max_length[255]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // 2. Get Data from POST
        $tourId = $this->request->getPost('tour_id');
        $teamIds = $this->request->getPost('teams');
        $format = $this->request->getPost('format');
        $numGroups = $this->request->getPost('num_groups');
        $matchRoundName = $this->request->getPost('match_round_name');


        // 3. Call Library to generate match pairs
        $scheduler = new MatchScheduler();
        $generatedPairs = [];
        $groupId = null; // for round_robin and knockout

        switch ($format) {
            case 'round_robin':
                $generatedPairs = $scheduler->generateRoundRobin($teamIds);
                break;
            case 'knockout':
                $generatedPairs = $scheduler->generateKnockout($teamIds);
                break;
            case 'group_stage':
                if (empty($numGroups) || $numGroups < 2) {
                    return redirect()->back()->withInput()->with('error', 'กรุณาระบุจำนวนกลุ่มให้ถูกต้อง');
                }
                $generatedPairs = $scheduler->generateGroupStage($teamIds, (int)$numGroups);
                break;
        }

        if (empty($generatedPairs)) {
            return redirect()->back()->withInput()->with('error', 'ไม่สามารถสร้างตารางแข่งขันได้ อาจมีทีมไม่พอ');
        }

        // 4. Prepare data for Batch Insert
        $userId = session()->get('member_id'); // สมมติว่าได้ User ID มาจากการ Login แล้ว

        foreach ($generatedPairs as $pair) {
            $matchesToInsert = [
                'tour_id'       => $tourId,
                'match_round'   => $matchRoundName,
                'team1_id'      => $pair['team1'],
                'team2_id'      => $pair['team2'],
                'result' => '',
                'referee' => '',
                'group_id'      => $pair['group_id'] ?? null, // ใช้ค่าจาก pair ถ้ามี
                'create_by'     => $userId,
                'create_date'   => date('Y-m-d H:i:s'),
                // field อื่นๆ เช่น match_datetime, stadium สามารถกำหนดค่าเริ่มต้นหรือปล่อยเป็น NULL ไว้ก่อนได้
            ];
            // 5. Insert to DB
            $this->tournamentModel->manageTournamentMatch($matchesToInsert);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'msg' => 'สร้างการแข่งขันสำเร็จ',
        ]);
    }

    public function delete_tournament_match_result()
    {
        $id = $this->request->getPost('id');
        $builder = $this->db->table("tournament_match_result");
        $builder->where('match_id', $id)->delete();
        return $this->response->setJSON([
            'status' => 'success',
            'msg' => 'ลบข้อมูลสำเร็จ'
        ]);
    }

    public function delete_match()
    {
        $match_id = $this->request->getPost('match_id');

        if (!$match_id) {
            return $this->response->setJSON([
                'status' => 'error',
                'msg' => 'match_id ไม่ถูกต้อง'
            ]);
        }

        $builder = $this->db->table("tournament_match_result");
        $builder->where('match_id', $match_id)->delete();

        return $this->response->setJSON([
            'status' => 'success',
            'msg' => 'ลบแมตช์สำเร็จ'
        ]);
    }

    public function delete_match_round()
    {
        $tour_id = $this->request->getPost('tour_id');
        $round_name = $this->request->getPost('round_name');

        if (!$tour_id || !$round_name) {
            return $this->response->setJSON([
                'status' => 'error',
                'msg' => 'ข้อมูลไม่ถูกต้อง'
            ]);
        }

        $this->tournamentModel->deleteMatchRound($tour_id, $round_name);

        return $this->response->setJSON([
            'status' => 'success',
            'msg' => 'ลบรอบแข่งขันสำเร็จ'
        ]);
    }

    /**
     * หน้าตั้งค่าการสร้างแมตช์การแข่งขัน
     */
    public function match_setup($tour_id = '')
    {
        if (empty($tour_id)) {
            return redirect()->to(base_url('admin/tournament'));
        }

        // อัปเดตสถานะแมตช์อัตโนมัติตามวันที่
        $this->tournamentModel->autoUpdateMatchStatus($tour_id);

        $data['tour_id'] = $tour_id;
        $data['tournament'] = $this->tournamentModel->get_Tournament($tour_id);
        $data['registered_teams'] = $this->tournamentModel->get_Tournament_Team($tour_id);
        $data['total_teams'] = count($data['registered_teams']);

        // ดึงรายการสนามสำหรับ dropdown
        $stadiumModel = new \Modules\Admin\Models\StadiumModel();
        $data['stadiums'] = $stadiumModel->getActiveStadiums();

        return view('Modules\Admin\Views\Tournament\match_setup.php', $data);
    }

    /**
     * สร้างแมตช์การแข่งขันตามรูปแบบที่เลือก
     */
    public function generate_matches()
    {
        $input = $this->request->getPost();
        $tour_id = $input['tour_id'] ?? '';
        $tournament_type = $input['tournament_type'] ?? '';
        $num_teams = (int)($input['num_teams'] ?? 0);

        if (empty($tour_id) || empty($tournament_type) || $num_teams < 2) {
            return $this->response->setJSON([
                'status' => 'error',
                'msg' => 'กรุณากรอกข้อมูลให้ครบถ้วน'
            ]);
        }

        // อัปเดตประเภทการแข่งขันในตาราง tournament
        $this->db->table('tournament')
            ->set('tournament_type', $tournament_type)
            ->where('tour_id', $tour_id)
            ->update();

        // สร้างแมตช์ตามรูปแบบ
        if ($tournament_type === 'league') {
            $result = $this->tournamentModel->generateLeagueMatches($tour_id, $num_teams);
        } else {
            $result = $this->tournamentModel->generateKnockoutMatches($tour_id, $num_teams);
        }

        return $this->response->setJSON($result);
    }

    /**
     * สร้างแมตช์จากหลายรอบการแข่งขัน
     */
    public function generate_matches_with_rounds()
    {
        $input = $this->request->getPost();
        $tour_id = $input['tour_id'] ?? '';
        $num_teams = (int)($input['num_teams'] ?? 0);
        $rounds = json_decode($input['rounds'] ?? '[]', true);

        if (empty($tour_id) || empty($rounds) || $num_teams < 2) {
            return $this->response->setJSON([
                'status' => 'error',
                'msg' => 'ข้อมูลไม่ครบถ้วน'
            ]);
        }

        try {
            // ลบแมตช์เก่าทั้งหมด
            $this->db->table('tournament_match_result')
                ->where('tour_id', $tour_id)
                ->delete();

            $totalMatches = 0;
            // Support both regular users and coaches
            $userId = session()->get('member_id') ?? session()->get('coach_user_id') ?? 0;
            $now = date('Y-m-d H:i:s');

            // สร้างแมตช์สำหรับแต่ละรอบ
            foreach ($rounds as $roundData) {
                $roundName = $roundData['name'] ?? '';
                $groupName = $roundData['group'] ?? '';
                $type = $roundData['type'] ?? 'league';

                // สมมติว่าจำนวนแมตช์ = n(n-1)/2 สำหรับลีก หรือ n-1 สำหรับ knockout
                $numMatches = $type === 'league'
                    ? ($num_teams * ($num_teams - 1)) / 2
                    : $num_teams - 1;

                // สร้างแมตช์แบบเปล่า (ยังไม่มีทีม)
                for ($i = 0; $i < $numMatches; $i++) {
                    $this->db->table('tournament_match_result')->insert([
                        'tour_id' => $tour_id,
                        'match_round' => $roundName,
                        'group_id' => $groupName,
                        'team1_id' => null,
                        'team2_id' => null,
                        'team1_score' => null,
                        'team2_score' => null,
                        'result' => null,
                        'referee' => null,
                        'stadium' => null,
                        'date' => null,
                        'time' => null,
                        'create_by' => $userId,
                        'create_date' => $now,
                        'update_by' => $userId,
                        'update_date' => $now
                    ]);
                    $totalMatches++;
                }
            }

            return $this->response->setJSON([
                'status' => 'success',
                'msg' => "สร้างแมตช์จาก " . count($rounds) . " รอบสำเร็จ",
                'total_matches' => $totalMatches
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'msg' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * สร้างแมตช์พร้อมทีมที่จับคู่แล้ว (ใช้ MatchScheduler)
     */
    public function generate_matches_with_teams()
    {
        $input = $this->request->getPost();
        $tour_id = $input['tour_id'] ?? '';
        $stage_id = $input['stage_id'] ?? null;
        $rounds = json_decode($input['rounds'] ?? '[]', true);

        // Receive structured teamsData if available, otherwise fallback to teamIds
        $teamsData = json_decode($input['teamsData'] ?? '[]', true);
        if (empty($teamsData)) {
            $teamsData = json_decode($input['teamIds'] ?? '[]', true);
        }

        log_message('error', 'GENERATE MATCHES DEBUG: tour_id=' . $tour_id . ', rounds=' . ($input['rounds'] ?? 'NULL') . ', teamsData=' . ($input['teamsData'] ?? 'NULL') . ', teamIds=' . ($input['teamIds'] ?? 'NULL'));

        $missing = [];
        if (empty($tour_id)) $missing[] = 'tour_id';
        if (empty($rounds)) $missing[] = 'rounds';
        if (empty($teamsData)) $missing[] = 'teamsData';

        if (!empty($missing)) {
            log_message('error', 'GENERATE MATCHES ERROR: Missing fields: ' . implode(', ', $missing));
            return $this->response->setJSON([
                'status' => 'error',
                'msg' => 'ข้อมูลไม่ครบถ้วน (' . implode(', ', $missing) . ')'
            ]);
        }

        // เรียก model เพื่อสร้างแมตช์ด้วย MatchScheduler
        $result = $this->tournamentModel->generateMatchesWithTeams($tour_id, $rounds, $teamsData, $stage_id);
        return $this->response->setJSON($result);
    }

    /**
     * หน้าจัดทีมตามสาย (แสดงตารางแบ่งสาย)
     */
    public function assign_matches_by_group($tour_id = '')
    {
        if (empty($tour_id)) {
            return redirect()->to(base_url('admin/tournament'));
        }

        $data['tour_id'] = $tour_id;
        $data['tournament'] = $this->tournamentModel->get_Tournament($tour_id);

        // ดึงข้อมูลแมตช์ทั้งหมด
        $all_matches = $this->db->table('tournament_match_result')
            ->where('tour_id', $tour_id)
            ->get()
            ->getResultArray();

        $data['all_matches'] = $all_matches;

        // ดึงข้อมูลทีมและแปลงเป็น associative array โดยใช้ id เป็น key
        $teams_list = $this->tournamentModel->get_Tournament_Team($tour_id);
        $teams_indexed = [];
        foreach ($teams_list as $team) {
            $teams_indexed[$team['id']] = $team;
        }
        $data['teams'] = $teams_indexed;

        // จัดกลุ่มแมตช์ตามสาย (group_id)
        $grouped_matches = [];
        foreach ($all_matches as $match) {
            $group = $match['group_id'] ?? 'ไม่ระบุสาย';
            if (!isset($grouped_matches[$group])) {
                $grouped_matches[$group] = [];
            }
            $grouped_matches[$group][] = $match;
        }
        $data['grouped_matches'] = $grouped_matches;

        return view('Modules\Admin\Views\Tournament\assign_matches_by_group.php', $data);
    }


    /**
     * AJAX: บันทึกทีมทั้งหมดพร้อมกัน
     */
    public function bulk_assign_teams()
    {
        $input = $this->request->getPost();
        $assignments = $input['assignments'] ?? [];

        if (empty($assignments)) {
            return $this->response->setJSON([
                'status' => 'error',
                'msg' => 'ไม่มีข้อมูลที่จะบันทึก'
            ]);
        }

        $this->db->transBegin();

        try {
            foreach ($assignments as $assignment) {
                $match_id = $assignment['match_id'] ?? '';
                $team1_id = $assignment['team1_id'] ?? 0;
                $team2_id = $assignment['team2_id'] ?? 0;

                if (!empty($match_id)) {
                    $this->tournamentModel->assignTeamsToMatch($match_id, $team1_id, $team2_id);
                }
            }

            $this->db->transCommit();

            return $this->response->setJSON([
                'status' => 'success',
                'msg' => 'บันทึกข้อมูลสำเร็จ'
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
     * Get match details via AJAX for modal editing
     */
    public function get_match_details($tour_id = '', $match_id = '')
    {
        try {
            if (!$tour_id || !$match_id) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'msg' => 'ไม่มีข้อมูลแมตช์ที่ขอ'
                ]);
            }

            $matchData = $this->tournamentModel->getOnceTournament_Match_Result($match_id);

            if (!$matchData) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'msg' => 'ไม่พบข้อมูลแมตช์'
                ]);
            }

            return $this->response->setJSON([
                'status' => 'success',
                'data' => $matchData
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'msg' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ]);
        }
    }
}
