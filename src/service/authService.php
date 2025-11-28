<?php
require_once __DIR__ . '/../repositories/UserRepository.php';
require_once __DIR__ . '/../repositories/DoctorRepository.php';
require_once __DIR__ . '/../repositories/PatientRepository.php';
require_once __DIR__ . '/../repositories/PharmacyRepository.php';
require_once __DIR__ . '/../config/db.php';

class AuthService {
    private $userRepo;
    private $doctorRepo;
    private $patientRepo;
    private $pharmacyRepo;
    private $conn;

    public function __construct() {
        try {
            $this->userRepo = new UserRepository();
            $this->doctorRepo = new DoctorRepository();
            $this->patientRepo = new PatientRepository();
            $this->pharmacyRepo = new PharmacyRepository();
            $database = new Database();
            $this->conn = $database->getConnection();
        } catch (PDOException $e) {
            throw new Exception("Database connection failed");
        } catch (Exception $e) {
            throw $e;
        }
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

            // $hash = password_hash($data['password'], PASSWORD_BCRYPT);
            // for prototype
            $plainPassword = $data['password'];

            $payload = [
                'last_name'   => $data['last_name'] ?? '',
                'first_name'  => $data['first_name'] ?? '',
                'email'       => $data['email'],
                'role'        => $data['role'] ?? 'PATIENT', // default to patient
                'contactno'   => $data['contactno'] ?? '',
                'address'     => $data['address'] ?? '',
                'pass_hash'   => $plainPassword,
                'created_at'  => date('Y-m-d H:i:s'),
            ];
            
            $contact = trim($payload['contactno'] ?? '');
            if ($contact === '') {
                unset($payload['contactno']);
            } else {
                $payload['contactno'] = preg_replace('/[^\d\+]/', '', $contact);
            }
            
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
            return ['error' => 'Registration failed: ' . $e->getMessage()];
        }
    }

    public function login($data) {
        try {
            if (empty($data['email']) || empty($data['password'])) {
                return ['error' => 'Email & password required'];
            }
            
            $user = $this->userRepo->findByEmail($data['email']);
            if (!$user) {
                return ['error' => 'Account not found'];
            }

            // for pass hash
            // if (!password_verify($data['password'], $user['pass_hash'])) {
            //     return ['error' => 'Invalid password'];
            // }

            if ($data['password'] !== $user['pass_hash']) {
                return ['error' => 'Invalid password'];
            }

            unset($user['pass_hash']);
            
            $roleData = $this->getRoleSpecificData($user['user_id'], $user['role']);
            
            return [
                'success' => true,
                'message' => 'Logged in',
                'user' => array_merge($user, $roleData)
            ];
        } catch (Exception $e) {
            return ['error' => 'Login failed: ' . $e->getMessage()];
        }
    }
    
    private function createRoleSpecificRecord($userId, $role, $data) {
        try {
            switch ($role) {
                case 'DOCTOR':
                    return $this->doctorRepo->createDoctorRecord($userId, $data);
                case 'PATIENT':
                    return $this->patientRepo->create($userId, $data);
                case 'PHARMACY':
                    return $this->pharmacyRepo->create($userId, $data);
            }
            return false;
        } catch (Exception $e) {
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
        }
        return [];
    }
}
?>