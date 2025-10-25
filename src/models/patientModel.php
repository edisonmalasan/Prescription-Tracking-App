<?php

require_once 'userModel.php';

class PatientModel extends UserModel {
    public $birth_date;
    public $medical_records;

    public function __construct($data = []) {
        parent::__construct($data);
        
        if (!empty($data)) {
            $this->birth_date = $data['birth_date'] ?? null;
            $this->medical_records = $data['medical_records'] ?? [];
        }
    }

    public function toArray() {
        $userData = parent::toArray();
        return array_merge($userData, [
            'birth_date' => $this->birth_date,
            'medical_records' => $this->medical_records
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
