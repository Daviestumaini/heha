<?php
// ---- Global configuration ----
define('API_BASE_URL', getenv('HEHA_API_URL') ?: 'https://heha-agency.onrender.com');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Call the Python (Flask) backend API.
 *
 * @param string $method  GET|POST|PUT|DELETE
 * @param string $endpoint e.g. "/auth/login"
 * @param array|null $data  request body, will be sent as JSON
 * @param bool $auth  whether to attach the logged-in user's JWT
 * @return array ['status' => int, 'body' => array]
 */
function api_request(string $method, string $endpoint, ?array $data = null, bool $auth = true): array
{
    $ch = curl_init(API_BASE_URL . $endpoint);
    $headers = ['Content-Type: application/json'];

    if ($auth && !empty($_SESSION['token'])) {
        $headers[] = 'Authorization: Bearer ' . $_SESSION['token'];
    }

    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

    if ($data !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }

    $response = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) {
        return ['status' => 0, 'body' => ['error' => "Connection error: $error"]];
    }

    $decoded = json_decode($response, true);
    return ['status' => $status, 'body' => $decoded ?? []];
}

function is_logged_in(): bool
{
    return !empty($_SESSION['token']);
}

function require_login(): void
{
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}
