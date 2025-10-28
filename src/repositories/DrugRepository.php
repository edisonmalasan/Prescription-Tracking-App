<?php

require_once '../config/db.php';
require_once '../models/drugModel.php';

class DrugRepository {
    private $conn;
    private $table_name = "DRUG";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Create a new drug
    public function create($drug) {
        $sql = "INSERT INTO " . $this->table_name . " (generic_name, brand_name, category, is_controlled, expiration_date, created_at) VALUES (:generic_name, :brand_name, :category, :is_controlled, :expiration_date, :created_at)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':generic_name' => $drug->generic_name,
            ':brand_name' => $drug->brand_name,
            ':category' => $drug->category,
            ':is_controlled' => $drug->is_controlled,
            ':expiration_date' => $drug->expiration_date,
            ':created_at' => $drug->created_at
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

    // Get controlled substances
    public function findControlled() {
        $sql = "SELECT * FROM " . $this->table_name . " WHERE is_controlled = 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Update drug
    public function update($drug) {
        $sql = "UPDATE " . $this->table_name . " SET generic_name = :generic_name, brand_name = :brand_name, category = :category, is_controlled = :is_controlled, expiration_date = :expiration_date WHERE drug_id = :drug_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':generic_name' => $drug['generic_name'] ?? null,
            ':brand_name' => $drug['brand_name'] ?? null,
            ':category' => $drug['category'] ?? null,
            ':is_controlled' => $drug['is_controlled'] ?? null,
            ':expiration_date' => $drug['expiration_date'] ?? null,
            ':drug_id' => $drug['drug_id'] ?? null,
        ]);
        return;
     
    }

    // Delete drug
    public function delete($id) {
        $sql = "DELETE FROM " . $this->table_name . " WHERE drug_id = :drug_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':drug_id' => $id]);
        return;
    }

    // Search drugs
    public function search($searchTerm) {
        $sql = "SELECT * FROM " . $this->table_name . " WHERE generic_name LIKE :searchTerm OR brand_name LIKE :searchTerm";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':searchTerm' => '%' . $searchTerm . '%']);
        return $stmt->fetchAll();
    }

    // Get non-expired drugs
    public function findNonExpired() {
        $sql = "SELECT * FROM " . $this->table_name . " WHERE expiration_date > NOW()";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

}

?>
