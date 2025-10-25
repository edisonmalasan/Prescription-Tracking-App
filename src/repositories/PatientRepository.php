<?php

require_once '../config/db.php';
require_once '../models/patientModel.php';

class PatientRepository {
    private $conn;
    private $table_name = "PATIENT";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Create a new patient
    public function create($patient) {
        // TODO: Implement create patient
        return;
    }

    // Get patient by user ID
    public function findByUserId($userId) {
        // TODO: Implement find patient by user ID
        return;
    }

    // Get all patients
    public function findAll() {
        // TODO: Implement find all patients
        return; 
    }

    // Update patient
    public function update($patient) {
        // TODO: Implement update patient
        return;
    }

    // Delete patient
    public function delete($userId) {
        // TODO: Implement delete patient
        return;
    }

    // Search patients
    public function search($searchTerm) {
        // TODO: Implement search patients
        return;
    }

    // Check if patient exists
    public function exists($userId) {
        // TODO: Implement check if patient exists
        return;
    }
}
?>
