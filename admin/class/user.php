<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/Utils.php';
class User extends Database
{
    public function getUsers(int $limit = 10, int $page = 1)
    {
        $userId = $_SESSION['user']['id'];
        

        $utils = new Utils($this->conn);
        $pagination = $utils->paginate('users', $page, $limit);

        $offset = $pagination['offset'];
        $limit = $pagination['limit'];

        $sql = "
        SELECT 
            users.id,
            users.username,
            users.email,
            users.isAdmin,
            users.isActive,
            COUNT(reports.id) AS reports

        FROM users

        LEFT JOIN reports
            ON users.id = reports.user_id

        WHERE users.id != ?

        GROUP BY users.id

        ORDER BY users.id DESC

        LIMIT ? OFFSET ?
    ";


        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(1, $userId, PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, PDO::PARAM_INT);
        $stmt->bindValue(3, $offset, PDO::PARAM_INT);

        $stmt->execute();

        return [
            'data' => $stmt->fetchAll(),
            'pagination' => $pagination
        ];
    }
    public function updateUserStatus(int $id, string $column, bool $value)
    {
        $allowed = ['isAdmin', 'isActive'];

        if (!in_array($column, $allowed)) {
            return false;
        }

        $sql = "UPDATE users SET $column = ? WHERE id = ?";

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$value, $id]);
    }



    public function getUser($id) {}
}
