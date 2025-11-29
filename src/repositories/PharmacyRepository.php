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

    public function create($userId, $pharmacy) {
        $data = [];
        if (is_array($pharmacy)) {
            $data = $pharmacy;
        } elseif ($pharmacy instanceof \stdClass) {
            $data = (array)$pharmacy;
        } elseif ($pharmacy instanceof PharmacyModel) {
            $data = $pharmacy->toArray();
        }

        $sql = "INSERT INTO " . $this->table_name . " (user_id, pharmacy_name, phar_license, open_time, close_time, dates_open, isVerified) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        if (! $stmt) {
            return false;
        }

        $pharmacy_name = $data['pharmacy_name'] ?? null;
        $phar_license = $data['phar_license'] ?? null;
        $open_time = $data['open_time'] ?? null;
        $close_time = $data['close_time'] ?? null;
        $dates_open = $data['dates_open'] ?? null;
        $isVerified = isset($data['isVerified']) ? (int)$data['isVerified'] : 0;

        $stmt->bind_param('isssssi', $userId, $pharmacy_name, $phar_license, $open_time, $close_time, $dates_open, $isVerified);
        $ok = $stmt->execute();

        if ($ok && $stmt->affected_rows > 0) {
            return $userId;
        }

        return false;
    }

    public function findByUserId($userId) {
        $sql = "SELECT p.*, u.email, u.contactno as contact_number, u.address FROM " . $this->table_name . " p JOIN users u ON p.user_id = u.user_id WHERE p.user_id = ?";
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
        $sql = "UPDATE " . $this->table_name . " SET pharmacy_name = ?, open_time = ?, close_time = ? WHERE user_id = ?";
        $stmt = $this->conn->prepare($sql);
        if (! $stmt) {
            return false;
        }

        $pharmacy_name = $pharmacy['pharmacy_name'] ?? null;
        $open_time = $pharmacy['open_time'] ?? null;
        $close_time = $pharmacy['close_time'] ?? null;
        $user_id = $pharmacy['user_id'] ?? null;

        if ($user_id === null) {
            return false;
        }

        $stmt->bind_param('sssi', $pharmacy_name, $open_time, $close_time, $user_id);
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
