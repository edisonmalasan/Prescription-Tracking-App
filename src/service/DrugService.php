<?php
<<<<<<< HEAD
/**
 * Drug Service
 * Business logic for drug operations
 */
=======
>>>>>>> 4658ee03da5d1374ed709d9794f9e156e7665d94

require_once '../repositories/DrugRepository.php';
require_once '../models/drugModel.php';

class DrugService {
    private $drugRepository;

    public function __construct() {
        $this->drugRepository = new DrugRepository();
    }

    public function createDrug($drugData) {
<<<<<<< HEAD
        // Validate required fields
        if (empty($drugData['generic_name'])) {
            return ['error' => 'Generic name is required'];
        }

        // Check if drug already exists
        $existingDrug = $this->drugRepository->findByGenericName($drugData['generic_name']);
        if ($existingDrug) {
            return ['error' => 'Drug with this generic name already exists'];
        }

        // Create drug
        $drugId = $this->drugRepository->create($drugData);

        if ($drugId) {
            return [
                'success' => true,
                'message' => 'Drug created successfully',
                'drug_id' => $drugId
            ];
        } else {
            return ['error' => 'Failed to create drug'];
        }
    }

    public function getDrug($drugId) {
        $drug = $this->drugRepository->findById($drugId);
        
        if ($drug) {
            return [
                'success' => true,
                'drug' => $drug
            ];
        } else {
            return ['error' => 'Drug not found'];
        }
    }

    public function getAllDrugs() {
        $drugs = $this->drugRepository->findAll();
        
        return [
            'success' => true,
            'drugs' => $drugs
        ];
    }

    public function getDrugsByCategory($category) {
        $drugs = $this->drugRepository->findByCategory($category);
        
        return [
            'success' => true,
            'drugs' => $drugs
        ];
    }

    public function getControlledDrugs() {
        $drugs = $this->drugRepository->findControlled();
        
        return [
            'success' => true,
            'drugs' => $drugs
        ];
    }

    public function searchDrugs($searchTerm) {
        $drugs = $this->drugRepository->search($searchTerm);
        
        return [
            'success' => true,
            'drugs' => $drugs
        ];
    }

    public function updateDrug($drugId, $drugData) {
        $drugData['drug_id'] = $drugId;
        $result = $this->drugRepository->update($drugData);
        
        if ($result !== false) {
            return [
                'success' => true,
                'message' => 'Drug updated successfully'
            ];
        } else {
            return ['error' => 'Failed to update drug'];
        }
    }

    public function deleteDrug($drugId) {
        $result = $this->drugRepository->delete($drugId);
        
        if ($result !== false) {
            return [
                'success' => true,
                'message' => 'Drug deleted successfully'
            ];
        } else {
            return ['error' => 'Failed to delete drug'];
        }
=======
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
>>>>>>> 4658ee03da5d1374ed709d9794f9e156e7665d94
    }
}
?>
