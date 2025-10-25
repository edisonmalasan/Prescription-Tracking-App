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
        // TODO: Implement create doctor
        return;
    }

    // Get doctor by user ID
    public function findByUserId($userId) {
        // TODO: Implement find doctor by user ID
        return;
    }

    // Get doctor by PRC license
    public function findByPrcLicense($license) {
        // TODO: Implement find doctor by PRC license
        return ;
    }

    // Get all doctors
    public function findAll() {
        // TODO: Implement find all doctors
        return;
    }

    // Get verified doctors
    public function findVerified() {
        // TODO: Implement find verified doctors
        return;
    }

    // Update doctor
    public function update($doctor) {
        // TODO: Implement update doctor
        return;
    }

    // Delete doctor
    public function delete($userId) {
        // TODO: Implement delete doctor
        return;
    }

    // Search doctors by specialization
    public function findBySpecialization($specialization) {
        // TODO: Implement find doctors by specialization
        return;
    }

    // Verify doctor
    public function verifyDoctor($userId) {
        //TODO: Implement verify doctor
        return;
    }
}
?>
