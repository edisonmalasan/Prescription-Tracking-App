<?php

require_once '../repositories/DoctorRepository.php';
require_once '../repositories/UserRepository.php';
require_once '../models/doctorModel.php';

class DoctorService {
    private $doctorRepository;
    private $userRepository;

    public function __construct() {
        $this->doctorRepository = new DoctorRepository();
        $this->userRepository = new UserRepository();
    }

    public function createDoctor($doctorData) {
        if (empty($doctorData['email']) || empty($doctorData['password'])) {
            return ['error' => 'Email and password are required'];
        }

        $existingUser = $this->userRepository->findByEmail($doctorData['email']);
        if ($existingUser) {
            return ['error' => 'Doctor with this email already exists'];
        }

        // $doctorData['pass_hash'] = password_hash($doctorData['password'], PASSWORD_BCRYPT);
        $doctorData['pass_hash'] = $doctorData['password'];
        unset($doctorData['password']);

        $doctorData['role'] = 'DOCTOR';
        $doctorData['created_at'] = date('Y-m-d H:i:s');

        $userId = $this->doctorRepository->create($doctorData);

        if ($userId) {
            return [
                'success' => true,
                'message' => 'Doctor registered successfully',
                'user_id' => $userId
            ];
        } else {
            return ['error' => 'Failed to register doctor'];
        }
    }

    public function getDoctorProfile($userId) {
        $user = $this->userRepository->findById($userId);
        $doctor = $this->doctorRepository->findByUserId($userId);
        
        if ($user && $doctor) {
            unset($user['pass_hash']);
            return [
                'success' => true,
                'doctor' => array_merge($user, $doctor)
            ];
        } else {
            return ['error' => 'Doctor not found'];
        }
    }

    public function updateDoctorProfile($userId, $doctorData) {
        $existingDoctor = $this->doctorRepository->findByUserId($userId);
        if (!$existingDoctor) {
            return ['error' => 'Doctor not found'];
        }

        $doctorData['user_id'] = $userId;
        $result = $this->doctorRepository->update($doctorData);
        
        if ($result !== false) {
            return [
                'success' => true,
                'message' => 'Doctor profile updated successfully'
            ];
        } else {
            return ['error' => 'Failed to update doctor profile'];
        }
    }

    public function getAllDoctors() {
        $doctors = $this->doctorRepository->findAll();
        return [
            'success' => true,
            'doctors' => $doctors
        ];
    }

    public function getVerifiedDoctors() {
        $doctors = $this->doctorRepository->findVerified();
        return [
            'success' => true,
            'doctors' => $doctors
        ];
    }

    public function getDoctorsBySpecialization($specialization) {
        $doctors = $this->doctorRepository->findBySpecialization($specialization);
        return [
            'success' => true,
            'doctors' => $doctors
        ];
    }

    public function verifyDoctor($userId) {
        $result = $this->doctorRepository->verifyDoctor($userId);
        
        if ($result !== false) {
            return [
                'success' => true,
                'message' => 'Doctor verified successfully'
            ];
        } else {
            return ['error' => 'Failed to verify doctor'];
        }
    }

    public function deleteDoctor($userId) {
        $doctor = $this->doctorRepository->findByUserId($userId);
        if (!$doctor) {
            return ['error' => 'Doctor not found'];
        }

        $result = $this->doctorRepository->delete($userId);
        
        if ($result !== false) {
            return [
                'success' => true,
                'message' => 'Doctor deleted successfully'
            ];
        } else {
            return ['error' => 'Failed to delete doctor'];
        }
    }
}
?>