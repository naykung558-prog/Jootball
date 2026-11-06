<?php

namespace Modules\Api\Controllers\V1;

use Modules\Api\Controllers\BaseApiController;

class SearchController extends BaseApiController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    /**
     * GET /api/v1/search?query=&filter=&lang=
     * Schema: ISearch = { competitions[], competitors[], athletes[] }
     */
    public function index()
    {
        $query = $this->request->getGet('query') ?? '';
        $filter = $this->request->getGet('filter'); // competitions, competitors, athletes, or null (all)
        $lang = $this->request->getGet('lang') ?? 'th';

        if (empty($query)) {
            return $this->successResponse([
                'competitions' => [],
                'competitors' => [],
                'athletes' => [],
            ]);
        }

        $searchTerm = '%' . $query . '%';

        $competitions = [];
        $competitors = [];
        $athletes = [];

        // Search tournaments (competitions)
        if (!$filter || $filter === 'competitions') {
            $nameCol = $lang === 'en' ? "COALESCE(NULLIF(t.name_en, ''), t.name)" : 't.name';
            $results = $this->db->query("
                SELECT t.tour_id, {$nameCol} AS name, t.logo, t.tournament_type
                FROM tournament t
                WHERE t.is_deleted = 0
                  AND (t.name LIKE ? OR t.name_en LIKE ?)
                LIMIT 10
            ", [$searchTerm, $searchTerm])->getResultArray();

            $competitions = array_map(function ($t) {
                return [
                    'id' => (int) $t['tour_id'],
                    'name' => $t['name'],
                    'logo' => $this->imageUrl($t['logo']),
                    'season' => (int) date('Y'),
                    'hasStandings' => $t['tournament_type'] === 'league',
                ];
            }, $results);
        }

        // Search teams (competitors)
        if (!$filter || $filter === 'competitors') {
            $teamNameCol = $lang === 'en' ? "COALESCE(NULLIF(name_en, ''), name)" : 'name';
            $results = $this->db->query("
                SELECT id, {$teamNameCol} AS name, logo
                FROM team
                WHERE is_active = '1' AND (name LIKE ? OR name_en LIKE ?)
                LIMIT 10
            ", [$searchTerm, $searchTerm])->getResultArray();

            $competitors = array_map(function ($t) {
                return [
                    'id' => (int) $t['id'],
                    'name' => $t['name'],
                    'logo' => $this->imageUrl($t['logo']),
                ];
            }, $results);
        }

        // Search players (athletes)
        if (!$filter || $filter === 'athletes') {
            $results = $this->db->query("
                SELECT id, first_name, last_name, image_path
                FROM player
                WHERE is_active = 1
                  AND (first_name LIKE ? OR last_name LIKE ? OR nick_name LIKE ?)
                LIMIT 10
            ", [$searchTerm, $searchTerm, $searchTerm])->getResultArray();

            $athletes = array_map(function ($p) {
                return [
                    'id' => (int) $p['id'],
                    'name' => trim($p['first_name'] . ' ' . $p['last_name']),
                    'photo' => $this->imageUrl($p['image_path']),
                ];
            }, $results);
        }

        return $this->successResponse([
            'competitions' => $competitions,
            'competitors' => $competitors,
            'athletes' => $athletes,
        ]);
    }
}
