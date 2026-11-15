<?php
class Session{
    public static function start(){
        if (session_status() == PHP_SESSION_NONE){session_start([
    'cookie_lifetime' => 0,
    'cookie_httponly' => true,
    'cookie_samesite' => 'Lax',
    'use_strict_mode' => true,
    'use_only_cookies' => true
]);}
    }

    public static function login($user){
        self::start();
        session_regenerate_id();
    }
}
?>