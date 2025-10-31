<?php

require_once '../controllers/PrescriptionController.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';
// create instance of controller to handle http requests
$prescriptionController = new PrescriptionController();

// routes
switch ($method) {
    case 'POST':
        switch ($action) {
            case 'create':
                echo $prescriptionController->createPrescription();
                break;
            case 'add-detail':
                echo $prescriptionController->addPrescriptionDetail();
                break;
            default:
                http_response_code(404);
                echo json_encode(['error' => 'Action not found']);
                break;
        }
        break;
    case 'GET':
        switch ($action) {
            case 'get':
                echo $prescriptionController->getPrescription();
                break;
            case 'by-patient':
                echo $prescriptionController->getPrescriptionsByPatient();
                break;
            case 'by-doctor':
                echo $prescriptionController->getPrescriptionsByDoctor();
                break;
            case 'all':
                echo $prescriptionController->getAllPrescriptions();
                break;
            case 'details':
                echo $prescriptionController->getPrescriptionDetails();
                break;
            case 'by-status':
                echo $prescriptionController->getPrescriptionsByStatus();
                break;
            default:
                http_response_code(404);
                echo json_encode(['error' => 'Action not found']);
                break;
        }
        break;
    case 'PUT':
        switch ($action) {
            case 'update':
                echo $prescriptionController->updatePrescription();
                break;
            case 'update-status':
                echo $prescriptionController->updatePrescriptionStatus();
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
                echo $prescriptionController->deletePrescription();
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
