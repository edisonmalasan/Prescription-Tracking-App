<?php

require_once '../config/db.php';
require_once '../models/adminModel.php';
require_once 'UserRepository.php';

class AdminRepository {
    private $conn;
    private $table_name = "admin";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function create($userId) {
        $sql = "INSERT INTO " . $this->table_name . " (user_id, isAdmin) VALUES (:user_id, :isAdmin)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':user_id' => $userId,
            ':isAdmin' => 1
        ]);
        return $stmt->rowCount() > 0;
    }

    public function findByUserId($userId) {
        $sql = "SELECT * FROM " . $this->table_name . " WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetch();
    }


    public function findAll() {
        $sql = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function update($admin) {
        $sql = "UPDATE " . $this->table_name . " SET isAdmin = :isAdmin WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':isAdmin' => $admin['isAdmin'] ?? 1,
            ':user_id' => $admin['user_id'] ?? null,
        ]); 
        return $stmt->rowCount() > 0;
    }

    public function delete($userId) {
        $sql = "DELETE FROM " . $this->table_name . " WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->rowCount() > 0;
    }
}
?>
