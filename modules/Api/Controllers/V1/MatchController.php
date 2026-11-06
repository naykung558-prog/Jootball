<?php

namespace Modules\Api\Controllers\V1;

use Modules\Api\Controllers\BaseApiController;

class MatchController extends BaseApiController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    /**
     * GET /api/v1/matches?lang=&time=
     * Returns matches grouped by competition (tournament)
     * Schema: IMatch[] = { competition, games[] }
     */
    public function index()
    {
        $date = $this->request->getGet('date') ?? $this->request->getGet('time');
        $lang = $this->request->getGet('lang') ?? 'th';

        $nameCol = $lang === 'en' ? "COALESCE(NULLIF(t.name_en, ''), t.name)" : 't.name';
        $homeNameCol = $lang === 'en' ? "COALESCE(NULLIF(h.name_en, ''), h.name)" : 'h.name';
        $awayNameCol = $lang === 'en' ? "COALESCE(NULLIF(a.name_en, ''), a.name)" : 'a.name';

        $dateFilter = '';
        $params = [];
        if (!empty($date)) {
            $dateFilter = 'AND m.date = ?';
            $params[] = $date;
        }

        $matches = $this->db->query("
            SELECT
                m.match_id, m.tour_id, m.match_round, m.group_id,
                m.team1_id, m.team2_id,
                m.team1_score, m.team2_score,
                m.team1_penalty, m.team2_penalty,
                m.winner_team_id, m.is_penalty_used,
                m.match_status, m.date, m.time,
                m.stadium, m.referee,
                {$nameCol} AS tour_name,
                t.logo AS tour_logo, t.tournament_type,
                {$homeNameCol} AS home_name, h.logo AS home_logo,
                {$awayNameCol} AS away_name, a.logo AS away_logo
            FROM tournament_match_result m
            JOIN tournament t ON t.tour_id = m.tour_id AND t.is_deleted = 0
            JOIN team h ON h.id = m.team1_id
            JOIN team a ON a.id = m.team2_id
            WHERE 1=1 {$dateFilter}
            ORDER BY m.tour_id, m.date DESC, m.time
        ", $params)->getResultArray();

        // Group by tournament
        $grouped = [];
        foreach ($matches as $m) {
            $tourId = (int) $m['tour_id'];
            if (!isset($grouped[$tourId])) {
                $grouped[$tourId] = [
                    'competition' => [
                        'id' => $tourId,
                        'name' => $m['tour_name'],
                        'logo' => $this->imageUrl($m['tour_logo']),
                        'season' => (int) date('Y'),
                        'hasStandings' => $m['tournament_type'] === 'league',
                    ],
                    'games' => [],
                ];
            }

            $grouped[$tourId]['games'][] = $this->formatGame($m);
        }

        return $this->successResponse(array_values($grouped));
    }

    /**
     * GET /api/v1/matches/:id
     * Schema: IMatchDetails
     */
    public function show($id = null)
    {
        $lang = $this->request->getGet('lang') ?? 'th';
        $nameCol = $lang === 'en' ? "COALESCE(NULLIF(t.name_en, ''), t.name)" : 't.name';
        $homeNameCol = $lang === 'en' ? "COALESCE(NULLIF(h.name_en, ''), h.name)" : 'h.name';
        $awayNameCol = $lang === 'en' ? "COALESCE(NULLIF(a.name_en, ''), a.name)" : 'a.name';

        $match = $this->db->query("
            SELECT
                m.match_id, m.tour_id, m.team1_id, m.team2_id,
                m.team1_score, m.team2_score, m.team1_penalty, m.team2_penalty,
                m.winner_team_id, m.is_penalty_used,
                m.match_status, m.match_round, m.group_id,
                m.date, m.time, m.stadium, m.referee,
                {$nameCol} AS tour_name,
                t.logo AS tour_logo, t.tournament_type,
                {$homeNameCol} AS home_name, h.logo AS home_logo,
                {$awayNameCol} AS away_name, a.logo AS away_logo
            FROM tournament_match_result m
            JOIN tournament t ON t.tour_id = m.tour_id
            LEFT JOIN team h ON h.id = m.team1_id
            LEFT JOIN team a ON a.id = m.team2_id
            WHERE m.match_id = ?
        ", [$id])->getRowArray();

        if (!$match) {
            return $this->notFoundResponse('Match not found');
        }

        // Events
        $events = $this->db->query("
            SELECT e.*, ev.name AS event_name, ev.name_en AS event_name_en, ev.icon,
                   p1.first_name AS p1_first, p1.last_name AS p1_last, p1.image_path AS p1_image,
                   p2.first_name AS p2_first, p2.last_name AS p2_last, p2.image_path AS p2_image
            FROM tournament_match_event e
            LEFT JOIN tbl_event ev ON ev.id = e.event_id
            LEFT JOIN player p1 ON p1.id = e.player_1_id
            LEFT JOIN player p2 ON p2.id = e.player_2_id
            WHERE e.match_id = ?
            ORDER BY CAST(e.minute AS UNSIGNED)
        ", [$id])->getResultArray();

        $formattedEvents = array_map(function ($e) use ($lang) {
            return [
                'id' => (int) $e['match_event_id'],
                'eventType' => (int) $e['event_id'],
                'eventName' => $lang === 'en' ? ($e['event_name_en'] ?? $e['event_name']) : $e['event_name'],
                'icon' => $e['icon'],
                'minute' => $e['minute'],
                'teamId' => (int) $e['team_id'],
                'player' => $e['p1_first'] ? [
                    'id' => (int) $e['player_1_id'],
                    'name' => trim($e['p1_first'] . ' ' . $e['p1_last']),
                    'photo' => $this->imageUrl($e['p1_image']),
                ] : null,
                'relatedPlayer' => $e['p2_first'] ? [
                    'id' => (int) $e['player_2_id'],
                    'name' => trim($e['p2_first'] . ' ' . $e['p2_last']),
                    'photo' => $this->imageUrl($e['p2_image']),
                ] : null,
            ];
        }, $events);

        // Lineups from player_to_match (if exists)
        $homeLineup = $this->getLineup($id, (int) $match['team1_id']);
        $awayLineup = $this->getLineup($id, (int) $match['team2_id']);

        $startTime = $match['date'] && $match['time']
            ? date('c', strtotime($match['date'] . ' ' . $match['time']))
            : ($match['date'] ? date('c', strtotime($match['date'])) : null);

        return $this->successResponse([
            'id' => (int) $match['match_id'],
            'statusGroup' => $this->mapStatusGroup((int) $match['match_status']),
            'statusText' => $this->mapStatusText((int) $match['match_status']),
            'startTime' => $startTime,
            'date' => $match['date'] ?? null,
            'time' => $match['time'] ? substr($match['time'], 0, 5) : null,
            'round' => $match['match_round'] ?? null,
            'competition' => [
                'id' => (int) $match['tour_id'],
                'name' => $match['tour_name'],
                'logo' => $this->imageUrl($match['tour_logo']),
                'season' => (int) date('Y'),
                'hasStandings' => $match['tournament_type'] === 'league',
            ],
            'homeCompetitor' => [
                'id' => (int) $match['team1_id'],
                'name' => $match['home_name'],
                'logo' => $this->imageUrl($match['home_logo']),
                'score' => $match['team1_score'] !== null ? (int) $match['team1_score'] : null,
                'isWinner' => (int) $match['winner_team_id'] === (int) $match['team1_id'],
            ],
            'awayCompetitor' => [
                'id' => (int) $match['team2_id'],
                'name' => $match['away_name'],
                'logo' => $this->imageUrl($match['away_logo']),
                'score' => $match['team2_score'] !== null ? (int) $match['team2_score'] : null,
                'isWinner' => (int) $match['winner_team_id'] === (int) $match['team2_id'],
            ],
            'venue' => $match['stadium'],
            'referee' => $match['referee'],
            'homeLineup' => $homeLineup,
            'awayLineup' => $awayLineup,
            'events' => $formattedEvents,
            'changes' => [],
            'topPerformers' => [],
        ]);
    }

    /**
     * GET /api/v1/matches/:id/h2h
     * Schema: IH2HGame[]
     */
    public function h2h($id = null)
    {
        $lang = $this->request->getGet('lang') ?? 'th';
        $nameCol = $lang === 'en' ? "COALESCE(NULLIF(t.name_en, ''), t.name)" : 't.name';
        $homeNameCol = $lang === 'en' ? "COALESCE(NULLIF(h.name_en, ''), h.name)" : 'h.name';
        $awayNameCol = $lang === 'en' ? "COALESCE(NULLIF(a.name_en, ''), a.name)" : 'a.name';

        $match = $this->db->table('tournament_match_result')
            ->where('match_id', $id)->get()->getRowArray();

        if (!$match) {
            return $this->notFoundResponse();
        }

        $team1 = (int) $match['team1_id'];
        $team2 = (int) $match['team2_id'];

        $h2hMatches = $this->db->query("
            SELECT m.match_id, m.team1_id, m.team2_id,
                   m.team1_score, m.team2_score,
                   m.date, m.time,
                   {$nameCol} AS tour_name, t.logo AS tour_logo,
                   {$homeNameCol} AS home_name, h.logo AS home_logo,
                   {$awayNameCol} AS away_name, a.logo AS away_logo
            FROM tournament_match_result m
            JOIN tournament t ON t.tour_id = m.tour_id
            JOIN team h ON h.id = m.team1_id
            JOIN team a ON a.id = m.team2_id
            WHERE m.match_status = 3
              AND ((m.team1_id = ? AND m.team2_id = ?) OR (m.team1_id = ? AND m.team2_id = ?))
            ORDER BY m.date DESC
            LIMIT 10
        ", [$team1, $team2, $team2, $team1])->getResultArray();

        $result = array_map(function ($m) {
            return [
                'id' => (int) $m['match_id'],
                'startTime' => $m['date'] ? date('c', strtotime($m['date'] . ' ' . ($m['time'] ?? '00:00'))) : null,
                'date' => $m['date'] ?? null,
                'time' => $m['time'] ? substr($m['time'], 0, 5) : null,
                'statusText' => 'Ended',
                'homeCompetitor' => [
                    'id' => (int) $m['team1_id'],
                    'name' => $m['home_name'],
                    'logo' => $this->imageUrl($m['home_logo']),
                    'score' => (int) $m['team1_score'],
                ],
                'awayCompetitor' => [
                    'id' => (int) $m['team2_id'],
                    'name' => $m['away_name'],
                    'logo' => $this->imageUrl($m['away_logo']),
                    'score' => (int) $m['team2_score'],
                ],
                'competition' => [
                    'name' => $m['tour_name'],
                    'logo' => $this->imageUrl($m['tour_logo']),
                ],
            ];
        }, $h2hMatches);

        return $this->successResponse($result);
    }

    /**
     * GET /api/v1/matches/:id/statistics
     * Schema: IStatistics
     */
    public function statistics($id = null)
    {
        $match = $this->db->table('tournament_match_result')
            ->where('match_id', $id)->get()->getRowArray();

        if (!$match) {
            return $this->notFoundResponse();
        }

        $team1 = (int) $match['team1_id'];
        $team2 = (int) $match['team2_id'];

        // Aggregate player_match_state for both teams
        $homeStats = $this->db->query("
            SELECT SUM(shots) AS shots, SUM(shots_on_target) AS shots_on_target,
                   SUM(total_passes) AS total_passes, SUM(success_passed) AS success_passed,
                   SUM(tackles) AS tackles, SUM(interceptions) AS interceptions,
                   SUM(fouls_committed) AS fouls, SUM(offside) AS offside, SUM(saves) AS saves
            FROM player_match_state WHERE match_id = ? AND player_id IN (SELECT player_id FROM academy_player_enrollments WHERE team_id = ? AND is_active = 1)
        ", [$id, $team1])->getRowArray();

        $awayStats = $this->db->query("
            SELECT SUM(shots) AS shots, SUM(shots_on_target) AS shots_on_target,
                   SUM(total_passes) AS total_passes, SUM(success_passed) AS success_passed,
                   SUM(tackles) AS tackles, SUM(interceptions) AS interceptions,
                   SUM(fouls_committed) AS fouls, SUM(offside) AS offside, SUM(saves) AS saves
            FROM player_match_state WHERE match_id = ? AND player_id IN (SELECT player_id FROM academy_player_enrollments WHERE team_id = ? AND is_active = 1)
        ", [$id, $team2])->getRowArray();

        // Count events in single query instead of 6 separate queries
        $eventCounts = $this->db->query("
            SELECT team_id, event_id, COUNT(*) AS cnt
            FROM tournament_match_event
            WHERE match_id = ?
            GROUP BY team_id, event_id
        ", [$id])->getResultArray();

        $eventMap = [];
        foreach ($eventCounts as $ec) {
            $eventMap[(int) $ec['team_id']][(int) $ec['event_id']] = (int) $ec['cnt'];
        }

        $homeGoals = $eventMap[$team1][1] ?? 0;
        $awayGoals = $eventMap[$team2][1] ?? 0;
        $homeYellow = $eventMap[$team1][2] ?? 0;
        $awayYellow = $eventMap[$team2][2] ?? 0;
        $homeRed = $eventMap[$team1][3] ?? 0;
        $awayRed = $eventMap[$team2][3] ?? 0;

        $stats = [];
        $statNames = [
            'Goals' => [$homeGoals, $awayGoals],
            'Total Shots' => [$homeStats['shots'] ?? 0, $awayStats['shots'] ?? 0],
            'Shots on Target' => [$homeStats['shots_on_target'] ?? 0, $awayStats['shots_on_target'] ?? 0],
            'Passes' => [$homeStats['total_passes'] ?? 0, $awayStats['total_passes'] ?? 0],
            'Successful Passes' => [$homeStats['success_passed'] ?? 0, $awayStats['success_passed'] ?? 0],
            'Tackles' => [$homeStats['tackles'] ?? 0, $awayStats['tackles'] ?? 0],
            'Interceptions' => [$homeStats['interceptions'] ?? 0, $awayStats['interceptions'] ?? 0],
            'Fouls' => [$homeStats['fouls'] ?? 0, $awayStats['fouls'] ?? 0],
            'Offsides' => [$homeStats['offside'] ?? 0, $awayStats['offside'] ?? 0],
            'Saves' => [$homeStats['saves'] ?? 0, $awayStats['saves'] ?? 0],
            'Yellow Cards' => [$homeYellow, $awayYellow],
            'Red Cards' => [$homeRed, $awayRed],
        ];

        $order = 0;
        foreach ($statNames as $name => [$hVal, $aVal]) {
            $stats[] = [
                'name' => $name,
                'home' => ['id' => $order, 'value' => (string) $hVal, 'competitorId' => $team1, 'order' => $order, 'categoryName' => null],
                'away' => ['id' => $order, 'value' => (string) $aVal, 'competitorId' => $team2, 'order' => $order, 'categoryName' => null],
            ];
            $order++;
        }

        return $this->successResponse([
            'actualGameStatistics' => null,
            'stats' => $stats,
        ]);
    }

    // === Private Helpers ===

    private function formatGame(array $m): array
    {
        $status = (int) ($m['match_status'] ?? 0);
        $timestamp = $m['date'] && $m['time']
            ? strtotime($m['date'] . ' ' . $m['time'])
            : ($m['date'] ? strtotime($m['date']) : null);
        $startTime = $timestamp ? date('c', $timestamp) : null;
        $startTimeDisplay = $timestamp ? date('d M Y, H:i', $timestamp) : null;

        $aggregated = null;
        if (!empty($m['is_penalty_used']) && $m['team1_penalty'] !== null) {
            $aggregated = "({$m['team1_penalty']}-{$m['team2_penalty']})";
        }

        return [
            'id' => (int) $m['match_id'],
            'statusGroup' => $this->mapStatusGroup($status),
            'statusText' => $this->mapStatusText($status),
            'startTime' => $startTime,
            'startTimeDisplay' => $startTimeDisplay,
            'date' => $m['date'] ?? null,
            'time' => $m['time'] ? substr($m['time'], 0, 5) : null,
            'round' => $m['match_round'] ?? null,
            'groupId' => $m['group_id'] ?? null,
            'groupName' => $m['group_id'] ?? null,
            'shortStatusText' => $this->mapShortStatus($status),
            'gameTimeDisplay' => null,
            'gameTime' => null,
            'hasLineups' => false,
            'hasStatistics' => false,
            'hasHighlights' => false,
            'aggregatedScore' => $aggregated,
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

    private function getLineup(int $matchId, int $teamId): array
    {
        // Check if player_to_match table exists
        if (!$this->db->tableExists('player_to_match')) {
            return ['formation' => null, 'starting' => [], 'substitutes' => [], 'management' => []];
        }

        $players = $this->db->query("
            SELECT ptm.*, p.first_name, p.last_name,
                   COALESCE(ape.shirt_number, p.shirt_number) AS shirt_number, p.image_path,
                   pp.name_en AS position_name
            FROM player_to_match ptm
            JOIN player p ON p.id = ptm.player_id
            LEFT JOIN academy_player_enrollments ape ON ape.player_id = p.id AND ape.team_id = ? AND ape.is_active = 1
            LEFT JOIN tbl_player_position pp ON pp.id = COALESCE(NULLIF(ape.position_id,0), p.player_position_id)
            WHERE ptm.match_id = ?
              AND EXISTS (SELECT 1 FROM academy_player_enrollments WHERE player_id = p.id AND team_id = ? AND is_active = 1)
        ", [$teamId, $matchId, $teamId])->getResultArray();

        $starting = [];
        $substitutes = [];
        foreach ($players as $p) {
            $formatted = [
                'id' => (int) $p['player_id'],
                'name' => trim($p['first_name'] . ' ' . $p['last_name']),
                'jerseyNum' => $p['shirt_number'] ? (int) $p['shirt_number'] : null,
                'photo' => $this->imageUrl($p['image_path']),
                'position' => $p['position_name'] ?? null,
            ];
            $starting[] = $formatted;
        }

        return [
            'formation' => null,
            'starting' => $starting,
            'substitutes' => $substitutes,
            'management' => [],
        ];
    }

    private function mapStatusGroup(int $status): int
    {
        // DB: 0=Not Started, 1=Live, 3=Finished
        $map = [0 => 1, 1 => 2, 3 => 3];
        return $map[$status] ?? 1;
    }

    private function mapStatusText(int $status): string
    {
        // DB: 0=Not Started, 1=Live, 3=Finished
        $map = [0 => 'Not Started', 1 => 'Live', 3 => 'Ended'];
        return $map[$status] ?? 'Not Started';
    }

    private function mapShortStatus(int $status): string
    {
        // DB: 0=Not Started, 1=Live, 3=Finished
        $map = [0 => 'NS', 1 => 'LIVE', 3 => 'FT'];
        return $map[$status] ?? 'NS';
    }
}
