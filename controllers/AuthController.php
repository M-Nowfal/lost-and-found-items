<?php
/**
 * Authentication Controller
 * Lost & Found Portal
 */

require_once ROOT_PATH . 'config/constants.php';
require_once ROOT_PATH . 'config/database.php';
require_once ROOT_PATH . 'config/mail.php';
require_once ROOT_PATH . 'models/User.php';
require_once ROOT_PATH . 'models/Notification.php';

class AuthController {
    private $userModel;
    private $notificationModel;
    
    public function __construct() {
        $this->userModel = new User();
        $this->notificationModel = new Notification();
    }
    
    public function register($data) {
        $errors = $this->validateRegistration($data);
        
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }
        
        if ($this->userModel->emailExists($data['email'])) {
            return ['success' => false, 'errors' => ['email' => 'Email already registered']];
        }
        
        $userId = $this->userModel->create([
            'name' => sanitize($data['name']),
            'email' => sanitize($data['email']),
            'password' => $data['password'],
            'role' => 'user',
            'verified' => false
        ]);
        
        if ($userId) {
            $this->notificationModel->createForUser(
                $userId,
                'Welcome to Lost & Found Portal! Your account is pending verification.',
                'system'
            );
            
            return ['success' => true, 'message' => 'Registration successful! Please wait for admin verification.'];
        }
        
        return ['success' => false, 'errors' => ['general' => 'Registration failed. Please try again.']];
    }
    
    public function login($email, $password) {
        $errors = $this->validateLogin(['email' => $email, 'password' => $password]);
        
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }
        
        $user = $this->userModel->findByEmailAndPassword($email, $password);
        
        if (!$user) {
            return ['success' => false, 'errors' => ['general' => 'Invalid email or password']];
        }
        
        if ($user['role'] === 'user' && !$user['verified']) {
            return ['success' => false, 'errors' => ['general' => 'Your account is pending verification. Please wait for admin approval.']];
        }
        
        $this->setSession($user);
        
        return ['success' => true, 'user' => $this->sanitizeUser($user)];
    }
    
    public function logout() {
        session_destroy();
        return ['success' => true];
    }
    
    public function getCurrentUser() {
        if (!isLoggedIn()) {
            return null;
        }
        
        return $this->userModel->findById(getCurrentUserId());
    }
    
    private function setSession($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user'] = $this->sanitizeUser($user);
        $_SESSION['role'] = $user['role'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['login_time'] = time();
    }
    
    private function sanitizeUser($user) {
        unset($user['password']);
        return $user;
    }
    
    private function validateRegistration($data) {
        $errors = [];
        
        if (empty($data['name'])) {
            $errors['name'] = 'Name is required';
        } elseif (strlen($data['name']) < 2) {
            $errors['name'] = 'Name must be at least 2 characters';
        }
        
        if (empty($data['email'])) {
            $errors['email'] = 'Email is required';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email format';
        }
        
        if (empty($data['password'])) {
            $errors['password'] = 'Password is required';
        } elseif (strlen($data['password']) < 6) {
            $errors['password'] = 'Password must be at least 6 characters';
        }
        
        if (empty($data['confirm_password'])) {
            $errors['confirm_password'] = 'Please confirm your password';
        } elseif ($data['password'] !== $data['confirm_password']) {
            $errors['confirm_password'] = 'Passwords do not match';
        }
        
        return $errors;
    }
    
    private function validateLogin($data) {
        $errors = [];
        
        if (empty($data['email'])) {
            $errors['email'] = 'Email is required';
        }
        
        if (empty($data['password'])) {
            $errors['password'] = 'Password is required';
        }
        
        return $errors;
    }
}
