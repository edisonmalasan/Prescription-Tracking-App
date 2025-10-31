<?php

require_once '../repositories/PrescriptionRepository.php';
require_once '../repositories/DrugRepository.php';
require_once '../repositories/MedicalRecordRepository.php';
require_once '../models/prescriptionModel.php';
require_once '../models/prescriptionDetailModel.php';

class PrescriptionService {
    private $prescriptionRepository;
    private $drugRepository;
    private $medicalRecordRepository;

    public function __construct() {
        $this->prescriptionRepository = new PrescriptionRepository();
        $this->drugRepository = new DrugRepository();
        $this->medicalRecordRepository = new MedicalRecordRepository();
    }

    public function createPrescription($prescriptionData) {
        // Validate required fields
        if (empty($prescriptionData['prescribing_doctor']) || empty($prescriptionData['record_id'])) {
            return ['error' => 'Prescribing doctor and medical record are required'];
        }

        // Set default values
        $prescriptionData['prescription_date'] = $prescriptionData['prescription_date'] ?? date('Y-m-d');
        $prescriptionData['status'] = $prescriptionData['status'] ?? 'pending';

        // Create prescription
        $prescriptionId = $this->prescriptionRepository->create($prescriptionData);

        if ($prescriptionId) {
            return [
                'success' => true,
                'message' => 'Prescription created successfully',
                'prescription_id' => $prescriptionId
            ];
        } else {
            return ['error' => 'Failed to create prescription'];
        }
    }

    public function getPrescription($prescriptionId) {
        $prescription = $this->prescriptionRepository->findById($prescriptionId);
        
        if ($prescription) {
            // Get prescription details
            $details = $this->prescriptionRepository->getPrescriptionDetails($prescriptionId);
            $prescription['details'] = $details;
            
            return [
                'success' => true,
                'prescription' => $prescription
            ];
        } else {
            return ['error' => 'Prescription not found'];
        }
    }

    public function getPrescriptionsByPatient($patientId) {
        $prescriptions = $this->prescriptionRepository->findByPatient($patientId);
        
        return [
            'success' => true,
            'prescriptions' => $prescriptions
        ];
    }

    public function getPrescriptionsByDoctor($doctorId) {
        $prescriptions = $this->prescriptionRepository->findByDoctor($doctorId);
        
        return [
            'success' => true,
            'prescriptions' => $prescriptions
        ];
    }

    public function getAllPrescriptions() {
        $prescriptions = $this->prescriptionRepository->findAll();
        
        return [
            'success' => true,
            'prescriptions' => $prescriptions
        ];
    }

    public function updatePrescription($prescriptionId, $prescriptionData) {
        $prescriptionData['prescription_id'] = $prescriptionId;
        $result = $this->prescriptionRepository->update($prescriptionData);
        
        if ($result !== false) {
            return [
                'success' => true,
                'message' => 'Prescription updated successfully'
            ];
        } else {
            return ['error' => 'Failed to update prescription'];
        }
    }

    public function addPrescriptionDetail($prescriptionId, $detailData) {
        // Validate required fields
        if (empty($detailData['drug_id'])) {
            return ['error' => 'Drug ID is required'];
        }

        $detailData['prescription_id'] = $prescriptionId;
        $result = $this->prescriptionRepository->addPrescriptionDetail($detailData);
        
        if ($result !== false) {
            return [
                'success' => true,
                'message' => 'Prescription detail added successfully'
            ];
        } else {
            return ['error' => 'Failed to add prescription detail'];
        }
    }

    public function getPrescriptionDetails($prescriptionId) {
        $details = $this->prescriptionRepository->getPrescriptionDetails($prescriptionId);
        
        return [
            'success' => true,
            'details' => $details
        ];
    }

    public function updatePrescriptionStatus($prescriptionId, $status) {
        $prescriptionData = [
            'prescription_id' => $prescriptionId,
            'status' => $status
        ];
        
        $result = $this->prescriptionRepository->update($prescriptionData);
        
        if ($result !== false) {
            return [
                'success' => true,
                'message' => 'Prescription status updated successfully'
            ];
        } else {
            return ['error' => 'Failed to update prescription status'];
        }
    }

    public function deletePrescription($prescriptionId) {
        $result = $this->prescriptionRepository->delete($prescriptionId);
        
        if ($result !== false) {
            return [
                'success' => true,
                'message' => 'Prescription deleted successfully'
            ];
        } else {
            return ['error' => 'Failed to delete prescription'];
        }
    }

    public function getPrescriptionsByStatus($status) {
        $prescriptions = $this->prescriptionRepository->findByStatus($status);
        
        return [
            'success' => true,
            'prescriptions' => $prescriptions
        ];
    }
}
?>