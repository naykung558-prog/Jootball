<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class MapsQuota extends Controller
{
    protected $request;
    protected $response;

    protected $helpers = ['permission', 'academy'];

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
    }

    public function check()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
        }

        try {
            $limit  = (int) env('GOOGLE_MAP_DAILY_LIMIT', 500);
            $warnAt = (int) floor($limit * 0.8);
            $today  = date('Y-m-d');
            $quotaDir = WRITEPATH . 'maps_quota';
            $file   = $quotaDir . '/' . $today . '.txt';

            $this->ensureDirs($quotaDir);
            $this->cleanupOldFiles($quotaDir, $today);

            // Read current count with file locking
            $count = 0;
            $fp = fopen($file, 'c+');
            if ($fp && flock($fp, LOCK_EX)) {
                $count = (int) fread($fp, 20);

                if ($count >= $limit) {
                    flock($fp, LOCK_UN);
                    fclose($fp);

                    $this->writeLog('BLOCKED', $count, $limit);

                    return $this->response->setJSON([
                        'allowed' => false,
                        'count'   => $count,
                        'limit'   => $limit,
                        'warning' => false,
                    ]);
                }

                $count++;
                ftruncate($fp, 0);
                rewind($fp);
                fwrite($fp, (string) $count);
                flock($fp, LOCK_UN);
            }
            if ($fp) fclose($fp);

            $warning = $count >= $warnAt;

            $this->writeLog($warning ? 'WARNING' : 'LOADED', $count, $limit);

            return $this->response->setJSON([
                'allowed' => true,
                'count'   => $count,
                'limit'   => $limit,
                'warning' => $warning,
            ]);

        } catch (\Throwable $e) {
            log_message('error', 'MapsQuota::check error: ' . $e->getMessage());
            // ถ้า error ให้ allow เพื่อไม่ block user
            return $this->response->setJSON([
                'allowed' => true,
                'count'   => 0,
                'limit'   => 500,
                'warning' => false,
            ]);
        }
    }

    private function writeLog(string $status, int $count, int $limit): void
    {
        try {
            $logDir  = WRITEPATH . 'maps_quota/logs';
            $logFile = $logDir . '/maps-' . date('Y-m-d') . '.log';
            $page    = $this->request->getPost('page') ?? '-';
            $ip      = $this->request->getIPAddress() ?? '-';
            $agent   = '-';

            $ua = $this->request->getUserAgent();
            if ($ua) {
                $agent = mb_substr($ua->getAgentString() ?? '-', 0, 100);
            }

            $userId   = session()->get('user_id') ?? session()->get('coach_user_id') ?? session()->get('member_id') ?? '-';
            $userType = session()->get('user_type') ?? (session()->get('member_id') ? 'admin' : 'user');

            $line = sprintf(
                "[%s] %s | %d/%d | user:%s(%s) | ip:%s | page:%s | ua:%s\n",
                date('Y-m-d H:i:s'),
                str_pad($status, 7),
                $count,
                $limit,
                $userId,
                $userType,
                $ip,
                $page,
                $agent
            );

            file_put_contents($logFile, $line, FILE_APPEND | LOCK_EX);
        } catch (\Throwable $e) {
            log_message('error', 'MapsQuota::writeLog error: ' . $e->getMessage());
        }
    }

    /**
     * หน้า Admin ดู log
     */
    public function log($date = null)
    {
        $date     = $date ?? date('Y-m-d');
        $limit    = (int) env('GOOGLE_MAP_DAILY_LIMIT', 500);
        $quotaDir = WRITEPATH . 'maps_quota';
        $logDir   = WRITEPATH . 'maps_quota/logs';

        $countFile  = $quotaDir . '/' . $date . '.txt';
        $todayCount = file_exists($countFile) ? (int) file_get_contents($countFile) : 0;

        $logFile = $logDir . '/maps-' . $date . '.log';
        $logs    = [];
        if (file_exists($logFile)) {
            $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach (array_reverse($lines) as $line) {
                if (preg_match('/^\[(.+?)\]\s+(\w+)\s+\|\s+(\d+)\/(\d+)\s+\|\s+user:(.+?)\((.+?)\)\s+\|\s+ip:(.+?)\s+\|\s+page:(.+?)\s+\|\s+ua:(.*)$/', $line, $m)) {
                    $logs[] = [
                        'time'      => $m[1],
                        'status'    => trim($m[2]),
                        'count'     => $m[3],
                        'limit'     => $m[4],
                        'user_id'   => $m[5],
                        'user_type' => $m[6],
                        'ip'        => $m[7],
                        'page'      => $m[8],
                        'ua'        => $m[9],
                    ];
                }
            }
        }

        $availableDates = [];
        foreach (glob($logDir . '/maps-*.log') as $f) {
            preg_match('/maps-(\d{4}-\d{2}-\d{2})\.log$/', $f, $m);
            if (!empty($m[1])) {
                $availableDates[] = $m[1];
            }
        }
        rsort($availableDates);

        $stats = [
            'total'   => count($logs),
            'loaded'  => count(array_filter($logs, fn($l) => $l['status'] === 'LOADED')),
            'warning' => count(array_filter($logs, fn($l) => $l['status'] === 'WARNING')),
            'blocked' => count(array_filter($logs, fn($l) => $l['status'] === 'BLOCKED')),
        ];

        $pageStats = [];
        foreach ($logs as $l) {
            $p = $l['page'];
            $pageStats[$p] = ($pageStats[$p] ?? 0) + 1;
        }
        arsort($pageStats);

        $ipStats = [];
        foreach ($logs as $l) {
            $ip = $l['ip'];
            $ipStats[$ip] = ($ipStats[$ip] ?? 0) + 1;
        }
        arsort($ipStats);

        return view('admin/maps_quota_log', [
            'date'           => $date,
            'todayCount'     => $todayCount,
            'limit'          => $limit,
            'logs'           => $logs,
            'stats'          => $stats,
            'pageStats'      => $pageStats,
            'ipStats'        => $ipStats,
            'availableDates' => $availableDates,
        ]);
    }

    private function ensureDirs(string $quotaDir): void
    {
        $logDir = $quotaDir . '/logs';
        if (!is_dir($quotaDir)) {
            mkdir($quotaDir, 0777, true);
        }
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }
    }

    private function cleanupOldFiles(string $quotaDir, string $today): void
    {
        foreach (glob($quotaDir . '/*.txt') as $old) {
            if (basename($old, '.txt') !== $today) {
                @unlink($old);
            }
        }

        $logDir = $quotaDir . '/logs';
        $cutoff = date('Y-m-d', strtotime('-30 days'));
        foreach (glob($logDir . '/maps-*.log') as $logFile) {
            preg_match('/maps-(\d{4}-\d{2}-\d{2})\.log$/', $logFile, $m);
            if (!empty($m[1]) && $m[1] < $cutoff) {
                @unlink($logFile);
            }
        }
    }
}
