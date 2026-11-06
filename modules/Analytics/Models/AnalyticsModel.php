<?php

namespace Modules\Analytics\Models;

use CodeIgniter\Model;

class AnalyticsModel extends Model
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

    /** จำนวนแมตช์ทั้งหมด */
    public function getTotalMatches()
    {
        return $this->countAll();
    }

    /** แมตช์ที่ประมวลผลเสร็จ */
    public function getCompletedMatches()
    {
        return $this->where('status', 'completed')->countAllResults();
    }

    /** แมตช์ที่กำลังประมวลผล */
    public function getProcessingMatches()
    {
        return $this->whereIn('status', ['processing', 'queued'])->countAllResults();
    }

    /** จำนวนผู้เล่นที่มีสถิติ (track แล้ว) */
    public function getTotalTrackedPlayers()
    {
        $db = \Config\Database::connect();
        return $db->table('analytics_player_stats')
                  ->where('player_id IS NOT NULL')
                  ->countAllResults();
    }

    /**
     * Top N ผู้เล่นตามระยะทางรวม
     */
    public function getTopPlayersByDistance($limit = 5)
    {
        $db = \Config\Database::connect();
        return $db->table('analytics_player_stats aps')
                  ->select('p.first_name as player_name, p.last_name as player_surname, p.image_path as image_url, SUM(aps.total_distance) as total_dist, MAX(aps.max_speed) as max_speed', false)
                  ->join('player p', 'p.id = aps.player_id', 'inner')
                  ->groupBy('aps.player_id')
                  ->orderBy('total_dist', 'DESC')
                  ->limit($limit)
                  ->get()->getResultArray();
    }

    /**
     * จำนวนแมตช์ต่อเดือน (12 เดือนล่าสุด)
     */
    public function getMatchesPerMonth()
    {
        $db = \Config\Database::connect();
        return $db->table('analytics_matches')
                  ->select("DATE_FORMAT(match_date, '%Y-%m') as month, COUNT(*) as total")
                  ->where("match_date >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)")
                  ->groupBy('month')
                  ->orderBy('month', 'ASC')
                  ->get()->getResultArray();
    }

    /**
     * แมตช์ล่าสุด
     */
    public function getRecentMatches($limit = 5)
    {
        return $this->select('analytics_matches.*,
                              ht.name as home_team, at2.name as away_team')
                    ->join('team ht', 'ht.id = analytics_matches.home_team_id', 'left')
                    ->join('team at2', 'at2.id = analytics_matches.away_team_id', 'left')
                    ->orderBy('match_date', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }
}
