<?php

class Database {
    private $host = 'localhost';
    private $db_name = // DB NAME HERE ;
    private $username = 'root';
    private $password = '';
    private $conn;

    public function getConnection() {
        $this->conn = null;

        // SETUP CONNECTION 
        return $this->conn;
    }
}
?>
