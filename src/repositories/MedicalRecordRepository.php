<?php

require_once '../config/db.php';
require_once '../models/medicalRecordModel.php';

class MedicalRecordRepository {
    private $conn;
    private $table_name = "medicalrecord";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function create($medicalRecord) {
        $sql = "INSERT INTO " . $this->table_name . " (user_id, height, weight, allergies) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        if (! $stmt) {
            return false;
        }

        $user_id = $medicalRecord['user_id'] ?? null;
        $height = $medicalRecord['height'] ?? null;
        $weight = $medicalRecord['weight'] ?? null;
        $allergies = $medicalRecord['allergies'] ?? '';

        $stmt->bind_param('isss', $user_id, $height, $weight, $allergies);
        $ok = $stmt->execute();
        if ($ok) {
            return $this->conn->insert_id;
        }
        return false;
    }

    public function findById($id) {
        $sql = "SELECT * FROM " . $this->table_name . " WHERE record_id = ?";
        $stmt = $this->conn->prepare($sql);
        if (! $stmt) {
            return null;
        }
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res) {
            return $res->fetch_assoc();
        }
        return null;
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

    public function findAll() {
        $sql = "SELECT * FROM " . $this->table_name;
        $res = $this->conn->query($sql);
        if ($res) {
            return $res->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }

    public function update($medicalRecord) {
        $sql = "UPDATE " . $this->table_name . " SET height = ?, weight = ?, allergies = ? WHERE record_id = ?";
        $stmt = $this->conn->prepare($sql);
        if (! $stmt) {
            return false;
        }

        $height = $medicalRecord['height'] ?? null;
        $weight = $medicalRecord['weight'] ?? null;
        $allergies = $medicalRecord['allergies'] ?? '';
        $record_id = $medicalRecord['record_id'] ?? null;
        if ($record_id === null) {
            return false;
        }

        $stmt->bind_param('sssi', $height, $weight, $allergies, $record_id);
        $stmt->execute();
        return ($stmt->affected_rows > 0);
    }

    public function delete($id) {
        $sql = "DELETE FROM " . $this->table_name . " WHERE record_id = ?";
        $stmt = $this->conn->prepare($sql);
        if (! $stmt) {
            return false;
        }
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return ($stmt->affected_rows > 0);
    }
}
?>
