<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;

class ApiGuard implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $authHeader = $request->getHeaderLine('Authorization');

        if (empty($authHeader) || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            return $this->unauthorizedResponse('Token not provided');
        }

        $token = $matches[1];
        $secret = getenv('JWT_SECRET') ?: 'jootball_jwt_secret_key';

        try {
            $decoded = JWT::decode($token, new Key($secret, 'HS256'));

            // Inject decoded user data into request for controllers
            $request->user = $decoded;

            // Check if token is near expiry (less than 5 minutes remaining)
            if (isset($decoded->exp) && ($decoded->exp - time()) < 300) {
                $request->tokenNearExpiry = true;
            }

        } catch (ExpiredException $e) {
            return $this->unauthorizedResponse('Token expired');
        } catch (\Exception $e) {
            return $this->unauthorizedResponse('Invalid token');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Add token refresh hint header if near expiry
        if (isset($request->tokenNearExpiry) && $request->tokenNearExpiry) {
            $response->setHeader('X-Token-Refresh', 'true');
        }
    }

    private function unauthorizedResponse(string $message)
    {
        $response = service('response');
        return $response->setStatusCode(401)->setJSON([
            'status' => 'error',
            'message' => $message
        ]);
    }
}
