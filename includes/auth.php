<?php
ini_set('session.cookie_secure',   '1');
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_samesite', 'Lax');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class KeycloakAuth {
    private $config;
    private $baseUrl;

    public function __construct() {
        $this->config  = include __DIR__ . '/../config/keycloak.php';
        $this->baseUrl = $this->config['auth-server-url'] . '/realms/' . $this->config['realm'];
    }

    public function getLoginUrl() {
        $params = [
            'client_id'     => $this->config['resource'],
            'redirect_uri'  => $this->config['redirect_uri'],
            'response_type' => 'code',
            'scope'         => 'openid profile email'
        ];
        return $this->baseUrl . '/protocol/openid-connect/auth?' . http_build_query($params);
    }

    public function getLogoutUrl() {
        $params = ['redirect_uri' => $this->config['logout_uri']];
        return $this->baseUrl . '/protocol/openid-connect/logout?' . http_build_query($params);
    }

    // Remplace file_get_contents par cURL
    private function httpPost($url, $data) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query($data),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false, // CA interne non reconnue
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/x-www-form-urlencoded'],
        ]);
        $response = curl_exec($ch);
        $error    = curl_error($ch);
        curl_close($ch);

        if ($error) {
            error_log("cURL POST error: " . $error);
            return null;
        }
        return json_decode($response, true);
    }

    private function httpGet($url, $token) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . $token],
        ]);
        $response = curl_exec($ch);
        $error    = curl_error($ch);
        curl_close($ch);

        if ($error) {
            error_log("cURL GET error: " . $error);
            return null;
        }
        return json_decode($response, true);
    }

    public function exchangeCodeForToken($code) {
        $tokenUrl = $this->baseUrl . '/protocol/openid-connect/token';
        $data = [
            'grant_type'    => 'authorization_code',
            'client_id'     => $this->config['resource'],
            'client_secret' => $this->config['credentials']['secret'],
            'code'          => $code,
            'redirect_uri'  => $this->config['redirect_uri']
        ];
        return $this->httpPost($tokenUrl, $data);
    }

    public function getUserInfo($accessToken) {
        $userInfoUrl = $this->baseUrl . '/protocol/openid-connect/userinfo';
        return $this->httpGet($userInfoUrl, $accessToken);
    }

    public function isAuthenticated() {
        return isset($_SESSION['user']) && isset($_SESSION['access_token']);
    }

    public function requireAuth() {
        if (!$this->isAuthenticated()) {
            header('Location: login.php');
            exit();
        }
    }

    public function getCurrentUser() {
        return $_SESSION['user'] ?? null;
    }
}
