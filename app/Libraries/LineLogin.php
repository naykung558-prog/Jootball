<?php
namespace App\Libraries;

class LineLogin
{
    #### change your id
    // private const CLIENT_ID = '';
    // private const CLIENT_SECRET = '';
    // private const REDIRECT_URL = 'http://localhost:8888/glo_football/line/callback';

    private const AUTH_URL = 'https://access.line.me/oauth2/v2.1/login';
    private const AUTHORIZE_URL = 'https://access.line.me/oauth2/v2.1/authorize/consent';
    private const PROFILE_URL = 'https://api.line.me/v2/profile';
    private const TOKEN_URL = 'https://api.line.me/oauth2/v2.1/token';
    private const REVOKE_URL = 'https://api.line.me/oauth2/v2.1/revoke';
    private const VERIFYTOKEN_URL = 'https://api.line.me/oauth2/v2.1/verify';

    protected $CLIENT_ID;
    protected $CLIENT_SECRET;
    protected $REDIRECT_URL;

    public function __construct()
    {
        $this->CLIENT_ID = $_SERVER['LINE_CLIENT_ID'];
        $this->CLIENT_SECRET = $_SERVER['LINE_CLIENT_SECRET'];
        $this->REDIRECT_URL = $_SERVER['LINE_REDIRECT_URL'];
    }

    function getLink($userType = 'user')
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $stateHash = hash('sha256', microtime(TRUE) . rand() . $_SERVER['REMOTE_ADDR']);
        // เพิ่ม prefix user_type เพื่อแยกประเภทการ login
        $_SESSION['state'] = $userType . ':' . $stateHash;

        // สร้าง login state สำหรับ LINE login screen
        $loginState = bin2hex(random_bytes(16));

        // สร้าง authorize URL พร้อม parameters
        $authorizeParams = http_build_query([
            'response_type' => 'code',
            'client_id' => $this->CLIENT_ID,
            'redirect_uri' => $this->REDIRECT_URL,
            'scope' => 'profile openid email',
            'state' => $_SESSION['state']
        ]);

        $returnUri = '/oauth2/v2.1/authorize/consent?' . $authorizeParams;

        // สร้าง login URL แบบใหม่
        $link = self::AUTH_URL . '?' . http_build_query([
            'returnUri' => $returnUri,
            'loginChannelId' => $this->CLIENT_ID,
            'loginState' => $loginState
        ]);

        return $link;
    }

    function refresh($token)
    {
        $header = ['Content-Type: application/x-www-form-urlencoded'];
        $data = [
            "grant_type" => "refresh_token",
            "refresh_token" => $token,
            "client_id" => $this->CLIENT_ID,
            "client_secret" => $this->CLIENT_SECRET
        ];

        $response = $this->sendCURL(self::TOKEN_URL, $header, 'POST', $data);
        return json_decode($response);
    }

    function token($code, $state)
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SESSION['state'] != $state) {
            return false;
        }

        $header = ['Content-Type: application/x-www-form-urlencoded'];
        $data = [
            "grant_type" => "authorization_code",
            "code" => $code,
            "redirect_uri" => $this->REDIRECT_URL,
            "client_id" => $this->CLIENT_ID,
            "client_secret" => $this->CLIENT_SECRET
        ];

        $response = $this->sendCURL(self::TOKEN_URL, $header, 'POST', $data);
        return json_decode($response);
    }

    function profileFormIdToken($token = null)
    {
        $payload = explode('.', $token->id_token);
        $ret = array(
            'id_token' => $token->id_token,
            'access_token' => $token->access_token,
            'refresh_token' => $token->refresh_token ?? null,
            'name' => '',
            'picture' => '',
            'email' => '',
            'sub' => '',
            'line_uid' => ''
        );

        if (count($payload) == 3) {
            // JWT uses base64url encoding, need to convert to standard base64
            $base64Payload = $payload[1];
            // Replace base64url characters with standard base64 characters
            $base64Payload = str_replace(['-', '_'], ['+', '/'], $base64Payload);
            // Add padding if needed
            $padLength = 4 - (strlen($base64Payload) % 4);
            if ($padLength < 4) {
                $base64Payload .= str_repeat('=', $padLength);
            }

            $decodedPayload = base64_decode($base64Payload);
            $data = json_decode($decodedPayload);

            // Debug log if decode fails
            if (!$data) {
                log_message('error', 'LINE JWT Decode Error: Failed to decode payload. Raw payload (first 100 chars): ' . substr($payload[1], 0, 100));
            }

            if (isset($data->sub)) {
                $ret['sub'] = $data->sub;
                $ret['line_uid'] = $data->sub;
            }

            if (isset($data->name))
                $ret['name'] = $data->name;

            if (isset($data->picture))
                $ret['picture'] = $data->picture;

            if (isset($data->email))
                $ret['email'] = $data->email;
        } else {
            log_message('error', 'LINE JWT Error: Invalid token format. Expected 3 parts, got ' . count($payload));
        }
        return (object) $ret;
    }

    function profile($token)
    {
        $header = ['Authorization: Bearer ' . $token];
        $response = $this->sendCURL(self::PROFILE_URL, $header, 'GET');
        return json_decode($response);
    }

    function verify($token)
    {
        $url = self::VERIFYTOKEN_URL . '?access_token=' . $token;
        $response = $this->sendCURL($url, NULL, 'GET');
        return $response;
    }

    function revoke($token)
    {
        $header = ['Content-Type: application/x-www-form-urlencoded'];
        $data = [
            "access_token" => $token,
            "client_id" => $this->CLIENT_ID,
            "client_secret" => $this->CLIENT_SECRET
        ];
        $response = $this->sendCURL(self::REVOKE_URL, $header, 'POST', $data);
        return $response;
    }

    private function sendCURL($url, $header, $type, $data = NULL)
    {
        $request = curl_init();

        if ($header != NULL) {
            curl_setopt($request, CURLOPT_HTTPHEADER, $header);
        }

        curl_setopt($request, CURLOPT_URL, $url);
        curl_setopt($request, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($request, CURLOPT_SSL_VERIFYPEER, false);

        if (strtoupper($type) === 'POST') {
            curl_setopt($request, CURLOPT_POST, TRUE);
            curl_setopt($request, CURLOPT_POSTFIELDS, http_build_query($data));
        }

        curl_setopt($request, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($request, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($request);
        return $response;
    }
}
