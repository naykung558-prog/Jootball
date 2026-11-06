<?php

namespace Modules\Api\Controllers\V1;

use Modules\Api\Controllers\BaseApiController;

class NotificationController extends BaseApiController
{
    protected $db;

    private const DEFAULT_PREFERENCES = [
        'teamKickoff' => true,
        'teamLineups' => true,
        'teamGoals' => true,
        'teamRedCards' => true,
        'teamSubstitutions' => false,
        'teamFinalScore' => true,
        'competitionMatches' => true,
        'competitionFinalScores' => true,
        'competitionStandings' => false,
        'playerLineups' => true,
        'playerGoals' => true,
        'playerRedCards' => true,
        'playerCards' => false,
        'playerSubstitutions' => false,
    ];

    private const PREFERENCE_COLUMNS = [
        'teamKickoff' => 'team_kickoff',
        'teamLineups' => 'team_lineups',
        'teamGoals' => 'team_goals',
        'teamRedCards' => 'team_red_cards',
        'teamSubstitutions' => 'team_substitutions',
        'teamFinalScore' => 'team_final_score',
        'competitionMatches' => 'competition_matches',
        'competitionFinalScores' => 'competition_final_scores',
        'competitionStandings' => 'competition_standings',
        'playerLineups' => 'player_lineups',
        'playerGoals' => 'player_goals',
        'playerRedCards' => 'player_red_cards',
        'playerCards' => 'player_cards',
        'playerSubstitutions' => 'player_substitutions',
    ];

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    /**
     * POST /api/v1/notification/devices
     * Register or refresh a mobile FCM token for the authenticated user.
     */
    public function registerDevice()
    {
        $authUser = $this->getAuthUser();
        if (!$authUser || empty($authUser->uid)) {
            return $this->errorResponse('Unauthorized', 401);
        }

        $json = $this->request->getJSON(true) ?: [];
        $token = trim((string) ($json['token'] ?? $this->request->getPost('token') ?? ''));
        $platform = strtolower(trim((string) ($json['platform'] ?? $this->request->getPost('platform') ?? '')));
        $timezone = trim((string) ($json['timezone'] ?? $this->request->getPost('timezone') ?? 'Asia/Bangkok'));
        $locale = trim((string) ($json['locale'] ?? $this->request->getPost('locale') ?? 'th'));

        if ($token === '') {
            return $this->errorResponse('Device token is required', 422);
        }

        if (!in_array($platform, ['ios', 'android'], true)) {
            return $this->errorResponse('Platform must be ios or android', 422);
        }

        if ($timezone === '') {
            $timezone = 'Asia/Bangkok';
        }

        if ($locale === '') {
            $locale = 'th';
        }

        $now = date('Y-m-d H:i:s');
        $tokenHash = hash('sha256', $token);
        $userId = (int) $authUser->uid;
        $userType = (string) ($authUser->user_type ?? 'user');

        $existing = $this->db->table('notification_devices')
            ->where('token_hash', $tokenHash)
            ->get()
            ->getRowArray();

        $payload = [
            'user_id' => $userId,
            'user_type' => $userType,
            'token' => $token,
            'token_hash' => $tokenHash,
            'platform' => $platform,
            'timezone' => $timezone,
            'locale' => $locale,
            'enabled' => 1,
            'last_seen_at' => $now,
            'updated_at' => $now,
        ];

        if ($existing) {
            $this->db->table('notification_devices')
                ->where('id', $existing['id'])
                ->update($payload);
            $deviceId = (int) $existing['id'];
        } else {
            $payload['created_at'] = $now;
            $this->db->table('notification_devices')->insert($payload);
            $deviceId = (int) $this->db->insertID();
        }

        return $this->successResponse([
            'id' => $deviceId,
            'token' => $token,
            'platform' => $platform,
            'timezone' => $timezone,
            'locale' => $locale,
            'enabled' => true,
            'updatedAt' => $now,
        ], 'Device registered');
    }

    /**
     * DELETE /api/v1/notification/devices/:token
     * Disable a device token for the authenticated user.
     */
    public function deleteDevice($encodedToken = null)
    {
        $authUser = $this->getAuthUser();
        if (!$authUser || empty($authUser->uid)) {
            return $this->errorResponse('Unauthorized', 401);
        }

        $json = $this->request->getJSON(true) ?: [];
        $token = $encodedToken !== null
            ? urldecode((string) $encodedToken)
            : trim((string) ($json['token'] ?? $this->request->getPost('token') ?? ''));

        if ($token === '') {
            return $this->errorResponse('Device token is required', 422);
        }

        $this->db->table('notification_devices')
            ->where('token_hash', hash('sha256', $token))
            ->where('user_id', (int) $authUser->uid)
            ->where('user_type', (string) ($authUser->user_type ?? 'user'))
            ->update([
                'enabled' => 0,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        return $this->successResponse(null, 'Device disabled');
    }

    /**
     * GET /api/v1/notification/preferences
     */
    public function preferences()
    {
        $authUser = $this->getAuthUser();
        if (!$authUser || empty($authUser->uid)) {
            return $this->errorResponse('Unauthorized', 401);
        }

        $row = $this->getPreferenceRow((int) $authUser->uid, (string) ($authUser->user_type ?? 'user'));

        return $this->successResponse($this->formatPreferences($row));
    }

    /**
     * PUT /api/v1/notification/preferences
     */
    public function updatePreferences()
    {
        $authUser = $this->getAuthUser();
        if (!$authUser || empty($authUser->uid)) {
            return $this->errorResponse('Unauthorized', 401);
        }

        $json = $this->request->getJSON(true) ?: [];
        $userId = (int) $authUser->uid;
        $userType = (string) ($authUser->user_type ?? 'user');
        $now = date('Y-m-d H:i:s');

        $payload = [
            'user_id' => $userId,
            'user_type' => $userType,
            'updated_at' => $now,
        ];

        foreach (self::PREFERENCE_COLUMNS as $apiKey => $column) {
            if (array_key_exists($apiKey, $json)) {
                $payload[$column] = $this->toBooleanInt($json[$apiKey]);
            }
        }

        $existing = $this->db->table('notification_preferences')
            ->where('user_id', $userId)
            ->where('user_type', $userType)
            ->get()
            ->getRowArray();

        if ($existing) {
            $this->db->table('notification_preferences')
                ->where('id', $existing['id'])
                ->update($payload);
        } else {
            foreach (self::DEFAULT_PREFERENCES as $apiKey => $value) {
                $column = self::PREFERENCE_COLUMNS[$apiKey];
                if (!array_key_exists($column, $payload)) {
                    $payload[$column] = $value ? 1 : 0;
                }
            }
            $payload['created_at'] = $now;
            $this->db->table('notification_preferences')->insert($payload);
        }

        $row = $this->getPreferenceRow($userId, $userType);

        return $this->successResponse($this->formatPreferences($row), 'Preferences updated');
    }

    /**
     * POST /api/v1/notification/favorites/sync
     */
    public function syncFavorites()
    {
        $authUser = $this->getAuthUser();
        if (!$authUser || empty($authUser->uid)) {
            return $this->errorResponse('Unauthorized', 401);
        }

        $json = $this->request->getJSON(true) ?: [];
        $userId = (int) $authUser->uid;
        $userType = (string) ($authUser->user_type ?? 'user');
        $now = date('Y-m-d H:i:s');

        $items = [];
        foreach (['teams' => 'team', 'competitions' => 'competition', 'players' => 'player'] as $requestKey => $favoriteType) {
            foreach ($this->normalizeIds($json[$requestKey] ?? []) as $favoriteId) {
                $items[] = [
                    'user_id' => $userId,
                    'user_type' => $userType,
                    'favorite_type' => $favoriteType,
                    'favorite_id' => $favoriteId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        $this->db->transStart();
        $this->db->table('notification_favorites')
            ->where('user_id', $userId)
            ->where('user_type', $userType)
            ->delete();

        if (!empty($items)) {
            $this->db->table('notification_favorites')->insertBatch($items);
        }
        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return $this->errorResponse('Failed to sync favorites', 500);
        }

        return $this->successResponse([
            'teams' => count($this->normalizeIds($json['teams'] ?? [])),
            'competitions' => count($this->normalizeIds($json['competitions'] ?? [])),
            'players' => count($this->normalizeIds($json['players'] ?? [])),
        ], 'Favorites synced');
    }

    private function getPreferenceRow(int $userId, string $userType): array
    {
        $row = $this->db->table('notification_preferences')
            ->where('user_id', $userId)
            ->where('user_type', $userType)
            ->get()
            ->getRowArray();

        if ($row) {
            return $row;
        }

        $now = date('Y-m-d H:i:s');
        $payload = [
            'user_id' => $userId,
            'user_type' => $userType,
            'created_at' => $now,
            'updated_at' => $now,
        ];

        foreach (self::DEFAULT_PREFERENCES as $apiKey => $value) {
            $payload[self::PREFERENCE_COLUMNS[$apiKey]] = $value ? 1 : 0;
        }

        $this->db->table('notification_preferences')->insert($payload);
        $payload['id'] = $this->db->insertID();

        return $payload;
    }

    private function formatPreferences(array $row): array
    {
        $preferences = [];

        foreach (self::PREFERENCE_COLUMNS as $apiKey => $column) {
            $preferences[$apiKey] = (bool) ($row[$column] ?? self::DEFAULT_PREFERENCES[$apiKey]);
        }

        return $preferences;
    }

    private function normalizeIds($value): array
    {
        if (!is_array($value)) {
            return [];
        }

        $ids = [];
        foreach ($value as $id) {
            $intId = (int) $id;
            if ($intId > 0) {
                $ids[$intId] = $intId;
            }
        }

        return array_values($ids);
    }

    private function toBooleanInt($value): int
    {
        if (is_bool($value)) {
            return $value ? 1 : 0;
        }

        if (is_numeric($value)) {
            return ((int) $value) === 1 ? 1 : 0;
        }

        return in_array(strtolower((string) $value), ['true', 'yes', 'on'], true) ? 1 : 0;
    }
}
