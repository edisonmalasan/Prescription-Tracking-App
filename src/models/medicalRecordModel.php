<?php

class MedicalRecordModel {
    public $record_id;
    public $user_id;
    public $height;
    public $weight;
    public $allergies;

    public function __construct($data = []) {
        if (!empty($data)) {
            $this->record_id = $data['record_id'] ?? null;
            $this->user_id = $data['user_id'] ?? null;
            $this->height = $data['height'] ?? null;
            $this->weight = $data['weight'] ?? null;
            $this->allergies = $data['allergies'] ?? '';
        }
    }

    public function toArray() {
        return [
            'record_id' => $this->record_id,
            'user_id' => $this->user_id,
            'height' => $this->height,
            'weight' => $this->weight,
            'allergies' => $this->allergies
        ];
    }

    public function getBMI() {
        if ($this->height && $this->weight) {
            $heightInMeters = $this->height / 100;
            return round($this->weight / ($heightInMeters * $heightInMeters), 2);
        }
        return null;
    }
}
?>
