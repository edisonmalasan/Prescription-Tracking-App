<?php

class DrugModel {
    public $drug_id;
    public $generic_name;
    public $brand;
    public $chemical_name;
    public $category;
    public $expiry_date;
    public $isControlled;
    public $created_at;

    public function __construct($data = []) {
        if (!empty($data)) {
            $this->drug_id = $data['drug_id'] ?? null;
            $this->generic_name = $data['generic_name'] ?? '';
            $this->brand = $data['brand'] ?? '';
            $this->chemical_name = $data['chemical_name'] ?? '';
            $this->category = $data['category'] ?? '';
            $this->expiry_date = $data['expiry_date'] ?? null;
            $this->isControlled = $data['isControlled'] ?? false;
            $this->created_at = $data['created_at'] ?? null;
        }
    }

    public function toArray() {
        return [
            'drug_id' => $this->drug_id,
            'generic_name' => $this->generic_name,
            'brand' => $this->brand,
            'chemical_name' => $this->chemical_name,
            'category' => $this->category,
            'expiry_date' => $this->expiry_date,
            'isControlled' => $this->isControlled,
            'created_at' => $this->created_at
        ];
    }

    public function isExpired() {
        if ($this->expiry_date) {
            $expiryDate = new DateTime($this->expiry_date);
            $today = new DateTime();
            return $today > $expiryDate;
        }
        return false;
    }
}
?>
