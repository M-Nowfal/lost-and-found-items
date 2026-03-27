<?php
/**
 * Admin Controller
 * Lost & Found Portal
 */

require_once ROOT_PATH . 'config/constants.php';
require_once ROOT_PATH . 'config/database.php';
require_once ROOT_PATH . 'config/mail.php';
require_once ROOT_PATH . 'models/User.php';
require_once ROOT_PATH . 'models/Item.php';
require_once ROOT_PATH . 'models/Match.php';
require_once ROOT_PATH . 'models/Notification.php';

class AdminController {
    private $userModel;
    private $itemModel;
    private $matchModel;
    private $notificationModel;
    
    public function __construct() {
        $this->userModel = new User();
        $this->itemModel = new Item();
        $this->matchModel = new MatchModel();
        $this->notificationModel = new Notification();
    }
    
    public function getUsers($limit = USERS_PER_PAGE, $offset = 0) {
        requireAdmin();
        return $this->userModel->getAll($limit, $offset);
    }
    
    public function verifyUser($userId, $verified = true) {
        requireAdmin();
        
        $user = $this->userModel->findById($userId);
        if (!$user) {
            return ['success' => false, 'errors' => ['general' => 'User not found']];
        }
        
        $this->userModel->verify($userId, $verified);
        
        $this->notificationModel->notifyUserVerified($userId, $verified);
        
        try {
            $mailer = getMailer();
            $mailer->sendVerificationNotification($user['email'], $user['name'], $verified);
        } catch (Exception $e) {
            error_log("Email notification failed: " . $e->getMessage());
        }
        
        return [
            'success' => true,
            'message' => $verified ? 'User verified successfully!' : 'User verification revoked'
        ];
    }
    
    public function getItems($limit = 100, $offset = 0, $filters = []) {
        requireAdmin();
        return $this->itemModel->getAll($limit, $offset, $filters);
    }
    
    public function verifyItem($itemId, $verified = true) {
        requireAdmin();
        
        $item = $this->itemModel->findById($itemId);
        if (!$item) {
            return ['success' => false, 'errors' => ['general' => 'Item not found']];
        }
        
        $this->itemModel->verify($itemId, $verified);
        
        $this->notificationModel->notifyItemVerified($item['user_id'], $item['title'], $verified);
        
        return [
            'success' => true,
            'message' => $verified ? 'Item verified successfully!' : 'Item verification revoked'
        ];
    }
    
    public function deleteItem($itemId) {
        requireAdmin();
        
        $item = $this->itemModel->findById($itemId);
        if (!$item) {
            return ['success' => false, 'errors' => ['general' => 'Item not found']];
        }
        
        if ($item['image_path'] && file_exists(ROOT_PATH . $item['image_path'])) {
            unlink(ROOT_PATH . $item['image_path']);
        }
        
        $this->itemModel->delete($itemId);
        
        $this->notificationModel->createForUser(
            $item['user_id'],
            "Your item '{$item['title']}' has been removed by an administrator.",
            'system'
        );
        
        return ['success' => true, 'message' => 'Item deleted successfully'];
    }
    
    public function getDashboardStats() {
        requireAdmin();
        
        return [
            'users' => [
                'total' => $this->userModel->count(),
                'verified' => $this->userModel->count('user'),
                'unverified' => count($this->userModel->getUnverified()),
                'admins' => $this->userModel->count('admin')
            ],
            'items' => $this->itemModel->getStats(),
            'matches' => [
                'pending' => $this->matchModel->count('pending'),
                'approved' => $this->matchModel->count('approved'),
                'rejected' => $this->matchModel->count('rejected'),
                'total' => $this->matchModel->count()
            ]
        ];
    }
    
    public function getRecentActivity() {
        requireAdmin();
        
        $recentItems = $this->itemModel->getRecent(5);
        $recentUsers = $this->userModel->getAll(5, 0);
        $pendingMatches = $this->matchModel->getPending();
        
        return [
            'items' => $recentItems,
            'users' => $recentUsers,
            'pending_matches' => count($pendingMatches)
        ];
    }
}
