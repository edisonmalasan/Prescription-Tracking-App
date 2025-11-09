<?php

require_once 'userModel.php';

class PharmacyModel extends UserModel {
    public $pharmacy_name;
    public $operating_hours;

    public function __construct($data = []) {
        parent::__construct($data);
        
        if (!empty($data)) {
            $this->pharmacy_name = $data['pharmacy_name'] ?? '';
            $this->operating_hours = $data['operating_hours'] ?? '';
        }
    }

    public function toArray() {
        $userData = parent::toArray();
        return array_merge($userData, [
            'pharmacy_name' => $this->pharmacy_name,
            'operating_hours' => $this->operating_hours
        ]);
    }
}
?>