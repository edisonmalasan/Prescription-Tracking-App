<?php

require_once 'userModel.php';

class PharmacyModel extends UserModel {
    public $pharmacy_name;
    public $phar_license;
    public $open_time;
    public $close_time;
    public $dates_open;

    public function __construct($data = []) {
        parent::__construct($data);
        
        if (!empty($data)) {
            $this->pharmacy_name = $data['pharmacy_name'] ?? '';
            $this->phar_license = $data['phar_license'] ?? '';
            $this->open_time = $data['open_time'] ?? '';
            $this->close_time = $data['close_time'] ?? '';
            $this->dates_open = $data['dates_open'] ?? [];
        }
    }

    public function toArray() {
        $userData = parent::toArray();
        return array_merge($userData, [
            'pharmacy_name' => $this->pharmacy_name,
            'phar_license' => $this->phar_license,
            'open_time' => $this->open_time,
            'close_time' => $this->close_time,
            'dates_open' => $this->dates_open,
        ]);
    }
}
?>
