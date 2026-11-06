<?php

namespace Modules\Api\Controllers\V1;

use Modules\Api\Controllers\BaseApiController;
use Modules\Academy\Models\AcademyModel;

class PlayerController extends BaseApiController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    /**
     * GET /api/v1/players/:id
     * Schema: IPlayer
     */
    public function show($id = null)
    {
        $lang = $this->request->getGet('lang') ?? 'th';
        $teamNameCol = $lang === 'en' ? "COALESCE(NULLIF(t.name_en, ''), t.name)" : 't.name';
        $tourNameCol = $lang === 'en' ? "COALESCE(NULLIF(t.name_en, ''), t.name)" : 't.name';

        $player = $this->db->query("
            SELECT p.*,
                   COALESCE(ape.shirt_number, p.shirt_number) AS shirt_number,
                   COALESCE(NULLIF(ape.position_id,0), p.player_position_id) AS player_position_id,
                   pp.id AS pos_id, pp.name_en AS position_name, pp.name_th AS position_name_th,
                   pp.parent_id AS pos_parent_id,
                   t.id AS team_id, {$teamNameCol} AS team_name, t.logo AS team_logo
            FROM player p
            LEFT JOIN academy_player_enrollments ape ON ape.player_id = p.id AND ape.is_primary = 1 AND ape.is_active = 1
            LEFT JOIN tbl_player_position pp ON pp.id = COALESCE(NULLIF(ape.position_id,0), p.player_position_id)
            LEFT JOIN team t ON t.id = COALESCE(ape.team_id, p.team_id)
            WHERE p.id = ?
        ", [$id])->getRowArray();

        if (!$player) {
            return $this->notFoundResponse('Player not found');
        }

        $age = $player['birth_date']
            ? (int) date_diff(date_create($player['birth_date']), date_create())->y
            : null;

        $fullName = trim($player['first_name'] . ' ' . $player['last_name']);

        // คำนวณ position พร้อม fallback จาก player_sub_position_id
        $allPositions = $this->db->table('tbl_player_position')->get()->getResultArray();
        $positionMap  = [];
        foreach ($allPositions as $pos) {
            $positionMap[(int) $pos['id']] = $pos;
        }

        $posParent = (int) ($player['pos_parent_id'] ?? 0);
        $posId     = $posParent > 0 ? $posParent : (int) ($player['player_position_id'] ?? 0);
        $posNameEn = $player['position_name'] ?? null;
        $posNameTh = $player['position_name_th'] ?? null;
        $subPosNameEn = null;

        if ($posId === 0 && !empty($player['player_sub_position_id'])) {
            $subIds = json_decode(html_entity_decode($player['player_sub_position_id']), true);
            if (!is_array($subIds)) {
                $subIds = array_filter(explode(',', html_entity_decode($player['player_sub_position_id'])));
            }
            $subNames = [];
            foreach ($subIds as $subId) {
                $subId = (int) $subId;
                if (!isset($positionMap[$subId])) continue;
                $subNames[] = $positionMap[$subId]['name_en'];
                if ($posId === 0 && (int) $positionMap[$subId]['parent_id'] > 0) {
                    $parentId  = (int) $positionMap[$subId]['parent_id'];
                    $posId     = $parentId;
                    $posNameEn = $positionMap[$parentId]['name_en'] ?? null;
                    $posNameTh = $positionMap[$parentId]['name_th'] ?? null;
                }
            }
            $subPosNameEn = !empty($subNames) ? implode(', ', $subNames) : null;
        }

        $posMap      = [1 => 'FWD', 2 => 'MID', 3 => 'DEF', 4 => 'GK'];
        $posCategory = $posMap[$posId] ?? null;
        $posDisplay  = $lang === 'en' ? ($subPosNameEn ?? $posNameEn) : ($posNameTh ?? $posNameEn);

        // Player stats per tournament (single JOIN instead of 3 correlated subqueries)
        $stats = $this->db->query("
            SELECT t.tour_id, {$tourNameCol} AS tour_name, t.logo AS tour_logo,
                   COUNT(DISTINCT pms.match_id) AS matches_played,
                   SUM(pms.minutes_played) AS total_minutes,
                   COALESCE(ev.goals, 0) AS goals,
                   COALESCE(ev.yellow_cards, 0) AS yellow_cards,
                   COALESCE(ev.red_cards, 0) AS red_cards
            FROM player_match_state pms
            JOIN tournament t ON t.tour_id = pms.tournament_id
            LEFT JOIN (
                SELECT tour_id,
                    SUM(event_id = 1) AS goals,
                    SUM(event_id = 2) AS yellow_cards,
                    SUM(event_id = 3) AS red_cards
                FROM tournament_match_event
                WHERE player_1_id = ?
                GROUP BY tour_id
            ) ev ON ev.tour_id = t.tour_id
            WHERE pms.player_id = ?
            GROUP BY t.tour_id, t.name, t.name_en, t.logo, t.start_date,
                     ev.goals, ev.yellow_cards, ev.red_cards
            ORDER BY t.start_date DESC
        ", [$id, $id])->getResultArray();

        $highlightStats = array_map(function ($s) {
            return [
                'competition' => [
                    'id' => (int) $s['tour_id'],
                    'name' => $s['tour_name'],
                    'logo' => $this->imageUrl($s['tour_logo']),
                ],
                'stats' => [
                    ['name' => 'Matches', 'value' => (int) $s['matches_played']],
                    ['name' => 'Minutes', 'value' => (int) $s['total_minutes']],
                    ['name' => 'Goals', 'value' => (int) $s['goals']],
                    ['name' => 'Yellow Cards', 'value' => (int) $s['yellow_cards']],
                    ['name' => 'Red Cards', 'value' => (int) $s['red_cards']],
                ],
            ];
        }, $stats);

        return $this->successResponse([
            'id' => (int) $player['id'],
            'name' => $fullName,
            'shortName' => $player['nick_name'] ?? $player['first_name'],
            'photo' => $this->imageUrl($player['image_path']),
            'age' => $age,
            'jerseyNum' => $player['shirt_number'] ? (int) $player['shirt_number'] : null,
            'height' => $player['height'],
            'weight' => $player['weight'],
            'position' => [
                'id'        => $posId ?: null,
                'name'      => $posDisplay,
                'nameEn'    => $subPosNameEn ?? $posNameEn,
                'nameTh'    => $posNameTh,
                'shortCode' => $subPosNameEn ?? $posCategory,
                'category'  => $posCategory,
            ],
            'dateOfBirth' => $player['birth_date'] ?: null,
            'countryOfBirth' => null,
            'placeOfBirth' => null,
            'nationality' => $player['nation'] ?? 'Thai',
            'team' => $player['team_id'] ? [
                'id' => (int) $player['team_id'],
                'name' => $player['team_name'],
                'logo' => $this->imageUrl($player['team_logo']),
            ] : null,
            'nationalTeam' => null,
            'highlightStats' => $highlightStats,
            'transfers' => [],
        ]);
    }

    /**
     * GET /api/v1/players/:id/idp
     * ?theme={template_id} — optional, auto-select ถ้าไม่ระบุ
     * Schema: IPlayerIDP = { templates[], selectedTemplateId, dimensions[], goals[] }
     */
    public function idp($id = null)
    {
        $theme = $this->request->getGet('theme'); // template_id
        $lang  = $this->request->getGet('lang') ?? 'th';

        $player = $this->db->table('player')->where('id', $id)->where('is_active', 1)->get()->getRowArray();
        if (!$player) {
            return $this->notFoundResponse('Player not found');
        }

        $model = new AcademyModel();

        // auto-หา academy_id จาก plan ล่าสุดของนักเตะ
        $anyPlan = $this->db->query("
            SELECT academy_id, template_id, status
            FROM idp_plans
            WHERE player_id = ? AND template_id IS NOT NULL
            ORDER BY FIELD(status,'active','completed','draft','cancelled'), created_at DESC
            LIMIT 1
        ", [$id])->getRowArray();

        if (!$anyPlan) {
            return $this->successResponse([
                'templates'          => [],
                'selectedTemplateId' => null,
                'dimensions'         => [],
                'goals'              => [],
            ]);
        }

        $academyId = (int) $anyPlan['academy_id'];

        // ดึง templates ทั้งหมดของนักเตะ (ข้ามทุก academy)
        $rawTemplates = $this->db->query("
            SELECT DISTINCT p.template_id AS id, t.title
            FROM idp_plans p
            JOIN idp_templates t ON t.id = p.template_id
            WHERE p.player_id = ? AND p.template_id IS NOT NULL
            ORDER BY t.title ASC
        ", [$id])->getResultArray();
        $templates = array_map(fn($t) => [
            'id'    => (int) $t['id'],
            'title' => $t['title'],
        ], $rawTemplates);

        // เลือก template: ?theme → active plan → template แรก
        $templateId = $theme ? (int) $theme : 0;
        if (!$templateId) {
            $plans = $model->getPlayerIDPPlans($id, $academyId);
            foreach ($plans as $plan) {
                if ($plan['status'] === 'active' && !empty($plan['template_id'])) {
                    $templateId = (int) $plan['template_id'];
                    break;
                }
            }
            if (!$templateId && !empty($templates)) {
                $templateId = $templates[0]['id'];
            }
        }

        // ดึง dimension scores (baseline vs current)
        $compare = $model->getPlayerDimensionScoresByTemplate($id, $academyId, $templateId);
        $peerMax = $model->getPeerMaxScoresForPlayer($id, $academyId, $templateId);

        $dimensions = array_map(function ($d) use ($peerMax, $lang) {
            $dimId = (int) $d['dimension_id'];
            return [
                'id'            => $dimId,
                'name'          => $lang === 'en' ? ($d['dimension_name_en'] ?? $d['dimension_name']) : $d['dimension_name'],
                'icon'          => $d['icon'] ?? null,
                'color'         => $d['color'] ?? null,
                'baselineScore' => round((float) ($d['baseline_score'] ?? 0), 2),
                'currentScore'  => round((float) ($d['current_score'] ?? 0), 2),
                'peerMaxScore'  => round((float) ($peerMax[$dimId] ?? 0), 2),
            ];
        }, $compare);

        // ดึง goals จาก active plan ของ template นี้
        $goalsData = [];
        $plans = $model->getPlayerIDPPlans($id, $academyId);
        foreach ($plans as $plan) {
            if ((int) $plan['template_id'] === $templateId && $plan['status'] === 'active') {
                $rawGoals = $model->getGoalsByDimension($plan['id']);
                foreach ($rawGoals as $g) {
                    $goalsData[] = [
                        'dimensionId'   => (int) $g['dimension_id'],
                        'dimensionName' => $g['dimension_name'],
                        'icon'          => $g['dimension_icon'] ?? null,
                        'color'         => $g['dimension_color'] ?? null,
                        'goals'         => array_map(fn($goal) => [
                            'id'                 => (int) $goal['id'],
                            'title'              => $goal['title'],
                            'achievementPercent' => (int) ($goal['achievement_percent'] ?? 0),
                            'status'             => $goal['status'] ?? 'not_started',
                        ], $g['goals']),
                    ];
                }
                break;
            }
        }

        return $this->successResponse([
            'templates'          => $templates,
            'selectedTemplateId' => $templateId,
            'dimensions'         => $dimensions,
            'goals'              => $goalsData,
        ]);
    }
}
