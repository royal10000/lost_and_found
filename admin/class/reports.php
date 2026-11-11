<?php require_once("config/database.php");
require_once("includes/utils.php");
class Report extends Database
{
    public function findReport(int $id): ?array
    {
        $stmt = $this->conn->prepare("
            SELECT
                reports.*,
                users.id AS user_id,
                users.username,
                users.email,
                users.profile_image,
                users.isAdmin,
                users.isActive,
                users.created_at AS user_created_at,
                category.categoryName
            FROM reports
            LEFT JOIN users
                ON reports.user_id = users.id
            LEFT JOIN category
                ON reports.category_id = category.id
            WHERE reports.id = ?
            LIMIT 1
        ");
        $stmt->execute([$id]);
        $report = $stmt->fetch(PDO::FETCH_ASSOC);

        return $report ?: null;
    }

    public function RecentReports(int $limit = 5)
    {
        $sql = "
            SELECT reports.*, users.username,category.categoryName
            FROM reports
            LEFT JOIN users
                ON reports.user_id = users.id
            LEFT JOIN category on reports.category_id=category.id
            ORDER BY reports.created_at DESC
            LIMIT $limit
        ";
        $query = $this->conn->query($sql);
        return $query->fetchAll();
    }
        public function fetchReports(
    int $limit = 5,
    int $page = 1,
    string $status = "",
    string $type = ""
) {
    $conditions = [];
    $params = [];

    // Build WHERE conditions dynamically
    if (!empty($status)) {
        $conditions[] = "reports.status = :status";
        $params[':status'] = $status;
    }

    if (!empty($type)) {
        $conditions[] = "reports.report_type = :type";
        $params[':type'] = $type;
    }

    $whereClause = "";

    if (!empty($conditions)) {
        $whereClause = "WHERE " . implode(" AND ", $conditions);
    }

    // Count total records
    $countSql = "
        SELECT COUNT(*) as total
        FROM reports
        $whereClause
    ";

    $countStmt = $this->conn->prepare($countSql);

    foreach ($params as $key => $value) {
        $countStmt->bindValue($key, $value);
    }

    $countStmt->execute();

    $totalRecords = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Pagination calculations
    $totalPages = ceil($totalRecords / $limit);

    $offset = ($page - 1) * $limit;

    // Main query
    $sql = "
        SELECT
            reports.*,
            users.username,
            category.categoryName
        FROM reports
        LEFT JOIN users
            ON reports.user_id = users.id
        LEFT JOIN category
            ON reports.category_id = category.id
        $whereClause
        ORDER BY reports.created_at DESC
        LIMIT :limit OFFSET :offset
    ";

    $stmt = $this->conn->prepare($sql);

    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }

    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

    $stmt->execute();

    return [
        'reports' => $stmt->fetchAll(PDO::FETCH_ASSOC),
        'pagination' => [
            'page' => $page,
            'limit' => $limit,
            'totalRecords' => $totalRecords,
            'totalPages' => $totalPages,
            'offset' => $offset
        ]
    ];
}

    public function updateStatus(int $id, string $status)
    {
        $allowedStatuses = ['pending', 'approved', 'rejected', 'claimed'];

        if (!in_array($status, $allowedStatuses, true)) {
            return false;
        }

        $currentStmt = $this->conn->prepare(
            "SELECT status FROM reports WHERE id = ? LIMIT 1"
        );
        $currentStmt->execute([$id]);
        $current = $currentStmt->fetchColumn();

        if (!$current) {
            return false;
        }

        $allowedTransitions = [
            'pending' => ['approved', 'rejected'],
            'approved' => ['claimed'],
            'rejected' => [],
            'claimed' => []
        ];

        if ($current === $status) {
            return true;
        }

        if (!in_array($status, $allowedTransitions[$current] ?? [], true)) {
            return false;
        }

        $stmt = $this->conn->prepare(
            "UPDATE reports SET status = ? WHERE id = ?"
        );

        return $stmt->execute([$status, $id]);
    }
}
