<?php

require_once '../config/db.php';
require_once '../models/medicalRecordModel.php';

class MedicalRecordRepository {
    private $conn;
    private $table_name = "medicalrecord";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function create($medicalRecord) {
        $sql = "INSERT INTO " . $this->table_name . " (user_id, height, weight, allergies) VALUES (:user_id, :height, :weight, :allergies)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':user_id' => $medicalRecord['user_id'] ?? null,
            ':height' => $medicalRecord['height'] ?? null,
            ':weight' => $medicalRecord['weight'] ?? null,
            ':allergies' => $medicalRecord['allergies'] ?? ''
        ]);
        return $this->conn->lastInsertId();
    }

    public function findById($id) {
        $sql = "SELECT * FROM " . $this->table_name . " WHERE record_id = :record_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':record_id' => $id]);
        return $stmt->fetch();
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

    public function update($medicalRecord) {
        $sql = "UPDATE " . $this->table_name . " SET height = :height, weight = :weight, allergies = :allergies WHERE record_id = :record_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':height' => $medicalRecord['height'] ?? null,
            ':weight' => $medicalRecord['weight'] ?? null,
            ':allergies' => $medicalRecord['allergies'] ?? '',
            ':record_id' => $medicalRecord['record_id'] ?? null,
        ]);
        return $stmt->rowCount() > 0;
    }

    public function delete($id) {
        $sql = "DELETE FROM " . $this->table_name . " WHERE record_id = :record_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':record_id' => $id]);
        return $stmt->rowCount() > 0;
    }
}
?>
