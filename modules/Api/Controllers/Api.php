<?php

namespace Modules\Api\Controllers;

use App\Controllers\BaseController;
use \Firebase\JWT\JWT;
use Modules\Admin\Models\MemberModel;
use Modules\Api\Models\ApiModel;
use CodeIgniter\API\ResponseTrait;

class Api extends BaseController
{
    use ResponseTrait;
    protected $memberModel;
    protected $apiModel;
    protected $db;
    public function __construct()
    {
        $this->apiModel = new ApiModel();
        $this->memberModel = new MemberModel();
        $this->db = db_connect();
    }

    public function getToken()
    {
        $username = $this->request->getVar('username');
        $password = $this->request->getVar('password');

        $user = $this->memberModel->verifyUser($username, $password, 'api');

        if (is_null($user)) {
            return $this->respond(['error' => 'Invalid username or password.'], 401);
        }

        $key = getenv('JWT_SECRET');
        $iat = time(); // current timestamp value
        $exp = $iat + 3600;

        $payload = array(
            // "iss" => "Issuer of the JWT",
            // "aud" => "Audience that the JWT",
            // "sub" => "Subject of the JWT",
            "iat" => $iat, //Time the JWT issued at
            "exp" => $exp, // Expiration time of token
            "username" => $user['username'],
        );

        $token = JWT::encode($payload, $key, 'HS256');

        $response = [
            'message' => 'Succesful',
            'token' => $token
        ];

        return $this->respond(json_encode($response, JSON_NUMERIC_CHECK), 200);
    }

    public function getEvent()
    {
        $data = $this->apiModel->getEvents();
        return $this->respond(json_encode($data, JSON_NUMERIC_CHECK), 200);
    }

    public function getMatchResult($match_id)
    {
        $jootball_match_id = $this->mapping_matchFocal_to_matchJootball($match_id);
        $data = $this->apiModel->getMatchResult($jootball_match_id);
        return $this->respond(json_encode($data, JSON_NUMERIC_CHECK), 200);
    }

    public function postEndMatch($jootball_match_id)
    {
        $data = $this->apiModel->getMatchResult($jootball_match_id);

        $events = [];
        foreach ($data['events'] as $row) {
            $event = [
                "minute" => $row['minute'],
                "teamId" => $row['teamId'],
                "playerMainActionId" => $row['playerMainAction']['id'],
                "action" => $row['action']
            ];
            array_push($events, $event);
        }

        $matchData = [
            "matchId" => $data['match_id'],
            "result" => [
                [
                    "id" => $data['home_team_id'],
                    "score" => $data['home_team_score']
                ],
                [
                    "id" => $data['away_team_id'],
                    "score" => $data['away_team_score']
                ]
            ],
            "events" => $events
        ];

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => env('FOCAL_API_URL') . '/end-match',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($matchData, JSON_NUMERIC_CHECK),
            CURLOPT_HTTPHEADER => array(
                'x-api-key: ' . env('FOCAL_API_KEY'),
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        return json_encode($response);
    }

    public function getPlayerState($player_id)
    {
        $jootball_player_id = $this->mapping_playerFocal_to_playerJootball($player_id);
        $data = $this->apiModel->getPlayerState($jootball_player_id);
        if (!empty($data['name'])) {
            return $this->respond(json_encode($data, JSON_NUMERIC_CHECK), 200);
        } else {
            $response = [
                'msg' => 'Not Found Player'
            ];
            return $this->respond(json_encode($response, JSON_NUMERIC_CHECK), 200);
        }
    }

    function mapping_matchFocal_to_matchJootball($focal_match_id)
    {
        $builder = $this->db->table('mapping_glo_match');
        $jootball_match_id = $builder->select('jootball_match_id')->where('focal_match_id', $focal_match_id)->get()->getRowArray();
        if (!empty($jootball_match_id)) {
            return $jootball_match_id['jootball_match_id'];
        } else {
            return null;
        }
    }

    function mapping_playerFocal_to_playerJootball($focal_player_id)
    {
        $builder = $this->db->table('mapping_glo_player');
        $jootball_player_id = $builder->select('jootball_player_id')->where('focal_player_id', $focal_player_id)->get()->getRowArray();
        if (!empty($jootball_player_id)) {
            return $jootball_player_id['jootball_player_id'];
        } else {
            return null;
        }
    }

    public function getTournamentPlayers($tour_id)
    {
        if (empty($tour_id)) {
            return $this->failNotFound('Tournament ID is required');
        }

        // 1. Get Tournament Info
        $tour = $this->db->table('tournament')->select('name, name_en')->where('tour_id', $tour_id)->get()->getRowArray();
        if (!$tour) {
            return $this->failNotFound('Tournament not found');
        }

        // 3. Get Players in these Teams
        $players = $this->_getPlayersData($tour_id);

        $response = [
            'tournament' => $tour,
            'count' => count($players),
            'players' => $players
        ];

        return $this->respond($response, 200);
    }

    private function _getPlayersData($tour_id, $ignoreStatus = false, $maskLastName = true)
    {
        // Get Teams in Tournament
        $teamBuilder = $this->db->table('tournament_team')
            ->select('team_id')
            ->where('tour_id', $tour_id);

        if (!$ignoreStatus) {
            $teamBuilder->where('register_status', 1);
        }

        $teams = $teamBuilder->get()->getResultArray();

        $teamIds = array_column($teams, 'team_id');

        if (empty($teamIds)) {
            return [];
        }

        // Get Players in these Teams
        $builder = $this->db->table('player');
        $builder->select("
            player.id as player_id,
            player.first_name,
            player.last_name,
            player.nick_name,
            pt_api.shirt_number as shirt_number,
            pt_api.team_id,
            team.name as team_name,
            coarse_api.name_th as position_th,
            coarse_api.name_en as position_en,
            player.image_path as player_image
        ");
        $builder->join('academy_player_enrollments pt_api', 'pt_api.player_id = player.id AND pt_api.is_active = 1', 'INNER');
        $builder->join('team', 'team.id = pt_api.team_id', 'left');
        $builder->join('tbl_player_position', 'tbl_player_position.id = pt_api.position_id', 'left');
        $builder->join('tbl_player_position coarse_api', 'coarse_api.id = COALESCE(NULLIF(tbl_player_position.parent_id,0), tbl_player_position.id)', 'left');
        $builder->whereIn('pt_api.team_id', $teamIds);
        $builder->groupBy('player.id, pt_api.team_id');
        if (!$ignoreStatus) {
            $builder->where('player.is_active', 1);
        }
        $builder->orderBy('team.name', 'ASC');
        $builder->orderBy('CAST(pt_api.shirt_number AS unsigned)', 'ASC');

        $players = $builder->get()->getResultArray();

        // Add full image URL and mask last name
        foreach ($players as &$player) {
            if (!empty($player['player_image'])) {
                $player['player_image'] = base_url($player['player_image']);
            }
            if ($maskLastName && !empty($player['last_name'])) {
                $player['last_name'] = mb_substr($player['last_name'], 0, 1) . '.';
            }
        }

        return $players;
    }
    public function getTournaments()
    {
        $tournModel = new \Modules\Admin\Models\TournamentModel();
        $data = $tournModel->get_TournamentList(null);

        $grouped = [];

        foreach ($data as $tour) {
            $name = $tour['name'];

            // Prepare full logo URL
            $fullLogoUrl = !empty($tour['logo']) ? base_url($tour['logo']) : null;
            $tour['logo'] = $fullLogoUrl; // Update item for the generations list

            if (!isset($grouped[$name])) {
                $grouped[$name] = [
                    'name' => $tour['name'],
                    'name_en' => $tour['name_en'],
                    'logo' => $fullLogoUrl,
                    'generations' => []
                ];
            }

            $grouped[$name]['generations'][] = $tour;
        }

        return $this->respond(array_values($grouped), 200);
    }

    public function getAllData($ids = null)
    {
        $tournModel = new \Modules\Admin\Models\TournamentModel();
        $data = $tournModel->get_TournamentList(null);

        $targetNames = [];
        $idsToFilter = [];

        // Parse IDs (supports single ID string or JSON array string)
        if ($ids !== null) {
            $ids = urldecode($ids); // Decode URL encoded string
            $decoded = json_decode($ids, true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $idsToFilter = array_map(function($v) { return (string)$v; }, $decoded);
            } else {
                $idsToFilter = [(string)$ids];
            }

            if (!empty($idsToFilter)) {
                foreach ($data as $tour) {
                    if (in_array((string)$tour['tour_id'], $idsToFilter)) {
                        if (!in_array($tour['name'], $targetNames)) {
                            $targetNames[] = $tour['name'];
                        }
                    }
                }

                // If IDs provided but no matches found
                if (empty($targetNames)) {
                    return $this->respond(['error' => 'Tournament IDs not found'], 404);
                }
            }
        }

        $grouped = [];

        foreach ($data as $tour) {
            $name = $tour['name'];

            // If filtering, skip if name doesn't match ANY of the target names
            if (!empty($targetNames) && !in_array($name, $targetNames)) {
                continue;
            }

            $current_tour_id = $tour['tour_id'];

            // Prepare full logo URL
            $fullLogoUrl = !empty($tour['logo']) ? base_url($tour['logo']) : null;
            $tour['logo'] = $fullLogoUrl;

            // Get players for this tournament version/generation (ignore status filters)
            $players = $this->_getPlayersData($current_tour_id, true, false);
            $tour['players'] = $players;

            if (!isset($grouped[$name])) {
                $grouped[$name] = [
                    'name' => $tour['name'],
                    'name_en' => $tour['name_en'],
                    'logo' => $fullLogoUrl,
                    'generations' => []
                ];
            }

            $grouped[$name]['generations'][] = $tour;
        }

        return $this->respond(array_values($grouped), 200);
    }
}
