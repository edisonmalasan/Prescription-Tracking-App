<?php
require_once '../service/PharmacyService.php';

class PharmacyController {
    private $pharmacyService;

    public function __construct() {
        $this->pharmacyService = new PharmacyService();
    }

    public function register() {
        //TODO
        return;
    }

    public function getProfile() {
        //TODO
        return;
    }

    public function updateProfile() {
        //TODO
        return;
    }

    public function getPrescriptions() {
        //TODO
        return;
    }

    public function updatePrescriptionStatus() {
        //TODO
        return;
    }

    public function filterPrescriptions() {
        //TODO
        return;
    }

    public function getStatistics() {
        //TODO
        return;
    }

    public function getPrescriptionDetails() {
        //TODO
        return;
    }

    public function searchByPatient() {
        //TODO
        return;
    }

    public function searchByDrug() {
        //TODO
        return;
    }
}
?>
