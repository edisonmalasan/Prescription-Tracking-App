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
        $sql = "INSERT INTO " . $this->table_name . " (generic_name, brand, chemical_name, category, isControlled) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        if (! $stmt) {
            return false;
        }

        $generic_name = $drug['generic_name'] ?? '';
        $brand = $drug['brand'] ?? '';
        $chemical_name = $drug['chemical_name'] ?? '';
        $category = $drug['category'] ?? '';
        $isControlled = isset($drug['isControlled']) ? (int)$drug['isControlled'] : 0;

        // types: generic_name(s), brand(s), chemical_name(s), category(s), isControlled(i)
        $stmt->bind_param('ssssi', $generic_name, $brand, $chemical_name, $category, $isControlled);
        $ok = $stmt->execute();

        if ($ok) {
            return $this->conn->insert_id;
        }
        return false;
    }

    // Get drug by ID
    public function findById($id) {
        $sql = "SELECT * FROM " . $this->table_name . " WHERE drug_id = ?";
        $stmt = $this->conn->prepare($sql);
        if (! $stmt) {
            return null;
        }
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res) {
            return $res->fetch_assoc();
        }
        return null;
    }

    // Get drug by generic name
    public function findByGenericName($genericName) {
        $sql = "SELECT * FROM " . $this->table_name . " WHERE generic_name = ?";
        $stmt = $this->conn->prepare($sql);
        if (! $stmt) {
            return null;
        }
        $stmt->bind_param('s', $genericName);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res) {
            return $res->fetch_assoc();
        }
        return null;
    }

    // Get all drugs
    public function findAll() {
        $sql = "SELECT * FROM " . $this->table_name;
        $res = $this->conn->query($sql);
        if ($res) {
            return $res->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }

    // Get drugs by category
    public function findByCategory($category) {
        $sql = "SELECT * FROM " . $this->table_name . " WHERE category = ?";
        $stmt = $this->conn->prepare($sql);
        if (! $stmt) {
            return [];
        }
        $stmt->bind_param('s', $category);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res) {
            return $res->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }

    // Get controlled drugs
    public function findControlled() {
        $sql = "SELECT * FROM " . $this->table_name . " WHERE isControlled = 1";
        $res = $this->conn->query($sql);
        if ($res) {
            return $res->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }

    // Update drug
    public function update($drug) {
        $sql = "UPDATE " . $this->table_name . " SET generic_name = ?, brand = ?, chemical_name = ?, category = ?, isControlled = ? WHERE drug_id = ?";
        $stmt = $this->conn->prepare($sql);
        if (! $stmt) {
            return false;
        }

        $generic_name = $drug['generic_name'] ?? '';
        $brand = $drug['brand'] ?? '';
        $chemical_name = $drug['chemical_name'] ?? '';
        $category = $drug['category'] ?? '';
        $isControlled = isset($drug['isControlled']) ? (int)$drug['isControlled'] : 0;
        $drug_id = $drug['drug_id'] ?? null;

        if ($drug_id === null) {
            return false;
        }

        $stmt->bind_param('ssssii', $generic_name, $brand, $chemical_name, $category, $isControlled, $drug_id);
        $stmt->execute();
        return ($stmt->affected_rows > 0);
    }

    // Delete drug
    public function delete($id) {
        $sql = "DELETE FROM " . $this->table_name . " WHERE drug_id = ?";
        $stmt = $this->conn->prepare($sql);
        if (! $stmt) {
            return false;
        }
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return ($stmt->affected_rows > 0);
    }

    // Search drugs
    public function search($searchTerm) {
        $sql = "SELECT * FROM " . $this->table_name . " WHERE generic_name LIKE ? OR brand LIKE ? OR chemical_name LIKE ?";
        $stmt = $this->conn->prepare($sql);
        if (! $stmt) {
            return [];
        }
        $like = "%" . $searchTerm . "%";
        $stmt->bind_param('sss', $like, $like, $like);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res) {
            return $res->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }
}
?>