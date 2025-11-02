<?php
require_once __DIR__ . '/../service/AuthService.php';

class AuthController {
    private $authService;

    public function __construct() {
        $this->authService = new AuthService();
    }

    public function register() {
        if (!headers_sent()) {
            header('Content-Type: application/json');
        }
        
        $data = json_decode(file_get_contents("php://input"), true);

        if (empty($data)) {
            $data = $_POST;
        }
        
        if (empty($data)) {
            http_response_code(400);
            return json_encode(['error' => 'No data received']);
        }
        
        try {
            $result = $this->authService->register($data);
            http_response_code(isset($result['error']) ? 400 : 201);
            return json_encode($result);
        } catch (Exception $e) {
            http_response_code(500);
            return json_encode(['error' => 'Registration failed: ' . $e->getMessage()]);
        }
    }

    public function login() {
        if (!headers_sent()) {
            header('Content-Type: application/json');
        }
        
        $data = json_decode(file_get_contents("php://input"), true);
        
        if (empty($data)) {
            $data = $_POST;
        }
        
        if (empty($data)) {
            http_response_code(400);
            return json_encode(['error' => 'No data received']);
        }
        
        try {
            $result = $this->authService->login($data);
            http_response_code(isset($result['error']) ? 401 : 200);
            return json_encode($result);
        } catch (Exception $e) {
            http_response_code(500);
            return json_encode(['error' => 'Login failed: ' . $e->getMessage()]);
        }
    }
}
?>