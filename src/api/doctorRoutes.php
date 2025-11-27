<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../controllers/DoctorController.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';
$doctorController = new DoctorController();

switch ($method) {
    case 'POST':
        switch ($action) {
            case 'register':
                echo $doctorController->register();
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
                echo $doctorController->getProfile();
                break;
            case 'all':
                echo $doctorController->getAllDoctors();
                break;
            case 'verified':
                echo $doctorController->getVerifiedDoctors();
                break;
            case 'specialization':
                echo $doctorController->getDoctorsBySpecialization();
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
                echo $doctorController->updateProfile();
                break;
            case 'verify':
                echo $doctorController->verifyDoctor();
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
                echo $doctorController->deleteDoctor();
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