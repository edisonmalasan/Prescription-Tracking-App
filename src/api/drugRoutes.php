<?php

require_once '../controllers/DrugController.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';
// create instance of controller to handle http requests
$drugController = new DrugController();

// routes
switch ($method) {
    case 'POST':
        switch ($action) {
            case 'create':
                echo $drugController->createDrug();
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
                echo $drugController->getDrug();
                break;
            case 'all':
                echo $drugController->getAllDrugs();
                break;
            case 'by-category':
                echo $drugController->getDrugsByCategory();
                break;
            case 'controlled':
                echo $drugController->getControlledDrugs();
                break;
            case 'search':
                echo $drugController->searchDrugs();
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
                echo $drugController->updateDrug();
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
                echo $drugController->deleteDrug();
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
