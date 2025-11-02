<?php

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/userModel.php';

class UserRepository {
    private $conn;
    private $table_name = "users";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Create a new user
        public function create(array $data) {
            $sql = "INSERT INTO " . $this->table_name . " (last_name, first_name, role, email, contactno, pass_hash, address, created_at) VALUES (:last_name, :first_name, :role, :email, :contactno, :pass_hash, :address, :created_at)";
        $stmt = $this->conn->prepare($sql);
        
        $result = $stmt->execute([
            ':last_name' => $data['last_name'] ?? null,
            ':first_name' => $data['first_name'] ?? null,
            ':role' => $data['role'] ?? null,
            ':email' => $data['email'] ?? null,
            ':contactno' => $data['contactno'] ?? null,
            ':pass_hash' => $data['pass_hash'] ?? null,
            ':address' => $data['address'] ?? null,
                ':created_at' => $data['created_at'] ?? null,
        ]);
        
        if ($result) {
            $id = $this->conn->lastInsertId();
            return $id;
        } else {
            return false;
        }
    }

    // Get user by ID
    public function findById($id) {
        // Table uses user_id as primary key in schema
        $sql = "SELECT * FROM " . $this->table_name . " WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':user_id' => $id]);
        $result = $stmt->fetch();
        return $result;
    }

    // Get user by email
    public function findByEmail($email) {
        $sql = "SELECT * FROM " . $this->table_name . " WHERE email = :email";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':email' => $email]);
        $result = $stmt->fetch();
        return $result;
    }
    // Get all users
    public function findAll() {
        $sql = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $result;
     
    }

    // Get users by role
    public function findByRole($role) {
        $sql = "SELECT * FROM " . $this->table_name . " WHERE role = :role";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':role' => $role]);
        $result = $stmt->fetchAll();
        return $result;
       
    }

    // Update user
    public function update($user) {
        $sql = "UPDATE " . $this->table_name . " SET last_name = :last_name, first_name = :first_name, role = :role, email = :email, contactno = :contactno, pass_hash = :pass_hash, address = :address WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($sql);
        $result = $stmt->execute([
            ':last_name' => $user['last_name'] ?? null,
            ':first_name' => $user['first_name'] ?? null,
            ':role' => $user['role'] ?? null,
            ':email' => $user['email'] ?? null,
            ':contactno' => $user['contactno'] ?? null,
            ':pass_hash' => $user['pass_hash'] ?? null,
            ':address' => $user['address'] ?? null,
            ':user_id' => $user['user_id'] ?? $user['id']
        ]);
        
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    // Delete user
    public function delete($id) {
        $sql = "DELETE FROM " . $this->table_name . " WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($sql);
        $result = $stmt->execute([':user_id' => $id]);
        
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    // Search users
    public function search($searchTerm) {
        $sql = "SELECT * FROM " . $this->table_name . " WHERE last_name LIKE :searchTerm OR first_name LIKE :searchTerm OR email LIKE :searchTerm";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':searchTerm' => "%$searchTerm%"]);
        $result = $stmt->fetchAll();
        return $result;
    }
}
?>