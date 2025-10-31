<?php

require_once '../repositories/PatientRepository.php';
require_once '../repositories/UserRepository.php';
require_once '../repositories/MedicalRecordRepository.php';
require_once '../models/patientModel.php';

class PatientService {
    private $patientRepository;
    private $userRepository;
    private $medicalRecordRepository;

    public function __construct() {
        $this->patientRepository = new PatientRepository();
        $this->userRepository = new UserRepository();
        $this->medicalRecordRepository = new MedicalRecordRepository();
    }

    public function createPatient($patientData) {
        if (empty($patientData['email']) || empty($patientData['password'])) {
            return ['error' => 'Email and password are required'];
        }

        $existingUser = $this->userRepository->findByEmail($patientData['email']);
        if ($existingUser) {
            return ['error' => 'Patient with this email already exists'];
        }

        $patientData['pass_hash'] = password_hash($patientData['password'], PASSWORD_BCRYPT);
        unset($patientData['password']); 

        $patientData['role'] = 'PATIENT';
        $patientData['created_at'] = date('Y-m-d H:i:s');

        $userId = $this->userRepository->create($patientData);

        if ($userId) {
            $this->patientRepository->create($userId, $patientData);
            
            return [
                'success' => true,
                'message' => 'Patient registered successfully',
                'user_id' => $userId
            ];
        } else {
            return ['error' => 'Failed to register patient'];
        }
    }

    public function getPatientProfile($userId) {
        $user = $this->userRepository->findById($userId);
        $patient = $this->patientRepository->findByUserId($userId);
        
        if ($user && $patient) {
            unset($user['pass_hash']);
            return [
                'success' => true,
                'patient' => array_merge($user, $patient)
            ];
        } else {
            return ['error' => 'Patient not found'];
        }
    }

    public function updatePatientProfile($userId, $patientData) {
        $existingPatient = $this->patientRepository->findByUserId($userId);
        if (!$existingPatient) {
            return ['error' => 'Patient not found'];
        }

        $patientData['user_id'] = $userId;
        $result = $this->patientRepository->update($patientData);
        
        if ($result !== false) {
            return [
                'success' => true,
                'message' => 'Patient profile updated successfully'
            ];
        } else {
            return ['error' => 'Failed to update patient profile'];
        }
    }

    public function getAllPatients() {
        $patients = $this->patientRepository->findAll();
        return [
            'success' => true,
            'patients' => $patients
        ];
    }

    public function createMedicalRecord($userId, $medicalRecordData) {
        $medicalRecordData['user_id'] = $userId;
        $recordId = $this->medicalRecordRepository->create($medicalRecordData);
        
        if ($recordId) {
            return [
                'success' => true,
                'message' => 'Medical record created successfully',
                'record_id' => $recordId
            ];
        } else {
            return ['error' => 'Failed to create medical record'];
        }
    }

    public function getMedicalRecord($userId) {
        $medicalRecord = $this->medicalRecordRepository->findByUserId($userId);
        
        if ($medicalRecord) {
            return [
                'success' => true,
                'medical_record' => $medicalRecord
            ];
        } else {
            return ['error' => 'Medical record not found'];
        }
    }

    public function updateMedicalRecord($recordId, $medicalRecordData) {
        $medicalRecordData['record_id'] = $recordId;
        $result = $this->medicalRecordRepository->update($medicalRecordData);
        
        if ($result !== false) {
            return [
                'success' => true,
                'message' => 'Medical record updated successfully'
            ];
        } else {
            return ['error' => 'Failed to update medical record'];
        }
    }

    public function deletePatient($userId) {
        $patient = $this->patientRepository->findByUserId($userId);
        if (!$patient) {
            return ['error' => 'Patient not found'];
        }

        $result = $this->patientRepository->delete($userId);
        
        if ($result !== false) {
            return [
                'success' => true,
                'message' => 'Patient deleted successfully'
            ];
        } else {
            return ['error' => 'Failed to delete patient'];
        }
    }
}
?>