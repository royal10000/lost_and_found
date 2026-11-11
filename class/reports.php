<?php require_once("config/database.php");
class Report extends Database
{
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
}


?>