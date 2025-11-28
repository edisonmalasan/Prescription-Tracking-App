<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);

require_once __DIR__ . '/../service/authService.php';

class AuthController {
    private $authService;

    public function __construct() {
        try {
            $this->authService = new AuthService();
        } catch (Exception $e) {
            throw new Exception("Failed to initialize AuthService: " . $e->getMessage());
        }
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
            return json_encode($result, JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            http_response_code(500);
            return json_encode(['error' => 'Registration failed: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
        } catch (Error $e) {
            http_response_code(500);
            return json_encode(['error' => 'Registration failed: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
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
            return json_encode(['error' => 'No data received'], JSON_UNESCAPED_UNICODE);
        }
        
        try {
            $result = $this->authService->login($data);
            http_response_code(isset($result['error']) ? 401 : 200);
            return json_encode($result, JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            http_response_code(500);
            return json_encode(['error' => 'Login failed: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
        } catch (Error $e) {
            http_response_code(500);
            return json_encode(['error' => 'Login failed: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
        }
    }
}
?>