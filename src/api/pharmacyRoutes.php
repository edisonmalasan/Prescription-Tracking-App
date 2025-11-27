<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../controllers/PharmacyController.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';
$pharmacyController = new PharmacyController();

switch ($method) {
    case 'POST':
        switch ($action) {
            case 'register':
                echo $pharmacyController->register();
                break;
            default:
                http_response_code(404);
                echo json_encode(['error' => 'Action not found']);
                break;
        }
        break;
    case 'GET':
        switch ($action) {
            case 'profile':
                echo $pharmacyController->getProfile();
                break;
            case 'all':
                echo $pharmacyController->getAllPharmacies();
                break;
            default:
                http_response_code(404);
                echo json_encode(['error' => 'Action not found']);
                break;
        }
        break;
    case 'PUT':
        switch ($action) {
            case 'profile':
                echo $pharmacyController->updateProfile();
                break;
            default:
                http_response_code(404);
                echo json_encode(['error' => 'Action not found']);
                break;
        }
        break;
    case 'DELETE':
        switch ($action) {
            case 'delete':
                echo $pharmacyController->deletePharmacy();
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
?>