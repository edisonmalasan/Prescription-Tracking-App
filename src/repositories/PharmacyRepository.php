<?php

require_once '../config/db.php';
require_once '../models/pharmacyModel.php';
require_once 'UserRepository.php';

class PharmacyRepository {
    private $conn;
    private $table_name = "pharmacy";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function create($pharmacy) {
        $data = [];
        if (is_array($pharmacy)) {
            $data = $pharmacy;
        } elseif ($pharmacy instanceof \stdClass) {
            $data = (array)$pharmacy;
        } elseif ($pharmacy instanceof PharmacyModel) {
            $data = $pharmacy->toArray();
        }

        $now = date('Y-m-d H:i:s');
        $userData = [
            'last_name' => $data['pharmacy_name'] ?? ($data['pharmacyName'] ?? null),
            'first_name' => '',
            'role' => isset($data['role']) ? strtoupper($data['role']) : 'PHARMACY',
            'email' => $data['email'] ?? null,
            'contactno' => $data['contact_number'] ?? ($data['contactNumber'] ?? null),
            'pass_hash' => $data['pass_hash'] ?? ($data['passHash'] ?? null),
            'address' => $data['address'] ?? null,
            'created_at' => $data['created_at'] ?? $now
        ];

        if (!empty($data['user_id'])) {
            $userId = $data['user_id'];
        } else {
            $userRepo = new UserRepository();
            $userId = $userRepo->create($userData);
        }

        $sql = "INSERT INTO " . $this->table_name . " (user_id, pharmacy_name, operating_hours) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        if (! $stmt) {
            return $userId;
        }

        $pharmacy_name = $data['pharmacy_name'] ?? ($data['pharmacyName'] ?? null);
        $operating_hours = $data['operating_hours'] ?? ($data['operatingHours'] ?? null);

        $stmt->bind_param('iss', $userId, $pharmacy_name, $operating_hours);
        $stmt->execute();
        return $userId;
    }

    public function findByUserId($userId) {
        $sql = "SELECT p.*, u.email, u.contactno as contact_number, u.address FROM " . $this->table_name . " p JOIN user u ON p.user_id = u.user_id WHERE p.user_id = ?";
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

    public function findAll() {
        $sql = "SELECT * FROM " . $this->table_name;
        $res = $this->conn->query($sql);
        if ($res) {
            return $res->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }

    public function update($pharmacy) {
        $sql = "UPDATE " . $this->table_name . " SET pharmacy_name = ?, operating_hours = ? WHERE user_id = ?";
        $stmt = $this->conn->prepare($sql);
        if (! $stmt) {
            return false;
        }

        $pharmacy_name = $pharmacy['pharmacy_name'] ?? null;
        $operating_hours = $pharmacy['operating_hours'] ?? null;
        $user_id = $pharmacy['user_id'] ?? null;

        if ($user_id === null) {
            return false;
        }

        $stmt->bind_param('ssi', $pharmacy_name, $operating_hours, $user_id);
        $stmt->execute();
        
        $userRepo = new UserRepository();
        $userRepo->update([
            'user_id' => $user_id,
            'email' => $pharmacy['email'],
            'contactno' => $pharmacy['contact_number'],
            'address' => $pharmacy['address'],
            'last_name' => $pharmacy['pharmacy_name']
        ]);

        return ($stmt->affected_rows > 0);
    }

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
