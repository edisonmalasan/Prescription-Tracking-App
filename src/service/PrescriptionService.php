<?php

require_once '../repositories/PrescriptionRepository.php';
require_once '../repositories/DrugRepository.php';
require_once '../repositories/MedicalRecordRepository.php';
require_once '../models/prescriptionModel.php';
require_once '../models/prescriptionDetailModel.php';

class PrescriptionService {
    private $prescriptionRepository;
    private $drugRepository;
    private $medicalRecordRepository;

    public function __construct() {
        $this->prescriptionRepository = new PrescriptionRepository();
        $this->drugRepository = new DrugRepository();
        $this->medicalRecordRepository = new MedicalRecordRepository();
    }

    public function createPrescription($prescriptionData) {
       //TODO
        return;
    }

    public function getPrescription($prescriptionId) {
        //TODO
        return;
    }

    public function getPrescriptionsByPatient($patientId) {
        //TODO
        return;
    }

    public function getPrescriptionsByDoctor($doctorId) {
        //TODO
        return;
    }

    public function getAllPrescriptions() {
        //TODO
        return;
    }

    public function updatePrescription($prescriptionId, $prescriptionData) {
        //TODO
        return;
    }

    public function addPrescriptionDetail($prescriptionId, $detailData) {
        //TODO
        return;
    }

    public function getPrescriptionDetails($prescriptionId) {
        //TODO
        return;
    }

    public function updatePrescriptionStatus($prescriptionId, $status) {
        //TODO
        return;
    }

    public function deletePrescription($prescriptionId) {
        //TODO
        return;
    }

    public function getPrescriptionsByStatus($status) {
        //TODO
        return;
    }
}
?>