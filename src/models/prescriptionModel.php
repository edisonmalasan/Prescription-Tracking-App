<?php

class PrescriptionModel {
    public $prescription_id;
    public $prescribing_doctor;
    public $record_id;
    public $prescription_date;
    public $status;

    public function __construct($data = []) {
        if (!empty($data)) {
            $this->prescription_id = $data['prescription_id'] ?? null;
            $this->prescribing_doctor = $data['prescribing_doctor'] ?? null;
            $this->record_id = $data['record_id'] ?? null;
            $this->prescription_date = $data['prescription_date'] ?? null;
            $this->status = $data['status'] ?? 'pending';
        }
    }

    public function toArray() {
        return [
            'prescription_id' => $this->prescription_id,
            'prescribing_doctor' => $this->prescribing_doctor,
            'record_id' => $this->record_id,
            'prescription_date' => $this->prescription_date,
            'status' => $this->status
        ];
    }

    public function isActive() {
        return $this->status === 'active';
    }

    public function isCompleted() {
        return $this->status === 'completed';
    }

    public function isCancelled() {
        return $this->status === 'cancelled';
    }
}
?>
