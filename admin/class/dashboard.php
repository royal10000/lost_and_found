<?php

require_once("config/database.php");

class Dashboard extends Database
{
    public function totalUser()
    {
        $sql = "SELECT COUNT(*) AS total
                FROM users
                WHERE isActive = 1";

        return $this->conn->query($sql)->fetch();
    }

    public function pendingReport()
    {
        $sql = "SELECT COUNT(*) AS pending
                FROM reports
                WHERE status = 'pending'";

        return $this->conn->query($sql)->fetch();
    }

    public function lostReport()
    {
        $sql = "SELECT COUNT(*) AS lost
                FROM reports
                WHERE report_type = 'lost'";

        return $this->conn->query($sql)->fetch();
    }

    public function foundReport()
    {
        $sql = "SELECT COUNT(*) AS found
                FROM reports
                WHERE report_type = 'found'";

        return $this->conn->query($sql)->fetch();
    }

    public function approvedReport()
    {
        $sql = "SELECT COUNT(*) AS approved
                FROM reports
                WHERE status = 'approved'";

        return $this->conn->query($sql)->fetch();
    }

    public function rejectedReport()
    {
        $sql = "SELECT COUNT(*) AS rejected
                FROM reports
                WHERE status = 'rejected'";

        return $this->conn->query($sql)->fetch();
    }

    public function claimedReport()
    {
        $sql = "SELECT COUNT(*) AS claimed
                FROM reports
                WHERE status = 'claimed'";

        return $this->conn->query($sql)->fetch();
    }
}?>
