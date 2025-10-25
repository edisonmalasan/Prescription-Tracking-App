<?php

require_once 'userModel.php';

class DoctorModel extends UserModel {
    public $birth_date;
    public $specialization;
    public $prc_license;
    public $clinic_name;
    public $isVerified;

    public function __construct($data = []) {
        parent::__construct($data);
        
        if (!empty($data)) {
            $this->birth_date = $data['birth_date'] ?? null;
            $this->specialization = $data['specialization'] ?? '';
            $this->prc_license = $data['prc_license'] ?? '';
            $this->clinic_name = $data['clinic_name'] ?? '';
            $this->isVerified = $data['isVerified'] ?? false;
        }
    }

    public function toArray() {
        $userData = parent::toArray();
        return array_merge($userData, [
            'birth_date' => $this->birth_date,
            'specialization' => $this->specialization,
            'prc_license' => $this->prc_license,
            'clinic_name' => $this->clinic_name,
            'isVerified' => $this->isVerified
        ]);
    }

    public function getAge() {
        if ($this->birth_date) {
            $birthDate = new DateTime($this->birth_date);
            $today = new DateTime();
            return $today->diff($birthDate)->y;
        }
        return null;
    }
}
?>
