<?php
require_once __DIR__ . '/../repositories/UserRepository.php';
require_once __DIR__ . '/../repositories/DoctorRepository.php';
require_once __DIR__ . '/../repositories/PatientRepository.php';
require_once __DIR__ . '/../repositories/PharmacyRepository.php';
require_once __DIR__ . '/../repositories/AdminRepository.php';
require_once __DIR__ . '/../config/db.php';

class AuthService {
    private $userRepo;
    private $doctorRepo;
    private $patientRepo;
    private $pharmacyRepo;
    private $adminRepo;
    private $conn;

    public function __construct() {
        $this->userRepo = new UserRepository();
        $this->doctorRepo = new DoctorRepository();
        $this->patientRepo = new PatientRepository();
        $this->pharmacyRepo = new PharmacyRepository();
        $this->adminRepo = new AdminRepository();
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function register($data) {
        try {
            if (empty($data['email']) || empty($data['password'])) {
                return ['error' => 'Email & password required'];
            }

            $exists = $this->userRepo->findByEmail($data['email']);
            if ($exists) {
                return ['error' => 'Email already exists'];
            }

            $hash = password_hash($data['password'], PASSWORD_BCRYPT);

            $payload = [
                'last_name'   => $data['last_name'] ?? '',
                'first_name'  => $data['first_name'] ?? '',
                'email'       => $data['email'],
                'role'        => $data['role'] ?? 'PATIENT', // default to patient
                'contactno'   => $data['contactno'] ?? '',
                'address'     => $data['address'] ?? '',
                'pass_hash'   => $hash,
                'created_at'  => date('Y-m-d H:i:s'),
            ];
            
            $id = $this->userRepo->create($payload);
            
            if (!$id) {
                return ['error' => 'Failed to create user'];
            }

            $roleResult = $this->createRoleSpecificRecord($id, $data['role'] ?? 'PATIENT', $data);
            
            if ($roleResult === false) {
                $this->userRepo->delete($id);
                return ['error' => 'Failed to create role-specific record'];
            }

            return [
                'success' => true,
                'message' => 'Registered successfully',
                'user_id' => $id
            ];
        } catch (Exception $e) {
            error_log("Registration error: " . $e->getMessage());
            return ['error' => 'Registration failed: ' . $e->getMessage()];
        }
    }

    public function login($data) {

        if (empty($data['email']) || empty($data['password'])) {
            return ['error' => 'Email & password required'];
        }
        
        $user = $this->userRepo->findByEmail($data['email']);
        if (!$user) {
            return ['error' => 'Account not found'];
        }

        if (!password_verify($data['password'], $user['pass_hash'])) {
            return ['error' => 'Invalid password'];
        }

        $roleData = $this->getRoleSpecificData($user['user_id'], $user['role']);
        
        return [
            'message' => 'Logged in',
            'user' => array_merge($user, $roleData)
        ];
    }
    
    private function createRoleSpecificRecord($userId, $role, $data) {
        try {
            switch ($role) {
                case 'DOCTOR':
                    // Create doctor-specific record only (user already exists)
                    return $this->doctorRepo->createDoctorRecord($userId, $data);
                case 'PATIENT':
                    return $this->patientRepo->create($userId, $data);
                case 'PHARMACY':
                    return $this->pharmacyRepo->create($userId, $data);
                case 'ADMIN':
                    return $this->adminRepo->create($userId);
            }
            return false;
        } catch (Exception $e) {
            error_log("Error creating role-specific record: " . $e->getMessage());
            return false;
        }
    }
    
    private function getRoleSpecificData($userId, $role) {
         switch ($role) {
            case 'DOCTOR':
                return $this->doctorRepo->findByUserId($userId) ?: [];
            case 'PATIENT':
                return $this->patientRepo->findByUserId($userId) ?: [];
            case 'PHARMACY':
                return $this->pharmacyRepo->findByUserId($userId) ?: [];
            case 'ADMIN':
                return $this->adminRepo->findByUserId($userId) ?: [];
        }
        return [];
    }
}
?>