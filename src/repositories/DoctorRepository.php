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
        // Actual `doctor` table (wium_lie) has columns: user_id, birth_date (NOT NULL), specialization, prc_license, clinic_name, isVerified
        $sql = "INSERT INTO " . $this->table_name . " (user_id, birth_date, specialization, prc_license, clinic_name, isVerified) VALUES (:user_id, :birth_date, :specialization, :prc_license, :clinic_name, :isVerified)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':user_id' => $doctor->user_id,
            ':birth_date' => $doctor->birth_date ?? null,
            ':specialization' => $doctor->specialization,
            ':prc_license' => $doctor->prc_license,
            ':clinic_name' => $doctor->clinic_name ?? null,
            ':isVerified' => isset($doctor->isVerified) ? (int)$doctor->isVerified : (isset($doctor->verified) ? (int)$doctor->verified : 0)
        ]);
        // doctor.user_id is primary key (not auto-increment) — return the user_id to indicate success
        return $doctor->user_id;
    }

    // Get doctor by user ID
    public function findByUserId($userId) {
        $sql = "SELECT * FROM " . $this->table_name . " WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetch();
    }

    // Get doctor by PRC license
    public function findByPrcLicense($license) {
        $sql = "SELECT * FROM " . $this->table_name . " WHERE prc_license = :prc_license";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':prc_license' => $license]);
        return $stmt->fetch();
     
    }

    // Get all doctors
    public function findAll() {
        $sql = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Get verified doctors
    public function findVerified() {
        $sql = "SELECT * FROM " . $this->table_name . " WHERE verified = 1";
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
        $sql = "DELETE FROM " . $this->table_name . " WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return;
    }

    

    // Search doctors by specialization
    public function findBySpecialization($specialization) {
        $sql = "SELECT * FROM " . $this->table_name . " WHERE specialization = :specialization";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':specialization' => $specialization]);
        return $stmt->fetchAll();
    }

    // Verify doctor
    public function verifyDoctor($userId) {
        $sql = "UPDATE " . $this->table_name . " SET verified = 1 WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return;
    }

}

?>
