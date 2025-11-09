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
        $sql = "INSERT INTO " . $this->table_name . " (prescribing_doctor, record_id, prescription_date, status) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        if (! $stmt) {
            return false;
        }

        $prescribing_doctor = $prescription['prescribing_doctor'] ?? null;
        $record_id = $prescription['record_id'] ?? null;
        $prescription_date = $prescription['prescription_date'] ?? date('Y-m-d');
        $status = $prescription['status'] ?? 'pending';

        $stmt->bind_param('iiss', $prescribing_doctor, $record_id, $prescription_date, $status);
        $ok = $stmt->execute();
        if ($ok) {
            return $this->conn->insert_id;
        }
        return false;
    }        

    // Get prescription by ID
    public function findById($id) {
        $sql = "SELECT * FROM " . $this->table_name . " WHERE prescription_id = ?";
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
    
    // Get prescriptions by patient (through medical record)
    public function findByPatient($patientId) {
        $sql = "SELECT p.* FROM " . $this->table_name . " p 
                JOIN medicalrecord m ON p.record_id = m.record_id 
                WHERE m.user_id = ?";
        $stmt = $this->conn->prepare($sql);
        if (! $stmt) {
            return [];
        }
        $stmt->bind_param('i', $patientId);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res) {
            return $res->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }

    // Get prescriptions by doctor
    public function findByDoctor($doctorId) {
        $sql = "SELECT * FROM " . $this->table_name . " WHERE prescribing_doctor = ?";
        $stmt = $this->conn->prepare($sql);
        if (! $stmt) {
            return [];
        }
        $stmt->bind_param('i', $doctorId);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res) {
            return $res->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }

    // Get all prescriptions
    public function findAll() {
        $sql = "SELECT 
                    p.prescription_id,
                    p.prescription_date,
                    p.status,
                    CONCAT(u.first_name, ' ', u.last_name) AS patient_name,
                    CONCAT(d_user.first_name, ' ', d_user.last_name) AS doctor_name,
                    pd.dosage,
                    pd.duration,
                    dr.medication_name,
                    p.notes
                FROM prescription p
                JOIN medicalrecord mr ON p.record_id = mr.record_id
                JOIN user u ON mr.user_id = u.user_id
                JOIN doctor d ON p.prescribing_doctor = d.user_id
                JOIN user d_user ON d.user_id = d_user.user_id
                LEFT JOIN prescriptiondetails pd ON p.prescription_id = pd.prescription_id
                LEFT JOIN drug dr ON pd.drug_id = dr.drug_id";
        $res = $this->conn->query($sql);
        if ($res) {
            return $res->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }

    // Update prescription
    public function update($prescription) {
        $prescription_id = $prescription['prescription_id'] ?? null;
        if ($prescription_id === null) {
            return false;
        }

        $fields = [];
        $params = [];
        $types = '';

        if (isset($prescription['prescribing_doctor'])) {
            $fields[] = 'prescribing_doctor = ?';
            $params[] = $prescription['prescribing_doctor'];
            $types .= 'i';
        }
        if (isset($prescription['record_id'])) {
            $fields[] = 'record_id = ?';
            $params[] = $prescription['record_id'];
            $types .= 'i';
        }
        if (isset($prescription['prescription_date'])) {
            $fields[] = 'prescription_date = ?';
            $params[] = $prescription['prescription_date'];
            $types .= 's';
        }
        if (isset($prescription['status'])) {
            $fields[] = 'status = ?';
            $params[] = $prescription['status'];
            $types .= 's';
        }

        if (empty($fields)) {
            return false;
        }

        $sql = "UPDATE " . $this->table_name . " SET " . implode(', ', $fields) . " WHERE prescription_id = ?";
        $stmt = $this->conn->prepare($sql);
        if (! $stmt) {
            return false;
        }

        $params[] = $prescription_id;
        $types .= 'i';

        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return ($stmt->affected_rows > 0);
    }

    public function delete($id) {
        $sql = "DELETE FROM " . $this->table_name . " WHERE prescription_id = ?";
        $stmt = $this->conn->prepare($sql);
        if (! $stmt) {
            return false;
        }
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return ($stmt->affected_rows > 0);
    }

    public function getPrescriptionDetails($prescriptionId) {
        $sql = "SELECT * FROM " . $this->details_table . " WHERE prescription_id = ?";
        $stmt = $this->conn->prepare($sql);
        if (! $stmt) {
            return [];
        }
        $stmt->bind_param('i', $prescriptionId);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res) {
            return $res->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }

    public function addPrescriptionDetail($detail) {
        $sql = "INSERT INTO " . $this->details_table . " (prescription_id, drug_id, duration, dosage, frequency, refills, special_instructions) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        if (! $stmt) {
            return false;
        }

        $prescription_id = $detail['prescription_id'] ?? null;
        $drug_id = $detail['drug_id'] ?? null;
        $duration = $detail['duration'] ?? '';
        $dosage = $detail['dosage'] ?? '';
        $frequency = $detail['frequency'] ?? '';
        $refills = isset($detail['refills']) ? (int)$detail['refills'] : 0;
        $special_instructions = $detail['special_instructions'] ?? '';

        $stmt->bind_param('iisssis', $prescription_id, $drug_id, $duration, $dosage, $frequency, $refills, $special_instructions);
        $stmt->execute();
        return ($stmt->affected_rows > 0);
    }
     
    // get prescriptions by status
    public function findByStatus($status) {
        $sql = "SELECT * FROM " . $this->table_name . " WHERE status = ?";
        $stmt = $this->conn->prepare($sql);
        if (! $stmt) {
            return [];
        }
        $stmt->bind_param('s', $status);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res) {
            return $res->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }
}

?>
