<?php
<<<<<<< HEAD

require_once '../controllers/authController.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';
// create instance of controller to handle http requests
$authController = new AuthController();

// routes
switch ($method) {
    case 'POST':
        switch ($action) {
            case 'register':
                echo $authController->register();
                break;
            case 'login':
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
=======
header('Content-Type: application/json');

try {
    require_once '../controllers/authController.php';

    $method = $_SERVER['REQUEST_METHOD'];
    $action = $_GET['action'] ?? '';
    
    // create instance of controller to handle http requests
    $authController = new AuthController();

    // routes
    switch ($method) {
        case 'POST':
            switch ($action) {
                case 'register':
                    echo $authController->register();
                    break;
                case 'login':
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
    http_response_code(500);
    echo json_encode([
        'error' => 'Server error: ' . $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
>>>>>>> 4658ee03da5d1374ed709d9794f9e156e7665d94
}
?>