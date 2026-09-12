<?php
// ---- Global configuration ----
define('API_BASE_URL', getenv('HEHA_API_URL') ?: 'http://localhost:5000/api');

/*
 * NOTE ON STATE: this app originally used PHP $_SESSION for the login token and
 * cart. On serverless hosts (Vercel) requests can land on different, stateless
 * instances, so native sessions aren't reliable. Everything below uses signed,
 * client-side cookies instead — works the same on a normal server (Render) or
 * a serverless one (Vercel).
 */

const COOKIE_TOKEN = 'heha_token';
const COOKIE_USER  = 'heha_user';
const COOKIE_CART  = 'heha_cart';
const COOKIE_TTL   = 60 * 60 * 24 * 7; // 7 days

function _cookie_set(string $name, string $value): void
{
    setcookie($name, $value, [
        'expires' => time() + COOKIE_TTL,
        'path' => '/',
        'secure' => !empty($_SERVER['HTTPS']),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    // make it available immediately in this same request too
    $_COOKIE[$name] = $value;
}

function _cookie_clear(string $name): void
{
    setcookie($name, '', ['expires' => time() - 3600, 'path' => '/']);
    unset($_COOKIE[$name]);
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

    $token = $_COOKIE[COOKIE_TOKEN] ?? null;
    if ($auth && !empty($token)) {
        $headers[] = 'Authorization: Bearer ' . $token;
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
    return !empty($_COOKIE[COOKIE_TOKEN]);
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
    $raw = $_COOKIE[COOKIE_USER] ?? null;
    if (!$raw) {
        return null;
    }
    $decoded = json_decode($raw, true);
    return is_array($decoded) ? $decoded : null;
}

function set_auth(string $token, array $user): void
{
    _cookie_set(COOKIE_TOKEN, $token);
    _cookie_set(COOKIE_USER, json_encode($user));
}

function clear_auth(): void
{
    _cookie_clear(COOKIE_TOKEN);
    _cookie_clear(COOKIE_USER);
}

function get_cart(): array
{
    $raw = $_COOKIE[COOKIE_CART] ?? null;
    if (!$raw) {
        return [];
    }
    $decoded = json_decode($raw, true);
    return is_array($decoded) ? $decoded : [];
}

function set_cart(array $cart): void
{
    _cookie_set(COOKIE_CART, json_encode($cart));
}

function clear_cart(): void
{
    _cookie_clear(COOKIE_CART);
}
