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
            "SELECT id, username, email, password, profile_image FROM users WHERE email = ? LIMIT 1",
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
    }
    public function getUser($id)
    {
        $columns = $this->query('SHOW COLUMNS FROM users');
        $safeColumns = [];
        foreach ($columns as $column) {
            $name = $column['Field'] ?? '';
            if (!preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $name)) {
                continue;
            }
            if (preg_match('/password|pass_hash|secret|token|salt/i', $name)) {
                continue;
            }
            $safeColumns[] = '`' . $name . '`';
        }

        if (!$safeColumns) {
            return null;
        }

        return $this->query(
            'SELECT ' . implode(', ', $safeColumns) . ' FROM users WHERE id = ? LIMIT 1',
            [(int) $id],
            true
        ) ?: null;
    }

    public function updateProfileImage(int $userId, array $file): string
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK || empty($file['tmp_name'])) {
            throw new Exception('Choose an image to upload.');
        }
        if (($file['size'] ?? 0) > 5 * 1024 * 1024) {
            throw new Exception('The image must be 5 MB or smaller.');
        }

        $mimeType = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
        $extensions = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
        ];
        if (!isset($extensions[$mimeType])) {
            throw new Exception('Use a JPG, PNG, GIF, or WebP image.');
        }

        $user = $this->getUser($userId);
        if (!$user) {
            throw new Exception('Your account could not be found.');
        }
        if (!array_key_exists('profile_image', $user)) {
            throw new Exception('The profile_image database column is missing. Apply the profile image migration first.');
        }

        $directory = __DIR__ . '/../public/uploads/profiles/';
        if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) {
            throw new Exception('The profile image folder could not be created.');
        }

        $filename = 'profile_' . bin2hex(random_bytes(16)) . '.' . $extensions[$mimeType];
        $destination = $directory . $filename;
        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            throw new Exception('The profile image could not be saved.');
        }

        try {
            $this->query('UPDATE users SET profile_image = ? WHERE id = ?', [$filename, $userId]);
        } catch (Throwable $exception) {
            @unlink($destination);
            throw $exception;
        }

        $oldFilename = basename((string) ($user['profile_image'] ?? ''));
        if ($oldFilename !== '' && $oldFilename !== $filename) {
            $oldPath = $directory . $oldFilename;
            if (is_file($oldPath)) {
                @unlink($oldPath);
            }
        }

        return $filename;
    }
}
