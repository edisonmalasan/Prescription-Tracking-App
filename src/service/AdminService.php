<?php

require_once '../repositories/UserRepository.php';
require_once '../repositories/DoctorRepository.php';
require_once '../repositories/PatientRepository.php';
require_once '../repositories/PharmacyRepository.php';
require_once '../repositories/PrescriptionRepository.php';
require_once '../repositories/DrugRepository.php';
require_once '../repositories/AdminRepository.php';

class AdminService {
    private $userRepository;
    private $doctorRepository;
    private $patientRepository;
    private $pharmacyRepository;
    private $prescriptionRepository;
    private $drugRepository;
    private $adminRepository;

    public function __construct() {
        $this->userRepository = new UserRepository();
        $this->doctorRepository = new DoctorRepository();
        $this->patientRepository = new PatientRepository();
        $this->pharmacyRepository = new PharmacyRepository();
        $this->prescriptionRepository = new PrescriptionRepository();
        $this->drugRepository = new DrugRepository();
        $this->adminRepository = new AdminRepository();
    }

    public function adminLogin($credentials) {
        if (empty($credentials['email']) || empty($credentials['password'])) {
            return ['error' => 'Email and password are required'];
        }

        $user = $this->userRepository->findByEmail($credentials['email']);
        
        if (!$user) {
            return ['error' => 'Admin not found'];
        }

        if ($user['role'] !== 'ADMIN') {
            return ['error' => 'Access denied. Admin privileges required'];
        }

        if (!password_verify($credentials['password'], $user['pass_hash'])) {
            return ['error' => 'Invalid password'];
        }

        $adminData = $this->adminRepository->findByUserId($user['user_id']);
        
        unset($user['pass_hash']);
        return [
            'success' => true,
            'message' => 'Admin login successful',
            'admin' => array_merge($user, $adminData ?: [])
        ];
    }

    public function getDashboardData() {
        try {
            $totalUsers = count($this->userRepository->findAll());
            $totalDoctors = count($this->doctorRepository->findAll());
            $totalPatients = count($this->patientRepository->findAll());
            $totalPharmacies = count($this->pharmacyRepository->findAll());
            $totalPrescriptions = count($this->prescriptionRepository->findAll());
            $totalDrugs = count($this->drugRepository->findAll());

            $unverifiedDoctors = $this->doctorRepository->findAll();
            $unverifiedDoctors = array_filter($unverifiedDoctors, function($doctor) {
                return $doctor['isVerified'] == 0;
            });

            return [
                'success' => true,
                'dashboard' => [
                    'statistics' => [
                        'total_users' => $totalUsers,
                        'total_doctors' => $totalDoctors,
                        'total_patients' => $totalPatients,
                        'total_pharmacies' => $totalPharmacies,
                        'total_prescriptions' => $totalPrescriptions,
                        'total_drugs' => $totalDrugs,
                        'pending_verifications' => count($unverifiedDoctors)
                    ],
                    'recent_activity' => [
                        'recent_users' => array_slice($this->userRepository->findAll(), -5),
                        'recent_prescriptions' => array_slice($this->prescriptionRepository->findAll(), -5)
                    ]
                ]
            ];
        } catch (Exception $e) {
            return ['error' => 'Failed to fetch dashboard data: ' . $e->getMessage()];
        }
    }

    public function getAllUsers($role = null) {
        try {
            if ($role) {
                $users = $this->userRepository->findByRole($role);
            } else {
                $users = $this->userRepository->findAll();
            }
            
            foreach ($users as &$user) {
                unset($user['pass_hash']);
            }
            
            return [
                'success' => true,
                'users' => $users
            ];
        } catch (Exception $e) {
            return ['error' => 'Failed to fetch users: ' . $e->getMessage()];
        }
    }

    public function createUser($userData) {
        try {
            if (empty($userData['email']) || empty($userData['password'])) {
                return ['error' => 'Email and password are required'];
            }

            $existingUser = $this->userRepository->findByEmail($userData['email']);
            if ($existingUser) {
                return ['error' => 'User with this email already exists'];
            }

            $userData['pass_hash'] = password_hash($userData['password'], PASSWORD_BCRYPT);
            unset($userData['password']);

            $userData['created_at'] = date('Y-m-d H:i:s');

            $userId = $this->userRepository->create($userData);

            if ($userId) {
                if (isset($userData['role'])) {
                    $this->createRoleSpecificRecord($userId, $userData['role'], $userData);
                }

                return [
                    'success' => true,
                    'message' => 'User created successfully',
                    'user_id' => $userId
                ];
            } else {
                return ['error' => 'Failed to create user'];
            }
        } catch (Exception $e) {
            return ['error' => 'Failed to create user: ' . $e->getMessage()];
        }
    }

    public function modifyUser($userId, $userData) {
        try {
            $existingUser = $this->userRepository->findById($userId);
            if (!$existingUser) {
                return ['error' => 'User not found'];
            }

            if (!empty($userData['password'])) {
                $userData['pass_hash'] = password_hash($userData['password'], PASSWORD_BCRYPT);
                unset($userData['password']);
            }

            $updatedData = array_merge($existingUser, $userData);
            
            $result = $this->userRepository->update($updatedData);
            
            if ($result !== false) {
                return [
                    'success' => true,
                    'message' => 'User updated successfully'
                ];
            } else {
                return ['error' => 'Failed to update user'];
            }
        } catch (Exception $e) {
            return ['error' => 'Failed to update user: ' . $e->getMessage()];
        }
    }

    public function deleteUser($userId) {
        try {
            $user = $this->userRepository->findById($userId);
            if (!$user) {
                return ['error' => 'User not found'];
            }

            $result = $this->userRepository->delete($userId);
            
            if ($result !== false) {
                return [
                    'success' => true,
                    'message' => 'User deleted successfully'
                ];
            } else {
                return ['error' => 'Failed to delete user'];
            }
        } catch (Exception $e) {
            return ['error' => 'Failed to delete user: ' . $e->getMessage()];
        }
    }

    public function verifyDoctor($doctorId) {
        try {
            $result = $this->doctorRepository->verifyDoctor($doctorId);
            
            if ($result !== false) {
                return [
                    'success' => true,
                    'message' => 'Doctor verified successfully'
                ];
            } else {
                return ['error' => 'Failed to verify doctor'];
            }
        } catch (Exception $e) {
            return ['error' => 'Failed to verify doctor: ' . $e->getMessage()];
        }
    }

    public function getPendingVerifications() {
        try {
            $doctors = $this->doctorRepository->findAll();
            $unverifiedDoctors = array_filter($doctors, function($doctor) {
                return $doctor['isVerified'] == 0;
            });
            
            return [
                'success' => true,
                'pending_verifications' => array_values($unverifiedDoctors)
            ];
        } catch (Exception $e) {
            return ['error' => 'Failed to fetch pending verifications: ' . $e->getMessage()];
        }
    }

    public function getSystemStatistics() {
        try {
            $totalUsers = count($this->userRepository->findAll());
            $totalDoctors = count($this->doctorRepository->findAll());
            $totalPatients = count($this->patientRepository->findAll());
            $totalPharmacies = count($this->pharmacyRepository->findAll());
            $totalPrescriptions = count($this->prescriptionRepository->findAll());
            $totalDrugs = count($this->drugRepository->findAll());
            
            $prescriptions = $this->prescriptionRepository->findAll();
            $statusCounts = [];
            foreach ($prescriptions as $prescription) {
                $status = $prescription['status'] ?? 'unknown';
                $statusCounts[$status] = ($statusCounts[$status] ?? 0) + 1;
            }

            return [
                'success' => true,
                'statistics' => [
                    'total_users' => $totalUsers,
                    'total_doctors' => $totalDoctors,
                    'total_patients' => $totalPatients,
                    'total_pharmacies' => $totalPharmacies,
                    'total_prescriptions' => $totalPrescriptions,
                    'total_drugs' => $totalDrugs,
                    'prescriptions_by_status' => $statusCounts
                ]
            ];
        } catch (Exception $e) {
            return ['error' => 'Failed to fetch system statistics: ' . $e->getMessage()];
        }
    }

    private function createRoleSpecificRecord($userId, $role, $data) {
        switch ($role) {
            case 'DOCTOR':
                return $this->doctorRepository->create($data);
            case 'PATIENT':
                return $this->patientRepository->create($userId, $data);
            case 'PHARMACY':
                return $this->pharmacyRepository->create($userId, $data);
            case 'ADMIN':
                return $this->adminRepository->create($userId);
        }
        return false;
    }
}
?>
