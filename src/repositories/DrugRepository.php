<?php

require_once '../config/db.php';
require_once '../models/drugModel.php';

class DrugRepository {
    private $conn;
    private $table_name = "drug";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Create a new drug
    public function create($drug) {
        $sql = "INSERT INTO " . $this->table_name . " (generic_name, brand, chemical_name, category, expiry_date, isControlled) VALUES (:generic_name, :brand, :chemical_name, :category, :expiry_date, :isControlled)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':generic_name' => $drug['generic_name'] ?? '',
            ':brand' => $drug['brand'] ?? '',
            ':chemical_name' => $drug['chemical_name'] ?? '',
            ':category' => $drug['category'] ?? '',
            ':expiry_date' => $drug['expiry_date'] ?? null,
            ':isControlled' => $drug['isControlled'] ?? 0
        ]);
        return $this->conn->lastInsertId();
    }

    // Get drug by ID
    public function findById($id) {
        $sql = "SELECT * FROM " . $this->table_name . " WHERE drug_id = :drug_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':drug_id' => $id]);
        return $stmt->fetch();
    }

    // Get drug by generic name
    public function findByGenericName($genericName) {
        $sql = "SELECT * FROM " . $this->table_name . " WHERE generic_name = :generic_name";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':generic_name' => $genericName]);
        return $stmt->fetch();
    }

    // Get all drugs
    public function findAll() {
        $sql = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Get drugs by category
    public function findByCategory($category) {
        $sql = "SELECT * FROM " . $this->table_name . " WHERE category = :category";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':category' => $category]);
        return $stmt->fetchAll();
    }

    // Get controlled drugs
    public function findControlled() {
        $sql = "SELECT * FROM " . $this->table_name . " WHERE isControlled = 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Update drug
    public function update($drug) {
        $sql = "UPDATE " . $this->table_name . " SET generic_name = :generic_name, brand = :brand, chemical_name = :chemical_name, category = :category, expiry_date = :expiry_date, isControlled = :isControlled WHERE drug_id = :drug_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':generic_name' => $drug['generic_name'] ?? '',
            ':brand' => $drug['brand'] ?? '',
            ':chemical_name' => $drug['chemical_name'] ?? '',
            ':category' => $drug['category'] ?? '',
            ':expiry_date' => $drug['expiry_date'] ?? null,
            ':isControlled' => $drug['isControlled'] ?? 0,
            ':drug_id' => $drug['drug_id'] ?? null,
        ]);
        return $stmt->rowCount() > 0;
    }

    // Delete drug
    public function delete($id) {
        $sql = "DELETE FROM " . $this->table_name . " WHERE drug_id = :drug_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':drug_id' => $id]);
        return $stmt->rowCount() > 0;
    }

    // Search drugs
    public function search($searchTerm) {
        $sql = "SELECT * FROM " . $this->table_name . " WHERE generic_name LIKE :searchTerm OR brand LIKE :searchTerm OR chemical_name LIKE :searchTerm";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':searchTerm' => "%$searchTerm%"]);
        return $stmt->fetchAll();
    }
}
?>