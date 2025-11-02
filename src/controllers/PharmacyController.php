<?php
require_once '../service/PharmacyService.php';

class PharmacyController {
    private $pharmacyService;

    public function __construct() {
        $this->pharmacyService = new PharmacyService();
    }

    public function register() {
        $data = json_decode(file_get_contents("php://input"), true);
        
        if (empty($data)) {
            $data = $_POST;
        }
        
        $result = $this->pharmacyService->registerPharmacy($data);
        
        header('Content-Type: application/json');
        http_response_code(isset($result['error']) ? 400 : 201);
        return json_encode($result);
    }

    public function getProfile() {
        $userId = $_GET['user_id'] ?? null;
        
        if (!$userId) {
            http_response_code(400);
            return json_encode(['error' => 'User ID is required']);
        }
        
        $result = $this->pharmacyService->getPharmacyProfile($userId);
        
        header('Content-Type: application/json');
        http_response_code(isset($result['error']) ? 404 : 200);
        return json_encode($result);
    }

    public function updateProfile() {
        $userId = $_GET['user_id'] ?? null;
        
        if (!$userId) {
            http_response_code(400);
            return json_encode(['error' => 'User ID is required']);
        }
                
        $data = json_decode(file_get_contents("php://input"), true);

        if (empty($data)) {
            $data = $_POST;
        }
        
        $result = $this->pharmacyService->updatePharmacyProfile($userId, $data);
        
        header('Content-Type: application/json');
        http_response_code(isset($result['error']) ? 400 : 200);
        return json_encode($result);
    }

    public function getPrescriptions() {
        $pharmacyId = $_GET['pharmacy_id'] ?? null;
        
        if (!$pharmacyId) {
            http_response_code(400);
            return json_encode(['error' => 'Pharmacy ID is required']);
        }
        
        $result = $this->pharmacyService->getPharmacyPrescriptions($pharmacyId);
        
        header('Content-Type: application/json');
        return json_encode($result);
    }

    public function updatePrescriptionStatus() {
        $prescriptionId = $_GET['prescription_id'] ?? null;
        
        if (!$prescriptionId) {
            http_response_code(400);
            return json_encode(['error' => 'Prescription ID is required']);
        }
        
        $data = json_decode(file_get_contents("php://input"), true);
        
        if (empty($data)) {
            $data = $_POST;
        }
        
        if (empty($data['status'])) {
            http_response_code(400);
            return json_encode(['error' => 'Status is required']);
        }
        
        $result = $this->pharmacyService->updatePrescriptionStatus($prescriptionId, $data['status']);
        
        header('Content-Type: application/json');
        http_response_code(isset($result['error']) ? 400 : 200);
        return json_encode($result);
    }

    public function filterPrescriptions() {
        $data = json_decode(file_get_contents("php://input"), true);
        
        if (empty($data)) {
            $data = $_GET;
        }
        
        $result = $this->pharmacyService->filterPrescriptions($data);
        
        header('Content-Type: application/json');
        return json_encode($result);
    }

    public function getStatistics() {
        $pharmacyId = $_GET['pharmacy_id'] ?? null;
        
        if (!$pharmacyId) {
            http_response_code(400);
            return json_encode(['error' => 'Pharmacy ID is required']);
        }
        
        $result = $this->pharmacyService->getPharmacyStatistics($pharmacyId);
        
        header('Content-Type: application/json');
        return json_encode($result);
    }

    public function getPrescriptionDetails() {
        $prescriptionId = $_GET['prescription_id'] ?? null;
        
        if (!$prescriptionId) {
            http_response_code(400);
            return json_encode(['error' => 'Prescription ID is required']);
        }
        
        require_once '../repositories/PrescriptionRepository.php';
        $prescriptionRepo = new PrescriptionRepository();
        $details = $prescriptionRepo->getPrescriptionDetails($prescriptionId);
        
        header('Content-Type: application/json');
        return json_encode([
            'success' => true,
            'details' => $details
        ]);
    }

    public function searchByPatient() {
        $searchTerm = $_GET['search'] ?? null;
        
        if (!$searchTerm) {
            http_response_code(400);
            return json_encode(['error' => 'Search term is required']);
        }
        
        $result = $this->pharmacyService->searchByPatient($searchTerm);
        
        header('Content-Type: application/json');
        return json_encode($result);
    }

    public function searchByDrug() {
        $drugId = $_GET['drug_id'] ?? null;
        
        if (!$drugId) {
            http_response_code(400);
            return json_encode(['error' => 'Drug ID is required']);
        }
        
        $result = $this->pharmacyService->searchByDrug($drugId);
        
        header('Content-Type: application/json');
        return json_encode($result);
    }
}
?>
