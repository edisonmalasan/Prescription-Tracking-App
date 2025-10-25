<?php

class PrescriptionDetailModel {
    public $detail_id;
    public $prescription_id;
    public $drug_id;
    public $duration;
    public $dosage;
    public $frequency;
    public $created_at;

    public function __construct($data = []) {
        if (!empty($data)) {
            $this->detail_id = $data['detail_id'] ?? null;
            $this->prescription_id = $data['prescription_id'] ?? null;
            $this->drug_id = $data['drug_id'] ?? null;
            $this->duration = $data['duration'] ?? 0;
            $this->dosage = $data['dosage'] ?? '';
            $this->frequency = $data['frequency'] ?? '';
            $this->created_at = $data['created_at'] ?? null;
        }
    }

    public function toArray() {
        return [
            'detail_id' => $this->detail_id,
            'prescription_id' => $this->prescription_id,
            'drug_id' => $this->drug_id,
            'duration' => $this->duration,
            'dosage' => $this->dosage,
            'frequency' => $this->frequency,
            'created_at' => $this->created_at,
        ];
    }

    public function getTotalDays() {
        return $this->duration;
    }

    public function getTotalRefills() {
        return $this->refills;
    }
}
?>
