<?php

require_once '../config/db.php';
require_once '../models/prescriptionModel.php';
require_once '../models/prescriptionDetailModel.php';

class PrescriptionRepository {
    private $conn;
    private $table_name = "prescription";
    private $details_table = "prescriptiondetails";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Create a new prescription
    public function create($prescription) {
        $sql = "INSERT INTO " . $this->table_name . " (prescribing_doctor, record_id, prescription_date, status) VALUES (:prescribing_doctor, :record_id, :prescription_date, :status)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':prescribing_doctor' => $prescription['prescribing_doctor'] ?? null,
            ':record_id' => $prescription['record_id'] ?? null,
            ':prescription_date' => $prescription['prescription_date'] ?? date('Y-m-d'),
            ':status' => $prescription['status'] ?? 'pending',
        ]);
        return $this->conn->lastInsertId();
    }        

    // Get prescription by ID
    public function findById($id) {
        $sql = "SELECT * FROM " . $this->table_name . " WHERE prescription_id = :prescription_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':prescription_id' => $id]);
        return $stmt->fetch();
    }
    
    // Get prescriptions by patient (through medical record)
    public function findByPatient($patientId) {
        $sql = "SELECT p.* FROM " . $this->table_name . " p 
                JOIN medicalrecord m ON p.record_id = m.record_id 
                WHERE m.user_id = :user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':user_id' => $patientId]);
        return $stmt->fetchAll();
    }

    // Get prescriptions by doctor
    public function findByDoctor($doctorId) {
        $sql = "SELECT * FROM " . $this->table_name . " WHERE prescribing_doctor = :prescribing_doctor";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':prescribing_doctor' => $doctorId]);
        return $stmt->fetchAll();
    }

    // Get all prescriptions
    public function findAll() {
        $sql = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Update prescription
    public function update($prescription) {
        $sql = "UPDATE " . $this->table_name . " SET prescribing_doctor = :prescribing_doctor, record_id = :record_id, prescription_date = :prescription_date, status = :status WHERE prescription_id = :prescription_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':prescribing_doctor' => $prescription['prescribing_doctor'] ?? null,
            ':record_id' => $prescription['record_id'] ?? null,
            ':prescription_date' => $prescription['prescription_date'] ?? null,
            ':status' => $prescription['status'] ?? null,
            ':prescription_id' => $prescription['prescription_id'] ?? null,
        ]);
        return $stmt->rowCount() > 0;
    }

    public function delete($id) {
        $sql = "DELETE FROM " . $this->table_name . " WHERE prescription_id = :prescription_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':prescription_id' => $id]);
        return $stmt->rowCount() > 0;
    }

    public function getPrescriptionDetails($prescriptionId) {
        $sql = "SELECT * FROM " . $this->details_table . " WHERE prescription_id = :prescription_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':prescription_id' => $prescriptionId]);
        return $stmt->fetchAll();
    }

    public function addPrescriptionDetail($detail) {
        $sql = "INSERT INTO " . $this->details_table . " (prescription_id, drug_id, duration, dosage, frequency, refills, special_instructions) VALUES (:prescription_id, :drug_id, :duration, :dosage, :frequency, :refills, :special_instructions)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':prescription_id' => $detail['prescription_id'] ?? null,
            ':drug_id' => $detail['drug_id'] ?? null,
            ':duration' => $detail['duration'] ?? '',
            ':dosage' => $detail['dosage'] ?? '',
            ':frequency' => $detail['frequency'] ?? '',
            ':refills' => $detail['refills'] ?? 0,
            ':special_instructions' => $detail['special_instructions'] ?? '',
        ]); 
        return $stmt->rowCount() > 0;
    }
     
    // get prescriptions by status
    public function findByStatus($status) {
        $sql = "SELECT * FROM " . $this->table_name . " WHERE status = :status";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':status' => $status]);
        return $stmt->fetchAll();
    }
}

?>
