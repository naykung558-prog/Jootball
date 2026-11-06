<?php

namespace Modules\Analytics\Models;

use CodeIgniter\Model;

class AnalysisModel extends Model
{
    protected $table      = 'analytics_tracking';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    /**
     * ดึงรายชื่อผู้เล่นในแมตช์ (ที่มี tracking data)
     */
    public function getMatchPlayers($matchId)
    {
        $db = \Config\Database::connect();
        return $db->table('analytics_player_stats aps')
                  ->select('aps.player_id, aps.jersey_num, aps.team_side,
                            p.first_name as player_name, p.last_name as player_surname, p.image_path as image_url,
                            aps.total_distance, aps.avg_speed, aps.max_speed', false)
                  ->join('player p', 'p.id = aps.player_id', 'left')
                  ->where('aps.match_id', $matchId)
                  ->orderBy('aps.team_side', 'ASC')
                  ->orderBy('aps.jersey_num', 'ASC')
                  ->get()->getResultArray();
    }

    /**
     * ดึง heatmap data (positions) ของผู้เล่น 1 คน
     * คืน array ของ [x, y, weight] สำหรับ heatmap
     */
    public function getHeatmapData($matchId, $playerId = null, $jerseyNum = null, $teamSide = null)
    {
        $db = \Config\Database::connect();
        $q  = $db->table('analytics_tracking')
                 ->select('x, y, COUNT(*) as weight', false)
                 ->where('match_id', $matchId)
                 ->groupBy('ROUND(x, 1), ROUND(y, 1)');

        if ($playerId) {
            $q->where('player_id', $playerId);
        } elseif ($jerseyNum !== null) {
            $q->where('jersey_num', $jerseyNum);
            if ($teamSide !== null) {
                $q->where('team_side', $teamSide);
            }
        }

        $rows = $q->get()->getResultArray();

        // Normalize weight
        $maxW = 1;
        foreach ($rows as $r) {
            if ($r['weight'] > $maxW) {
                $maxW = $r['weight'];
            }
        }

        $result = [];
        foreach ($rows as $r) {
            $result[] = [
                'x' => (float)$r['x'],
                'y' => (float)$r['y'],
                'v' => round($r['weight'] / $maxW, 3),
            ];
        }
        return $result;
    }

    /**
     * ดึงสถิติผู้เล่นทุกคนในแมตช์ (สำหรับ speed analysis)
     */
    public function getAllPlayerStats($matchId)
    {
        $db = \Config\Database::connect();
        return $db->table('analytics_player_stats aps')
                  ->select('aps.*, p.first_name as player_name, p.last_name as player_surname,
                            aps.zone_walk, aps.zone_jog, aps.zone_run, aps.zone_fast, aps.zone_sprint', false)
                  ->join('player p', 'p.id = aps.player_id', 'left')
                  ->where('aps.match_id', $matchId)
                  ->orderBy('aps.max_speed', 'DESC')
                  ->get()->getResultArray();
    }

    /**
     * Speed zone distribution รวมทั้งทีม
     */
    public function getSpeedZoneData($matchId, $teamSide = null)
    {
        $db = \Config\Database::connect();
        $q  = $db->table('analytics_player_stats')
                 ->select('AVG(zone_walk) as walk, AVG(zone_jog) as jog,
                           AVG(zone_run) as run, AVG(zone_fast) as fast,
                           AVG(zone_sprint) as sprint', false)
                 ->where('match_id', $matchId);

        if ($teamSide !== null) {
            $q->where('team_side', $teamSide);
        }

        return $q->get()->getRowArray();
    }

    /**
     * ดึง tracking positions (สำหรับ AJAX animation หรือ heatmap)
     */
    public function getTrackingPositions($matchId, $playerId = null, $jerseyNum = null, $teamSide = null, $half = null)
    {
        $db = \Config\Database::connect();
        $q  = $db->table('analytics_tracking t')
                 ->select('t.minute, t.x, t.y, t.speed, t.player_id, t.jersey_num, t.team_side,
                           v.half', false)
                 ->join('analytics_videos v', 'v.id = t.video_id', 'left')
                 ->where('t.match_id', $matchId)
                 ->orderBy('t.minute', 'ASC');

        if ($playerId) {
            $q->where('t.player_id', $playerId);
        } elseif ($jerseyNum !== null) {
            $q->where('t.jersey_num', $jerseyNum);
        }

        if ($teamSide !== null) {
            $q->where('t.team_side', $teamSide);
        }

        if ($half) {
            $q->where('v.half', $half);
        }

        return $q->limit(5000)->get()->getResultArray();
    }
}
