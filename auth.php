<?php
declare(strict_types=1);

function ensure_session_started(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
}

function is_logged_in(): bool
{
    ensure_session_started();
    return isset($_SESSION['user_id']);
}

function current_user_type(): string
{
    ensure_session_started();
    $type = $_SESSION['user_type'] ?? 'user';
    return is_string($type) && $type !== '' ? $type : 'user';
}

function require_login(string $redirectTo = 'login.php'): void
{
    if (!is_logged_in()) {
        header('Location: ' . $redirectTo);
        exit;
    }
}

function require_admin(string $loginRedirectTo = 'login.php', string $nonAdminRedirectTo = 'userDashboard.php'): void
{
    require_login($loginRedirectTo);
    if (current_user_type() !== 'admin') {
        header('Location: ' . $nonAdminRedirectTo);
        exit;
    }
}

function login_user(array $userRow): void
{
    ensure_session_started();
    session_regenerate_id(true);

    $_SESSION['user_id'] = (int)($userRow['id'] ?? 0);
    $_SESSION['user_name'] = (string)($userRow['name'] ?? '');
    $_SESSION['user_email'] = (string)($userRow['email'] ?? '');

    $isAdmin = (int)($userRow['is_admin'] ?? 0);
    $_SESSION['user_type'] = $isAdmin === 1 ? 'admin' : 'user';
}

function logout_user(): void
{
    ensure_session_started();

    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            (bool)$params['secure'],
            (bool)$params['httponly']
        );
    }

    session_destroy();
}
