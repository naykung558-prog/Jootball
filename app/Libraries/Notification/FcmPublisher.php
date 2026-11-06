<?php

namespace App\Libraries\Notification;

use Firebase\JWT\JWT;
use RuntimeException;

class FcmPublisher
{
    private string $projectId;
    private string $serviceAccountFile;
    private ?string $accessToken = null;
    private int $accessTokenExpiresAt = 0;

    public function __construct(?string $projectId = null, ?string $serviceAccountFile = null)
    {
        $this->projectId = $projectId ?: (string) env('FCM_PROJECT_ID', '');
        $this->serviceAccountFile = $serviceAccountFile ?: (string) env('FCM_SERVICE_ACCOUNT_FILE', '');

        if ($this->projectId === '') {
            throw new RuntimeException('FCM_PROJECT_ID is not configured');
        }

        if ($this->serviceAccountFile === '' || !is_file($this->serviceAccountFile)) {
            throw new RuntimeException('FCM_SERVICE_ACCOUNT_FILE is missing or invalid');
        }
    }

    public function sendToToken(string $token, string $title, string $body, array $data = [], array $options = []): array
    {
        $accessToken = $this->getAccessToken();
        $payload = [
            'message' => [
                'token' => $token,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                ],
                'data' => $this->normalizeData($data),
            ],
        ];

        if (!empty($options['android'])) {
            $payload['message']['android'] = $options['android'];
        }

        if (!empty($options['apns'])) {
            $payload['message']['apns'] = $options['apns'];
        }

        return $this->postJson(
            sprintf('https://fcm.googleapis.com/v1/projects/%s/messages:send', $this->projectId),
            $payload,
            [
                'Authorization: Bearer ' . $accessToken,
                'Content-Type: application/json',
            ],
        );
    }

    public function sendToTokens(array $tokens, string $title, string $body, array $data = []): array
    {
        $results = [];

        foreach ($tokens as $token) {
            $token = trim((string) $token);
            if ($token === '') {
                continue;
            }

            $results[] = [
                'token' => $token,
                'result' => $this->sendToToken($token, $title, $body, $data),
            ];
        }

        return $results;
    }

    private function getAccessToken(): string
    {
        if ($this->accessToken !== null && $this->accessTokenExpiresAt > time() + 60) {
            return $this->accessToken;
        }

        $serviceAccount = json_decode((string) file_get_contents($this->serviceAccountFile), true);
        if (!is_array($serviceAccount)) {
            throw new RuntimeException('Invalid FCM service account JSON');
        }

        $clientEmail = $serviceAccount['client_email'] ?? '';
        $privateKey = $serviceAccount['private_key'] ?? '';

        if ($clientEmail === '' || $privateKey === '') {
            throw new RuntimeException('Service account is missing client_email or private_key');
        }

        $now = time();
        $jwt = JWT::encode([
            'iss' => $clientEmail,
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud' => 'https://oauth2.googleapis.com/token',
            'iat' => $now,
            'exp' => $now + 3600,
        ], $privateKey, 'RS256');

        $response = $this->postForm('https://oauth2.googleapis.com/token', [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt,
        ]);

        $accessToken = $response['access_token'] ?? null;
        if (!$accessToken) {
            throw new RuntimeException('Failed to retrieve Google access token');
        }

        $expiresIn = (int) ($response['expires_in'] ?? 3600);
        $this->accessToken = (string) $accessToken;
        $this->accessTokenExpiresAt = time() + max(60, $expiresIn);

        return $this->accessToken;
    }

    private function normalizeData(array $data): array
    {
        $result = [];
        foreach ($data as $key => $value) {
            if ($value === null) {
                continue;
            }
            $result[(string) $key] = is_bool($value) ? ($value ? 'true' : 'false') : (string) $value;
        }
        return $result;
    }

    private function postJson(string $url, array $payload, array $headers): array
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE),
        ]);

        $response = curl_exec($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            throw new RuntimeException('FCM request failed: ' . $error);
        }

        $decoded = json_decode((string) $response, true);
        if ($httpCode >= 300) {
            $message = is_array($decoded) ? ($decoded['error']['message'] ?? $response) : $response;
            throw new RuntimeException('FCM send failed: ' . $message);
        }

        return is_array($decoded) ? $decoded : ['raw' => $response];
    }

    private function postForm(string $url, array $fields): array
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
            CURLOPT_POSTFIELDS => http_build_query($fields),
        ]);

        $response = curl_exec($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            throw new RuntimeException('OAuth token request failed: ' . $error);
        }

        $decoded = json_decode((string) $response, true);
        if ($httpCode >= 300) {
            $message = is_array($decoded) ? ($decoded['error_description'] ?? $decoded['error'] ?? $response) : $response;
            throw new RuntimeException('OAuth token request failed: ' . $message);
        }

        return is_array($decoded) ? $decoded : [];
    }
}
