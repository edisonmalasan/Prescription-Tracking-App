<?php

class DrugModel {
    public $drug_id;
    public $generic_name;
    public $brand;
    public $chemical_name;
    public $category;
    public $isControlled;

    public function __construct($data = []) {
        if (!empty($data)) {
            $this->drug_id = $data['drug_id'] ?? null;
            $this->generic_name = $data['generic_name'] ?? '';
            $this->brand = $data['brand'] ?? '';
            $this->chemical_name = $data['chemical_name'] ?? '';
            $this->category = $data['category'] ?? '';
            $this->isControlled = $data['isControlled'] ?? false;
        }
    }

    public function toArray() {
        return [
            'drug_id' => $this->drug_id,
            'generic_name' => $this->generic_name,
            'brand' => $this->brand,
            'chemical_name' => $this->chemical_name,
            'category' => $this->category,
            'isControlled' => $this->isControlled
        ];
    }
}
?>
