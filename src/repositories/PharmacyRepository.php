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
            'last_name' => $data['last_name'] ?? null,
            'first_name' => $data['first_name'] ?? null,
            'role' => isset($data['role']) ? strtoupper($data['role']) : 'PHARMACY',
            'email' => $data['email'] ?? null,
            'contactno' => $data['contactno'] ?? null,
            'pass_hash' => $data['pass_hash'] ?? null,
            'address' => $data['address'] ?? null,
            'created_at' => $data['created_at'] ?? $now
        ];

        // Use provided user_id if available
        if (!empty($data['user_id'])) {
            $userId = $data['user_id'];
        } else {
            $userRepo = new UserRepository();
            $userId = $userRepo->create($userData);
        }

        $sql = "INSERT INTO " . $this->table_name . " (user_id, pharmacy_name, phar_license, open_time, close_time, dates_open) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        if (! $stmt) {
            return false;
        }

        $pharmacy_name = $data['pharmacy_name'] ?? ($data['pharmacyName'] ?? '');
        $phar_license = $data['phar_license'] ?? ($data['pharLicense'] ?? '');
        $open_time = $data['open_time'] ?? ($data['openTime'] ?? '');
        $close_time = $data['close_time'] ?? ($data['closeTime'] ?? '');
        $dates_open = $data['dates_open'] ?? ($data['datesOpen'] ?? '');

        $stmt->bind_param('isssss', $userId, $pharmacy_name, $phar_license, $open_time, $close_time, $dates_open);
        $ok = $stmt->execute();

        if ($ok && $stmt->affected_rows > 0) {
            return $userId;
        }

        return false;
    }

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

    public function findByPharmacyName($pharmacyName) {
        $sql = "SELECT * FROM " . $this->table_name . " WHERE pharmacy_name = ?";
        $stmt = $this->conn->prepare($sql);
        if (! $stmt) {
            return null;
        }
        $stmt->bind_param('s', $pharmacyName);
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
        $sql = "UPDATE " . $this->table_name . " SET pharmacy_name = ?, phar_license = ?, open_time = ?, close_time = ?, dates_open = ? WHERE user_id = ?";
        $stmt = $this->conn->prepare($sql);
        if (! $stmt) {
            return false;
        }

        $pharmacy_name = $pharmacy['pharmacy_name'] ?? null;
        $phar_license = $pharmacy['phar_license'] ?? null;
        $open_time = $pharmacy['open_time'] ?? null;
        $close_time = $pharmacy['close_time'] ?? null;
        $dates_open = $pharmacy['dates_open'] ?? null;
        $user_id = $pharmacy['user_id'] ?? null;

        if ($user_id === null) {
            return false;
        }

        $stmt->bind_param('sssssi', $pharmacy_name, $phar_license, $open_time, $close_time, $dates_open, $user_id);
        $stmt->execute();
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