<?php

require_once '../repositories/UserRepository.php';
require_once '../models/userModel.php';

class UserService {
    private $userRepository;

    public function __construct() {
        $this->userRepository = new UserRepository();
    }

    public function registerUser($userData) {
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
            return [
                'success' => true,
                'message' => 'User registered successfully',
                'user_id' => $userId
            ];
        } else {
            return ['error' => 'Failed to register user'];
        }
    }

    public function authenticateUser($email, $password) {
        $user = $this->userRepository->findByEmail($email);
        
        if (!$user) {
            return ['error' => 'User not found'];
        }

        if (!password_verify($password, $user['pass_hash'])) {
            return ['error' => 'Invalid password'];
        }

        unset($user['pass_hash']);
        return [
            'success' => true,
            'user' => $user
        ];
    }

    public function updateProfile($userId, $userData) {
        $existingUser = $this->userRepository->findById($userId);
        if (!$existingUser) {
            return ['error' => 'User not found'];
        }

        $updatedData = array_merge($existingUser, $userData);
    
        $result = $this->userRepository->update($updatedData);
        
        if ($result !== false) {
            return [
                'success' => true,
                'message' => 'Profile updated successfully'
            ];
        } else {
            return ['error' => 'Failed to update profile'];
        }
    }

    public function getUserProfile($userId) {
        $user = $this->userRepository->findById($userId);
        
        if ($user) {
            unset($user['pass_hash']);
            return [
                'success' => true,
                'user' => $user
            ];
        } else {
            return ['error' => 'User not found'];
        }
    }

    public function searchUsers($searchTerm, $role = null) {
        if ($role) {
            return $this->userRepository->findByRole($role);
        } else {
            return $this->userRepository->search($searchTerm);
        }
    }

    public function deleteUser($userId) {
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
    }

    public function changePassword($userId, $oldPassword, $newPassword) {
        $user = $this->userRepository->findById($userId);
        if (!$user) {
            return ['error' => 'User not found'];
        }

        if (!password_verify($oldPassword, $user['pass_hash'])) {
            return ['error' => 'Current password is incorrect'];
        }

        $newHash = password_hash($newPassword, PASSWORD_BCRYPT);

        $user['pass_hash'] = $newHash;
        $result = $this->userRepository->update($user);
        
        if ($result !== false) {
            return [
                'success' => true,
                'message' => 'Password changed successfully'
            ];
        } else {
            return ['error' => 'Failed to change password'];
        }
    }

    public function resetPassword($email) {
        $user = $this->userRepository->findByEmail($email);
        if (!$user) {
            return ['error' => 'No user found with this email'];
        }

        return [
            'success' => true,
            'message' => 'Password reset instructions sent to your email'
        ];
    }
}
?>