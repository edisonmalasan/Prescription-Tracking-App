<?php

require_once '../service/DoctorService.php';

class DoctorController {
    private $doctorService;

    public function __construct() {
        $this->doctorService = new DoctorService();
    }

    public function register() {
        $data = json_decode(file_get_contents("php://input"), true);
        
        if (empty($data)) {
            $data = $_POST;
        }
        
        $result = $this->doctorService->createDoctor($data);
        
        header('Content-Type: application/json');
        http_response_code(isset($result['error']) ? 400 : 201);
        return json_encode($result);
    }

    public function getProfile() {
        $userId = $_GET['user_id'] ?? null;
        
        if (!$userId) {
            http_response_code(400);
            return json_encode(['error' => 'User ID is required']);
        }
        
        $result = $this->doctorService->getDoctorProfile($userId);
        
        header('Content-Type: application/json');
        http_response_code(isset($result['error']) ? 404 : 200);
        return json_encode($result);
    }

    public function updateProfile() {
        $userId = $_GET['user_id'] ?? null;
        
        if (!$userId) {
            http_response_code(400);
            return json_encode(['error' => 'User ID is required']);
        }
        
        $data = json_decode(file_get_contents("php://input"), true);
        
        if (empty($data)) {
            $data = $_POST;
        }
        
        $result = $this->doctorService->updateDoctorProfile($userId, $data);
        
        header('Content-Type: application/json');
        http_response_code(isset($result['error']) ? 400 : 200);
        return json_encode($result);
    }

    public function getAllDoctors() {
        $result = $this->doctorService->getAllDoctors();
        
        header('Content-Type: application/json');
        return json_encode($result);
    }

    public function getVerifiedDoctors() {
        $result = $this->doctorService->getVerifiedDoctors();
        
        header('Content-Type: application/json');
        return json_encode($result);
    }

    public function getDoctorsBySpecialization() {
        $specialization = $_GET['specialization'] ?? null;
        
        if (!$specialization) {
            http_response_code(400);
            return json_encode(['error' => 'Specialization is required']);
        }
        
        $result = $this->doctorService->getDoctorsBySpecialization($specialization);
        
        header('Content-Type: application/json');
        return json_encode($result);
    }

    public function verifyDoctor() {
        $userId = $_GET['user_id'] ?? null;
        
        if (!$userId) {
            http_response_code(400);
            return json_encode(['error' => 'User ID is required']);
        }
        
        $result = $this->doctorService->verifyDoctor($userId);
        
        header('Content-Type: application/json');
        http_response_code(isset($result['error']) ? 400 : 200);
        return json_encode($result);
    }

    public function deleteDoctor() {
        $userId = $_GET['user_id'] ?? null;
        
        if (!$userId) {
            http_response_code(400);
            return json_encode(['error' => 'User ID is required']);
        }
        
        $result = $this->doctorService->deleteDoctor($userId);
        
        header('Content-Type: application/json');
        http_response_code(isset($result['error']) ? 404 : 200);
        return json_encode($result);
    }
}
?>