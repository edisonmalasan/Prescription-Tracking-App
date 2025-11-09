<?php

require_once '../service/PharmacyService.php';

class PharmacyController {
    private $pharmacyService;

    public function __construct() {
        $this->pharmacyService = new PharmacyService();
    }

    public function register() {
        $data = json_decode(file_get_contents("php://input"), true);
        
        if (empty($data)) {
            $data = $_POST;
        }
        
        $result = $this->pharmacyService->createPharmacy($data);
        
        header('Content-Type: application/json');
        http_response_code(isset($result['error']) ? 400 : 201);
        return json_encode($result);
    }

    public function getProfile() {
        $userId = $_GET['user_id'] ?? null;
        
        if (!$userId) {
            // For now, let's assume a logged-in pharmacy. 
        
            
            $userId = 1; 
        }
        
        $result = $this->pharmacyService->getPharmacyProfile($userId);
        
        header('Content-Type: application/json');
        http_response_code(isset($result['error']) ? 404 : 200);
        return json_encode($result);
    }

    public function updateProfile() {
        $userId = $_GET['user_id'] ?? null;
        
        if (!$userId) {
            // For now, let's assume a logged-in pharmacy. 
            
            $userId = 1; 
        }
        
        $data = json_decode(file_get_contents("php://input"), true);
        
        if (empty($data)) {
            $data = $_POST;
        }
        
        $result = $this->pharmacyService->updatePharmacyProfile($userId, $data);
        
        header('Content-Type: application/json');
        http_response_code(isset($result['error']) ? 400 : 200);
        return json_encode($result);
    }

    public function getAllPharmacies() {
        $result = $this->pharmacyService->getAllPharmacies();
        
        header('Content-Type: application/json');
        return json_encode($result);
    }

    public function deletePharmacy() {
        $userId = $_GET['user_id'] ?? null;
        
        if (!$userId) {
            http_response_code(400);
            return json_encode(['error' => 'User ID is required']);
        }
        
        $result = $this->pharmacyService->deletePharmacy($userId);
        
        header('Content-Type: application/json');
        http_response_code(isset($result['error']) ? 404 : 200);
        return json_encode($result);
    }
}
?>