<?php

require_once '../service/PatientService.php';

class PatientController {
    private $patientService;

    public function __construct() {
        $this->patientService = new PatientService();
    }

    public function register() {
        $data = json_decode(file_get_contents("php://input"), true);
        
        if (empty($data)) {
            $data = $_POST;
        }
        
        $result = $this->patientService->createPatient($data);
        
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
        
        $result = $this->patientService->getPatientProfile($userId);
        
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
                
        // validate if json data is empty if true then it will get form dataa
        $data = json_decode(file_get_contents("php://input"), true);

        if (empty($data)) {
            $data = $_POST;
        }
        
        $result = $this->patientService->updatePatientProfile($userId, $data);
        
        header('Content-Type: application/json');
        http_response_code(isset($result['error']) ? 400 : 200);
        return json_encode($result);
    }

    public function getAllPatients() {
        $result = $this->patientService->getAllPatients();
        
        header('Content-Type: application/json');
        return json_encode($result);
    }

    public function createMedicalRecord() {
        $userId = $_GET['user_id'] ?? null;
        
        if (!$userId) {
            http_response_code(400);
            return json_encode(['error' => 'User ID is required']);
        }
        
        $data = json_decode(file_get_contents("php://input"), true);
   
        if (empty($data)) {
            $data = $_POST;
        }
        
        $result = $this->patientService->createMedicalRecord($userId, $data);
        
        header('Content-Type: application/json');
        http_response_code(isset($result['error']) ? 400 : 201);
        return json_encode($result);
    }

    public function getMedicalRecord() {
        $userId = $_GET['user_id'] ?? null;
        
        if (!$userId) {
            http_response_code(400);
            return json_encode(['error' => 'User ID is required']);
        }
        
        $result = $this->patientService->getMedicalRecord($userId);
        
        header('Content-Type: application/json');
        http_response_code(isset($result['error']) ? 404 : 200);
        return json_encode($result);
    }

    public function updateMedicalRecord() {
        $recordId = $_GET['record_id'] ?? null;
        
        if (!$recordId) {
            http_response_code(400);
            return json_encode(['error' => 'Record ID is required']);
        }
        
        $data = json_decode(file_get_contents("php://input"), true);
        
        if (empty($data)) {
            $data = $_POST;
        }
        
        $result = $this->patientService->updateMedicalRecord($recordId, $data);
        
        header('Content-Type: application/json');
        http_response_code(isset($result['error']) ? 400 : 200);
        return json_encode($result);
    }

    public function deletePatient() {
        $userId = $_GET['user_id'] ?? null;
        
        if (!$userId) {
            http_response_code(400);
            return json_encode(['error' => 'User ID is required']);
        }
        
        $result = $this->patientService->deletePatient($userId);
        
        header('Content-Type: application/json');
        http_response_code(isset($result['error']) ? 404 : 200);
        return json_encode($result);
    }
}
?>