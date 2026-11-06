<?php

namespace Modules\Analytics\Controllers;

use App\Controllers\BaseController;
use App\Libraries\PythonApi;

class SettingAnalytics extends BaseController
{
    protected $db;
    protected $pythonApi;

    public function __construct()
    {
        $this->db        = \Config\Database::connect();
        $this->pythonApi = new PythonApi();
    }

    /**
     * หน้าตั้งค่าระบบ
     */
    public function index()
    {
        $settings = $this->db->table('analytics_settings')
                             ->get()->getResultArray();

        $settingsMap = [];
        foreach ($settings as $s) {
            $settingsMap[$s['setting_key']] = $s['setting_value'];
        }

        $pythonOnline = $this->pythonApi->isOnline();

        return view('template/admin_layout', [
            'title'   => 'ตั้งค่า Football Analytics',
            'content' => view('Modules\Analytics\Views\settings', [
                'settings'     => $settingsMap,
                'pythonOnline' => $pythonOnline,
            ]),
        ]);
    }

    /**
     * บันทึกตั้งค่า
     */
    public function save()
    {
        $keys = [
            'python_api_url', 'python_api_timeout',
            'confidence_threshold', 'iou_threshold',
            'sprint_threshold', 'pitch_width', 'pitch_height',
            'max_upload_size',
        ];

        foreach ($keys as $key) {
            $value = $this->request->getPost($key);
            if ($value !== null) {
                $this->db->table('analytics_settings')
                         ->where('setting_key', $key)
                         ->update(['setting_value' => $value]);
            }
        }

        return redirect()->to('/admin/analytics/settings')->with('success', 'บันทึกตั้งค่าสำเร็จ');
    }

    /**
     * หน้า Calibration (ปรับ homography)
     */
    public function calibration($videoId)
    {
        $video = $this->db->table('analytics_videos')
                          ->where('id', $videoId)
                          ->get()->getRowArray();

        if (!$video) {
            return redirect()->to('/admin/analytics/matches')->with('error', 'ไม่พบวิดีโอ');
        }

        $match = $this->db->table('analytics_matches')
                          ->where('id', $video['match_id'])
                          ->get()->getRowArray();

        $existing = $this->db->table('analytics_calibration')
                             ->where('video_id', $videoId)
                             ->get()->getRowArray();

        // ดึง frame สำหรับ calibration
        $framePath  = null;
        $frameResult = $this->pythonApi->getFrame(FCPATH . $video['original_path'], 5);
        if ($frameResult['status'] === 'success' && isset($frameResult['frame_path'])) {
            $framePath = base_url($frameResult['frame_path']);
        }

        return view('template/admin_layout', [
            'title'   => 'Calibration สนาม',
            'content' => view('Modules\Analytics\Views\calibration', [
                'video'    => $video,
                'match'    => $match,
                'existing' => $existing,
                'framePath'=> $framePath,
            ]),
        ]);
    }

    /**
     * ทดสอบการเชื่อมต่อ Python backend (AJAX)
     */
    public function testConnection()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }
        $online = $this->pythonApi->isOnline();
        if ($online) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Python backend พร้อมใช้งาน',
                'online'  => true,
            ]);
        }
        return $this->response->setJSON([
            'status'  => 'error',
            'message' => 'ไม่สามารถเชื่อมต่อ Python backend ได้',
            'online'  => false,
        ]);
    }

    /**
     * Auto Calibrate — เรียก Python API เพื่อตรวจเส้นสนามอัตโนมัติ (AJAX)
     */
    public function autoCalibrate($videoId)
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

        // ดึง frame ก่อน แล้วค่อย auto calibrate
        $frameResult = $this->pythonApi->getFrame(FCPATH . $video['original_path'], 5);
        if ($frameResult['status'] !== 'success' || empty($frameResult['frame_path'])) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ไม่สามารถดึง frame ได้ — ตรวจสอบ Python backend']);
        }

        $result = $this->pythonApi->autoCalibrateFrame(FCPATH . $frameResult['frame_path']);
        return $this->response->setJSON($result);
    }

    /**
     * บันทึก Calibration data
     */
    public function saveCalibration()
    {
        $body    = $this->request->getJSON(true) ?? $this->request->getPost();
        $videoId = isset($body['video_id']) ? (int)$body['video_id'] : 0;
        $matchId = isset($body['match_id']) ? (int)$body['match_id'] : 0;

        if (!$videoId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ข้อมูลไม่ครบ']);
        }

        $calData = [
            'match_id'   => $matchId,
            'video_id'   => $videoId,
            'src_points' => isset($body['src_points']) ? json_encode($body['src_points']) : null,
            'dst_points' => isset($body['dst_points']) ? json_encode($body['dst_points']) : null,
            'matrix'     => isset($body['matrix']) ? json_encode($body['matrix']) : null,
            'pitch_w'    => isset($body['pitch_width'])  ? (float)$body['pitch_width']  : (isset($body['pitch_w'])  ? (float)$body['pitch_w']  : 105),
            'pitch_h'    => isset($body['pitch_height']) ? (float)$body['pitch_height'] : (isset($body['pitch_h']) ? (float)$body['pitch_h'] : 68),
        ];

        $existing = $this->db->table('analytics_calibration')
                             ->where('video_id', $videoId)
                             ->get()->getRowArray();

        if ($existing) {
            $this->db->table('analytics_calibration')
                     ->where('id', $existing['id'])
                     ->update($calData);
        } else {
            $this->db->table('analytics_calibration')->insert($calData);
        }

        return $this->response->setJSON(['status' => 'success', 'message' => 'บันทึก calibration สำเร็จ']);
    }
}
