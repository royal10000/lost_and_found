<?php
// require_once __DIR__ ."/../configs/Database.php";
require_once __DIR__ . "/crud.php";
require_once __DIR__."/../includes/session.php";

class User extends CRUD
{
    public function signUp($username, $email, $password)
    {
        // Check if email already exists
        $user = $this->query(
            "SELECT id FROM users WHERE email = ?",
            [$email],
            true
        );
        if ($user) {
            throw new Exception("User already exists");
        }
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert new user
        $this->insert(
            "users",
            [
                "username" => $username,
                "email" => $email,
                "password" => $hashedPassword
            ]
        );
        return true;
    }
    public function logIn($email, $password)
    {
        $user = $this->query(
            "SELECT id, username, email, password FROM users WHERE email = ? LIMIT 1",
            [$email],
            true
        );
        if (!$user) {
            throw new Exception("Invalid email or password.");
        }
        if (!password_verify($password, $user['password'])) {
            throw new Exception("Invalid email or password.");
        }

        Session::login($user);
        return true;
    }
    public function getUser($id)
    {
        return $this->query("SELECT id,username,email,profile_image,isAdmin,isActive FROM USERS", [], true);
    }
}
