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

    //create a new admin 
    public function create($userId) {
        $sql = "INSERT INTO " . $this->table_name . " (user_id, isAdmin) VALUES (?, ?)";
        $stmt = $this->conn->prepare($sql);
        if (! $stmt) {
            return false;
        }

        $isAdmin = 1;
        $stmt->bind_param('ii', $userId, $isAdmin);
        $ok = $stmt->execute();

        if ($ok && $stmt->affected_rows > 0) {
            return $userId;
        }

        return false;
    }


    // Get admin by user ID
    public function findByUserId($userId) {
        $sql = "SELECT * FROM " . $this->table_name . " WHERE user_id = ?";
        $stmt = $this->conn->prepare($sql);
        if (! $stmt) {
            return null;
        }
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res) {
            return $res->fetch_assoc();
        }
        return null;
    }

    // Get all admins
    public function findAll() {
        $sql = "SELECT * FROM " . $this->table_name;
        $res = $this->conn->query($sql);
        if ($res) {
            return $res->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }

    // Update admin
    public function update($admin) {
        $sql = "UPDATE " . $this->table_name . " SET isAdmin = ? WHERE user_id = ?";
        $stmt = $this->conn->prepare($sql);
        if (! $stmt) {
            return false;
        }

        $isAdmin = isset($admin['isAdmin']) ? (int)$admin['isAdmin'] : 1;
        $user_id = $admin['user_id'] ?? null;
        if ($user_id === null) {
            return false;
        }

        $stmt->bind_param('ii', $isAdmin, $user_id);
        $stmt->execute();
        return ($stmt->affected_rows > 0);
    }

    // Delete admin
    public function delete($userId) {
        $sql = "DELETE FROM " . $this->table_name . " WHERE user_id = ?";
        $stmt = $this->conn->prepare($sql);
        if (! $stmt) {
            return false;
        }
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        return ($stmt->affected_rows > 0);
    }
}
?>
