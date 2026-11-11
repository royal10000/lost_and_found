<?php

require_once('config/database.php');

class User extends Database
{
    public function getUsers()
    {
        $sql = "SELECT * FROM users";

        $stmt= $this->conn->query($sql);
        return   $stmt->fetchAll();
    }
    public function getUxser($id){}
}

?>