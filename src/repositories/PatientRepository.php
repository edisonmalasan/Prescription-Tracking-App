<?php

require_once '../config/db.php';
require_once '../models/patientModel.php';
require_once 'UserRepository.php';

class PatientRepository {
    private $conn;
    private $table_name = "patient";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }


    public function create($userId, $patient) {
        $data = [];
        if (is_array($patient)) {
            $data = $patient;
        } elseif ($patient instanceof \stdClass) {
            $data = (array)$patient;
        } elseif ($patient instanceof PatientModel) {
            $data = $patient->toArray();
        }

        $sql = "INSERT INTO " . $this->table_name . " (user_id, birth_date) VALUES (?, ?)";
        $stmt = $this->conn->prepare($sql);
        if (! $stmt) {
            return false;
        }

        $birth_date = $data['birth_date'] ?? ($data['birthDate'] ?? null);
        $stmt->bind_param('is', $userId, $birth_date);
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

    public function findAll() {
        $sql = "SELECT * FROM " . $this->table_name;
        $res = $this->conn->query($sql);
        if ($res) {
            return $res->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }

    public function update($patient) {
        $sql = "UPDATE " . $this->table_name . " SET birth_date = ? WHERE user_id = ?";
        $stmt = $this->conn->prepare($sql);
        if (! $stmt) {
            return false;
        }

        $birth_date = $patient['birth_date'] ?? null;
        $user_id = $patient['user_id'] ?? null;
        if ($user_id === null) {
            return false;
        }

        $stmt->bind_param('si', $birth_date, $user_id);
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

    public function findByDoctor($userId) {
    $sql = "SELECT DISTINCT u.user_id, u.first_name, u.last_name, u.contactno, p.birth_date
            FROM patient p
            JOIN users u ON p.user_id = u.user_id
            JOIN medicalrecord m ON p.user_id = m.user_id
            JOIN prescription pr ON m.record_id = pr.record_id
            WHERE pr.prescribing_doctor = ?";
    
    $stmt = $this->conn->prepare($sql);
    if (!$stmt) return [];
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

}
?>