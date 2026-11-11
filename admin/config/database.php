<?php

session_start();

class Database
{
    private $host = "localhost";
    private $dbname = "lost_and_found";
    private $username = "root";
    private $password = "";

    protected $conn;

    public function __construct()
    {
        try {

            // Check if user is logged in and is admin
            if (
                !isset($_SESSION['user'])  || $_SESSION['user']['is_admin'] !== true
            ) {
                throw new Exception("Access Denied");
            }
            $this->conn = new PDO(
                "mysql:host={$this->host};dbname={$this->dbname}",
                $this->username,
                $this->password
            );

            $this->conn->setAttribute(
                PDO::ATTR_ERRMODE,
                PDO::ERRMODE_EXCEPTION
            );

            $this->conn->setAttribute(
                PDO::ATTR_DEFAULT_FETCH_MODE,
                PDO::FETCH_ASSOC
            );
        } catch (PDOException $e) {

            die("Connection Failed: " . $e->getMessage());
        } catch (Exception $e) {

            die($e->getMessage());
        }
    }
}
