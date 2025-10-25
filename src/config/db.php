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
            $dsn = "mysql:host={$this->host};dbname={$this->db_name};charset=utf8mb4";
            $this->conn = new PDO($dsn, $this->username, $this->password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch (PDOException $e) {

            throw $e;
        }

        return $this->conn;
    }
}
?>
