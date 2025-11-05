<?php

require_once '../config/db.php';
require_once '../models/doctorModel.php';
require_once 'UserRepository.php';

class DoctorRepository {
    private $conn;
    private $table_name = "doctor";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function create($doctor) {
        $data = [];
        if (is_array($doctor)) {
            $data = $doctor;
        } elseif ($doctor instanceof \stdClass) {
            $data = (array)$doctor;
        } elseif ($doctor instanceof DoctorModel) {
            $data = $doctor->toArray();
        }

        $now = date('Y-m-d H:i:s');
        $userData = [
            'last_name' => $data['last_name'] ?? ($data['lastName'] ?? null),
            'first_name' => $data['first_name'] ?? ($data['firstName'] ?? null),
            'role' => isset($data['role']) ? strtoupper($data['role']) : 'DOCTOR',
            'email' => $data['email'] ?? null,
            'contactno' => $data['contactno'] ?? ($data['contactNo'] ?? null),
            'pass_hash' => $data['pass_hash'] ?? ($data['passHash'] ?? null),
            'address' => $data['address'] ?? null,
            'created_at' => $data['created_at'] ?? $now
        ];

        if (!empty($data['user_id'])) {
            $userId = $data['user_id'];
        } else {
            $userRepo = new UserRepository();
            $userId = $userRepo->create($userData);
        }

        $sql = "INSERT INTO " . $this->table_name . " (user_id, birth_date, specialization, prc_license, clinic_name, isVerified) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        if (! $stmt) {
            return $userId;
        }

        $birth_date = $data['birth_date'] ?? ($data['birthDate'] ?? null);
        $specialization = $data['specialization'] ?? null;
        $prc_license = $data['prc_license'] ?? ($data['prcLicense'] ?? null);
        $clinic_name = $data['clinic_name'] ?? ($data['clinicName'] ?? null);
        $isVerified = isset($data['isVerified']) ? (int)$data['isVerified'] : (isset($data['verified']) ? (int)$data['verified'] : 0);

        $stmt->bind_param('issssi', $userId, $birth_date, $specialization, $prc_license, $clinic_name, $isVerified);
        $stmt->execute();
        return $userId;
    }

    public function createDoctorRecord($userId, $data) {
        $sql = "INSERT INTO " . $this->table_name . " (user_id, birth_date, specialization, prc_license, clinic_name, isVerified) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        if (! $stmt) {
            return false;
        }

        $birth_date = $data['birth_date'] ?? ($data['birthDate'] ?? null);
        $specialization = $data['specialization'] ?? null;
        $prc_license = $data['prc_license'] ?? ($data['prcLicense'] ?? null);
        $clinic_name = $data['clinic_name'] ?? ($data['clinicName'] ?? null);
        $isVerified = isset($data['isVerified']) ? (int)$data['isVerified'] : (isset($data['verified']) ? (int)$data['verified'] : 0);

        $stmt->bind_param('issssi', $userId, $birth_date, $specialization, $prc_license, $clinic_name, $isVerified);
        $res = $stmt->execute();

        return (bool) $res;
    }
    public function findByUserId($userId) {
        $sql = "SELECT * FROM " . $this->table_name . " WHERE user_id = ?";
        $stmt = $this->conn->prepare($sql);
        if (! $stmt) {
            return null;
        }
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res) {
            return $res->fetch_assoc();
        }
        return null;
    }

    // Get doctor by PRC license
    public function findByPrcLicense($license) {
        $sql = "SELECT * FROM " . $this->table_name . " WHERE prc_license = ?";
        $stmt = $this->conn->prepare($sql);
        if (! $stmt) {
            return null;
        }
        $stmt->bind_param('s', $license);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res) {
            return $res->fetch_assoc();
        }
        return null;
     
    }

    // Get all doctors
    public function findAll() {
        $sql = "SELECT * FROM " . $this->table_name;
        $res = $this->conn->query($sql);
        if ($res) {
            return $res->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }

    // Get verified doctors
    public function findVerified() {
        $sql = "SELECT * FROM " . $this->table_name . " WHERE isVerified = 1";
        $res = $this->conn->query($sql);
        if ($res) {
            return $res->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }

    // Update doctor
    public function update($doctor) {
        $sql = "UPDATE " . $this->table_name . " SET birth_date = ?, specialization = ?, prc_license = ?, clinic_name = ?, isVerified = ? WHERE user_id = ?";
        $stmt = $this->conn->prepare($sql);
        if (! $stmt) {
            return false;
        }

        $birth_date = $doctor['birth_date'] ?? null;
        $specialization = $doctor['specialization'] ?? null;
        $prc_license = $doctor['prc_license'] ?? null;
        $clinic_name = $doctor['clinic_name'] ?? null;
        $isVerified = isset($doctor['isVerified']) ? (int)$doctor['isVerified'] : 0;
        $user_id = $doctor['user_id'] ?? null;

        if ($user_id === null) {
            return false;
        }

        $stmt->bind_param('ssssii', $birth_date, $specialization, $prc_license, $clinic_name, $isVerified, $user_id);
        $stmt->execute();
        return ($stmt->affected_rows > 0);
    }

    // Delete doctor
    public function delete($userId) {
        $sql = "DELETE FROM " . $this->table_name . " WHERE user_id = ?";
        $stmt = $this->conn->prepare($sql);
        if (! $stmt) {
            return false;
        }
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        return ($stmt->affected_rows > 0);
    }

    

    // Search doctors by specialization
    public function findBySpecialization($specialization) {
        $sql = "SELECT * FROM " . $this->table_name . " WHERE specialization = ?";
        $stmt = $this->conn->prepare($sql);
        if (! $stmt) {
            return [];
        }
        $stmt->bind_param('s', $specialization);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res) {
            return $res->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }

    // Verify doctor
    public function verifyDoctor($userId) {
        $sql = "UPDATE " . $this->table_name . " SET isVerified = 1 WHERE user_id = ?";
        $stmt = $this->conn->prepare($sql);
        if (! $stmt) {
            return false;
        }
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        return ($stmt->affected_rows > 0);
    }

}

?>
