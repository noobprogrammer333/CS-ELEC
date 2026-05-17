<?php

require_once __DIR__ . '/SupabaseAuthClient.php';

function currentUser(): ?array
{
    return $_SESSION['noteai_user'] ?? null;
}

function isAuthenticated(): bool
{
    return !empty($_SESSION['noteai_access_token']) && currentUser() !== null;
}

function requireAuth(): void
{
    if (!isAuthenticated()) {
        header('Location: login.php');
        exit;
    }
}

function requireAuthJson(): void
{
    if (!isAuthenticated()) {
        sendJson(['error' => 'Authentication required. Please log in again.'], 401);
    }
}

function redirectIfAuthenticated(): void
{
    if (isAuthenticated()) {
        header('Location: index.php');
        exit;
    }
}

function storeAuthSession(array $authData): void
{
    $user = $authData['user'] ?? null;

    if (!$user && isset($authData['id'])) {
        $user = $authData;
    }

    $_SESSION['noteai_access_token'] = $authData['access_token'] ?? '';
    $_SESSION['noteai_refresh_token'] = $authData['refresh_token'] ?? '';
    $_SESSION['noteai_user'] = [
        'id' => $user['id'] ?? '',
        'email' => $user['email'] ?? '',
    ];
}

function clearAuthSession(): void
{
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }

    session_destroy();
}
