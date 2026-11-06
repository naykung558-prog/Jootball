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

trait TournamentStagesTrait
{
    /**
     * Get tournament stages configuration
     */
    public function get_tournament_stages($tour_id = '')
    {
        if (empty($tour_id)) {
            return $this->response->setJSON([
                'status' => 'error',
                'data' => []
            ]);
        }

        try {
            $stages = $this->db->table('tournament_stages')
                ->where('tour_id', $tour_id)
                ->orderBy('order_num', 'ASC')
                ->get()
                ->getResultArray();

            // Add match count for each stage
            foreach ($stages as &$stage) {
                $matchCount = $this->db->table('tournament_match_result')
                    ->where('stage_id', $stage['stage_id'])
                    ->countAllResults();
                $stage['match_count'] = $matchCount;
            }

            return $this->response->setJSON([
                'status' => 'success',
                'data' => $stages
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'msg' => 'เกิดข้อผิดพลาด: ' . $e->getMessage(),
                'data' => []
            ]);
        }
    }

    /**
     * Save tournament stage configuration
     */
    public function save_tournament_stage()
    {
        $input = $this->request->getPost();
        $tour_id = $input['tour_id'] ?? '';
        $stage_id = $input['stage_id'] ?? null;
        $stage_name = $input['stage_name'] ?? '';
        $format = $input['format'] ?? 'knockout';
        $team_count = $input['team_count'] ?? 4;
        $group_count = $input['group_count'] ?? 1;
        $order_num = $input['order_num'] ?? 0;

        if (empty($tour_id)) {
            return $this->response->setJSON([
                'status' => 'error',
                'msg' => 'กรุณากรอกข้อมูลให้ครบถ้วน'
            ]);
        }

        // ถ้า stage_name ว่างใช้ default
        if (empty($stage_name)) {
            $stage_name = 'รอบการแข่งขันที่ ' . ($order_num ?? 1);
        }

        try {
            if ($stage_id) {
                // Update existing stage
                $this->db->table('tournament_stages')
                    ->set([
                        'stage_name' => $stage_name,
                        'format' => $format,
                        'team_count' => $team_count,
                        'group_count' => $group_count,
                        'order_num' => $order_num,
                        'updated_at' => date('Y-m-d H:i:s')
                    ])
                    ->where('stage_id', $stage_id)
                    ->update();
            } else {
                // Create new stage
                $this->db->table('tournament_stages')
                    ->insert([
                        'tour_id' => $tour_id,
                        'stage_name' => $stage_name,
                        'format' => $format,
                        'team_count' => $team_count,
                        'group_count' => $group_count,
                        'order_num' => $order_num
                    ]);

                $stage_id = $this->db->insertID();
            }

            return $this->response->setJSON([
                'status' => 'success',
                'msg' => 'บันทึกข้อมูลสำเร็จ',
                'stage_id' => $stage_id
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'msg' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Update stage name only (for inline editing)
     */
    public function update_stage_name()
    {
        $input = $this->request->getPost();
        $stage_id = $input['stage_id'] ?? null;
        $stage_name = $input['stage_name'] ?? '';

        if (empty($stage_id)) {
            return $this->response->setJSON([
                'status' => 'error',
                'msg' => 'กรุณาระบุรหัสรอบการแข่งขัน'
            ]);
        }

        if (empty($stage_name)) {
            return $this->response->setJSON([
                'status' => 'error',
                'msg' => 'กรุณาระบุชื่อรอบการแข่งขัน'
            ]);
        }

        try {
            $this->db->table('tournament_stages')
                ->set([
                    'stage_name' => $stage_name,
                    'updated_at' => date('Y-m-d H:i:s')
                ])
                ->where('stage_id', $stage_id)
                ->update();

            // Carry the new name over to the stage's matches. match_round is a plain
            // string that several screens still group by, so leaving it on the old
            // name detaches every match from its stage: the setup page shows the stage
            // as having no matches (which invites a second Generate, duplicating them)
            // and the public bracket renders an extra column under the stale title.
            $renamedMatches = $this->db->table('tournament_match_result')
                ->where('stage_id', $stage_id)
                ->where('match_round !=', $stage_name)
                ->set([
                    'match_round' => $stage_name,
                    'update_date' => date('Y-m-d H:i:s'),
                ])
                ->update();

            return $this->response->setJSON([
                'status' => 'success',
                'msg' => 'บันทึกชื่อรอบการแข่งขันสำเร็จ',
                'stage_id' => $stage_id,
                'matches_renamed' => $renamedMatches,
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'msg' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Delete tournament stage and associated matches
     */
    public function delete_tournament_stage($stage_id = '')
    {
        if (empty($stage_id)) {
            return $this->response->setJSON([
                'status' => 'error',
                'msg' => 'ข้อมูลไม่ครบถ้วน'
            ]);
        }

        $this->db->transBegin();

        try {
            // Get stage info
            $stage = $this->db->table('tournament_stages')
                ->select('stage_id, tour_id, stage_name')
                ->where('stage_id', $stage_id)
                ->get()
                ->getRow();

            if (!$stage) {
                $this->db->transRollback();
                return $this->response->setJSON([
                    'status' => 'error',
                    'msg' => 'ไม่พบรอบการแข่งขันที่ต้องการลบ'
                ]);
            }

            // Find all match IDs to be deleted
            // Primary: match has this stage_id
            // Fallback: belongs to this tour and has this stage name (for legacy matches without stage_id)
            $matchesWithStageId = $this->db->table('tournament_match_result')
                ->select('match_id')
                ->where('stage_id', $stage_id)
                ->get()
                ->getResultArray();

            $matches = $matchesWithStageId;

            // If no matches found by stage_id, try finding by tour_id + stage_name (legacy)
            if (empty($matches)) {
                $matches = $this->db->table('tournament_match_result')
                    ->select('match_id')
                    ->where('tour_id', $stage->tour_id)
                    ->where('match_round', $stage->stage_name)
                    ->get()
                    ->getResultArray();
            }

            $matchIds = array_column($matches, 'match_id');

            log_message('info', '[delete_tournament_stage] Found ' . count($matchIds) . ' matches to delete for stage_id: ' . $stage_id);

            if (!empty($matchIds)) {
                // 1. Delete related events first (to avoid FK constraints)
                $deletedEvents = $this->db->table('tournament_match_event')
                    ->whereIn('match_id', $matchIds)
                    ->delete();
                log_message('info', '[delete_tournament_stage] Deleted ' . $deletedEvents . ' events');

                // 2. Delete player_to_match
                $deletedPlayerToMatch = $this->db->table('player_to_match')
                    ->whereIn('match_id', $matchIds)
                    ->delete();
                log_message('info', '[delete_tournament_stage] Deleted ' . $deletedPlayerToMatch . ' player_to_match records');

                // 3. Delete player_match_state
                $deletedPlayerMatchState = $this->db->table('player_match_state')
                    ->whereIn('match_id', $matchIds)
                    ->delete();
                log_message('info', '[delete_tournament_stage] Deleted ' . $deletedPlayerMatchState . ' player_match_state records');

                // 4. Delete matches
                $deletedMatches = $this->db->table('tournament_match_result')
                    ->whereIn('match_id', $matchIds)
                    ->delete();
                log_message('info', '[delete_tournament_stage] Deleted ' . $deletedMatches . ' matches');
            }

            // 3. Delete the stage
            $deletedStage = $this->db->table('tournament_stages')
                ->where('stage_id', $stage_id)
                ->delete();
            log_message('info', '[delete_tournament_stage] Deleted stage with stage_id: ' . $stage_id);

            // Commit transaction
            $this->db->transCommit();
            log_message('info', '[delete_tournament_stage] Transaction committed successfully');

            return $this->response->setJSON([
                'status' => 'success',
                'msg' => 'ลบข้อมูลสำเร็จ'
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
     * ดึงรายการทัวร์นาเมนต์ที่มี stages (สำหรับ import match-setup)
     */
    public function get_tournaments_with_stages()
    {
        try {
            $tournaments = $this->db->table('tournament')
                ->select('tournament.tour_id, tournament.name, tournament.name_en, tournament.logo')
                ->orderBy('tournament.tour_id', 'DESC')
                ->get()
                ->getResultArray();

            // เพิ่มจำนวน stages และ matches ของแต่ละทัวร์นาเมนต์
            foreach ($tournaments as &$tour) {
                $tour['stages_count'] = $this->db->table('tournament_stages')
                    ->where('tour_id', $tour['tour_id'])
                    ->countAllResults();

                $tour['matches_count'] = $this->db->table('tournament_match_result')
                    ->where('tour_id', $tour['tour_id'])
                    ->countAllResults();
            }

            // กรองเฉพาะที่มี stages
            $tournaments = array_values(array_filter($tournaments, function ($t) {
                return $t['stages_count'] > 0;
            }));

            return $this->response->setJSON([
                'status' => 'success',
                'data' => $tournaments
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'msg' => $e->getMessage(),
                'data' => []
            ]);
        }
    }

    /**
     * นำเข้า match-setup (stages + matches) จากทัวร์นาเมนต์อื่น
     */
    public function import_match_setup()
    {
        $sourceTourId = $this->request->getPost('source_tour_id');
        $targetTourId = $this->request->getPost('target_tour_id');

        if (empty($sourceTourId) || empty($targetTourId)) {
            return $this->response->setJSON([
                'status' => 'error',
                'msg' => 'ข้อมูลไม่ครบถ้วน'
            ]);
        }

        if ($sourceTourId == $targetTourId) {
            return $this->response->setJSON([
                'status' => 'error',
                'msg' => 'ไม่สามารถนำเข้าจากทัวร์นาเมนต์เดียวกันได้'
            ]);
        }

        // ตรวจสอบว่าทัวร์นาเมนต์ต้นทางมี stages หรือไม่
        $sourceStages = $this->db->table('tournament_stages')
            ->where('tour_id', $sourceTourId)
            ->orderBy('order_num', 'ASC')
            ->get()
            ->getResultArray();

        if (empty($sourceStages)) {
            return $this->response->setJSON([
                'status' => 'error',
                'msg' => lang('Common.no_stages_to_import')
            ]);
        }

        $this->db->transBegin();

        try {
            // 1. ลบ match-setup เดิมของทัวร์นาเมนต์ปลายทาง
            // ลบ events ก่อน
            $existingMatchIds = $this->db->table('tournament_match_result')
                ->select('match_id')
                ->where('tour_id', $targetTourId)
                ->get()
                ->getResultArray();

            $matchIds = array_column($existingMatchIds, 'match_id');

            if (!empty($matchIds)) {
                $this->db->table('tournament_match_event')
                    ->whereIn('match_id', $matchIds)
                    ->delete();

                $this->db->table('player_to_match')
                    ->whereIn('match_id', $matchIds)
                    ->delete();

                $this->db->table('player_match_state')
                    ->whereIn('match_id', $matchIds)
                    ->delete();

                $this->db->table('tournament_match_result')
                    ->where('tour_id', $targetTourId)
                    ->delete();
            }

            // ลบ stages เดิม
            $this->db->table('tournament_stages')
                ->where('tour_id', $targetTourId)
                ->delete();

            // 2. คัดลอก stages
            $stageIdMap = []; // old stage_id => new stage_id
            foreach ($sourceStages as $stage) {
                $this->db->table('tournament_stages')->insert([
                    'tour_id' => $targetTourId,
                    'stage_name' => $stage['stage_name'],
                    'format' => $stage['format'],
                    'team_count' => $stage['team_count'],
                    'group_count' => $stage['group_count'],
                    'order_num' => $stage['order_num'],
                    'teams_json' => $stage['teams_json'] ?? null,
                ]);
                $stageIdMap[$stage['stage_id']] = $this->db->insertID();
            }

            // 3. คัดลอก matches (คู่ทีม) โดย reset score/status
            $userId = session()->get('member_id') ?? 0;
            $now = date('Y-m-d H:i:s');

            $sourceMatches = $this->db->table('tournament_match_result')
                ->where('tour_id', $sourceTourId)
                ->get()
                ->getResultArray();

            foreach ($sourceMatches as $match) {
                $this->db->table('tournament_match_result')->insert([
                    'tour_id' => $targetTourId,
                    'stage_id' => isset($stageIdMap[$match['stage_id']]) ? $stageIdMap[$match['stage_id']] : null,
                    'match_round' => $match['match_round'],
                    'group_id' => $match['group_id'],
                    // Slots come along because teams_json is copied verbatim above, so
                    // the imported rows stay identifiable the same way the source's are.
                    'team1_slot' => $match['team1_slot'],
                    'team2_slot' => $match['team2_slot'],
                    //'team1_id' => $match['team1_id'],
                    //'team2_id' => $match['team2_id'],
                    'team1_score' => null,
                    'team2_score' => null,
                    'result' => null,
                    'match_status' => 0,
                    //'referee' => $match['referee'],
                    //'stadium' => $match['stadium'],
                    //'date' => $match['date'],
                    //'time' => $match['time'],
                    'create_by' => $userId,
                    'create_date' => $now,
                    'update_by' => $userId,
                    'update_date' => $now,
                ]);
            }

            $this->db->transCommit();

            return $this->response->setJSON([
                'status' => 'success',
                'msg' => lang('Common.import_success'),
                'stages_imported' => count($sourceStages),
                'matches_imported' => count($sourceMatches)
            ]);
        } catch (\Exception $e) {
            $this->db->transRollback();
            return $this->response->setJSON([
                'status' => 'error',
                'msg' => lang('Common.import_error') . ': ' . $e->getMessage()
            ]);
        }
    }

    /**
     * อัปเดตข้อมูลทีมในรอบการแข่งขัน
     */
    public function update_stage_teams()
    {
        $stageId = $this->request->getPost('stage_id');
        $teamsJson = $this->request->getPost('teams_json');  // FIX: Match frontend parameter name

        if (empty($stageId) || empty($teamsJson)) {
            return $this->response->setJSON([
                'status' => 'error',
                'msg' => 'ข้อมูลไม่ครบถ้วน'
            ]);
        }

        try {
            $teams = json_decode($teamsJson, true);

            if (!is_array($teams)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'msg' => 'รูปแบบข้อมูลไม่ถูกต้อง'
                ]);
            }

            // Update database
            $this->db->table('tournament_stages')
                ->where('stage_id', $stageId)
                ->update([
                    'teams_json' => $teamsJson,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

            // Sync matches
            $this->tournamentModel->syncStageMatches($stageId, $teams);

            return $this->response->setJSON([
                'status' => 'success',
                'msg' => 'อัปเดตข้อมูลทีมสำเร็จ',
                'teams_count' => count($teams)
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'msg' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ]);
        }
    }
}
