<?php

require_once '../config/db.php';
require_once '../models/userModel.php';

class UserRepository {
    private $conn;
    private $table_name = "USERS";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Create a new user
        public function create(array $data) {
        $sql = "INSERT INTO USERS (last_name, first_name, role, email, contactno, pass_hash, address, created_at)
                VALUES (:last_name, :first_name, :role, :email, :contactno, :pass_hash, :address, :created_at)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':last_name'  => $data['last_name']  ?? '',
            ':first_name' => $data['first_name'] ?? '',
            ':role'       => $data['role']       ?? 'user',
            ':email'      => $data['email']      ?? null,
            ':contactno'  => $data['contactno']  ?? '',
            ':pass_hash'  => $data['pass_hash']  ?? null,
            ':address'    => $data['address']    ?? '',
            ':created_at' => $data['created_at'] ?? date('Y-m-d H:i:s'),    
        ]);
        return $this->conn->lastInsertId();
    }

    // Get user by ID
    public function findById($id) {
        $sql = "SELECT * FROM USERS WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    // Get user by email
    public function findByEmail($email) {
        $sql = "SELECT * FROM USERS WHERE email = :email";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':email' => $email]);
        return $stmt->fetch();
    }

    // Get all users
    public function findAll() {
        $sql = "SELECT * FROM USERS";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Get users by role
    public function findByRole($role) {
        $sql = "SELECT * FROM USERS WHERE role = :role";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':role' => $role]);
        return $stmt->fetchAll();
    }

    // Update user
    public function update($user) {
        $sql = "UPDATE USERS SET last_name = :last_name, first_name = :first_name, role = :role, email = :email, contactno = :contactno, pass_hash = :pass_hash, address = :address WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':last_name'  => $user['last_name']  ?? '',
            ':first_name' => $user['first_name'] ?? '',
            ':role'       => $user['role']       ?? 'user',
            ':email'      => $user['email']      ?? null,
            ':contactno'  => $user['contactno']  ?? '',
            ':pass_hash'  => $user['pass_hash']  ?? null,
            ':address'    => $user['address']    ?? '',
        ]);
        return;
       
    }

    // Delete user
    public function delete($id) {
        $sql = "DELETE FROM USERS WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $id]);
        return;
    }

    // Search users
    public function search($searchTerm) {
        $sql = "SELECT * FROM USERS WHERE last_name LIKE :searchTerm OR first_name LIKE :searchTerm OR email LIKE :searchTerm";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':searchTerm' => "%$searchTerm%"]);
        return $stmt->fetchAll();
    }
}
?>
