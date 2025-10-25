<?php

require_once '../config/db.php';
require_once '../models/prescriptionModel.php';
require_once '../models/prescriptionDetailModel.php';

class PrescriptionRepository {
    private $conn;
    private $table_name = "PRESCRIPTION";
    private $details_table = "PRESCRIPTIONDETAILS";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Create a new prescription
    public function create($prescription) {
        // TODO: Implement create prescription
        return;
    }

    // Get prescription by ID
    public function findById($id) {
        // TODO: Implement find prescription by ID
        return;
    }

    // Get prescriptions by doctor
    public function findByDoctor($doctorId) {
        // TODO: Implement find prescriptions by doctor
        return;
    }

    // Get prescriptions by patient
    public function findByPatient($patientId) {
        // TODO: Implement find prescriptions by patient
        return;
    }

    // Get all prescriptions
    public function findAll() {
        // TODO: Implement find all prescriptions
        return;
    }

    // Update prescription
    public function update($prescription) {
        // TODO: Implement update prescription
    }

    // Delete prescription
    public function delete($id) {
        // TODO: Implement delete prescription
        return;
    }

    // Get prescription details
    public function getPrescriptionDetails($prescriptionId) {
        // TODO: Implement get prescription details
        return;
    }

    // Add prescription detail
    public function addPrescriptionDetail($detail) {
        // TODO: Implement add prescription detail
        return;
    }

    // Get prescriptions by status
    public function findByStatus($status) {
        // TODO: Implement find prescriptions by status
        return;
    }
}
?>
