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
            $sql = "INSERT INTO " . $this->table_name . " (last_name, first_name, role, email, contactno, pass_hash, address, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->conn->prepare($sql);
            if (! $stmt) {
                return false;
            }

            $last_name = $data['last_name'] ?? null;
            $first_name = $data['first_name'] ?? null;
            $role = $data['role'] ?? null;
            $email = $data['email'] ?? null;
            $contactno = $data['contactno'] ?? null;
            $pass_hash = $data['pass_hash'] ?? null;
            $address = $data['address'] ?? null;
            $created_at = $data['created_at'] ?? null;

            // bind all as strings; mysqli will convert types as needed
            $stmt->bind_param('ssssssss', $last_name, $first_name, $role, $email, $contactno, $pass_hash, $address, $created_at);
            $res = $stmt->execute();

            if ($res) {
                return $this->conn->insert_id;
            }

            return false;
    }

    // Get user by ID (mysqli syntax)
    public function findById($id) {
        $sql = "SELECT * FROM " . $this->table_name . " WHERE user_id = ?";
        $stmt = $this->conn->prepare($sql);
        if (! $stmt) {
            return null;
        }
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            return $result->fetch_assoc();
        }
        return null;
    }

    // Get user by email
    public function findByEmail($email) {
        $sql = "SELECT * FROM " . $this->table_name . " WHERE email = ?";
        $stmt = $this->conn->prepare($sql);
        if (! $stmt) {
            return null;
        }
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            return $result->fetch_assoc();
        }
        return null;
    }
    // Get all users
    public function findAll() {
        $sql = "SELECT * FROM " . $this->table_name;
        $result = $this->conn->query($sql);
        if ($result) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return [];
     
    }

    // Get users by role
    public function findByRole($role) {
        $sql = "SELECT * FROM " . $this->table_name . " WHERE role = ?";
        $stmt = $this->conn->prepare($sql);
        if (! $stmt) {
            return [];
        }
        $stmt->bind_param('s', $role);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res) {
            return $res->fetch_all(MYSQLI_ASSOC);
        }
        return [];
       
    }

    // Update user
    public function update($user) {
        $sql = "UPDATE " . $this->table_name . " SET last_name = ?, first_name = ?, role = ?, email = ?, contactno = ?, pass_hash = ?, address = ? WHERE user_id = ?";
        $stmt = $this->conn->prepare($sql);
        if (! $stmt) {
            return false;
        }

        $last_name = $user['last_name'] ?? null;
        $first_name = $user['first_name'] ?? null;
        $role = $user['role'] ?? null;
        $email = $user['email'] ?? null;
        $contactno = $user['contactno'] ?? null;
        $pass_hash = $user['pass_hash'] ?? null;
        $address = $user['address'] ?? null;
        $user_id = $user['user_id'] ?? $user['id'] ?? null;

        if ($user_id === null) {
            return false;
        }

        $stmt->bind_param('sssssssi', $last_name, $first_name, $role, $email, $contactno, $pass_hash, $address, $user_id);
        $res = $stmt->execute();
        return (bool) $res;
    }

    // Delete user
    public function delete($id) {
        $sql = "DELETE FROM " . $this->table_name . " WHERE user_id = ?";
        $stmt = $this->conn->prepare($sql);
        if (! $stmt) {
            return false;
        }
        $stmt->bind_param('i', $id);
        $res = $stmt->execute();
        return (bool) $res;
    }

    // Search users
    public function search($searchTerm) {
        $sql = "SELECT * FROM " . $this->table_name . " WHERE last_name LIKE ? OR first_name LIKE ? OR email LIKE ?";
        $stmt = $this->conn->prepare($sql);
        if (! $stmt) {
            return [];
        }
        $like = "%" . $searchTerm . "%";
        $stmt->bind_param('sss', $like, $like, $like);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res) {
            return $res->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }
}
?>