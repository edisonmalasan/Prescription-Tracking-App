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
        // Expecting $prescription to be an array with keys: prescribing_doctor, record_id, prescription_date, status, details
        // details should be an array of detail arrays (drug_id, duration, dosage, frequency, refills, special_instructions)
        $prescribing_doctor = $prescription['prescribing_doctor'] ?? null;
        $record_id = $prescription['record_id'] ?? null;
        $prescription_date = $prescription['prescription_date'] ?? date('Y-m-d');
        $status = $prescription['status'] ?? 'pending';
        $details = $prescription['details'] ?? [];

    // Debug: show received input (CLI-friendly)
    echo "[DEBUG] create input: prescribing_doctor=" . var_export($prescribing_doctor, true) . ", record_id=" . var_export($record_id, true) . ", details_count=" . count($details) . "\n";

    // Start transaction so prescription + details are atomic
        $this->conn->begin_transaction();
        try {
            $sql = "INSERT INTO " . $this->table_name . " (prescribing_doctor, record_id, prescription_date, status) VALUES (?, ?, ?, ?)";
            $stmt = $this->conn->prepare($sql);
            if (! $stmt) {
                error_log("PrescriptionRepository.create prepare failed: " . $this->conn->error);
                // echo for CLI visibility
                echo "[DEBUG] prepare failed: " . $this->conn->error . "\n";
                $this->conn->rollback();
                return false;
            }

            error_log("[DEBUG] Creating prescription: Doctor={$prescribing_doctor}, Record={$record_id}, Date={$prescription_date}, Status={$status}");
            $stmt->bind_param('iiss', $prescribing_doctor, $record_id, $prescription_date, $status);
            if (! $stmt->execute()) {
                error_log("PrescriptionRepository.create execute failed: " . $stmt->error);
                echo "[DEBUG] execute failed: " . $stmt->error . "\n";
                $this->conn->rollback();
                return false;
            }
            $prescription_id = $this->conn->insert_id;

            // Insert details if any
            if (!empty($details)) {
                $detailSql = "INSERT INTO " . $this->details_table . " (prescription_id, drug_id, duration, dosage, frequency, refills, special_instructions) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $dStmt = $this->conn->prepare($detailSql);
                if (! $dStmt) {
                    error_log("PrescriptionRepository.create detail prepare failed: " . $this->conn->error);
                    echo "[DEBUG] detail prepare failed: " . $this->conn->error . "\n";
                    $this->conn->rollback();
                    return false;
                }

                foreach ($details as $d) {
                    $drug_id = $d['drug_id'] ?? $d->drug_id ?? null;
                    $duration = $d['duration'] ?? $d->duration ?? '';
                    $dosage = $d['dosage'] ?? $d->dosage ?? '';
                    $frequency = $d['frequency'] ?? $d->frequency ?? '';
                    $refills = isset($d['refills']) ? (int)$d['refills'] : 0;
                    $special_instructions = $d['special_instructions'] ?? $d->special_instructions ?? '';

                    $dStmt->bind_param('iisssis', $prescription_id, $drug_id, $duration, $dosage, $frequency, $refills, $special_instructions);
                    if (! $dStmt->execute()) {
                        error_log("PrescriptionRepository.create detail execute failed: " . $dStmt->error);
                        echo "[DEBUG] detail execute failed: " . $dStmt->error . "\n";
                        $this->conn->rollback();
                        return false;
                    }
                }
                $dStmt->close();
            }

            $stmt->close();
            $this->conn->commit();
            return $prescription_id;
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("PrescriptionRepository.create exception: " . $e->getMessage());
            echo "[DEBUG] exception: " . $e->getMessage() . "\n";
            return false;
        }
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

    public function findByPatientWithDoctorFields($patientId) {
    $sql = "SELECT 
                p.*,
                u.first_name AS doctor_first_name,
                u.last_name AS doctor_last_name
            FROM prescription p
            JOIN medicalrecord m ON p.record_id = m.record_id
            JOIN users u ON p.prescribing_doctor = u.user_id
            WHERE m.user_id = ?";
    
    $stmt = $this->conn->prepare($sql);
    if (! $stmt) {
        return [];
    }

    $stmt->bind_param('i', $patientId);
    $stmt->execute();
    $res = $stmt->get_result();

    return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
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
                    pd.frequency,
                    dr.generic_name AS medication_name,
                    pd.special_instructions AS notes
                FROM prescription p
                JOIN medicalrecord mr ON p.record_id = mr.record_id
                JOIN users u ON mr.user_id = u.user_id
                JOIN doctor d ON p.prescribing_doctor = d.user_id
                JOIN users d_user ON d.user_id = d_user.user_id
                LEFT JOIN prescriptiondetails pd ON p.prescription_id = pd.prescription_id
                LEFT JOIN drug dr ON pd.drug_id = dr.drug_id";
        $res = $this->conn->query($sql);
        if ($res) {
            return $res->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }

    //update prescription details
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
