<?php

class Database {
    private $host = 'localhost';
    private $db_name = 'wium_lie' ;
    private $username = 'root';
    private $password = '';
    private $conn;

     public function getConnection() {
        $this->conn = null;
        try {
            error_log("Attempting to connect to database: " . $this->db_name);
            $dsn = "mysql:host={$this->host};dbname={$this->db_name};charset=utf8mb4";
            $this->conn = new PDO($dsn, $this->username, $this->password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
            error_log("Database connection successful");
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            throw $e;
        }

        return $this->conn;
    }
}
?>