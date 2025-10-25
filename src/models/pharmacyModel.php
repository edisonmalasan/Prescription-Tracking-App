<?php

require_once 'userModel.php';

class PharmacyModel extends UserModel {
    public $pharmacy_name;
    public $phar_license;

    public function __construct($data = []) {
        parent::__construct($data);
        
        if (!empty($data)) {
            $this->pharmacy_name = $data['pharmacy_name'] ?? '';
            $this->phar_license = $data['phar_license'] ?? '';
        }
    }

    public function toArray() {
        $userData = parent::toArray();
        return array_merge($userData, [
            'pharmacy_name' => $this->pharmacy_name,
            'phar_license' => $this->phar_license,
        ]);
    }
}
?>
