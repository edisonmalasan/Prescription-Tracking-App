<?php

require_once '../controllers/PatientController.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';
// create instance of controller to handle http requests
$patientController = new PatientController()

switch ($method) {
    case 'POST':
        switch ($action) {
            case 'register':
                echo $patientController->register();
                break;
            case 'medical-record':
                echo $patientController->createMedicalRecord();
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
                echo $patientController->getProfile();
                break;
            case 'all':
                echo $patientController->getAllPatients();
                break;
            case 'medical-record':
                echo $patientController->getMedicalRecord();
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
                echo $patientController->updateProfile();
                break;
            case 'medical-record':
                echo $patientController->updateMedicalRecord();
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
                echo $patientController->deletePatient();
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