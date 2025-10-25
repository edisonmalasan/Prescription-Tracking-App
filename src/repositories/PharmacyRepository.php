<?php

require_once '../config/db.php';
require_once '../models/pharmacyModel.php';

class PharmacyRepository {
    private $conn;
    private $table_name = "PHARMACY";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Create a new pharmacy
    public function create($pharmacy) {
        // TODO: Implement create pharmacy
        return;
    }

    // Get pharmacy by user ID
    public function findByUserId($userId) {
        // TODO: Implement find pharmacy by user ID
        return;
    }

    // Get pharmacy by name
    public function findByName($pharmacyName) {
        // TODO: Implement find pharmacy by name
        return;
    }

    // Get all pharmacies
    public function findAll() {
        // TODO: Implement find all pharmacies
    }

    // Update pharmacy
    public function update($pharmacy) {
        // TODO: Implement update pharmacy
    }

    // Delete pharmacy
    public function delete($userId) {
        // TODO: Implement delete pharmacy
        return;
    }

    // Search pharmacies
    public function search($searchTerm) {
        // TODO: Implement search pharmacies
    }

    // Check if pharmacy exists
    public function exists($userId) {
        // TODO: Implement check if pharmacy exists
        return;
    }
}
?>
