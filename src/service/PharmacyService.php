<?php

require_once '../repositories/PharmacyRepository.php';
require_once '../repositories/UserRepository.php';
require_once '../repositories/PrescriptionRepository.php';
require_once '../repositories/DrugRepository.php';
require_once '../models/pharmacyModel.php';

class PharmacyService {
    private $pharmacyRepository;
    private $userRepository;
    private $prescriptionRepository;
    private $drugRepository;

    public function __construct() {
        $this->pharmacyRepository = new PharmacyRepository();
        $this->userRepository = new UserRepository();
        $this->prescriptionRepository = new PrescriptionRepository();
        $this->drugRepository = new DrugRepository();
    }

    public function registerPharmacy($pharmacyData) {
        if (empty($pharmacyData['email']) || empty($pharmacyData['password'])) {
            return ['error' => 'Email and password are required'];
        }

        $existingUser = $this->userRepository->findByEmail($pharmacyData['email']);
        if ($existingUser) {
            return ['error' => 'Pharmacy with this email already exists'];
        }

        $pharmacyData['pass_hash'] = password_hash($pharmacyData['password'], PASSWORD_BCRYPT);
        unset($pharmacyData['password']);

        $pharmacyData['role'] = 'PHARMACY';
        $pharmacyData['created_at'] = date('Y-m-d H:i:s');

        $userId = $this->userRepository->create($pharmacyData);

        if ($userId) {
            $pharmacyProfileData = [
                'pharmacy_name' => $pharmacyData['pharmacy_name'] ?? '',
                'phar_license' => $pharmacyData['phar_license'] ?? '',
                'open_time' => $pharmacyData['open_time'] ?? '',
                'close_time' => $pharmacyData['close_time'] ?? '',
                'dates_open' => $pharmacyData['dates_open'] ?? ''
            ];
            
            $this->pharmacyRepository->create($userId, $pharmacyProfileData);
            
            return [
                'success' => true,
                'message' => 'Pharmacy registered successfully',
                'user_id' => $userId
            ];
        } else {
            return ['error' => 'Failed to register pharmacy'];
        }
    }

    public function getPharmacyProfile($userId) {
        $user = $this->userRepository->findById($userId);
        $pharmacy = $this->pharmacyRepository->findByUserId($userId);
        
        if ($user && $pharmacy) {
            unset($user['pass_hash']);
            return [
                'success' => true,
                'pharmacy' => array_merge($user, $pharmacy)
            ];
        } else {
            return ['error' => 'Pharmacy not found'];
        }
    }

    public function updatePharmacyProfile($userId, $pharmacyData) {
        $existingPharmacy = $this->pharmacyRepository->findByUserId($userId);
        if (!$existingPharmacy) {
            return ['error' => 'Pharmacy not found'];
        }

        $pharmacyData['user_id'] = $userId;
        $result = $this->pharmacyRepository->update($pharmacyData);
        
        if ($result !== false) {
            return [
                'success' => true,
                'message' => 'Pharmacy profile updated successfully'
            ];
        } else {
            return ['error' => 'Failed to update pharmacy profile'];
        }
    }

    public function getPharmacyPrescriptions($pharmacyId) {
        // get all prescription
        $prescriptions = $this->prescriptionRepository->findAll();
        
        return [
            'success' => true,
            'prescriptions' => $prescriptions
        ];
    }

    public function updatePrescriptionStatus($prescriptionId, $status) {
        $prescriptionData = [
            'prescription_id' => $prescriptionId,
            'status' => $status
        ];
        
        $result = $this->prescriptionRepository->update($prescriptionData);
        
        if ($result !== false) {
            return [
                'success' => true,
                'message' => 'Prescription status updated successfully'
            ];
        } else {
            return ['error' => 'Failed to update prescription status'];
        }
    }

    public function getPharmacyStatistics($pharmacyId) {
        $allPrescriptions = $this->prescriptionRepository->findAll();
        $allDetails = [];
        
        foreach ($allPrescriptions as $prescription) {
            $details = $this->prescriptionRepository->getPrescriptionDetails($prescription['prescription_id']);
            foreach ($details as $detail) {
                $allDetails[] = $detail;
            }
        }

        $drugCounts = [];
        foreach ($allDetails as $detail) {
            $drugId = $detail['drug_id'];
            if (!isset($drugCounts[$drugId])) {
                $drug = $this->drugRepository->findById($drugId);
                $drugCounts[$drugId] = [
                    'drug' => $drug,
                    'count' => 0
                ];
            }
            $drugCounts[$drugId]['count']++;
        }

        usort($drugCounts, function($a, $b) {
            return $b['count'] - $a['count'];
        });

        $popularDrugs = array_slice($drugCounts, 0, 10);

        return [
            'success' => true,
            'total_prescriptions' => count($allPrescriptions),
            'popular_drugs' => $popularDrugs
        ];
    }

    public function filterPrescriptions($filters) {
        $allPrescriptions = $this->prescriptionRepository->findAll();
        $filtered = $allPrescriptions;

        if (!empty($filters['status'])) {
            $filtered = array_filter($filtered, function($prescription) use ($filters) {
                return $prescription['status'] === $filters['status'];
            });
        }

        if (!empty($filters['start_date'])) {
            $filtered = array_filter($filtered, function($prescription) use ($filters) {
                return $prescription['prescription_date'] >= $filters['start_date'];
            });
        }

        if (!empty($filters['end_date'])) {
            $filtered = array_filter($filtered, function($prescription) use ($filters) {
                return $prescription['prescription_date'] <= $filters['end_date'];
            });
        }

        return [
            'success' => true,
            'prescriptions' => array_values($filtered)
        ];
    }

    public function searchByPatient($searchTerm) {
        $prescriptions = $this->prescriptionRepository->findAll();
        
        return [
            'success' => true,
            'prescriptions' => $prescriptions
        ];
    }

    public function searchByDrug($drugId) {
        $allPrescriptions = $this->prescriptionRepository->findAll();
        $filtered = [];

        foreach ($allPrescriptions as $prescription) {
            $details = $this->prescriptionRepository->getPrescriptionDetails($prescription['prescription_id']);
            foreach ($details as $detail) {
                if ($detail['drug_id'] == $drugId) {
                    $prescription['details'] = $details;
                    $filtered[] = $prescription;
                    break;
                }
            }
        }

        return [
            'success' => true,
            'prescriptions' => $filtered
        ];
    }
}
?>
