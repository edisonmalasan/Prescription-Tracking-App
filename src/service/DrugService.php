<?php

require_once '../repositories/DrugRepository.php';
require_once '../models/drugModel.php';

class DrugService {
    private $drugRepository;

    public function __construct() {
        $this->drugRepository = new DrugRepository();
    }

    public function createDrug($drugData) {
        //TODO
        return;
    }

    public function getDrug($drugId) {
        //TODO
        return;
    }

    public function getAllDrugs() {
        //TODO
        return;
    }

    public function getDrugsByCategory($category) {
        //TODO
        return;
    }

    public function getControlledDrugs() {
        //TODO
        return;
    }

    public function searchDrugs($searchTerm) {
        //TODO
        return;
    }

    public function updateDrug($drugId, $drugData) {
        //TODO
        return;
    }

    public function deleteDrug($drugId) {
        //TODO
        return;
    }
}
?>
