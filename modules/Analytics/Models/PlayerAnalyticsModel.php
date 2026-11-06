<?php

namespace Modules\Analytics\Models;

use CodeIgniter\Model;

class PlayerAnalyticsModel extends Model
{
    protected $table      = 'analytics_player_stats';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    /**
     * ดึงสถิติของผู้เล่น 1 คนทุกแมตช์
     */
    public function getPlayerAllMatchStats($playerId)
    {
        $db = \Config\Database::connect();
        return $db->table('analytics_player_stats aps')
                  ->select('aps.*, am.match_name, am.match_date, am.home_score, am.away_score,
                            COALESCE(ht.name, am.home_team_name) as home,
                            COALESCE(at2.name, am.away_team_name) as away', false)
                  ->join('analytics_matches am', 'am.id = aps.match_id', 'left')
                  ->join('team ht',  'ht.id = am.home_team_id',  'left')
                  ->join('team at2', 'at2.id = am.away_team_id', 'left')
                  ->where('aps.player_id', $playerId)
                  ->orderBy('am.match_date', 'DESC')
                  ->get()->getResultArray();
    }

    /**
     * สถิติรวมทุกแมตช์ของผู้เล่น 1 คน
     */
    public function getPlayerTotalStats($playerId)
    {
        $db = \Config\Database::connect();
        return $db->table('analytics_player_stats')
                  ->select('SUM(total_distance) as total_distance,
                            AVG(avg_speed) as avg_speed,
                            MAX(max_speed) as max_speed,
                            SUM(sprint_count) as total_sprints,
                            COUNT(*) as match_count', false)
                  ->where('player_id', $playerId)
                  ->get()->getRowArray();
    }

    /**
     * Speed trend ของผู้เล่น (ต่อแมตช์)
     */
    public function getSpeedTrend($playerId, $limit = 10)
    {
        $db = \Config\Database::connect();
        return $db->table('analytics_player_stats aps')
                  ->select('am.match_date, am.match_name, aps.avg_speed, aps.max_speed', false)
                  ->join('analytics_matches am', 'am.id = aps.match_id', 'left')
                  ->where('aps.player_id', $playerId)
                  ->orderBy('am.match_date', 'DESC')
                  ->limit($limit)
                  ->get()->getResultArray();
    }

    /**
     * Distance trend ของผู้เล่น (ต่อแมตช์)
     */
    public function getDistanceTrend($playerId, $limit = 10)
    {
        $db = \Config\Database::connect();
        return $db->table('analytics_player_stats aps')
                  ->select('am.match_date, am.match_name, aps.total_distance, aps.sprint_count', false)
                  ->join('analytics_matches am', 'am.id = aps.match_id', 'left')
                  ->where('aps.player_id', $playerId)
                  ->orderBy('am.match_date', 'DESC')
                  ->limit($limit)
                  ->get()->getResultArray();
    }

    /**
     * ดึงข้อมูลผู้เล่น + สถิติรวม + 5 แมตช์ล่าสุด (สำหรับ modal)
     */
    public function getPlayerDetail($playerId)
    {
        $db = \Config\Database::connect();

        $player = $db->table('player p')
                     ->select('p.id, p.first_name as player_name, p.last_name as player_surname,
                               COALESCE(ape.shirt_number, p.shirt_number) as player_number, p.image_path as image_url,
                               t.name as team_name, t.id as team_id', false)
                     ->join('academy_player_enrollments ape', 'ape.player_id = p.id AND ape.is_primary = 1 AND ape.is_active = 1', 'left', false)
                     ->join('team t', 't.id = ape.team_id', 'left')
                     ->where('p.id', $playerId)
                     ->get()->getRowArray();

        if (!$player) {
            return null;
        }

        $stats = $db->table('analytics_player_stats')
                    ->select('SUM(total_distance) as total_distance,
                              AVG(avg_speed) as avg_speed,
                              MAX(max_speed) as max_speed,
                              SUM(sprint_count) as total_sprints,
                              COUNT(*) as match_count', false)
                    ->where('player_id', $playerId)
                    ->get()->getRowArray();

        $recentMatches = $db->table('analytics_player_stats aps')
                            ->select('am.match_name, am.match_date,
                                      aps.total_distance, aps.avg_speed, aps.max_speed, aps.sprint_count', false)
                            ->join('analytics_matches am', 'am.id = aps.match_id', 'left')
                            ->where('aps.player_id', $playerId)
                            ->orderBy('am.match_date', 'DESC')
                            ->limit(5)
                            ->get()->getResultArray();

        return [
            'player'         => $player,
            'stats'          => $stats,
            'recent_matches' => $recentMatches,
        ];
    }

    /**
     * DataTable server-side ผู้เล่น
     */
    public function getPlayerTable($post, $matchId = null)
    {
        $db     = \Config\Database::connect();
        $start  = isset($post['start'])  ? (int)$post['start']  : 0;
        $length = isset($post['length']) ? (int)$post['length'] : 10;
        $search = isset($post['search']['value']) ? trim($post['search']['value']) : '';

        // Build two independent builders to avoid exhausted state
        $countBase = $db->table('analytics_player_stats aps')
                        ->select('COUNT(DISTINCT aps.player_id) as cnt', false)
                        ->join('player p', 'p.id = aps.player_id', 'inner');

        $dataBase = $db->table('analytics_player_stats aps')
                       ->select('aps.player_id,
                                 p.first_name as player_name, p.last_name as player_surname, p.image_path as image_url,
                                 COUNT(DISTINCT aps.match_id) as match_count,
                                 SUM(aps.total_distance) as total_distance,
                                 AVG(aps.avg_speed) as avg_speed,
                                 MAX(aps.max_speed) as max_speed,
                                 SUM(aps.sprint_count) as total_sprints', false)
                       ->join('player p', 'p.id = aps.player_id', 'inner')
                       ->groupBy('aps.player_id');

        if ($matchId) {
            $countBase->where('aps.match_id', $matchId);
            $dataBase->where('aps.match_id', $matchId);
        }

        $teamId = isset($post['team_id']) && $post['team_id'] !== '' ? (int)$post['team_id'] : null;
        if ($teamId) {
            // membership = active enrollment ในทีมนี้ หรือ fallback legacy player.team_id (สำหรับนักเตะที่ยังไม่มี enrollment)
            $teamCond = "(EXISTS (SELECT 1 FROM academy_player_enrollments e WHERE e.player_id = p.id AND e.team_id = {$teamId} AND e.is_active = 1) OR p.team_id = {$teamId})";
            $countBase->where($teamCond, null, false);
            $dataBase->where($teamCond, null, false);
        }

        if ($search !== '') {
            $countBase->groupStart()
                      ->like('p.first_name', $search)
                      ->orLike('p.last_name', $search)
                      ->groupEnd();
            $dataBase->groupStart()
                     ->like('p.first_name', $search)
                     ->orLike('p.last_name', $search)
                     ->groupEnd();
        }

        $total = (int)($countBase->get()->getRowArray()['cnt'] ?? 0);

        $rows = $dataBase->orderBy('total_distance', 'DESC')
                         ->limit($length, $start)
                         ->get()->getResultArray();

        $data = [];
        foreach ($rows as $i => $row) {
            $img  = $row['image_url']
                    ? '<img src="' . base_url($row['image_url']) . '" class="rounded-circle" width="36" height="36">'
                    : '<div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center" style="width:36px;height:36px"><i class="fas fa-user text-white fa-sm"></i></div>';

            $dist = number_format((float)$row['total_distance'], 2) . ' กม.';
            $avg  = number_format((float)$row['avg_speed'], 1) . ' กม./ชม.';
            $max  = number_format((float)$row['max_speed'], 1) . ' กม./ชม.';

            $actions = '<button class="btn btn-sm btn-info btn-player-detail" data-id="' . $row['player_id'] . '">'
                     . '<i class="fas fa-chart-bar"></i></button>';

            $data[] = [
                $start + $i + 1,
                $img . ' <a href="' . base_url('admin/analytics/report/player/' . $row['player_id']) . '">'
                    . esc($row['player_name']) . ' ' . esc($row['player_surname']) . '</a>',
                $row['match_count'] . ' แมตช์',
                $dist,
                $avg,
                $max,
                $row['total_sprints'],
                $actions,
            ];
        }

        return [
            'draw'            => isset($post['draw']) ? (int)$post['draw'] : 1,
            'recordsTotal'    => $total,
            'recordsFiltered' => $total,
            'data'            => $data,
        ];
    }
}
