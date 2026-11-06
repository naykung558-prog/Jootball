<?php

namespace Modules\Api\Controllers\V1;

use Modules\Api\Controllers\BaseApiController;

class CompetitionController extends BaseApiController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    /**
     * GET /api/v1/competitions
     * Schema: ICountry[] - returns tournaments grouped (flat list since Jootball is Thailand-centric)
     */
    public function index()
    {
        $lang    = $this->request->getGet('lang') ?? 'th';
        $status  = $this->request->getGet('status') ?? 'active'; // active | past
        $nameCol = $lang === 'en' ? "COALESCE(NULLIF(t.name_en, ''), t.name)" : 't.name';

        $activeFilter = $status === 'past'
            ? 't.is_active = 0 AND t.tournament_end_date < CURDATE()'
            : 't.is_active = 1 AND t.tournament_start_date <= CURDATE() AND t.tournament_end_date >= CURDATE()';

        $tournaments = $this->db->query("
            SELECT t.tour_id, {$nameCol} AS name, t.logo, t.tournament_type,
                   t.start_date, t.end_date,
                   COALESCE(mc.totalGames, 0) AS totalGames,
                   COALESCE(mc.liveGames, 0) AS liveGames
            FROM tournament t
            LEFT JOIN (
                SELECT tour_id, COUNT(*) AS totalGames, SUM(match_status = 1) AS liveGames
                FROM tournament_match_result GROUP BY tour_id
            ) mc ON mc.tour_id = t.tour_id
            WHERE {$activeFilter} AND t.is_deleted = 0
            ORDER BY t.start_date DESC
        ")->getResultArray();

        $competitions = array_map(function ($t) {
            return [
                'id' => (int) $t['tour_id'],
                'name' => $t['name'],
                'logo' => $this->imageUrl($t['logo']),
                'currentSeasonNum' => (int) date('Y'),
                'currentStageNum' => 1,
                'hasStandings' => $t['tournament_type'] === 'league',
            ];
        }, $tournaments);

        // Return as single "country" (Thailand)
        $result = [
            [
                'id' => 1,
                'name' => $lang === 'en' ? 'Thailand' : 'ไทย',
                'flag' => $this->imageUrl('assets/img/flag-th.png'),
                'totalGames' => array_sum(array_column($tournaments, 'totalGames')),
                'liveGames' => array_sum(array_column($tournaments, 'liveGames')),
                'competitions' => $competitions,
            ],
        ];

        return $this->successResponse($result);
    }

    /**
     * GET /api/v1/competitions/:id/standings
     * Schema: IStanding[]
     */
    public function standings($id = null)
    {
        $lang = $this->request->getGet('lang') ?? 'th';
        $teamNameCol = $lang === 'en' ? "COALESCE(NULLIF(te.name_en, ''), te.name)" : 'te.name';

        $tournament = $this->db->table('tournament')
            ->where('tour_id', $id)->get()->getRowArray();

        if (!$tournament) {
            return $this->notFoundResponse('Competition not found');
        }

        // Get all teams in tournament
        $teams = $this->db->query("
            SELECT tt.team_id, {$teamNameCol} AS name, te.logo
            FROM tournament_team tt
            JOIN team te ON te.id = tt.team_id
            WHERE tt.tour_id = ? AND tt.register_status = 1
        ", [$id])->getResultArray();

        $teamMap = [];
        foreach ($teams as $t) {
            $teamMap[(int) $t['team_id']] = $t;
        }

        // Calculate standings from match results
        $matches = $this->db->query("
            SELECT team1_id, team2_id, team1_score, team2_score, winner_team_id, group_id
            FROM tournament_match_result
            WHERE tour_id = ? AND match_status = 3
        ", [$id])->getResultArray();

        $standings = [];
        foreach ($teamMap as $teamId => $team) {
            $standings[$teamId] = [
                'teamId' => $teamId,
                'name' => $team['name'],
                'logo' => $team['logo'],
                'gp' => 0, 'w' => 0, 'd' => 0, 'l' => 0,
                'gf' => 0, 'ga' => 0, 'gd' => 0, 'pts' => 0,
                'group' => null,
            ];
        }

        // Map team -> group from ALL matches (regardless of status) so the group
        // breakdown shows even before any match is finished. Stat counting below
        // still only uses finished matches (match_status = 3).
        $groupRows = $this->db->query("
            SELECT team1_id, team2_id, group_id
            FROM tournament_match_result
            WHERE tour_id = ? AND group_id IS NOT NULL AND group_id <> ''
        ", [$id])->getResultArray();
        foreach ($groupRows as $g) {
            $gt1 = (int) $g['team1_id'];
            $gt2 = (int) $g['team2_id'];
            if (isset($standings[$gt1])) $standings[$gt1]['group'] = $g['group_id'];
            if (isset($standings[$gt2])) $standings[$gt2]['group'] = $g['group_id'];
        }

        foreach ($matches as $m) {
            $t1 = (int) $m['team1_id'];
            $t2 = (int) $m['team2_id'];
            $s1 = (int) $m['team1_score'];
            $s2 = (int) $m['team2_score'];

            if (!isset($standings[$t1]) || !isset($standings[$t2])) continue;

            $standings[$t1]['gp']++;
            $standings[$t2]['gp']++;
            $standings[$t1]['gf'] += $s1;
            $standings[$t1]['ga'] += $s2;
            $standings[$t2]['gf'] += $s2;
            $standings[$t2]['ga'] += $s1;

            if ($s1 > $s2) {
                $standings[$t1]['w']++;
                $standings[$t1]['pts'] += 3;
                $standings[$t2]['l']++;
            } elseif ($s1 < $s2) {
                $standings[$t2]['w']++;
                $standings[$t2]['pts'] += 3;
                $standings[$t1]['l']++;
            } else {
                $standings[$t1]['d']++;
                $standings[$t2]['d']++;
                $standings[$t1]['pts'] += 1;
                $standings[$t2]['pts'] += 1;
            }
        }

        // Sort by points, then goal difference, then goals for
        usort($standings, function ($a, $b) {
            if ($a['pts'] !== $b['pts']) return $b['pts'] - $a['pts'];
            $a['gd'] = $a['gf'] - $a['ga'];
            $b['gd'] = $b['gf'] - $b['ga'];
            if ($a['gd'] !== $b['gd']) return $b['gd'] - $a['gd'];
            return $b['gf'] - $a['gf'];
        });

        // Group by group_id
        $groups = [];
        foreach ($standings as $s) {
            $groupKey = $s['group'] ?? 'all';
            if (!isset($groups[$groupKey])) {
                $groups[$groupKey] = [];
            }
            $groups[$groupKey][] = $s;
        }

        $result = [];
        foreach ($groups as $groupName => $rows) {
            $formattedRows = [];
            $pos = 1;
            foreach ($rows as $r) {
                $formattedRows[] = [
                    'position' => $pos++,
                    'logo' => $this->imageUrl($teamMap[$r['teamId']]['logo'] ?? null),
                    'competitor' => [
                        'id' => $r['teamId'],
                        'name' => $r['name'],
                        'symbolicName' => mb_substr($r['name'], 0, 3),
                    ],
                    'gamePlayed' => $r['gp'],
                    'gamesWon' => $r['w'],
                    'gamesEven' => $r['d'],
                    'gamesLost' => $r['l'],
                    'forr' => $r['gf'],
                    'against' => $r['ga'],
                    'ratio' => $r['gf'] - $r['ga'],
                    'points' => $r['pts'],
                    'destination' => null,
                ];
            }

            $result[] = [
                'competitionId' => (int) $id,
                'seasonNum' => (int) date('Y'),
                'stageNum' => 1,
                'displayName' => $lang === 'en' ? ($tournament['name_en'] ?? $tournament['name']) : $tournament['name'],
                'groupName' => $groupName !== 'all' ? $groupName : null,
                'isCurrentStage' => true,
                'currentGroupCategory' => 0,
                'rows' => $formattedRows,
            ];
        }

        return $this->successResponse($result);
    }

    /**
     * GET /api/v1/competitions/:id/top-stats
     * Schema: ITopStats
     */
    public function topStats($id = null)
    {
        $lang = $this->request->getGet('lang') ?? 'th';
        $teamNameCol = $lang === 'en' ? "COALESCE(NULLIF(t.name_en, ''), t.name)" : 't.name';

        // Top scorers
        $scorers = $this->db->query("
            SELECT e.player_1_id AS player_id, COUNT(*) AS goals,
                   p.first_name, p.last_name, p.image_path,
                   t.id AS team_id, {$teamNameCol} AS team_name, t.logo AS team_logo
            FROM tournament_match_event e
            JOIN player p ON p.id = e.player_1_id
            JOIN team t ON t.id = e.team_id
            WHERE e.tour_id = ? AND e.event_id IN (1)
            GROUP BY e.player_1_id, p.first_name, p.last_name, p.image_path, t.id, t.name, t.name_en, t.logo
            ORDER BY goals DESC
            LIMIT 10
        ", [$id])->getResultArray();

        $topScorers = [];
        $pos = 1;
        foreach ($scorers as $s) {
            $topScorers[] = [
                'athlete' => [
                    'id' => (int) $s['player_id'],
                    'name' => trim($s['first_name'] . ' ' . $s['last_name']),
                    'photo' => $this->imageUrl($s['image_path']),
                ],
                'competitor' => [
                    'id' => (int) $s['team_id'],
                    'name' => $s['team_name'],
                    'logo' => $this->imageUrl($s['team_logo']),
                ],
                'value' => (int) $s['goals'],
                'position' => $pos++,
            ];
        }

        // Top assists (event_id = 4 substitution doesn't map well, use yellow cards as example)
        $yellowCards = $this->db->query("
            SELECT e.player_1_id AS player_id, COUNT(*) AS cards,
                   p.first_name, p.last_name, p.image_path,
                   t.id AS team_id, {$teamNameCol} AS team_name, t.logo AS team_logo
            FROM tournament_match_event e
            JOIN player p ON p.id = e.player_1_id
            JOIN team t ON t.id = e.team_id
            WHERE e.tour_id = ? AND e.event_id = 2
            GROUP BY e.player_1_id, p.first_name, p.last_name, p.image_path, t.id, t.name, t.name_en, t.logo
            ORDER BY cards DESC
            LIMIT 10
        ", [$id])->getResultArray();

        $topCards = [];
        $pos = 1;
        foreach ($yellowCards as $c) {
            $topCards[] = [
                'athlete' => [
                    'id' => (int) $c['player_id'],
                    'name' => trim($c['first_name'] . ' ' . $c['last_name']),
                    'photo' => $this->imageUrl($c['image_path']),
                ],
                'competitor' => [
                    'id' => (int) $c['team_id'],
                    'name' => $c['team_name'],
                    'logo' => $this->imageUrl($c['team_logo']),
                ],
                'value' => (int) $c['cards'],
                'position' => $pos++,
            ];
        }

        // Team stats - most goals
        $teamGoals = $this->db->query("
            SELECT e.team_id, COUNT(*) AS goals,
                   {$teamNameCol} AS team_name, t.logo AS team_logo
            FROM tournament_match_event e
            JOIN team t ON t.id = e.team_id
            WHERE e.tour_id = ? AND e.event_id = 1
            GROUP BY e.team_id, t.name, t.name_en, t.logo
            ORDER BY goals DESC
            LIMIT 10
        ", [$id])->getResultArray();

        $teamStats = [];
        $pos = 1;
        foreach ($teamGoals as $tg) {
            $teamStats[] = [
                'competitor' => [
                    'id' => (int) $tg['team_id'],
                    'name' => $tg['team_name'],
                    'logo' => $this->imageUrl($tg['team_logo']),
                ],
                'value' => (int) $tg['goals'],
                'position' => $pos++,
            ];
        }

        return $this->successResponse([
            'athletesStats' => [
                ['name' => 'Top Scorers', 'rows' => $topScorers],
                ['name' => 'Most Yellow Cards', 'rows' => $topCards],
            ],
            'competitorsStats' => [
                ['name' => 'Most Goals', 'rows' => $teamStats],
            ],
        ]);
    }

    /**
     * GET /api/v1/competitions/:id/highlights
     * Schema: IHighlight[]
     */
    public function highlights($id = null)
    {
        $lang = $this->request->getGet('lang') ?? 'th';
        $nameCol = $lang === 'en' ? "COALESCE(NULLIF(t.name_en, ''), t.name)" : 't.name';
        $homeNameCol = $lang === 'en' ? "COALESCE(NULLIF(h.name_en, ''), h.name)" : 'h.name';
        $awayNameCol = $lang === 'en' ? "COALESCE(NULLIF(a.name_en, ''), a.name)" : 'a.name';

        // Return recent finished matches as "highlights"
        $matches = $this->db->query("
            SELECT m.match_id, m.team1_score, m.team2_score, m.stadium,
                   {$homeNameCol} AS home_name, h.logo AS home_logo,
                   {$awayNameCol} AS away_name, a.logo AS away_logo,
                   {$nameCol} AS tour_name
            FROM tournament_match_result m
            JOIN team h ON h.id = m.team1_id
            JOIN team a ON a.id = m.team2_id
            JOIN tournament t ON t.tour_id = m.tour_id
            WHERE m.tour_id = ? AND m.match_status = 3
            ORDER BY m.date DESC
            LIMIT 10
        ", [$id])->getResultArray();

        $result = array_map(function ($m) {
            return [
                'game' => [
                    'id' => (int) $m['match_id'],
                    'homeCompetitor' => [
                        'name' => $m['home_name'],
                        'logo' => $this->imageUrl($m['home_logo']),
                        'score' => (int) $m['team1_score'],
                    ],
                    'awayCompetitor' => [
                        'name' => $m['away_name'],
                        'logo' => $this->imageUrl($m['away_logo']),
                        'score' => (int) $m['team2_score'],
                    ],
                ],
                'competition' => ['name' => $m['tour_name']],
                'venue' => $m['stadium'],
                'videoUrl' => null,
            ];
        }, $matches);

        return $this->successResponse($result);
    }

    /**
     * GET /api/v1/competitions/:id/winners
     * Schema: IWinner[]
     */
    public function winners($id = null)
    {
        // For knockout tournaments, winner is the last match's winner
        // For league, winner is top of standings
        // Return empty if no historical data
        return $this->successResponse([]);
    }

    /**
     * GET /api/v1/competitions/:id/matches?type=next|last
     * Schema: IMatchesLeague[] = { round, games[] }
     */
    public function matches($id = null)
    {
        $type = $this->request->getGet('type') ?? 'next';
        $lang = $this->request->getGet('lang') ?? 'th';
        $homeNameCol = $lang === 'en' ? "COALESCE(NULLIF(h.name_en, ''), h.name)" : 'h.name';
        $awayNameCol = $lang === 'en' ? "COALESCE(NULLIF(a.name_en, ''), a.name)" : 'a.name';

        if ($type === 'next') {
            $condition = "m.match_status = 0 ORDER BY m.date ASC, m.time ASC";
        } else {
            $condition = "m.match_status = 3 ORDER BY m.date DESC, m.time DESC";
        }

        $matches = $this->db->query("
            SELECT m.match_id, m.tour_id, m.team1_id, m.team2_id,
                   m.team1_score, m.team2_score, m.winner_team_id,
                   m.match_status, m.match_round, m.group_id, m.date, m.time,
                   {$homeNameCol} AS home_name, h.logo AS home_logo,
                   {$awayNameCol} AS away_name, a.logo AS away_logo
            FROM tournament_match_result m
            JOIN team h ON h.id = m.team1_id
            JOIN team a ON a.id = m.team2_id
            WHERE m.tour_id = ? AND {$condition}
            LIMIT 50
        ", [$id])->getResultArray();

        // Group by round
        $grouped = [];
        foreach ($matches as $m) {
            $round = $m['match_round'] ?? 'Round';
            if (!isset($grouped[$round])) {
                $grouped[$round] = [];
            }
            $grouped[$round][] = $this->formatGameSimple($m);
        }

        $result = [];
        foreach ($grouped as $round => $games) {
            $result[] = [
                'round' => $round,
                'games' => $games,
            ];
        }

        return $this->successResponse($result);
    }

    private function formatGameSimple(array $m): array
    {
        $status = (int) ($m['match_status'] ?? 0);
        $timestamp = $m['date'] ? strtotime($m['date'] . ' ' . ($m['time'] ?? '00:00')) : null;
        $startTime = $timestamp ? date('c', $timestamp) : null;
        $startTimeDisplay = $timestamp ? date('d M Y, H:i', $timestamp) : null;

        return [
            'id' => (int) $m['match_id'],
            'statusGroup' => ([0 => 1, 1 => 2, 3 => 3])[$status] ?? 1,
            'statusText' => ([0 => 'Not Started', 1 => 'Live', 3 => 'Ended'])[$status] ?? 'Not Started',
            'startTime' => $startTime,
            'startTimeDisplay' => $startTimeDisplay,
            'date' => $m['date'] ?? null,
            'time' => isset($m['time']) && $m['time'] ? substr($m['time'], 0, 5) : null,
            'groupId' => $m['group_id'] ?? null,
            'groupName' => $m['group_id'] ?? null,
            'shortStatusText' => ([0 => 'NS', 1 => 'LIVE', 2 => 'FT'])[$status] ?? 'NS',
            'gameTimeDisplay' => null,
            'gameTime' => null,
            'hasLineups' => false,
            'hasStatistics' => false,
            'hasHighlights' => false,
            'aggregatedScore' => null,
            'homeCompetitor' => [
                'id' => (int) $m['team1_id'],
                'name' => $m['home_name'],
                'logo' => $this->imageUrl($m['home_logo']),
                'score' => $m['team1_score'] !== null ? (int) $m['team1_score'] : null,
                'isWinner' => isset($m['winner_team_id']) && (int) $m['winner_team_id'] === (int) $m['team1_id'],
            ],
            'awayCompetitor' => [
                'id' => (int) $m['team2_id'],
                'name' => $m['away_name'],
                'logo' => $this->imageUrl($m['away_logo']),
                'score' => $m['team2_score'] !== null ? (int) $m['team2_score'] : null,
                'isWinner' => isset($m['winner_team_id']) && (int) $m['winner_team_id'] === (int) $m['team2_id'],
            ],
        ];
    }
}
