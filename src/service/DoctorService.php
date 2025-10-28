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

        $doctorData['pass_hash'] = password_hash($doctorData['password'], PASSWORD_BCRYPT);
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
        //TODO
        return;
    }

    public function updateDoctorProfile($userId, $doctorData) {
        //TODO
        return;
    }

    public function getAllDoctors() {
        //TODO
        return;
    }

    public function getVerifiedDoctors() {
        //TODO
        return;
    }

    public function getDoctorsBySpecialization($specialization) {
        //TODO
        return;
    }

    public function verifyDoctor($userId) {
        //TODO
        return;
    }

    public function deleteDoctor($userId) {
        //TODO
        return;
    }
}
?>