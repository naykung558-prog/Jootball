<?php

namespace Modules\Api\Controllers\V1;

use Modules\Api\Controllers\BaseApiController;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthController extends BaseApiController
{
    /**
     * POST /api/v1/auth/login
     * Login with username/password (supports member, user, coach)
     */
    public function login()
    {
        $json = $this->request->getJSON(true) ?: [];
        $username = $json['username'] ?? $this->request->getPost('username');
        $password = $json['password'] ?? $this->request->getPost('password');

        if (empty($username) || empty($password)) {
            return $this->errorResponse('Username and password are required', 422);
        }

        $db = \Config\Database::connect();

        // Check member (admin/staff)
        $member = $db->table('member')
            ->where('username', $username)
            ->where('access_login', 1)
            ->get()
            ->getRowArray();

        if ($member && !empty($member['password']) && $this->verifyPassword($password, $member['password'])) {
            $token = $this->generateToken($member['id'], 'member', $member['username']);
            $refreshToken = $this->generateRefreshToken($member['id'], 'member');

            return $this->successResponse([
                'accessToken' => $token,
                'refreshToken' => $refreshToken,
                'tokenType' => 'Bearer',
                'expiresIn' => 3600,
                'user' => [
                    'id' => (int) $member['id'],
                    'username' => $member['username'],
                    'name' => $member['username'],
                    'profileImage' => null,
                    'type' => 'member',
                ]
            ], 'Login successful');
        }

        // Check regular user
        $user = $db->table('user')
            ->where('username', $username)
            ->get()
            ->getRowArray();

        if ($user && !empty($user['password']) && $this->verifyPassword($password, $user['password'])) {
            $token = $this->generateToken($user['id'], 'user', $user['username']);
            $refreshToken = $this->generateRefreshToken($user['id'], 'user');

            return $this->successResponse([
                'accessToken' => $token,
                'refreshToken' => $refreshToken,
                'tokenType' => 'Bearer',
                'expiresIn' => 3600,
                'user' => [
                    'id' => (int) $user['id'],
                    'username' => $user['username'],
                    'name' => $user['name'] ?? $user['username'],
                    'profileImage' => $this->imageUrl($user['profile_image'] ?? ''),
                    'type' => 'user',
                ]
            ], 'Login successful');
        }

        // Check coach user (table may not exist)
        try {
            $coach = $db->table('coach_users')
                ->where('username', $username)
                ->where('is_active', 1)
                ->get()
                ->getRowArray();

            if ($coach && !empty($coach['password']) && $this->verifyPassword($password, $coach['password'])) {
                $token = $this->generateToken($coach['id'], 'coach', $coach['username']);
                $refreshToken = $this->generateRefreshToken($coach['id'], 'coach');

                return $this->successResponse([
                    'accessToken' => $token,
                    'refreshToken' => $refreshToken,
                    'tokenType' => 'Bearer',
                    'expiresIn' => 3600,
                    'user' => [
                        'id' => (int) $coach['id'],
                        'username' => $coach['username'],
                        'name' => $coach['line_name'] ?? $coach['username'],
                        'profileImage' => $coach['line_picture'] ?? null,
                        'type' => 'coach',
                    ]
                ], 'Login successful');
            }
        } catch (\Exception $e) {
            // coach_users table may not exist - skip
        }

        return $this->errorResponse('Invalid username or password', 401);
    }

    /**
     * POST /api/v1/auth/login-line
     * Login with LINE access token (for mobile app)
     *
     * Flow: Mobile LINE SDK → access_token → verify with LINE API → find/create user → JWT
     */
    public function loginLine()
    {
        $json = $this->request->getJSON(true) ?: [];
        $accessToken = $json['access_token'] ?? $this->request->getPost('access_token');

        if (empty($accessToken)) {
            return $this->errorResponse('access_token is required', 422);
        }

        // 1. Verify token with LINE
        $verifyResponse = $this->lineApiRequest(
            'https://api.line.me/oauth2/v2.1/verify?access_token=' . urlencode($accessToken)
        );

        if (!$verifyResponse || isset($verifyResponse['error'])) {
            return $this->errorResponse('Invalid LINE access token', 401);
        }

        // Verify client_id matches our LINE app
        $lineClientId = $_SERVER['LINE_CLIENT_ID'] ?? '';
        if ($lineClientId && isset($verifyResponse['client_id']) && $verifyResponse['client_id'] !== $lineClientId) {
            return $this->errorResponse('LINE token does not belong to this app', 401);
        }

        // 2. Get LINE profile
        $profile = $this->lineApiRequest(
            'https://api.line.me/v2/profile',
            ['Authorization: Bearer ' . $accessToken]
        );

        if (!$profile || !isset($profile['userId'])) {
            return $this->errorResponse('Failed to get LINE profile', 401);
        }

        $lineUserId = $profile['userId'];
        $lineName = $profile['displayName'] ?? '';
        $linePicture = $profile['pictureUrl'] ?? '';

        $db = \Config\Database::connect();

        // 3. Find user by uid_line
        $user = $db->table('user')
            ->where('uid_line', $lineUserId)
            ->get()
            ->getRowArray();

        // 4. Create new user if not found
        if (!$user) {
            $db->table('user')->insert([
                'name' => $lineName,
                'uid_line' => $lineUserId,
                'profile_image' => $linePicture,
                'create_date' => date('Y-m-d H:i:s'),
            ]);
            $newId = $db->insertID();

            $user = $db->table('user')
                ->where('id', $newId)
                ->get()
                ->getRowArray();
        } else {
            // Update profile image if changed
            if ($linePicture && ($user['profile_image'] !== $linePicture)) {
                $db->table('user')
                    ->where('id', $user['id'])
                    ->update(['profile_image' => $linePicture]);
                $user['profile_image'] = $linePicture;
            }
        }

        // 5. Generate JWT
        $token = $this->generateToken($user['id'], 'user', $user['username'] ?? '');
        $refreshToken = $this->generateRefreshToken($user['id'], 'user');

        return $this->successResponse([
            'accessToken' => $token,
            'refreshToken' => $refreshToken,
            'tokenType' => 'Bearer',
            'expiresIn' => 3600,
            'user' => [
                'id' => (int) $user['id'],
                'username' => $user['username'] ?? null,
                'name' => $user['name'] ?? $lineName,
                'profileImage' => $user['profile_image'] ? $this->imageUrl($user['profile_image']) : ($linePicture ?: null),
                'lineUid' => $lineUserId,
                'type' => 'user',
            ]
        ], 'Login successful');
    }

    /**
     * GET /api/v1/profile
     * Get current user profile from JWT token
     */
    public function profile()
    {
        $authUser = $this->getAuthUser();
        if (!$authUser) {
            return $this->errorResponse('Unauthorized', 401);
        }

        $db = \Config\Database::connect();

        if ($authUser->user_type === 'coach') {
            try {
                $coach = $db->query("
                    SELECT cu.id AS coach_user_id, cu.username, cu.email, cu.line_name, cu.line_picture,
                           c.id AS coach_id, c.first_name, c.last_name, c.nick_name, c.image_path
                    FROM coach_users cu
                    LEFT JOIN coach c ON c.coach_user_id = cu.id
                    WHERE cu.id = ?
                ", [$authUser->uid])->getRowArray();

                if (!$coach) {
                    return $this->notFoundResponse('Coach not found');
                }

                $name = trim(($coach['first_name'] ?? '') . ' ' . ($coach['last_name'] ?? ''));
                if (empty($name)) {
                    $name = $coach['line_name'] ?? $coach['username'];
                }

                return $this->successResponse([
                    'id' => (int) $coach['coach_user_id'],
                    'username' => $coach['username'],
                    'name' => $name,
                    'profileImage' => !empty($coach['image_path']) ? $this->imageUrl($coach['image_path']) : ($coach['line_picture'] ?? null),
                    'email' => $coach['email'] ?? null,
                    'type' => 'coach',
                ]);
            } catch (\Exception $e) {
                return $this->notFoundResponse('Coach not found');
            }
        }

        // Regular user
        $user = $db->table('user')
            ->where('id', $authUser->uid)
            ->get()
            ->getRowArray();

        if (!$user) {
            return $this->notFoundResponse('User not found');
        }

        return $this->successResponse([
            'id' => (int) $user['id'],
            'username' => $user['username'] ?? null,
            'name' => $user['name'] ?? '',
            'profileImage' => $user['profile_image'] ? $this->imageUrl($user['profile_image']) : null,
            'lineUid' => $user['uid_line'] ?? null,
            'type' => 'user',
        ]);
    }

    /**
     * POST /api/v1/auth/refresh
     * Refresh JWT token using refresh token
     */
    public function refresh()
    {
        $json = $this->request->getJSON(true) ?: [];
        $refreshToken = $json['refresh_token'] ?? $this->request->getPost('refresh_token');

        if (empty($refreshToken)) {
            return $this->errorResponse('refresh_token is required', 422);
        }
        $secret = getenv('JWT_SECRET') ?: 'jootball_jwt_secret_key';

        try {
            $decoded = JWT::decode($refreshToken, new Key($secret, 'HS256'));

            if ($decoded->type !== 'refresh') {
                return $this->errorResponse('Invalid refresh token', 401);
            }

            $newToken = $this->generateToken($decoded->uid, $decoded->user_type, $decoded->username ?? '');
            $newRefreshToken = $this->generateRefreshToken($decoded->uid, $decoded->user_type);

            return $this->successResponse([
                'accessToken' => $newToken,
                'refreshToken' => $newRefreshToken,
                'tokenType' => 'Bearer',
                'expiresIn' => 3600,
            ], 'Token refreshed');

        } catch (\Exception $e) {
            return $this->errorResponse('Invalid or expired refresh token', 401);
        }
    }

    /**
     * Make HTTP request to LINE API
     */
    private function lineApiRequest(string $url, array $headers = []): ?array
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        if (!empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200 || !$response) {
            return null;
        }

        return json_decode($response, true);
    }

    /**
     * Verify password - supports both bcrypt hash and plain text (legacy)
     */
    private function verifyPassword(string $input, string $stored): bool
    {
        if (password_verify($input, $stored)) {
            return true;
        }
        // Fallback: plain text comparison (legacy data)
        return $input === $stored;
    }

    /**
     * Generate access JWT token (1 hour)
     */
    private function generateToken(int $userId, string $userType, string $username = ''): string
    {
        $secret = getenv('JWT_SECRET') ?: 'jootball_jwt_secret_key';

        $payload = [
            'iss' => base_url(),
            'iat' => time(),
            'exp' => time() + 3600,
            'uid' => $userId,
            'user_type' => $userType,
            'username' => $username,
        ];

        return JWT::encode($payload, $secret, 'HS256');
    }

    /**
     * Generate refresh token (30 days)
     */
    private function generateRefreshToken(int $userId, string $userType): string
    {
        $secret = getenv('JWT_SECRET') ?: 'jootball_jwt_secret_key';

        $payload = [
            'iss' => base_url(),
            'iat' => time(),
            'exp' => time() + (30 * 24 * 3600),
            'uid' => $userId,
            'user_type' => $userType,
            'type' => 'refresh',
        ];

        return JWT::encode($payload, $secret, 'HS256');
    }
}
