<?php
class Session
{
    public static function start()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start([
                'cookie_lifetime' => 0,
                'cookie_httponly' => true,
                'cookie_samesite' => 'Lax',
                'use_strict_mode' => true,
                'use_only_cookies' => true
            ]);
        }
    }

    public static function login($user)
    {
        self::start();
        session_regenerate_id(true);

        $_SESSION['user'] = [
            'user_id'=>$user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
        ];
    }

      public static function isLoggedIn()
    {
        self::start();

        return isset($_SESSION['user']);
    }

    public static function logOut()
    {
        self::start();

        // Remove all session data
        $_SESSION = [];

        // Destroy the session
        session_destroy();
        
    }
}
