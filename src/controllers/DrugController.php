<?php

require_once '../service/DrugService.php';

class DrugController {
    private $drugService;

    public function __construct() {
        $this->drugService = new DrugService();
    }

    public function createDrug() {
        $data = json_decode(file_get_contents("php://input"), true);
        
        if (empty($data)) {
            $data = $_POST;
        }
        
        $result = $this->drugService->createDrug($data);
        
        header('Content-Type: application/json');
        http_response_code(isset($result['error']) ? 400 : 201);
        return json_encode($result);
    }

    public function getDrug() {
        $drugId = $_GET['drug_id'] ?? null;
        
        if (!$drugId) {
            http_response_code(400);
            return json_encode(['error' => 'Drug ID is required']);
        }
        
        $result = $this->drugService->getDrug($drugId);
        
        header('Content-Type: application/json');
        http_response_code(isset($result['error']) ? 404 : 200);
        return json_encode($result);
    }

    public function getAllDrugs() {
        $result = $this->drugService->getAllDrugs();
        
        header('Content-Type: application/json');
        return json_encode($result);
    }

    public function getDrugsByCategory() {
        $category = $_GET['category'] ?? null;
        
        if (!$category) {
            http_response_code(400);
            return json_encode(['error' => 'Category is required']);
        }
        
        $result = $this->drugService->getDrugsByCategory($category);
        
        header('Content-Type: application/json');
        return json_encode($result);
    }

    public function getControlledDrugs() {
        $result = $this->drugService->getControlledDrugs();
        
        header('Content-Type: application/json');
        return json_encode($result);
    }

    public function searchDrugs() {
        $searchTerm = $_GET['search'] ?? '';
        
        if (empty($searchTerm)) {
            http_response_code(400);
            return json_encode(['error' => 'Search term is required']);
        }
        
        $result = $this->drugService->searchDrugs($searchTerm);
        
        header('Content-Type: application/json');
        return json_encode($result);
    }

    public function updateDrug() {
        $drugId = $_GET['drug_id'] ?? null;
        
        if (!$drugId) {
            http_response_code(400);
            return json_encode(['error' => 'Drug ID is required']);
        }
        
        $data = json_decode(file_get_contents("php://input"), true);
        
        if (empty($data)) {
            $data = $_POST;
        }
        
        $result = $this->drugService->updateDrug($drugId, $data);
        
        header('Content-Type: application/json');
        http_response_code(isset($result['error']) ? 400 : 200);
        return json_encode($result);
    }

    public function deleteDrug() {
        $drugId = $_GET['drug_id'] ?? null;
        
        if (!$drugId) {
            http_response_code(400);
            return json_encode(['error' => 'Drug ID is required']);
        }
        
        $result = $this->drugService->deleteDrug($drugId);
        
        header('Content-Type: application/json');
        http_response_code(isset($result['error']) ? 404 : 200);
        return json_encode($result);
    }
}
?>
