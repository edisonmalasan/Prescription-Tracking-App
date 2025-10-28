<?php

require_once '../config/db.php';
require_once '../models/pharmacyModel.php';
// Use UserRepository to create the base user record for a pharmacy
require_once 'UserRepository.php';

class PharmacyRepository {
    private $conn;
    private $table_name = "PHARMACY";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Create a new pharmacy
    public function create($pharmacy) {
        // accept array, stdClass or model
        $data = [];
        if (is_array($pharmacy)) {
            $data = $pharmacy;
        } elseif ($pharmacy instanceof \stdClass) {
            $data = (array)$pharmacy;
        } elseif ($pharmacy instanceof PharmacyModel) {
            $data = $pharmacy->toArray();
        }

        $now = date('Y-m-d H:i:s');
        $userData = [
            'last_name' => $data['last_name'] ?? null,
            'first_name' => $data['first_name'] ?? null,
            'role' => isset($data['role']) ? strtoupper($data['role']) : 'PHARMACY',
            'email' => $data['email'] ?? null,
            'contactno' => $data['contactno'] ?? null,
            'pass_hash' => $data['pass_hash'] ?? null,
            'address' => $data['address'] ?? null,
            'created_at' => $data['created_at'] ?? $now
        ];

        // If caller provided user_id, reuse it. Otherwise create user first.
        if (!empty($data['user_id'])) {
            $userId = $data['user_id'];
        } else {
            $userRepo = new UserRepository();
            $userId = $userRepo->create($userData);
        }

        // Insert pharmacy-specific data. Note: DB dump 'pharmacy' table does not include an address column.
        $sql = "INSERT INTO " . $this->table_name . " (user_id, pharmacy_name, phar_license, open_time, close_time, dates_open) VALUES (:user_id, :pharmacy_name, :phar_license, :open_time, :close_time, :dates_open)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':user_id' => $userId,
            ':pharmacy_name' => $data['pharmacy_name'] ?? ($data['pharmacyName'] ?? null),
            ':phar_license' => $data['phar_license'] ?? ($data['pharLicense'] ?? null),
            ':open_time' => $data['open_time'] ?? ($data['openTime'] ?? null),
            ':close_time' => $data['close_time'] ?? ($data['closeTime'] ?? null),
            ':dates_open' => $data['dates_open'] ?? ($data['datesOpen'] ?? null),
        ]);

        // Return the user_id (pharmacy.user_id is PK)
        return $userId;
    }

    // Get pharmacy by user ID
    public function findByUserId($userId) {
        $sql = "SELECT * FROM " . $this->table_name . " WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetch();
      
    }

    // Get pharmacy by name
    public function findByName($pharmacyName) {
        $sql = "SELECT * FROM " . $this->table_name . " WHERE pharmacy_name = :pharmacy_name";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':pharmacy_name' => $pharmacyName]);
        return $stmt->fetch();
    }

    // Get all pharmacies
    public function findAll() {
        $sql = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
        
    }

    // Update pharmacy
    public function update($pharmacy) {
        $sql = "UPDATE " . $this->table_name . " SET pharmacy_name = :pharmacy_name, phar_license = :phar_license, address = :address, open_time = :open_time, close_time = :close_time, dates_open = :dates_open WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':pharmacy_name' => $pharmacy->pharmacy_name,
            ':phar_license' => $pharmacy->phar_license,
            ':address' => $pharmacy->address,
            ':open_time' => $pharmacy->open_time,
            ':close_time' => $pharmacy->close_time,
            ':dates_open' => $pharmacy->dates_open,
            ':user_id' => $pharmacy->user_id
        ]);
        return;
    }

    // Delete pharmacy
    public function delete($userId) {
        $sql = "DELETE FROM " . $this->table_name . " WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return;
       
    }

    // Search pharmacies
    public function search($searchTerm) {
        $sql = "SELECT * FROM " . $this->table_name . " WHERE pharmacy_name LIKE :searchTerm";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':searchTerm' => "%$searchTerm%"]);
        return $stmt->fetchAll();
    }

    // Check if pharmacy exists
    public function exists($userId) {
        $sql = "SELECT COUNT(*) FROM " . $this->table_name . " WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchColumn() > 0;
    }
}
?>
