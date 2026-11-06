<?php

namespace App\Libraries;

/**
 * PythonApi - Library สำหรับเรียก Python FastAPI backend
 * ใช้ cURL เพื่อส่ง request ไปยัง football-cv-backend
 */
class PythonApi
{
    protected $baseUrl;
    protected $timeout;

    public function __construct()
    {
        $db = \Config\Database::connect();
        $this->baseUrl = $this->getSetting($db, 'python_api_url', 'http://localhost:8000');
        $this->timeout = (int) $this->getSetting($db, 'python_api_timeout', '30');
    }

    /**
     * ดึง setting จาก analytics_settings
     */
    private function getSetting($db, $key, $default = '')
    {
        $row = $db->table('analytics_settings')
                  ->where('setting_key', $key)
                  ->get()->getRowArray();
        return $row ? $row['setting_value'] : $default;
    }

    /**
     * ส่งวิดีโอให้ Python ประมวลผล
     * @param int    $videoId   analytics_videos.id
     * @param string $videoPath absolute path ไฟล์วิดีโอ
     * @param array  $options   ตัวเลือกเพิ่มเติม (confidence, iou, ...)
     * @return array ['status'=>'success'/'error', 'task_id'=>..., 'message'=>...]
     */
    public function processVideo($videoId, $videoPath, $options = [])
    {
        $payload = json_encode([
            'video_id'   => $videoId,
            'video_path' => $videoPath,
            'match_id'   => isset($options['match_id']) ? (int)$options['match_id'] : null,
            'confidence' => isset($options['confidence']) ? (float)$options['confidence'] : 0.5,
            'iou'        => isset($options['iou']) ? (float)$options['iou'] : 0.45,
        ]);

        $response = $this->curlPost('/api/process-video', $payload);

        if ($response === false) {
            return ['status' => 'error', 'message' => 'ไม่สามารถเชื่อมต่อ Python backend ได้'];
        }

        $data = json_decode($response, true);
        if (isset($data['task_id'])) {
            return ['status' => 'success', 'task_id' => $data['task_id'], 'message' => 'เริ่มประมวลผลแล้ว'];
        }

        return ['status' => 'error', 'message' => isset($data['detail']) ? $data['detail'] : 'เกิดข้อผิดพลาด'];
    }

    /**
     * ตรวจสอบสถานะ Celery task
     * @param string $taskId Celery task ID
     * @return array ['status'=>..., 'progress'=>..., 'result'=>...]
     */
    public function getTaskStatus($taskId)
    {
        $response = $this->curlGet('/api/task-status/' . urlencode($taskId));

        if ($response === false) {
            return ['status' => 'error', 'message' => 'ไม่สามารถตรวจสอบสถานะได้'];
        }

        $data = json_decode($response, true);
        return $data ?: ['status' => 'error', 'message' => 'Response ไม่ถูกต้อง'];
    }

    /**
     * ขอ calibrate อัตโนมัติจาก frame
     * @param string $framePath path ภาพ frame
     * @return array ['status'=>..., 'matrix'=>..., 'src_points'=>..., 'dst_points'=>...]
     */
    public function autoCalibrateFrame($framePath)
    {
        $payload = json_encode(['frame_path' => $framePath]);
        $response = $this->curlPost('/api/auto-calibrate', $payload);

        if ($response === false) {
            return ['status' => 'error', 'message' => 'ไม่สามารถ calibrate ได้'];
        }

        $data = json_decode($response, true);
        return $data ?: ['status' => 'error', 'message' => 'Response ไม่ถูกต้อง'];
    }

    /**
     * ดึง frame จากวิดีโอ
     * @param string $videoPath
     * @param int    $second    วินาทีที่ต้องการ frame
     * @return array ['status'=>..., 'frame_path'=>...]
     */
    public function getFrame($videoPath, $second = 5)
    {
        $payload = json_encode([
            'video_path' => $videoPath,
            'second'     => $second,
        ]);
        $response = $this->curlPost('/api/get-frame', $payload);

        if ($response === false) {
            return ['status' => 'error', 'message' => 'ไม่สามารถดึง frame ได้'];
        }

        $data = json_decode($response, true);
        return $data ?: ['status' => 'error', 'message' => 'Response ไม่ถูกต้อง'];
    }

    /**
     * ตรวจสอบว่า Python backend ออนไลน์หรือไม่
     * @return bool
     */
    public function isOnline()
    {
        $response = $this->curlGet('/health', 3);
        if ($response === false) {
            return false;
        }
        $data = json_decode($response, true);
        return isset($data['status']) && $data['status'] === 'ok';
    }

    // ----------------------------------------------------------------
    // Private helpers
    // ----------------------------------------------------------------

    /**
     * GET request ผ่าน cURL
     */
    private function curlGet($endpoint, $timeout = null)
    {
        $ch = curl_init($this->baseUrl . $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout ?: $this->timeout);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

        $response = curl_exec($ch);
        $error    = curl_error($ch);
        curl_close($ch);

        if ($error) {
            log_message('error', 'PythonApi GET ' . $endpoint . ': ' . $error);
            return false;
        }
        return $response;
    }

    /**
     * POST request ผ่าน cURL (JSON body)
     */
    private function curlPost($endpoint, $payload)
    {
        $ch = curl_init($this->baseUrl . $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($payload),
        ]);

        $response = curl_exec($ch);
        $error    = curl_error($ch);
        curl_close($ch);

        if ($error) {
            log_message('error', 'PythonApi POST ' . $endpoint . ': ' . $error);
            return false;
        }
        return $response;
    }
}
