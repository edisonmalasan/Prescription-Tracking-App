<?php

class Database {
    private $host = 'localhost';
    private $db_name = 'wium_lie';
    private $username = 'root';
    private $password = '';
    private $conn;

      public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new mysqli($this->host, $this->username, $this->password, $this->db_name);
            
            if ($this->conn->connect_error) {
            throw new Exception("Database connection failed: " . $this->conn->connect_error);
            }
            if (! $this->conn->set_charset('utf8mb4')) {
            throw new Exception("Error setting charset: " . $this->conn->error);
            }
        } catch (Exception $e) {
            throw new Exception("Database connection failed: " . $e->getMessage());
        }
        return $this->conn;
    }
}
?>
