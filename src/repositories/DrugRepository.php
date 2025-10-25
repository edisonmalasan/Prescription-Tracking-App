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
        // TODO: Implement create drug
        return;
    }

    // Get drug by ID
    public function findById($id) {
        // TODO: Implement find drug by ID
        return;
    }

    // Get drug by generic name
    public function findByGenericName($genericName) {
        // TODO: Implement find drug by generic name
    }

    // Get all drugs
    public function findAll() {
        // TODO: Implement find all drugs
        return;
    }

    // Get drugs by category
    public function findByCategory($category) {
        // TODO: Implement find drugs by category
    }

    // Get controlled substances
    public function findControlled() {
        // TODO: Implement find controlled substances
        return;
    }

    // Update drug
    public function update($drug) {
        // TODO: Implement update drug
        return;
    }

    // Delete drug
    public function delete($id) {
        // TODO: Implement delete drug
        return;
    }

    // Search drugs
    public function search($searchTerm) {
        // TODO: Implement search drugs
        return;
    }

    // Get non-expired drugs
    public function findNonExpired() {
        // TODO: Implement find non-expired drugs
        return;
    }
}
?>
