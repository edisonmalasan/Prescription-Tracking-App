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
    public function create($user) {
        // TODO: Implement create user
        return;
    }

    // Get user by ID
    public function findById($id) {
        // TODO: Implement find user by ID
        return;
    }

    // Get user by email
    public function findByEmail($email) {
        // TODO: Implement find user by email
        return;
    }

    // Get all users
    public function findAll() {
        // TODO: Implement find all users
        return;
    }

    // Get users by role
    public function findByRole($role) {
        // TODO: Implement find users by role
        return;
    }

    // Update user
    public function update($user) {
        // TODO: Implement update user
        return;
    }

    // Delete user
    public function delete($id) {
        // TODO: Implement delete user
        return;
    }

    // Search users
    public function search($searchTerm) {
        // TODO: Implement search users
        return;
    }
}
?>
