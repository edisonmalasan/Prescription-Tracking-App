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
        $sql = "INSERT INTO PRESCRIPTION (doctor_id, patient_id, date, status) VALUES (:doctor_id, :patient_id, :date, :status)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':doctor_id' => $prescription['doctor_id'] ?? null,
            ':patient_id' => $prescription['patient_id'] ?? null,
            ':date' => $prescription['date'] ?? null,
            ':status' => $prescription['status'] ?? null,
        ]);
        return $this->conn->lastInsertId();
    }

    // Get prescription by ID
    public function findById($id) {
        $sql = "SELECT * FROM PRESCRIPTION WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    // Get prescriptions by patient
    public function findByPatient($patientId) {
        $sql = "SELECT * FROM PRESCRIPTION WHERE patient_id = :patient_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':patient_id' => $patientId]);
        return $stmt->fetchAll();
    }

    // Get prescriptions by doctor
    public function findByDoctor($doctorId) {
        $sql = "SELECT * FROM PRESCRIPTION WHERE doctor_id = :doctor_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':doctor_id' => $doctorId]);
        return $stmt->fetchAll();
    }

    // Get all prescriptions
    public function findAll() {
        $sql = "SELECT * FROM PRESCRIPTION";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Update prescription
    public function update($prescription) {
        $sql = "UPDATE PRESCRIPTION SET doctor_id = :doctor_id, patient_id = :patient_id, date = :date, status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':doctor_id' => $prescription['doctor_id'] ?? null,
            ':patient_id' => $prescription['patient_id'] ?? null,
            ':date' => $prescription['date'] ?? null,
            ':status' => $prescription['status'] ?? null,
            ':id' => $prescription['id'] ?? null,
        ]);
        return;
    }

    // Delete prescription
    public function delete($id) {
        $sql = "DELETE FROM PRESCRIPTION WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $id]);
        return;
    }

    // Get prescription details
    public function getPrescriptionDetails($prescriptionId) {
        $sql = "SELECT * FROM PRESCRIPTIONDETAILS WHERE prescription_id = :prescription_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':prescription_id' => $prescriptionId]);
        return $stmt->fetchAll();
    }

    // Add prescription detail
    public function addPrescriptionDetail($detail) {
        // Insert prescription detail record (prescription_id, drug_id,duration, dosage, frequency, refills, special_instructions)
        $sql = "INSERT INTO PRESCRIPTIONDETAILS (prescription_id, drug_id, duration, dosage, frequency, refills, special_instructions) VALUES (:prescription_id, :drug_id, :duration, :dosage, :frequency, :refills, :special_instructions)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':prescription_id' => $detail['prescription_id'] ?? null,
            ':drug_id' => $detail['drug_id'] ?? null,
            ':duration' => $detail['duration'] ?? null,
            ':dosage' => $detail['dosage'] ?? null,
            ':frequency' => $detail['frequency'] ?? null,
            ':refills' => $detail['refills'] ?? null,
            ':special_instructions' => $detail['special_instructions'] ?? null,
        ]); 
        return $this->conn->lastInsertId();
    }
     

    // Get prescriptions by status
    public function findByStatus($status) {
        $sql = "SELECT * FROM PRESCRIPTION WHERE status = :status";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':status' => $status]);
        return $stmt->fetchAll();
    }
}
?>
