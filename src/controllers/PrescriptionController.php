<?php
require_once '../service/PrescriptionService.php';

class PrescriptionController {
    private $prescriptionService;

    public function __construct() {
        $this->prescriptionService = new PrescriptionService();
    }

    public function createPrescription() {
        $data = json_decode(file_get_contents("php://input"), true);
                
        // validate if json data is empty if true then it will get form dataa
        if (empty($data)) {
            $data = $_POST;
        }
        
        $result = $this->prescriptionService->createPrescription($data);
        
        header('Content-Type: application/json');
        http_response_code(isset($result['error']) ? 400 : 201);
        return json_encode($result);
    }

    public function getPrescription() {
        $prescriptionId = $_GET['prescription_id'] ?? null;
        
        if (!$prescriptionId) {
            http_response_code(400);
            return json_encode(['error' => 'Prescription ID is required']);
        }
        
        $result = $this->prescriptionService->getPrescription($prescriptionId);
        
        header('Content-Type: application/json');
        http_response_code(isset($result['error']) ? 404 : 200);
        return json_encode($result);
    }

    public function getPrescriptionsByPatient() {
        $patientId = $_GET['patient_id'] ?? null;
        
        if (!$patientId) {
            http_response_code(400);
            return json_encode(['error' => 'Patient ID is required']);
        }
        
        $result = $this->prescriptionService->getPrescriptionsByPatient($patientId);
        
        header('Content-Type: application/json');
        return json_encode($result);
    }

    public function getPrescriptionsByDoctor() {
        $doctorId = $_GET['doctor_id'] ?? null;
        
        if (!$doctorId) {
            http_response_code(400);
            return json_encode(['error' => 'Doctor ID is required']);
        }
        
        $result = $this->prescriptionService->getPrescriptionsByDoctor($doctorId);
        
        header('Content-Type: application/json');
        return json_encode($result);
    }

    public function getAllPrescriptions() {
        $result = $this->prescriptionService->getAllPrescriptions();
        
        header('Content-Type: application/json');
        return json_encode($result);
    }

    public function updatePrescription() {
        $prescriptionId = $_GET['prescription_id'] ?? null;
        
        if (!$prescriptionId) {
            http_response_code(400);
            return json_encode(['error' => 'Prescription ID is required']);
        }
        
        $data = json_decode(file_get_contents("php://input"), true);
        if (empty($data)) {
            $data = $_POST;
        }
        
        $result = $this->prescriptionService->updatePrescription($prescriptionId, $data);
        
        header('Content-Type: application/json');
        http_response_code(isset($result['error']) ? 400 : 200);
        return json_encode($result);
    }

    public function addPrescriptionDetail() {
        $prescriptionId = $_GET['prescription_id'] ?? null;
        
        if (!$prescriptionId) {
            http_response_code(400);
            return json_encode(['error' => 'Prescription ID is required']);
        }
        
        $data = json_decode(file_get_contents("php://input"), true);

        if (empty($data)) {
            $data = $_POST;
        }
        
        $result = $this->prescriptionService->addPrescriptionDetail($prescriptionId, $data);
        
        header('Content-Type: application/json');
        http_response_code(isset($result['error']) ? 400 : 201);
        return json_encode($result);
    }

    public function getPrescriptionDetails() {
        $prescriptionId = $_GET['prescription_id'] ?? null;
        
        if (!$prescriptionId) {
            http_response_code(400);
            return json_encode(['error' => 'Prescription ID is required']);
        }
        
        $result = $this->prescriptionService->getPrescriptionDetails($prescriptionId);
        
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
        
        $result = $this->prescriptionService->updatePrescriptionStatus($prescriptionId, $data['status']);
        
        header('Content-Type: application/json');
        http_response_code(isset($result['error']) ? 400 : 200);
        return json_encode($result);
    }

    public function deletePrescription() {
        $prescriptionId = $_GET['prescription_id'] ?? null;
        
        if (!$prescriptionId) {
            http_response_code(400);
            return json_encode(['error' => 'Prescription ID is required']);
        }
        
        $result = $this->prescriptionService->deletePrescription($prescriptionId);
        
        header('Content-Type: application/json');
        http_response_code(isset($result['error']) ? 404 : 200);
        return json_encode($result);
    }

    public function getPrescriptionsByStatus() {
        $status = $_GET['status'] ?? null;
        
        if (!$status) {
            http_response_code(400);
            return json_encode(['error' => 'Status is required']);
        }
        
        $result = $this->prescriptionService->getPrescriptionsByStatus($status);
        
        header('Content-Type: application/json');
        return json_encode($result);
    }
}
?>
