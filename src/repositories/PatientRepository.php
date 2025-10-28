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
        //insert patient into database
        $sql = "INSERT INTO " . $this->table_name . " (user_id, medical_history, allergies, created_at) VALUES (:user_id, :medical_history, :allergies, :created_at)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':user_id' => $patient['user_id'] ?? null,
            ':medical_history' => $patient['medical_history'] ?? null,
            ':allergies' => $patient['allergies'] ?? null,
            ':created_at' => $patient['created_at'] ?? null,
        ]);
        return $this->conn->lastInsertId();
    }

    // Get patient by user ID
    public function findByUserId($userId) {
        $sql = "SELECT * FROM " . $this->table_name . " WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetch();
    }

    

    // Get all patients
    public function findAll() {
        $sql = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Update patient
    public function update($patient) {
        $sql = "UPDATE " . $this->table_name . " SET medical_history = :medical_history, allergies = :allergies, created_at = :created_at WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':medical_history' => $patient['medical_history'] ?? null,
            ':allergies' => $patient['allergies'] ?? null,
            ':created_at' => $patient['created_at'] ?? null,
            ':user_id' => $patient['user_id'] ?? null,
        ]);
        return;
    }

    // Delete patient
    public function delete($userId) {
        $sql = "DELETE FROM PATIENT WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return;
    }

    // Search patients
    public function search($searchTerm) {
        $sql = "SELECT * FROM PATIENT WHERE name LIKE :searchTerm";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':searchTerm' => "%$searchTerm%"]);
    return $stmt->fetchAll();
    }

    // Check if patient exists
    public function exists($userId) {
        $sql = "SELECT COUNT(*) FROM PATIENT WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchColumn() > 0;
    }
}
?>
