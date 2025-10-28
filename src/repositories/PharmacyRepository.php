<?php

require_once '../config/db.php';
require_once '../models/pharmacyModel.php';
require_once 'UserRepository.php';

class PharmacyRepository {
    private $conn;
    private $table_name = "pharmacy";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function create($userId, $data = []) {
        $sql = "INSERT INTO " . $this->table_name . " (user_id, pharmacy_name, phar_license, open_time, close_time, dates_open) VALUES (:user_id, :pharmacy_name, :phar_license, :open_time, :close_time, :dates_open)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':user_id' => $userId,
            ':pharmacy_name' => $data['pharmacy_name'] ?? ($data['pharmacyName'] ?? ''),
            ':phar_license' => $data['phar_license'] ?? ($data['pharLicense'] ?? ''),
            ':open_time' => $data['open_time'] ?? ($data['openTime'] ?? ''),
            ':close_time' => $data['close_time'] ?? ($data['closeTime'] ?? ''),
            ':dates_open' => $data['dates_open'] ?? ($data['datesOpen'] ?? '')
        ]);
        return $stmt->rowCount() > 0;
    }

    public function findByUserId($userId) {
        $sql = "SELECT * FROM " . $this->table_name . " WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetch();
    }

    public function findByPharmacyName($pharmacyName) {
        $sql = "SELECT * FROM " . $this->table_name . " WHERE pharmacy_name = :pharmacy_name";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':pharmacy_name' => $pharmacyName]);
        return $stmt->fetch();
    }

    public function findAll() {
        $sql = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function update($pharmacy) {
        $sql = "UPDATE " . $this->table_name . " SET pharmacy_name = :pharmacy_name, phar_license = :phar_license, open_time = :open_time, close_time = :close_time, dates_open = :dates_open WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':pharmacy_name' => $pharmacy['pharmacy_name'] ?? null,
            ':phar_license' => $pharmacy['phar_license'] ?? null,
            ':open_time' => $pharmacy['open_time'] ?? null,
            ':close_time' => $pharmacy['close_time'] ?? null,
            ':dates_open' => $pharmacy['dates_open'] ?? null,
            ':user_id' => $pharmacy['user_id'] ?? null,
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