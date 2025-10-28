<?php

require_once '../service/AdminService.php';

class AdminController {
    private $adminService;

    public function __construct() {
        $this->adminService = new AdminService();
    }

    public function login() {
        $data = json_decode(file_get_contents("php://input"), true);
        
        
        if (empty($data)) {
            $data = $_POST;
        }
        
        $result = $this->adminService->adminLogin($data);
        
        header('Content-Type: application/json');
        http_response_code(isset($result['error']) ? 401 : 200);
        return json_encode($result);
    }

    public function getDashboard() {
        $result = $this->adminService->getDashboardData();
        
        header('Content-Type: application/json');
        http_response_code(isset($result['error']) ? 500 : 200);
        return json_encode($result);
    }

    public function getAllUsers() {
        $role = $_GET['role'] ?? null;
        $result = $this->adminService->getAllUsers($role);
        
        header('Content-Type: application/json');
        http_response_code(isset($result['error']) ? 500 : 200);
        return json_encode($result);
    }

    public function createUser() {
        $data = json_decode(file_get_contents("php://input"), true);
        
        
        if (empty($data)) {
            $data = $_POST;
        }
        
        $result = $this->adminService->createUser($data);
        
        header('Content-Type: application/json');
        http_response_code(isset($result['error']) ? 400 : 201);
        return json_encode($result);
    }

    public function modifyUser() {
        $userId = $_GET['user_id'] ?? null;
        
        if (!$userId) {
            http_response_code(400);
            return json_encode(['error' => 'User ID is required']);
        }
        
        $data = json_decode(file_get_contents("php://input"), true);
        
        
        if (empty($data)) {
            $data = $_POST;
        }
        
        $result = $this->adminService->modifyUser($userId, $data);
        
        header('Content-Type: application/json');
        http_response_code(isset($result['error']) ? 400 : 200);
        return json_encode($result);
    }

    public function deleteUser() {
        $userId = $_GET['user_id'] ?? null;
        
        if (!$userId) {
            http_response_code(400);
            return json_encode(['error' => 'User ID is required']);
        }
        
        $result = $this->adminService->deleteUser($userId);
        
        header('Content-Type: application/json');
        http_response_code(isset($result['error']) ? 404 : 200);
        return json_encode($result);
    }

    public function verifyDoctor() {
        $doctorId = $_GET['doctor_id'] ?? null;
        
        if (!$doctorId) {
            http_response_code(400);
            return json_encode(['error' => 'Doctor ID is required']);
        }
        
        $result = $this->adminService->verifyDoctor($doctorId);
        
        header('Content-Type: application/json');
        http_response_code(isset($result['error']) ? 400 : 200);
        return json_encode($result);
    }

    public function getPendingVerifications() {
        $result = $this->adminService->getPendingVerifications();
        
        header('Content-Type: application/json');
        http_response_code(isset($result['error']) ? 500 : 200);
        return json_encode($result);
    }

    public function viewDatabaseTables() {
        header('Content-Type: application/json');
        return json_encode([
            'success' => true,
            'message' => 'Database tables view - implementation needed',
            'tables' => ['users', 'doctor', 'patient', 'pharmacy', 'admin', 'medicalrecord', 'drug', 'prescription', 'prescriptiondetails']
        ]);
    }

    public function createDatabaseRecord() {
        header('Content-Type: application/json');
        return json_encode([
            'success' => true,
            'message' => 'Generic database record creation - implementation needed'
        ]);
    }

    public function modifyDatabaseRecord() {
        header('Content-Type: application/json');
        return json_encode([
            'success' => true,
            'message' => 'Generic database record modification - implementation needed'
        ]);
    }

    public function getSystemStatistics() {
        $result = $this->adminService->getSystemStatistics();
        
        header('Content-Type: application/json');
        http_response_code(isset($result['error']) ? 500 : 200);
        return json_encode($result);
    }

    public function manageDrugDatabase() {
        header('Content-Type: application/json');
        return json_encode([
            'success' => true,
            'message' => 'Drug database management - implementation needed'
        ]);
    }
}
?>
