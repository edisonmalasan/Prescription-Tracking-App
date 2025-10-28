<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set JSON header first
header('Content-Type: application/json');

// Log the request for debugging
error_log("AuthRoutes accessed: " . $_SERVER['REQUEST_METHOD'] . " " . $_SERVER['REQUEST_URI']);

try {
    require_once '../controllers/authController.php';

    $method = $_SERVER['REQUEST_METHOD'];
    $action = $_GET['action'] ?? '';
    
    error_log("Method: $method, Action: $action");
    
    // create instance of controller to handle http requests
    $authController = new AuthController();

    // routes
    switch ($method) {
        case 'POST':
            switch ($action) {
                case 'register':
                    error_log("Processing registration");
                    echo $authController->register();
                    break;
                case 'login':
                    error_log("Processing login");
                    echo $authController->login();
                    break;
                default:
                    http_response_code(404);
                    echo json_encode(['error' => 'Action not found']);
                    break;
            }
            break;
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            break;
    }
} catch (Exception $e) {
    error_log("AuthRoutes error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'error' => 'Server error: ' . $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
}
?>