<?php

class MedicalRecordModel {
    public $record_id;
    public $user_id;
    public $height;
    public $weight;
    public $allergies;
    public $created_at;
    public $updated_at;

    public function __construct($data = []) {
        if (!empty($data)) {
            $this->record_id = $data['record_id'] ?? null;
            $this->user_id = $data['user_id'] ?? null;
            $this->height = $data['height'] ?? null;
            $this->weight = $data['weight'] ?? null;
            $this->allergies = $data['allergies'] ?? '';
            $this->created_at = $data['created_at'] ?? null;
            $this->updated_at = $data['updated_at'] ?? null;
        }
    }

    public function toArray() {
        return [
            'record_id' => $this->record_id,
            'user_id' => $this->user_id,
            'height' => $this->height,
            'weight' => $this->weight,
            'allergies' => $this->allergies,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
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
