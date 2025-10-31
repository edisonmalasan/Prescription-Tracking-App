<?php
require_once __DIR__ . '/../service/AuthService.php';

class AuthController {
    private $authService;

    public function __construct() {
        $this->authService = new AuthService();
    }

    public function register() {
        $data = json_decode(file_get_contents("php://input"), true);

        if (empty($data)) {
            $data = $_POST;
        }
        
        if (empty($data)) {
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'No data received']);
            return;
        }
        
        $result = $this->authService->register($data);
        
        header('Content-Type: application/json');
        http_response_code(isset($result['error']) ? 400 : 201);
        echo json_encode($result);
    }

    public function login() {
        $data = json_decode(file_get_contents("php://input"), true);
        
        if (empty($data)) {
            $data = $_POST;
        }
        
        if (empty($data)) {
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'No data received']);
            return;
        }
        
        $result = $this->authService->login($data);
        
        header('Content-Type: application/json');
        http_response_code(isset($result['error']) ? 401 : 200);
        echo json_encode($result);
    }
}
?>