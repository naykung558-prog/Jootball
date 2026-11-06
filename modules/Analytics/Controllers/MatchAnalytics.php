<?php

namespace Modules\Analytics\Controllers;

use App\Controllers\BaseController;
use App\Libraries\PythonApi;
use Modules\Analytics\Models\MatchAnalyticsModel;

class MatchAnalytics extends BaseController
{
    protected $matchModel;
    protected $pythonApi;
    protected $db;

    public function __construct()
    {
        $this->matchModel = new MatchAnalyticsModel();
        $this->pythonApi  = new PythonApi();
        $this->db         = \Config\Database::connect();
    }

    /**
     * รายการแมตช์ทั้งหมด (DataTable)
     * GET  → แสดง view
     * POST AJAX → คืน DataTable JSON
     */
    public function index()
    {
        log_message('critical', 'DEBUG INDEX method=' . $this->request->getMethod() . ' isAJAX=' . ($this->request->isAJAX() ? 'yes' : 'no'));
        if ($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'post') {
            $post = $this->request->getPost();
            $data = $this->matchModel->getMatchTable($post);
            return $this->response->setJSON($data);
        }
        return view('template/admin_layout', [
            'title'   => 'จัดการแมตช์วิเคราะห์',
            'content' => view('Modules\Analytics\Views\match_index'),
        ]);
    }

    /**
     * ฟอร์มสร้าง/แก้ไขแมตช์
     */
    public function manage($id = null)
    {
        $match  = null;
        $videos = [];

        if ($id) {
            $match = $this->matchModel->find($id);
            if (!$match) {
                return redirect()->to('/admin/analytics/matches')->with('error', 'ไม่พบข้อมูลแมตช์');
            }
            $videos = $this->db->table('analytics_videos')
                               ->where('match_id', $id)
                               ->orderBy('half', 'ASC')
                               ->get()->getResultArray();
        }

        $teams = $this->db->table('team')
                          ->select('id, name as team_name')
                          ->orderBy('name', 'ASC')
                          ->get()->getResultArray();

        return view('template/admin_layout', [
            'title'   => $id ? 'แก้ไขแมตช์' : 'เพิ่มแมตช์ใหม่',
            'content' => view('Modules\Analytics\Views\match_manage', [
                'match'  => $match,
                'videos' => $videos,
                'teams'  => $teams,
            ]),
        ]);
    }

    /**
     * บันทึกข้อมูลแมตช์ + อัปโหลดวิดีโอ
     */
    public function save()
    {
        log_message('critical', 'DEBUG SAVE_START method=' . $this->request->getMethod() .
            ' post_count=' . count($this->request->getPost()) .
            ' match_name=[' . $this->request->getPost('match_name') . ']');

        // ถ้า POST data ว่างทั้งหมด แสดงว่าไฟล์ที่อัปโหลดใหญ่เกิน post_max_size ใน php.ini
        if (empty($this->request->getPost()) && $this->request->getMethod() === 'post') {
            log_message('critical', 'DEBUG => EMPTY_POST early return');
            return redirect()->back()->with('error', 'ไฟล์วิดีโอใหญ่เกิน limit กรุณาติดต่อผู้ดูแลระบบ หรืออัปโหลดวิดีโอแยกหลังบันทึกแมตช์แล้ว');
        }

        if (empty($this->request->getPost('match_name'))) {
            log_message('critical', 'DEBUG => NO_MATCH_NAME early return');
            return redirect()->back()->with('error', 'กรุณากรอกชื่อแมตช์')->withInput();
        }

        $id   = $this->request->getPost('id');
        $data = [
            'match_name'     => $this->request->getPost('match_name'),
            'match_date'     => $this->request->getPost('match_date'),
            'home_team_id'   => $this->request->getPost('home_team_id') ?: null,
            'away_team_id'   => $this->request->getPost('away_team_id') ?: null,
            'home_team_name' => $this->request->getPost('home_team_name'),
            'away_team_name' => $this->request->getPost('away_team_name'),
            'home_score'     => (int) $this->request->getPost('home_score'),
            'away_score'     => (int) $this->request->getPost('away_score'),
            'venue'          => $this->request->getPost('venue'),
            'description'    => $this->request->getPost('description'),
            'created_by'     => session()->get('id'),
        ];

        if ($id) {
            $this->matchModel->update($id, $data);
            $matchId = $id;
        } else {
            $this->matchModel->insert($data);
            // ใช้ insertID() จาก connection เดียวกับที่ model ใช้ insert
            $matchId = $this->matchModel->db->insertID();
            if (!$matchId) {
                // fallback: query LAST_INSERT_ID() ผ่าน connection ของ model
                $row     = $this->matchModel->db->query('SELECT LAST_INSERT_ID() AS id')->getRowArray();
                $matchId = $row['id'] ?? 0;
            }
        }

        // อัปโหลดวิดีโอ (half1 + half2)
        $uploadPath = FCPATH . 'uploads/analytics/videos/';
        foreach ([1, 2] as $half) {
            $file = $this->request->getFile('video_half' . $half);
            if ($file && $file->isValid() && !$file->hasMoved()) {
                $newName = 'match_' . $matchId . '_half' . $half . '_' . time() . '.' . $file->getExtension();
                $file->move($uploadPath, $newName);

                // ลบวิดีโอเก่าของ half นี้ (ถ้ามี)
                $oldVideo = $this->db->table('analytics_videos')
                                     ->where(['match_id' => $matchId, 'half' => $half])
                                     ->get()->getRowArray();
                if ($oldVideo) {
                    $oldFile = FCPATH . 'uploads/analytics/videos/' . basename($oldVideo['original_path']);
                    if (file_exists($oldFile)) {
                        @unlink($oldFile);
                    }
                    $this->db->table('analytics_videos')->delete(['id' => $oldVideo['id']]);
                }

                $this->db->table('analytics_videos')->insert([
                    'match_id'      => $matchId,
                    'half'          => $half,
                    'original_path' => 'uploads/analytics/videos/' . $newName,
                    'file_size'     => $file->getSize(),
                    'status'        => 'uploaded',
                ]);
            }
        }

        log_message('critical', 'DEBUG SAVE matchId=' . $matchId . ' type=' . gettype($matchId));
        return redirect()->to('/admin/analytics/matches/detail/' . $matchId)
                         ->with('success', $id ? 'แก้ไขแมตช์สำเร็จ' : 'เพิ่มแมตช์สำเร็จ');
    }

    /**
     * รายละเอียดแมตช์
     */
    public function detail($id)
    {
        log_message('critical', 'DEBUG DETAIL id=' . $id);
        $match = $this->matchModel->getMatchWithStats($id);
        if (!$match) {
            return redirect()->to('/admin/analytics/matches')->with('error', 'ไม่พบข้อมูล');
        }

        $videos = $this->db->table('analytics_videos')
                           ->where('match_id', $id)
                           ->orderBy('half', 'ASC')
                           ->get()->getResultArray();

        $playerStats = $this->db->table('analytics_player_stats aps')
                                ->select('aps.*, p.first_name as player_name, p.last_name as player_surname, p.image_path as image_url')
                                ->join('player p', 'p.id = aps.player_id', 'left')
                                ->where('aps.match_id', $id)
                                ->orderBy('aps.total_distance', 'DESC')
                                ->get()->getResultArray();

        return view('template/admin_layout', [
            'title'   => 'รายละเอียดแมตช์',
            'content' => view('Modules\Analytics\Views\match_detail', [
                'match'       => $match,
                'videos'      => $videos,
                'playerStats' => $playerStats,
            ]),
        ]);
    }

    /**
     * ส่งวิดีโอให้ Python ประมวลผล (AJAX)
     */
    public function process($videoId)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $video = $this->db->table('analytics_videos')
                          ->where('id', $videoId)
                          ->get()->getRowArray();

        if (!$video) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ไม่พบวิดีโอ']);
        }

        if (in_array($video['status'], ['processing', 'queued'])) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'วิดีโอกำลังประมวลผลอยู่']);
        }

        $videoPath = FCPATH . $video['original_path'];
        $result    = $this->pythonApi->processVideo($videoId, $videoPath, [
            'match_id' => $video['match_id'],
        ]);

        if ($result['status'] === 'success') {
            $this->db->table('analytics_videos')->update(['id' => $videoId], [
                'status'          => 'queued',
                'task_id'         => $result['task_id'],
                'process_started' => date('Y-m-d H:i:s'),
                'error_message'   => null,
            ]);

            // อัปเดตสถานะแมตช์
            $this->db->table('analytics_matches')
                     ->where('id', $video['match_id'])
                     ->update(['status' => 'processing']);
        }

        return $this->response->setJSON($result);
    }

    /**
     * ตรวจสอบสถานะ Celery task (AJAX polling)
     */
    public function checkStatus($videoId)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $video = $this->db->table('analytics_videos')
                          ->where('id', $videoId)
                          ->get()->getRowArray();

        if (!$video || !$video['task_id']) {
            return $this->response->setJSON(['status' => 'pending', 'progress' => 0]);
        }

        $taskResult = $this->pythonApi->getTaskStatus($video['task_id']);
        $celeryStatus = isset($taskResult['state']) ? $taskResult['state'] : 'UNKNOWN';

        if ($celeryStatus === 'SUCCESS') {
            $this->db->table('analytics_videos')->update(['id' => $videoId], [
                'status'        => 'completed',
                'process_ended' => date('Y-m-d H:i:s'),
            ]);
            $this->_updateMatchStatus($video['match_id']);

        } elseif ($celeryStatus === 'FAILURE') {
            $this->db->table('analytics_videos')->update(['id' => $videoId], [
                'status'        => 'error',
                'error_message' => isset($taskResult['error']) ? $taskResult['error'] : 'Unknown error',
                'process_ended' => date('Y-m-d H:i:s'),
            ]);
        } elseif ($celeryStatus === 'STARTED' || $celeryStatus === 'PROGRESS') {
            $this->db->table('analytics_videos')->update(['id' => $videoId], ['status' => 'processing']);
        }

        return $this->response->setJSON([
            'status'   => strtolower($celeryStatus),
            'progress' => isset($taskResult['progress']) ? $taskResult['progress'] : 0,
            'message'  => isset($taskResult['message']) ? $taskResult['message'] : '',
        ]);
    }

    /**
     * ลบแมตช์
     */
    public function delete($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        // ลบวิดีโอก่อน
        $videos = $this->db->table('analytics_videos')
                           ->where('match_id', $id)
                           ->get()->getResultArray();
        foreach ($videos as $v) {
            $vPath = FCPATH . $v['original_path'];
            if (file_exists($vPath)) {
                @unlink($vPath);
            }
        }

        // ลบข้อมูลทั้งหมดของแมตช์
        $tables = ['analytics_videos', 'analytics_tracking', 'analytics_ball',
                   'analytics_player_stats', 'analytics_events', 'analytics_player_map',
                   'analytics_calibration'];
        foreach ($tables as $table) {
            $this->db->table($table)->where('match_id', $id)->delete();
        }
        $this->matchModel->delete($id);

        return $this->response->setJSON(['status' => 'success', 'message' => 'ลบแมตช์สำเร็จ']);
    }

    /**
     * ลบวิดีโอเดี่ยว
     */
    public function deleteVideo($videoId)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $video = $this->db->table('analytics_videos')
                          ->where('id', $videoId)
                          ->get()->getRowArray();
        if (!$video) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ไม่พบวิดีโอ']);
        }

        $vPath = FCPATH . $video['original_path'];
        if (file_exists($vPath)) {
            @unlink($vPath);
        }

        $this->db->table('analytics_videos')->delete(['id' => $videoId]);

        return $this->response->setJSON(['status' => 'success', 'message' => 'ลบวิดีโอสำเร็จ']);
    }

    /**
     * Map jersey number -> player_id
     */
    public function mapPlayer()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $body = $this->request->getJSON(true) ?? $this->request->getPost();
        $matchId   = isset($body['match_id']) ? (int)$body['match_id'] : 0;
        $jerseyNum = isset($body['jersey_num']) ? (int)$body['jersey_num'] : 0;
        $teamSide  = isset($body['team_side']) ? (int)$body['team_side'] : 0;
        $playerId  = isset($body['player_id']) ? (int)$body['player_id'] : 0;

        if (!$matchId || !$playerId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ข้อมูลไม่ครบ']);
        }

        $existing = $this->db->table('analytics_player_map')
                             ->where(['match_id' => $matchId, 'jersey_num' => $jerseyNum, 'team_side' => $teamSide])
                             ->get()->getRowArray();

        if ($existing) {
            $this->db->table('analytics_player_map')
                     ->where('id', $existing['id'])
                     ->update(['player_id' => $playerId]);
        } else {
            $this->db->table('analytics_player_map')->insert([
                'match_id'   => $matchId,
                'jersey_num' => $jerseyNum,
                'team_side'  => $teamSide,
                'player_id'  => $playerId,
            ]);
        }

        // อัปเดต player_id ใน tracking + player_stats
        $this->db->table('analytics_tracking')
                 ->where(['match_id' => $matchId, 'jersey_num' => $jerseyNum, 'team_side' => $teamSide])
                 ->update(['player_id' => $playerId]);
        $this->db->table('analytics_player_stats')
                 ->where(['match_id' => $matchId, 'jersey_num' => $jerseyNum, 'team_side' => $teamSide])
                 ->update(['player_id' => $playerId]);

        return $this->response->setJSON(['status' => 'success', 'message' => 'Map ผู้เล่นสำเร็จ']);
    }

    /**
     * Private — อัปเดตสถานะแมตช์ตามสถานะวิดีโอทั้งหมด
     */
    private function _updateMatchStatus($matchId)
    {
        $videos   = $this->db->table('analytics_videos')
                             ->where('match_id', $matchId)
                             ->get()->getResultArray();
        $statuses = array_column($videos, 'status');

        if (in_array('error', $statuses)) {
            $newStatus = 'error';
        } elseif (count(array_filter($statuses, function($s) { return $s === 'completed'; })) === count($statuses)) {
            $newStatus = 'completed';
        } else {
            $newStatus = 'processing';
        }

        $this->db->table('analytics_matches')
                 ->where('id', $matchId)
                 ->update(['status' => $newStatus]);
    }
}
