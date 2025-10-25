<?php

require_once '../config/db.php';
require_once '../models/doctorModel.php';

class DoctorRepository {
    private $conn;
    private $table_name = "DOCTOR";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Create a new doctor
    public function create($doctor) {
        $sql = "INSERT INTO DOCTOR (name, specialization, user_id, prc_license) VALUES (:name, :specialization, :user_id, :prc_license)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':name' => $doctor['name'] ?? null,
            ':specialization' => $doctor['specialization'] ?? null,
            ':user_id' => $doctor['user_id'] ?? null,
            ':prc_license' => $doctor['prc_license'] ?? null,
        ]);
        return $this->conn->lastInsertId();
    }

    // Get doctor by user ID
    public function findByUserId($userId) {
        $sql = "SELECT * FROM DOCTOR WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetch();
    }

    // Get doctor by PRC license
    public function findByPrcLicense($license) {
        $sql = "SELECT * FROM DOCTOR WHERE prc_license = :prc_license";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':prc_license' => $license]);
        return $stmt->fetch();
    }

    // Get all doctors
    public function findAll() {
        $sql = "SELECT * FROM DOCTOR";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Get verified doctors
    public function findVerified() {
        $sql = "SELECT * FROM DOCTOR WHERE is_verified = TRUE";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Update doctor
    public function update($doctor) {
        $sql = "UPDATE DOCTOR SET name = :name, specialization = :specialization, prc_license = :prc_license WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':name' => $doctor['name'] ?? null,
            ':specialization' => $doctor['specialization'] ?? null,
            ':prc_license' => $doctor['prc_license'] ?? null,
            ':user_id' => $doctor['user_id'] ?? null,
        ]); 
        return;
    }

    // Delete doctor
    public function delete($userId) {
        $sql = "DELETE FROM DOCTOR WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return;
    }

    // Search doctors by specialization
    public function findBySpecialization($specialization) {
        $sql = "SELECT * FROM DOCTOR WHERE specialization = :specialization";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':specialization' => $specialization]);
        return $stmt->fetchAll();
    }

    // Verify doctor
    public function verifyDoctor($userId) {
        $sql = "UPDATE DOCTOR SET is_verified = TRUE WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return;
    }
}
?>
