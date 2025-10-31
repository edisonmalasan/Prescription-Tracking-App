<?php

require_once '../controllers/AdminController.php';
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';
// create instance of controller to handle http requests
$adminController = new AdminController();

// routes
switch ($method) {
    case 'POST':
        switch ($action) {
            case 'login':
                echo $adminController->login();
                break;
            case 'create-user':
                echo $adminController->createUser();
                break;
            case 'create-record':
                echo $adminController->createDatabaseRecord();
                break;
            default:
                http_response_code(404);
                echo json_encode(['error' => 'Action not found']);
                break;
        }
        break;
    case 'GET':
        switch ($action) {
            case 'dashboard':
                echo $adminController->getDashboard();
                break;
            case 'all-users':
                echo $adminController->getAllUsers();
                break;
            case 'pending-verifications':
                echo $adminController->getPendingVerifications();
                break;
            case 'database-tables':
                echo $adminController->viewDatabaseTables();
                break;
            case 'statistics':
                echo $adminController->getSystemStatistics();
                break;
            case 'drug-database':
                echo $adminController->manageDrugDatabase();
                break;
            default:
                http_response_code(404);
                echo json_encode(['error' => 'Action not found']);
                break;
        }
        break;
    case 'PUT':
        switch ($action) {
            case 'modify-user':
                echo $adminController->modifyUser();
                break;
            case 'verify-doctor':
                echo $adminController->verifyDoctor();
                break;
            case 'modify-record':
                echo $adminController->modifyDatabaseRecord();
                break;
            default:
                http_response_code(404);
                echo json_encode(['error' => 'Action not found']);
                break;
        }
        break;
    case 'DELETE':
        switch ($action) {
            case 'delete-user':
                echo $adminController->deleteUser();
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
