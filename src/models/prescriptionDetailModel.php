<?php

class PrescriptionDetailModel {
    public $prescription_id;
    public $drug_id;
    public $duration;
    public $dosage;
    public $frequency;
    public $refills;
    public $special_instructions;

    public function __construct($data = []) {
        if (!empty($data)) {
            $this->prescription_id = $data['prescription_id'] ?? null;
            $this->drug_id = $data['drug_id'] ?? null;
            $this->duration = $data['duration'] ?? '';
            $this->dosage = $data['dosage'] ?? '';
            $this->frequency = $data['frequency'] ?? '';
            $this->refills = $data['refills'] ?? 0;
            $this->special_instructions = $data['special_instructions'] ?? '';
        }
    }

    public function toArray() {
        return [
            'prescription_id' => $this->prescription_id,
            'drug_id' => $this->drug_id,
            'duration' => $this->duration,
            'dosage' => $this->dosage,
            'frequency' => $this->frequency,
            'refills' => $this->refills,
            'special_instructions' => $this->special_instructions
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
