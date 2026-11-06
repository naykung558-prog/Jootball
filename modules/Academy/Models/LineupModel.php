<?php

namespace Modules\Academy\Models;

use CodeIgniter\Model;

/**
 * LineupModel
 *
 * Handles lineup and player document data for tournament matches.
 */
class LineupModel extends Model
{
    protected $table      = 'tournament_match_line_up';
    protected $primaryKey = 'match_lineup_id';
    protected $allowedFields = [
        'tour_id',
        'team_id',
        'match_id',
        'player_id',
        'player_order',
        'round'
    ];
    protected $useTimestamps = false;

    /**
     * Get academy data
     */
    public function getAcademy($id)
    {
        return $this->db->table('academy')
            ->select('academy.*,
                      tbl_provinces.province_name,
                      tbl_districts.district_name as amphure_name,
                      tbl_subdistricts.subdistrict_name as tambon_name')
            ->join('tbl_provinces', 'tbl_provinces.province_id = academy.province_id', 'left')
            ->join('tbl_districts', 'tbl_districts.district_id = academy.amphure_id', 'left')
            ->join('tbl_subdistricts', 'tbl_subdistricts.subdistrict_id = academy.tambon_id', 'left')
            ->where('academy.id', $id)
            ->get()
            ->getRowArray();
    }

    /**
     * Get academies by coach user ID
     */
    public function getAcademyByCoach($coach_user_id)
    {
        return $this->db->table('academy_members')
            ->select('academy.*, academy_members.role as member_role')
            ->join('academy', 'academy.id = academy_members.academy_id')
            ->where('academy_members.coach_user_id', $coach_user_id)
            ->where('academy_members.status', 'approved')
            ->where('academy.is_active', 1)
            ->get()
            ->getResultArray();
    }

    /**
     * Get academies by user ID
     */
    public function getAcademyByUser($user_id)
    {
        // Super academy operator → return all active academies with virtual owner role
        helper('academy');
        if (isSuperAcademyUser($user_id)) {
            return $this->db->table('academy')
                ->select("academy.*, 'owner' as member_role")
                ->where('academy.is_active', 1)
                ->orderBy('academy.name', 'ASC')
                ->get()
                ->getResultArray();
        }

        return $this->db->table('academy_members')
            ->select('academy.*, academy_members.role as member_role')
            ->join('academy', 'academy.id = academy_members.academy_id')
            ->where('academy_members.user_id', $user_id)
            ->where('academy_members.status', 'approved')
            ->where('academy.is_active', 1)
            ->get()
            ->getResultArray();
    }

    /**
     * Get member role in academy
     */
    public function getMemberRole($academy_id, $user_id = null, $coach_user_id = null)
    {
        // Super academy operator → virtual owner of any academy (same rule as AcademyModel)
        if ($user_id) {
            helper('academy');
            if (isSuperAcademyUser($user_id)) {
                return 'owner';
            }
        }

        $builder = $this->db->table('academy_members')
            ->select('role')
            ->where('academy_id', $academy_id)
            ->where('status', 'approved');

        if ($user_id && $coach_user_id) {
            $builder->groupStart()
                ->where('user_id', $user_id)
                ->orWhere('coach_user_id', $coach_user_id)
            ->groupEnd();
        } elseif ($user_id) {
            $builder->where('user_id', $user_id);
        } elseif ($coach_user_id) {
            $builder->where('coach_user_id', $coach_user_id);
        } else {
            return null;
        }

        $result = $builder->get()->getRowArray();
        return $result ? $result['role'] : null;
    }

    /**
     * Get teams for academy
     * Query from academy_teams table
     */
    public function getAcademyTeams($academy_id)
    {
        return $this->db->table('academy_teams')
            ->select('team.*, academy_teams.sort_order,
                      tbl_zone.description as zone_name,
                      (SELECT COUNT(*) FROM academy_player_enrollments ape WHERE ape.team_id = team.id AND ape.academy_id = academy_teams.academy_id AND ape.is_active = 1) as player_count,
                      (SELECT GROUP_CONCAT(CONCAT(c.first_name, \' \', c.last_name) ORDER BY c.first_name SEPARATOR \', \')
                       FROM coach c
                       WHERE c.is_active = 1
                         AND (
                             c.team_id = team.id
                             OR EXISTS (
                                 SELECT 1 FROM coach_teams ct
                                 WHERE ct.coach_id = c.id AND ct.team_id = team.id AND ct.is_active = 1
                             )
                         )) as coach_names', false)
            ->join('team', 'team.id = academy_teams.team_id')
            ->join('tbl_zone', 'tbl_zone.id = team.zone_id', 'left')
            ->where('academy_teams.academy_id', $academy_id)
            ->where('team.is_active', '1')
            ->orderBy('academy_teams.sort_order', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Get active tournaments
     * Filter by tournament_start_date, tournament_end_date and is_deleted
     * Only show tournaments where selected team has matches
     */
    public function getActiveTournaments($team_id = null)
    {

        $today = date('Y-m-d');

        $query = $this->db->table('tournament t')
            ->select('t.tour_id, t.name, t.name_en, t.logo,
                      t.tournament_start_date, t.tournament_end_date,
                      t.start_date, t.end_date')
            ->where('t.tournament_start_date <=', $today)
            ->where('t.tournament_end_date >=', $today)
            ->where('t.is_deleted !=', 1)
            ->where('t.is_active', 1);

        // If team_id is provided, filter only tournaments with matches for this team
        if ($team_id) {
            $query->join('tournament_match_result tmr', 'tmr.tour_id = t.tour_id')
                  ->groupStart()
                      ->where('tmr.team1_id', (int)$team_id)
                      ->orWhere('tmr.team2_id', (int)$team_id)
                  ->groupEnd()
                  ->groupBy('t.tour_id'); // Prevent duplicates when team has multiple matches
        }

        return $query->orderBy('t.tournament_start_date', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Get matches for teams
     * Query from tournament_match_result table
     */
    public function getTeamMatches($teams)
    {
        if (empty($teams)) {
            return [];
        }

        // Get all team IDs
        $teamIds = array_column($teams, 'id');

        // Query matches where team is either team1 or team2
        $matches = $this->db->table('tournament_match_result tmr')
            ->select('tmr.match_id, tmr.tour_id, tmr.match_round, tmr.team1_id, tmr.team2_id,
                      tmr.time as match_time, tmr.date as match_date, tmr.stadium as venue, tmr.match_status,
                      t.name as tournament_name,
                      team1.name as team1_name, team1.logo as team1_logo,
                      team2.name as team2_name, team2.logo as team2_logo')
            ->join('tournament t', 't.tour_id = tmr.tour_id')
            ->join('team team1', 'team1.id = tmr.team1_id', 'left')
            ->join('team team2', 'team2.id = tmr.team2_id', 'left')
            ->whereIn('tmr.team1_id', $teamIds)
            ->orWhereIn('tmr.team2_id', $teamIds)
            ->where('t.is_deleted !=', 1)
            ->where('t.is_active', 1)
            ->orderBy('tmr.date', 'ASC')
            ->orderBy('tmr.time', 'ASC')
            ->get()
            ->getResultArray();

        // Format matches for JavaScript
        $formattedMatches = [];
        foreach ($matches as $match) {
            $formattedMatches[] = [
                'match_id' => $match['match_id'],
                'tour_id' => $match['tour_id'],
                'tournament_name' => $match['tournament_name'],
                'round' => $match['match_round'] ?? '-',
                'match_date' => $match['match_date'],
                'match_time' => $match['match_time'] ? date('H:i', strtotime($match['match_time'])) : '-',
                'team1_id' => $match['team1_id'],
                'team1_name' => $match['team1_name'],
                'team1_logo' => $match['team1_logo'],
                'team2_id' => $match['team2_id'],
                'team2_name' => $match['team2_name'],
                'team2_logo' => $match['team2_logo'],
                'venue' => $match['venue'] ?? '-',
                'status' => $match['match_status'] == 3 ? 'finished' : 'scheduled'
            ];
        }

        return $formattedMatches;
    }

    /**
     * Get line-up data for a specific match
     */
    public function getLineUpData($tournamentId, $teamId, $matchId)
    {
        $lineUps = $this->db->table($this->table)
            ->where('tour_id', (int)$tournamentId)
            ->where('team_id', (int)$teamId)
            ->where('match_id', (int)$matchId)
            ->get()
            ->getResultArray();

        $result = [];
        foreach ($lineUps as $lineUp) {
            $playerId = $lineUp['player_id'];
            $round = $lineUp['round'];
            $status = $lineUp['player_order'];

            if (!isset($result[$playerId])) {
                $result[$playerId] = [];
            }
            if (!isset($result[$playerId]['match_' . $round])) {
                $result[$playerId]['match_' . $round] = [];
            }
            $result[$playerId]['match_' . $round][$status] = 1;
        }

        return $result;
    }

    /**
     * Save line-up data for a match
     */
    public function saveLineUpData($tournamentId, $teamId, $matchId, $players)
    {
        // Check if table exists
        if (!$this->db->tableExists($this->table)) {
            throw new \Exception("Table {$this->table} does not exist");
        }

        // Delete existing data
        $this->db->table($this->table)
            ->where('tour_id', (int)$tournamentId)
            ->where('team_id', (int)$teamId)
            ->where('match_id', (int)$matchId)
            ->delete();

        // Prepare new data
        $insertData = [];
        foreach ($players as $playerId => $matches) {
            foreach ($matches as $matchKey => $statuses) {
                $round = (int)str_replace('match_', '', $matchKey);

                foreach ($statuses as $status => $value) {
                    if ($value == 1) {
                        $insertData[] = [
                            'tour_id' => (int)$tournamentId,
                            'team_id' => (int)$teamId,
                            'match_id' => (int)$matchId,
                            'player_id' => (int)$playerId,
                            'player_order' => $status,
                            'round' => $round
                        ];
                    }
                }
            }
        }

        if (!empty($insertData)) {
            $result = $this->db->table($this->table)->insertBatch($insertData);
            if (!$result) {
                throw new \Exception("Failed to insert data: " . json_encode($this->db->error()));
            }
            return $result;
        }

        return true;
    }

    /**
     * Get tournament data
     */
    public function getTournament($tournamentId)
    {
        return $this->db->table('tournament')
            ->where('tour_id', (int)$tournamentId)
            ->where('is_deleted', 0)
            ->get()
            ->getRowArray();
    }

    /**
     * Get team data
     */
    public function getTeam($teamId)
    {
        return $this->db->table('team')
            ->select("team.*,
                      tbl_zone.description as zone_name,
                      tbl_districts.district_name as amphure_name,
                      tbl_subdistricts.subdistrict_name as tambon_name")
            ->join('tbl_zone', 'tbl_zone.id = team.zone_id', 'left')
            ->join('tbl_districts', 'tbl_districts.district_id = team.amphure_id', 'left')
            ->join('tbl_subdistricts', 'tbl_subdistricts.subdistrict_id = team.tambon_id', 'left')
            ->where('team.id', (int)$teamId)
            ->get()
            ->getRowArray();
    }

    /**
     * Check if tournament-team relationship exists
     */
    public function getTournamentTeam($tournamentId, $teamId)
    {
        return $this->db->table('tournament_team')
            ->where('tour_id', (int)$tournamentId)
            ->where('team_id', (int)$teamId)
            ->get()
            ->getRowArray();
    }

    /**
     * Get players by team_id and tournament_id (from tournament_submitted_players)
     * Same pattern as Team.php export_player_document
     */
    public function getPlayersByTeam($team_id, $tournament_id = null)
    {
        $playerModel = new \Modules\Player\Models\PlayerModel();

        if ($tournament_id) {
            // Get tour_team_id first
            $tourTeam = $this->db->table('tournament_team')
                ->where('tour_id', $tournament_id)
                ->where('team_id', $team_id)
                ->get()
                ->getRowArray();

            if ($tourTeam) {
                // Get submitted player IDs from tournament_submitted_players
                $submittedPlayers = $this->db->table('tournament_submitted_players')
                    ->where('tour_team_id', $tourTeam['tour_team_id'])
                    ->get()
                    ->getResultArray();

                $selectedPlayerIds = array_column($submittedPlayers, 'player_id');

                if (!empty($selectedPlayerIds)) {
                    // Get all players from team (same as Team.php)
                    $allPlayers = $playerModel->get_PlayerList($team_id, [], '');

                    // Filter to get only submitted players (same as Team.php array_filter)
                    $players = array_filter($allPlayers, function ($player) use ($selectedPlayerIds) {
                        return in_array($player['id'], $selectedPlayerIds);
                    });

                    return $players;
                } else {
                    // No submitted players - get all players with position filter
                    return $playerModel->get_PlayerList($team_id, [5, 6, 7], '');
                }
            } else {
                // No tour_team found - get all players with position filter
                return $playerModel->get_PlayerList($team_id, [5, 6, 7], '');
            }
        }

        // No tournament_id - get all players with position filter
        return $playerModel->get_PlayerList($team_id, [5, 6, 7], '');
    }
}
