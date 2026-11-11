<?php

require_once __DIR__ . '/../config/database.php';

class Utils
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function paginate(string $table, int $page = 1, int $limit = 5, string $where = "")
    {
        $page = max(1, (int)$page);
        $limit = (int)$limit;
        $offset = ($page - 1) * $limit;

        $countSql = "SELECT COUNT(*) as total FROM $table";

        if (!empty($where)) {
            $countSql .= " WHERE $where";
        }

        $totalStmt = $this->conn->query($countSql);
        $total = $totalStmt->fetch()['total'];

        $totalPages = ceil($total / $limit);

        return [
            'limit' => $limit,
            'offset' => $offset,
            'total' => $total,
            'totalPages' => $totalPages,
            'currentPage' => $page
        ];
    }
}
