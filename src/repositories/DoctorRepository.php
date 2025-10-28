<?php

require_once '../config/db.php';
require_once '../models/doctorModel.php';
require_once 'UserRepository.php';

class DoctorRepository {
    private $conn;
    private $table_name = "DOCTOR";

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
            // schema uses uppercase enum values (e.g. 'DOCTOR') in some DB dumps; normalize to uppercase
            'role' => isset($data['role']) ? strtoupper($data['role']) : 'DOCTOR',
            'email' => $data['email'] ?? null,
            'contactno' => $data['contactno'] ?? ($data['contactNo'] ?? null),
            'pass_hash' => $data['pass_hash'] ?? ($data['passHash'] ?? null),
            'address' => $data['address'] ?? null,
            'created_at' => $data['created_at'] ?? $now
        ];

        // If caller already provided a user_id, use it instead of creating a new user
        if (!empty($data['user_id'])) {
            $userId = $data['user_id'];
        } else {
            // Create user record first
            $userRepo = new UserRepository();
            $userId = $userRepo->create($userData);
        }

        // Now insert doctor-specific data
        $sql = "INSERT INTO " . $this->table_name . " (user_id, birth_date, specialization, prc_license, clinic_name, isVerified) VALUES (:user_id, :birth_date, :specialization, :prc_license, :clinic_name, :isVerified)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':user_id' => $userId,
            ':birth_date' => $data['birth_date'] ?? ($data['birthDate'] ?? null),
            ':specialization' => $data['specialization'] ?? null,
            ':prc_license' => $data['prc_license'] ?? ($data['prcLicense'] ?? null),
            ':clinic_name' => $data['clinic_name'] ?? ($data['clinicName'] ?? null),
            ':isVerified' => isset($data['isVerified']) ? (int)$data['isVerified'] : (isset($data['verified']) ? (int)$data['verified'] : 0)
        ]);

        // Return the created user_id to indicate success
        return $userId;
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
        $sql = "SELECT * FROM " . $this->table_name . " WHERE isVerified = 1";
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
        $sql = "UPDATE " . $this->table_name . " SET isVerified = 1 WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return;
    }

}

?>
