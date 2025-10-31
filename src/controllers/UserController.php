<?php

require_once '../service/UserService.php';

class UserController {
    private $userService;

    public function __construct() {
        $this->userService = new UserService();
    }

    public function register() {
        $data = json_decode(file_get_contents("php://input"), true);
        
        if (empty($data)) {
            $data = $_POST;
        }
        
        $result = $this->userService->registerUser($data);
        
        header('Content-Type: application/json');
        http_response_code(isset($result['error']) ? 400 : 201);
        return json_encode($result);
    }

    public function login() {
        $data = json_decode(file_get_contents("php://input"), true);
        
        if (empty($data)) {
            $data = $_POST;
        }
        
        if (empty($data['email']) || empty($data['password'])) {
            http_response_code(400);
            return json_encode(['error' => 'Email and password are required']);
        }
        
        $result = $this->userService->authenticateUser($data['email'], $data['password']);
        
        header('Content-Type: application/json');
        http_response_code(isset($result['error']) ? 401 : 200);
        return json_encode($result);
    }

    public function logout() {
        header('Content-Type: application/json');
        return json_encode(['message' => 'Logged out successfully']);
    }

    public function getProfile() {
        $userId = $_GET['user_id'] ?? null;
        
        if (!$userId) {
            http_response_code(400);
            return json_encode(['error' => 'User ID is required']);
        }
        
        $result = $this->userService->getUserProfile($userId);
        
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
        
        $result = $this->userService->updateProfile($userId, $data);
        
        header('Content-Type: application/json');
        http_response_code(isset($result['error']) ? 400 : 200);
        return json_encode($result);
    }

    public function searchUsers() {
        $searchTerm = $_GET['search'] ?? '';
        $role = $_GET['role'] ?? null;
        
        $users = $this->userService->searchUsers($searchTerm, $role);
        
        header('Content-Type: application/json');
        return json_encode([
            'success' => true,
            'users' => $users
        ]);
    }

    public function deleteUser() {
        $userId = $_GET['user_id'] ?? null;
        
        if (!$userId) {
            http_response_code(400);
            return json_encode(['error' => 'User ID is required']);
        }
        
        $result = $this->userService->deleteUser($userId);
        
        header('Content-Type: application/json');
        http_response_code(isset($result['error']) ? 404 : 200);
        return json_encode($result);
    }

    public function changePassword() {
        $userId = $_GET['user_id'] ?? null;
        
        if (!$userId) {
            http_response_code(400);
            return json_encode(['error' => 'User ID is required']);
        }
        
        $data = json_decode(file_get_contents("php://input"), true);
        
        if (empty($data)) {
            $data = $_POST;
        }
        
        if (empty($data['old_password']) || empty($data['new_password'])) {
            http_response_code(400);
            return json_encode(['error' => 'Old and new passwords are required']);
        }
        
        $result = $this->userService->changePassword($userId, $data['old_password'], $data['new_password']);
        
        header('Content-Type: application/json');
        http_response_code(isset($result['error']) ? 400 : 200);
        return json_encode($result);
    }

    public function resetPassword() {
        $data = json_decode(file_get_contents("php://input"), true);
 
        if (empty($data)) {
            $data = $_POST;
        }
        
        if (empty($data['email'])) {
            http_response_code(400);
            return json_encode(['error' => 'Email is required']);
        }
        
        $result = $this->userService->resetPassword($data['email']);
        
        header('Content-Type: application/json');
        http_response_code(isset($result['error']) ? 404 : 200);
        return json_encode($result);
    }
}
?>