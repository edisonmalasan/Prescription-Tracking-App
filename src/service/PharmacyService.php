<?php

require_once '../repositories/PharmacyRepository.php';
require_once '../repositories/UserRepository.php';
require_once '../models/pharmacyModel.php';

class PharmacyService {
    private $pharmacyRepository;
    private $userRepository;

    public function __construct() {
        $this->pharmacyRepository = new PharmacyRepository();
        $this->userRepository = new UserRepository();
    }

    public function createPharmacy($pharmacyData) {
        if (empty($pharmacyData['email']) || empty($pharmacyData['password'])) {
            return ['error' => 'Email and password are required'];
        }

        $existingUser = $this->userRepository->findByEmail($pharmacyData['email']);
        if ($existingUser) {
            return ['error' => 'Pharmacy with this email already exists'];
        }

        // $patientData['pass_hash'] = password_hash($patientData['password'], PASSWORD_BCRYPT);
        $pharmacyData['pass_hash'] = $pharmacyData['password'];
        unset($pharmacyData['password']);

        $pharmacyData['role'] = 'PHARMACY';
        $pharmacyData['created_at'] = date('Y-m-d H:i:s');

        $userId = $this->pharmacyRepository->create($pharmacyData);

        if ($userId) {
            return [
                'success' => true,
                'message' => 'Pharmacy registered successfully',
                'user_id' => $userId
            ];
        } else {
            return ['error' => 'Failed to register pharmacy'];
        }
    }

    public function getPharmacyProfile($userId) {
        $user = $this->userRepository->findById($userId);
        $pharmacy = $this->pharmacyRepository->findByUserId($userId);
        
        if ($user && $pharmacy) {
            unset($user['pass_hash']);
            return [
                'success' => true,
                'profile' => array_merge($user, $pharmacy)
            ];
        } else {
            return ['error' => 'Pharmacy not found'];
        }
    }

    public function updatePharmacyProfile($userId, $pharmacyData) {
        $existingPharmacy = $this->pharmacyRepository->findByUserId($userId);
        if (!$existingPharmacy) {
            return ['error' => 'Pharmacy not found'];
        }

        $pharmacyData['user_id'] = $userId;
        $result = $this->pharmacyRepository->update($pharmacyData);
        
        if ($result !== false) {
            return [
                'success' => true,
                'message' => 'Pharmacy profile updated successfully'
            ];
        } else {
            return ['error' => 'Failed to update pharmacy profile'];
        }
    }

    public function getAllPharmacies() {
        $pharmacies = $this->pharmacyRepository->findAll();
        return [
            'success' => true,
            'pharmacies' => $pharmacies
        ];
    }

    public function deletePharmacy($userId) {
        $pharmacy = $this->pharmacyRepository->findByUserId($userId);
        if (!$pharmacy) {
            return ['error' => 'Pharmacy not found'];
        }

        $result = $this->pharmacyRepository->delete($userId);
        
        if ($result !== false) {
            return [
                'success' => true,
                'message' => 'Pharmacy deleted successfully'
            ];
        } else {
            return ['error' => 'Failed to delete pharmacy'];
        }
    }
}
?>