<?php

require_once '../config/db.php';
require_once '../models/patientModel.php';
require_once 'UserRepository.php';

class PatientRepository {
    private $conn;
    private $table_name = "patient";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Create a new patient. Accepts array, stdClass, or PatientModel.
    // If caller provides user_id it will be reused; otherwise a users record will be created.
    // Returns the user_id on success, or false on failure.
    public function create($patient) {
        $data = [];
        if (is_array($patient)) {
            $data = $patient;
        } elseif ($patient instanceof \stdClass) {
            $data = (array)$patient;
        } elseif ($patient instanceof PatientModel) {
            $data = $patient->toArray();
        }

        $now = date('Y-m-d H:i:s');
        $userData = [
            'last_name' => $data['last_name'] ?? null,
            'first_name' => $data['first_name'] ?? null,
            'role' => isset($data['role']) ? strtoupper($data['role']) : 'PATIENT',
            'email' => $data['email'] ?? null,
            'contactno' => $data['contactno'] ?? null,
            'pass_hash' => $data['pass_hash'] ?? null,
            'address' => $data['address'] ?? null,
            'created_at' => $data['created_at'] ?? $now
        ];

        // Use provided user_id if available
        if (!empty($data['user_id'])) {
            $userId = $data['user_id'];
        } else {
            $userRepo = new UserRepository();
            $userId = $userRepo->create($userData);
        }

        $sql = "INSERT INTO " . $this->table_name . " (user_id, birth_date) VALUES (:user_id, :birth_date)";
        $stmt = $this->conn->prepare($sql);
        $ok = $stmt->execute([
            ':user_id' => $userId,
            ':birth_date' => $data['birth_date'] ?? ($data['birthDate'] ?? null)
        ]);

        if ($ok && $stmt->rowCount() > 0) {
            return $userId;
        }

        return false;
    }

    public function findByUserId($userId) {
        $sql = "SELECT * FROM " . $this->table_name . " WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetch();
    }

    public function findAll() {
        $sql = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function update($patient) {
        $sql = "UPDATE " . $this->table_name . " SET birth_date = :birth_date WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':birth_date' => $patient['birth_date'] ?? null,
            ':user_id' => $patient['user_id'] ?? null,
        ]); 
        return $stmt->rowCount() > 0;
    }

    public function delete($userId) {
        $sql = "DELETE FROM " . $this->table_name . " WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->rowCount() > 0;
    }
}
?>