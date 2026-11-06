<?php

namespace Modules\Api\Controllers\V1;

use Modules\Api\Controllers\BaseApiController;

class TeamController extends BaseApiController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    /**
     * GET /api/v1/teams
     * Schema: ITeam[] - returns all teams with player count
     */
    public function index()
    {
        $lang = $this->request->getGet('lang') ?? 'th';
        $teamNameCol = $lang === 'en' ? "COALESCE(NULLIF(t.name_en, ''), t.name)" : 't.name';

        $teams = $this->db->query("
            SELECT t.id, {$teamNameCol} AS name, t.logo,
                   COALESCE(pc.player_count, 0) AS player_count
            FROM team t
            LEFT JOIN (
                SELECT pt.team_id, COUNT(*) AS player_count
                FROM academy_player_enrollments pt
                JOIN player p ON p.id = pt.player_id AND p.is_active = 1
                WHERE pt.is_active = 1
                GROUP BY pt.team_id
            ) pc ON pc.team_id = t.id
            LEFT JOIN (
                SELECT team_id, MAX(update_date) as latest_update
                FROM academy_teams
                GROUP BY team_id
            ) at ON at.team_id = t.id
            ORDER BY at.latest_update DESC, t.name ASC
        ")->getResultArray();

        $result = array_map(function ($t) {
            return [
                'id'          => (int) $t['id'],
                'name'        => $t['name'],
                'logo'        => $this->imageUrl($t['logo']),
                'playerCount' => (int) $t['player_count'],
            ];
        }, $teams);

        return $this->successResponse($result);
    }

    /**
     * GET /api/v1/teams/:id/matches?type=next|last
     * Schema: IMatchesTeam[] = { competition, games[] }
     */
    public function matches($id = null)
    {
        $type = $this->request->getGet('type') ?? 'next';
        $lang = $this->request->getGet('lang') ?? 'th';
        $nameCol = $lang === 'en' ? "COALESCE(NULLIF(t.name_en, ''), t.name)" : 't.name';
        $homeNameCol = $lang === 'en' ? "COALESCE(NULLIF(h.name_en, ''), h.name)" : 'h.name';
        $awayNameCol = $lang === 'en' ? "COALESCE(NULLIF(a.name_en, ''), a.name)" : 'a.name';

        if ($type === 'next') {
            $condition = "m.match_status = 0 ORDER BY m.date ASC, m.time ASC";
        } else {
            $condition = "m.match_status = 3 ORDER BY m.date DESC, m.time DESC";
        }

        $matches = $this->db->query("
            SELECT m.match_id, m.tour_id, m.team1_id, m.team2_id,
                   m.team1_score, m.team2_score, m.team1_penalty, m.team2_penalty,
                   m.winner_team_id, m.is_penalty_used,
                   m.match_status, m.match_round, m.date, m.time,
                   {$nameCol} AS tour_name, t.logo AS tour_logo, t.tournament_type,
                   {$homeNameCol} AS home_name, h.logo AS home_logo,
                   {$awayNameCol} AS away_name, a.logo AS away_logo
            FROM tournament_match_result m
            JOIN tournament t ON t.tour_id = m.tour_id AND t.is_deleted = 0
            JOIN team h ON h.id = m.team1_id
            JOIN team a ON a.id = m.team2_id
            WHERE (m.team1_id = ? OR m.team2_id = ?) AND {$condition}
            LIMIT 20
        ", [$id, $id])->getResultArray();

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
     * GET /api/v1/teams/:id/squads
     * Schema: ISquads = { competitorId, athletes[] }
     */
    public function squads($id = null)
    {
        $lang = $this->request->getGet('lang') ?? 'th';
        $tourId = (int) (
            $this->request->getGet('tour_id')
            ?? $this->request->getGet('tourId')
            ?? $this->request->getGet('competition_id')
            ?? $this->request->getGet('competitionId')
            ?? 0
        );

        $team = $this->db->table('team')->where('id', $id)->get()->getRowArray();
        if (!$team) {
            return $this->notFoundResponse('Team not found');
        }

        // โหลด position map ทั้งหมดสำหรับ fallback จาก sub-position
        $allPositions = $this->db->table('tbl_player_position')->get()->getResultArray();
        $positionMap = [];
        foreach ($allPositions as $pos) {
            $positionMap[(int) $pos['id']] = $pos;
        }

        $posNameCol = $lang === 'en' ? 'pp.name_en' : 'COALESCE(pp.name_th, pp.name_en)';

        if ($tourId > 0) {
            $players = $this->db->query("
                SELECT p.id, p.first_name, p.last_name, p.nick_name,
                       p.birth_date,
                       COALESCE(pt_sq.shirt_number, p.shirt_number) AS shirt_number,
                       p.image_path,
                       COALESCE(NULLIF(pt_sq.position_id,0), p.player_position_id) AS player_position_id,
                       COALESCE(pt_sq.sub_position_ids, p.player_sub_position_id) AS player_sub_position_id,
                       pp.parent_id AS pos_parent_id,
                       {$posNameCol} AS position_name
                FROM tournament_team tt
                INNER JOIN tournament_submitted_players tsp ON tsp.tour_team_id = tt.tour_team_id
                INNER JOIN player p ON p.id = tsp.player_id
                LEFT JOIN academy_player_enrollments pt_sq ON pt_sq.player_id = p.id AND pt_sq.team_id = tt.team_id AND pt_sq.is_active = 1
                LEFT JOIN tbl_player_position pp ON pp.id = COALESCE(NULLIF(pt_sq.position_id,0), p.player_position_id)
                WHERE tt.tour_id = ? AND tt.team_id = ? AND p.is_active = 1
                GROUP BY p.id
                ORDER BY CAST(COALESCE(pt_sq.shirt_number, p.shirt_number) AS UNSIGNED)
            ", [$tourId, $id])->getResultArray();
        } else {
            $players = $this->db->query("
                SELECT p.id, p.first_name, p.last_name, p.nick_name,
                       p.birth_date,
                       COALESCE(pt_sq.shirt_number, p.shirt_number) AS shirt_number,
                       p.image_path,
                       COALESCE(NULLIF(pt_sq.position_id,0), p.player_position_id) AS player_position_id,
                       COALESCE(pt_sq.sub_position_ids, p.player_sub_position_id) AS player_sub_position_id,
                       pp.parent_id AS pos_parent_id,
                       {$posNameCol} AS position_name
                FROM player p
                JOIN academy_player_enrollments pt_sq ON pt_sq.player_id = p.id AND pt_sq.team_id = ? AND pt_sq.is_active = 1
                LEFT JOIN tbl_player_position pp ON pp.id = COALESCE(NULLIF(pt_sq.position_id,0), p.player_position_id)
                WHERE p.is_active = 1
                ORDER BY COALESCE(NULLIF(pt_sq.position_id,0), p.player_position_id), CAST(COALESCE(pt_sq.shirt_number, p.shirt_number) AS UNSIGNED)
            ", [$id])->getResultArray();
        }

        $posMap = [1 => 'FWD', 2 => 'MID', 3 => 'DEF', 4 => 'GK'];

        $athletes = array_map(function ($p) use ($positionMap, $posMap, $lang) {
            $age = $p['birth_date'] ? (int) date_diff(date_create($p['birth_date']), date_create())->y : null;

            $posParent  = (int) ($p['pos_parent_id'] ?? 0);
            $posId      = $posParent > 0 ? $posParent : (int) ($p['player_position_id'] ?? 0);
            $posName    = $p['position_name'] ?? null;
            $subPosName = null;

            // fallback: ถ้าไม่มี player_position_id ให้ดึงจาก player_sub_position_id
            if ($posId === 0 && !empty($p['player_sub_position_id'])) {
                $subIds = json_decode(html_entity_decode($p['player_sub_position_id']), true);
                if (!is_array($subIds)) {
                    $subIds = array_filter(explode(',', html_entity_decode($p['player_sub_position_id'])));
                }
                $subNames = [];
                foreach ($subIds as $subId) {
                    $subId = (int) $subId;
                    if (!isset($positionMap[$subId])) continue;
                    $subNames[] = $positionMap[$subId]['name_en'];
                    if ($posId === 0 && (int) $positionMap[$subId]['parent_id'] > 0) {
                        $parentId = (int) $positionMap[$subId]['parent_id'];
                        $posId    = $parentId;
                        $posName  = $lang === 'en'
                            ? ($positionMap[$parentId]['name_en'] ?? null)
                            : ($positionMap[$parentId]['name_th'] ?? $positionMap[$parentId]['name_en'] ?? null);
                    }
                }
                $subPosName = !empty($subNames) ? implode(', ', $subNames) : null;
            }

            return [
                'id'        => (int) $p['id'],
                'name'      => trim($p['first_name'] . ' ' . $p['last_name']),
                'shortName' => $p['nick_name'] ?? $p['first_name'],
                'age'       => $age,
                'jerseyNum' => $p['shirt_number'] ? (int) $p['shirt_number'] : null,
                'photo'     => $this->imageUrl($p['image_path']),
                'position'  => [
                    'id'        => $posId,
                    'name'      => $posMap[$posId] ?? $posName ?? 'Unknown',
                    'shortCode' => $subPosName ?? $posMap[$posId] ?? null,
                    'category'  => $posMap[$posId] ?? null,
                ],
                'formationPosition' => [
                    'id'    => $posId,
                    'name'  => $subPosName ?? $posName ?? 'Unknown',
                    'order' => $posId,
                ],
            ];
        }, $players);

        return $this->successResponse([
            'competitorId' => (int) $id,
            'tourId'       => $tourId > 0 ? $tourId : null,
            'athletes'     => $athletes,
        ]);
    }

    /**
     * GET /api/v1/teams/:id/standings
     * Schema: ITeamStanding[] — standings ของทัวร์นาเมนต์ที่ทีมนี้เข้าร่วม
     */
    public function standings($id = null)
    {
        $lang = $this->request->getGet('lang') ?? 'th';

        $team = $this->db->table('team')->where('id', $id)->get()->getRowArray();
        if (!$team) {
            return $this->notFoundResponse('Team not found');
        }

        // หาทัวร์นาเมนต์ที่ทีมนี้ลงทะเบียนและได้รับอนุมัติ (GROUP BY เพื่อไม่ให้ซ้ำ)
        $nameCol = $lang === 'en' ? "COALESCE(NULLIF(t.name_en, ''), t.name)" : 't.name';
        $teamNameCol = $lang === 'en' ? "COALESCE(NULLIF(te.name_en, ''), te.name)" : 'te.name';
        $tournaments = $this->db->query("
            SELECT t.tour_id, {$nameCol} AS name, t.logo, t.tournament_type
            FROM tournament_team tt
            JOIN tournament t ON t.tour_id = tt.tour_id AND t.is_deleted = 0
            WHERE tt.team_id = ? AND tt.register_status = 1
            GROUP BY t.tour_id
            ORDER BY t.start_date DESC
        ", [$id])->getResultArray();

        $result = [];

        foreach ($tournaments as $tour) {
            $tourId = (int) $tour['tour_id'];

            // ดึงทีมทั้งหมดในทัวร์นาเมนต์
            $teams = $this->db->query("
                SELECT tt.team_id, {$teamNameCol} AS name, te.logo
                FROM tournament_team tt
                JOIN team te ON te.id = tt.team_id
                WHERE tt.tour_id = ? AND tt.register_status = 1
                GROUP BY tt.team_id
            ", [$tourId])->getResultArray();

            $teamMap = [];
            foreach ($teams as $t) {
                $teamMap[(int) $t['team_id']] = $t;
            }

            // ดึงเฉพาะแมตช์ที่มี group_id (รอบแบ่งกลุ่ม) เพื่อคำนวณ standings
            $matches = $this->db->query("
                SELECT team1_id, team2_id, team1_score, team2_score, group_id
                FROM tournament_match_result
                WHERE tour_id = ? AND match_status = 3 AND group_id IS NOT NULL AND group_id != ''
            ", [$tourId])->getResultArray();

            // จัดกลุ่มแมตช์ตาม group_id แล้วคำนวณ standings แยกแต่ละกลุ่ม
            $matchesByGroup = [];
            foreach ($matches as $m) {
                $gid = $m['group_id'];
                $matchesByGroup[$gid][] = $m;
            }

            foreach ($matchesByGroup as $groupId => $groupMatches) {
                // สร้าง standings เฉพาะทีมที่เล่นในกลุ่มนี้
                $groupStandings = [];

                foreach ($groupMatches as $m) {
                    $t1 = (int) $m['team1_id'];
                    $t2 = (int) $m['team2_id'];
                    $s1 = (int) $m['team1_score'];
                    $s2 = (int) $m['team2_score'];

                    // สร้าง entry ถ้ายังไม่มี
                    foreach ([$t1, $t2] as $tid) {
                        if (!isset($groupStandings[$tid]) && isset($teamMap[$tid])) {
                            $groupStandings[$tid] = [
                                'teamId' => $tid,
                                'name' => $teamMap[$tid]['name'],
                                'logo' => $teamMap[$tid]['logo'],
                                'gp' => 0, 'w' => 0, 'd' => 0, 'l' => 0,
                                'gf' => 0, 'ga' => 0, 'pts' => 0,
                            ];
                        }
                    }

                    if (!isset($groupStandings[$t1]) || !isset($groupStandings[$t2])) continue;

                    $groupStandings[$t1]['gp']++;
                    $groupStandings[$t2]['gp']++;
                    $groupStandings[$t1]['gf'] += $s1;
                    $groupStandings[$t1]['ga'] += $s2;
                    $groupStandings[$t2]['gf'] += $s2;
                    $groupStandings[$t2]['ga'] += $s1;

                    if ($s1 > $s2) {
                        $groupStandings[$t1]['w']++;
                        $groupStandings[$t1]['pts'] += 3;
                        $groupStandings[$t2]['l']++;
                    } elseif ($s1 < $s2) {
                        $groupStandings[$t2]['w']++;
                        $groupStandings[$t2]['pts'] += 3;
                        $groupStandings[$t1]['l']++;
                    } else {
                        $groupStandings[$t1]['d']++;
                        $groupStandings[$t2]['d']++;
                        $groupStandings[$t1]['pts']++;
                        $groupStandings[$t2]['pts']++;
                    }
                }

                // เรียงลำดับ: แต้ม → ผลต่างประตู → ประตูได้
                $standingsArr = array_values($groupStandings);
                usort($standingsArr, function ($a, $b) {
                    if ($a['pts'] !== $b['pts']) return $b['pts'] - $a['pts'];
                    $adiff = $a['gf'] - $a['ga'];
                    $bdiff = $b['gf'] - $b['ga'];
                    if ($adiff !== $bdiff) return $bdiff - $adiff;
                    return $b['gf'] - $a['gf'];
                });

                $formattedRows = [];
                $pos = 1;
                foreach ($standingsArr as $r) {
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
                        'goals' => $r['gf'] . ':' . $r['ga'],
                        'forr' => $r['gf'],
                        'against' => $r['ga'],
                        'ratio' => $r['gf'] - $r['ga'],
                        'points' => $r['pts'],
                        'isSelected' => $r['teamId'] === (int) $id,
                    ];
                }

                $result[] = [
                    'competition' => [
                        'id' => $tourId,
                        'name' => $tour['name'],
                        'logo' => $this->imageUrl($tour['logo']),
                    ],
                    'groupName' => $groupId,
                    'rows' => $formattedRows,
                ];
            }
        }

        return $this->successResponse($result);
    }

    /**
     * GET /api/v1/teams/:id/transfers
     * Schema: ITransfer[] — Jootball ไม่มี transfer system จริง, return empty
     */
    public function transfers($id = null)
    {
        $team = $this->db->table('team')->where('id', $id)->get()->getRowArray();
        if (!$team) {
            return $this->notFoundResponse('Team not found');
        }

        return $this->successResponse([]);
    }

    private function formatGame(array $m): array
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
            'shortStatusText' => ([0 => 'NS', 1 => 'LIVE', 3 => 'FT'])[$status] ?? 'NS',
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
