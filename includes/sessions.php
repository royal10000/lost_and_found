<?php

session_start([
    'cookie_lifetime' => 0,
    'cookie_httponly' => true,
    'cookie_samesite' => 'Lax',
    'use_strict_mode' => true,
    'use_only_cookies' => true
]);

function setSession(int $id, string $username, string $email, bool $isAdmin): void
{
    session_regenerate_id(true);
    $_SESSION['user'] = [
        'id' => $id,
        'username' => $username,
        'email' => $email,
        'is_admin' => $isAdmin
    ];
}


function message(string $message, string $type = "success"): void
{
    $_SESSION['toast'] = [
        'message' => $message,
        'type' => $type
    ];
}
function isLoggedIn(): bool
{
    return isset($_SESSION['user']);
}

function currentUser(): ?array
{
    return $_SESSION['user'] ?? null;
}

function logout(): void
{
    $_SESSION = [];

    if (ini_get("session.use_cookies")) {
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
