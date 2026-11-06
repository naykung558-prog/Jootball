<?php

namespace Modules\Analytics\Models;

use CodeIgniter\Model;

class MatchAnalyticsModel extends Model
{
    protected $table      = 'analytics_matches';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'match_name', 'match_date', 'home_team_id', 'away_team_id',
        'home_team_name', 'away_team_name', 'home_score', 'away_score',
        'venue', 'description', 'status', 'created_by',
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * ดึงแมตช์พร้อมสถิติ (videos count, players count)
     */
    public function getMatchWithStats($matchId)
    {
        log_message('critical', 'DEBUG getMatchWithStats matchId=' . $matchId);

        $db  = \Config\Database::connect();
        $sql = "SELECT am.*,
                    ht.name  AS home_team_name_real,
                    at2.name AS away_team_name_real,
                    (SELECT COUNT(*) FROM analytics_videos WHERE match_id = am.id) AS video_count,
                    (SELECT COUNT(*) FROM analytics_videos WHERE match_id = am.id AND status = 'completed') AS completed_videos,
                    (SELECT COUNT(DISTINCT player_id) FROM analytics_player_stats WHERE match_id = am.id AND player_id IS NOT NULL) AS player_count
                FROM analytics_matches am
                LEFT JOIN team ht  ON ht.id  = am.home_team_id
                LEFT JOIN team at2 ON at2.id = am.away_team_id
                WHERE am.id = ?";

        $query = $db->query($sql, [(int) $matchId]);
        $result = $query ? $query->getRowArray() : null;

        log_message('critical', 'DEBUG query=' . ($query ? 'OK' : 'FALSE') .
            ' result=' . ($result ? 'HAS_DATA' : 'NULL') .
            ' err=' . json_encode($db->error()));
        return $result;
    }

    /**
     * ดึงสถิติ summary ของแมตช์
     */
    public function getMatchStats($matchId)
    {
        $db = \Config\Database::connect();
        return $db->table('analytics_player_stats')
                  ->select('SUM(total_distance) as total_distance,
                            AVG(avg_speed) as avg_speed,
                            MAX(max_speed) as max_speed,
                            SUM(sprint_count) as total_sprints,
                            COUNT(DISTINCT player_id) as player_count', false)
                  ->where('match_id', $matchId)
                  ->get()->getRowArray();
    }

    /**
     * DataTable server-side สำหรับรายการแมตช์
     */
    public function getMatchTable($post)
    {
        $db     = \Config\Database::connect();
        $start  = isset($post['start'])  ? (int)$post['start']  : 0;
        $length = isset($post['length']) ? (int)$post['length'] : 10;
        $search = isset($post['search']['value']) ? trim($post['search']['value']) : '';

        $base = $db->table('analytics_matches am')
                   ->select('am.id, am.match_name, am.match_date,
                             COALESCE(ht.name, am.home_team_name) as home,
                             COALESCE(at2.name, am.away_team_name) as away,
                             am.home_score, am.away_score, am.status,
                             (SELECT COUNT(*) FROM analytics_videos WHERE match_id = am.id) as videos', false)
                   ->join('team ht',  'ht.id = am.home_team_id',  'left')
                   ->join('team at2', 'at2.id = am.away_team_id', 'left');

        if ($search !== '') {
            $base->groupStart()
                 ->like('am.match_name', $search)
                 ->orLike('ht.name', $search)
                 ->orLike('at2.name', $search)
                 ->groupEnd();
        }

        $total    = $db->table('analytics_matches')->countAllResults();
        $filtered = clone $base;
        // count filtered (ง่ายๆ)
        $filteredCount = count($filtered->get()->getResultArray());

        $rows = $base->orderBy('am.match_date', 'DESC')
                     ->limit($length, $start)
                     ->get()->getResultArray();

        $data = [];
        foreach ($rows as $i => $row) {
            $statusBadge = $this->_statusBadge($row['status']);
            $actions     = $this->_actionButtons($row['id']);
            $data[] = [
                $start + $i + 1,
                esc($row['match_name']),
                date('d/m/Y', strtotime($row['match_date'])),
                esc($row['home']) . ' ' . $row['home_score'] . ' - ' . $row['away_score'] . ' ' . esc($row['away']),
                $row['videos'] . ' ไฟล์',
                $statusBadge,
                $actions,
            ];
        }

        return [
            'draw'            => isset($post['draw']) ? (int)$post['draw'] : 1,
            'recordsTotal'    => $total,
            'recordsFiltered' => $filteredCount,
            'data'            => $data,
        ];
    }

    private function _statusBadge($status)
    {
        $map = [
            'pending'    => ['warning', 'รอดำเนินการ'],
            'processing' => ['info',    'กำลังประมวลผล'],
            'completed'  => ['success', 'เสร็จสิ้น'],
            'error'      => ['danger',  'ผิดพลาด'],
        ];
        $s = isset($map[$status]) ? $map[$status] : ['secondary', $status];
        return '<span class="badge badge-' . $s[0] . '">' . $s[1] . '</span>';
    }

    private function _actionButtons($id)
    {
        return '
        <a href="/admin/analytics/matches/detail/' . $id . '" class="btn btn-sm btn-info" title="รายละเอียด"><i class="fas fa-eye"></i></a>
        <a href="/admin/analytics/matches/manage/' . $id . '" class="btn btn-sm btn-warning" title="แก้ไข"><i class="fas fa-edit"></i></a>
        <button class="btn btn-sm btn-danger btn-delete" data-id="' . $id . '" title="ลบ"><i class="fas fa-trash"></i></button>
        ';
    }
}
