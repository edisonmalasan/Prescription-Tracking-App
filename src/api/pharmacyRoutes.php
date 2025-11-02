<?php

require_once '../controllers/PharmacyController.php';

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
             case 'all':
                echo $pharmacyController->getAllPharmacies();
                break;
            case 'profile':
                echo $pharmacyController->getProfile();
                break;
            case 'prescriptions':
                echo $pharmacyController->getPrescriptions();
                break;
            case 'statistics':
                echo $pharmacyController->getStatistics();
                break;
            case 'prescription-details':
                echo $pharmacyController->getPrescriptionDetails();
                break;
            case 'filter':
                echo $pharmacyController->filterPrescriptions();
                break;
            case 'search-patient':
                echo $pharmacyController->searchByPatient();
                break;
            case 'search-drug':
                echo $pharmacyController->searchByDrug();
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
            case 'prescription-status':
                echo $pharmacyController->updatePrescriptionStatus();
                break;
            default:
                http_response_code(404);
                echo json_encode(['error' => 'Action not found']);
                break;
        }
        break;
    case 'DELETE':
        switch ($action) {
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
