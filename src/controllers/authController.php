<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);


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
            
            if (isset($result['success']) && $result['success']) {
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                $_SESSION['user_id'] = $result['user']['user_id'];
                $_SESSION['role'] = $result['user']['role'];
                $_SESSION['email'] = $result['user']['email'];
                $_SESSION['logged_in'] = true;
            }

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


    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $_SESSION = array();
        
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 10800,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        session_destroy();
        
        return json_encode([
            'success' => true, 
            'message' => 'Logged out successfully'
        ]);
    }
}
?>
