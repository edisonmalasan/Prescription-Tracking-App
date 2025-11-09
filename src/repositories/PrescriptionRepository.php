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
        // details should be an array of detail arrays (drug_id, duration, dosage, frequency, refills, special_instructions, description)
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
                $detailSql = "INSERT INTO " . $this->details_table . " (prescription_id, drug_id, duration, dosage, frequency, refills, special_instructions, description) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
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
                    $description = $d['description'] ?? $d->description ?? '';

                    $dStmt->bind_param('iisssiss', $prescription_id, $drug_id, $duration, $dosage, $frequency, $refills, $special_instructions, $description);
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
        $sql = "SELECT * FROM " . $this->table_name;
        $res = $this->conn->query($sql);
        if ($res) {
            return $res->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }

    //update prescription details
    public function update($prescription) {
        $prescription_id = $prescription['prescription_id'] ?? null;
        $details = $prescription['details'] ?? [];
        if ($prescription_id === null || empty($details)) {
            return false;
        }

        $this->conn->begin_transaction();
        try {
            $updateSql = "UPDATE " . $this->details_table . " SET duration = ?, dosage = ?, frequency = ?, refills = ?, special_instructions = ?, description = ? WHERE prescription_id = ? AND drug_id = ?";
            $uStmt = $this->conn->prepare($updateSql);
            if (! $uStmt) {
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
                $description = $d['description'] ?? $d->description ?? '';

                $uStmt->bind_param('sssissii', $duration, $dosage, $frequency, $refills, $special_instructions, $description, $prescription_id, $drug_id);
                if (! $uStmt->execute()) {
                    $this->conn->rollback();
                    return false;
                }

                if ($uStmt->affected_rows === 0) {
                    $insOk = $this->addPrescriptionDetail([
                        'prescription_id' => $prescription_id,
                        'drug_id' => $drug_id,
                        'duration' => $duration,
                        'dosage' => $dosage,
                        'frequency' => $frequency,
                        'refills' => $refills,
                        'special_instructions' => $special_instructions,
                        'description' => $description
                    ]);
                    if (! $insOk) {
                        $this->conn->rollback();
                        return false;
                    }
                }
            }
            $uStmt->close();
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
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
        $sql = "INSERT INTO " . $this->details_table . " (prescription_id, drug_id, duration, dosage, frequency, refills, special_instructions, description) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
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
        $description = $detail['description'] ?? '';

        $stmt->bind_param('iisssiss', $prescription_id, $drug_id, $duration, $dosage, $frequency, $refills, $special_instructions, $description);
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
