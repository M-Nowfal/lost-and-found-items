<?php
/**
 * Notification Controller
 * Lost & Found Portal
 */

require_once ROOT_PATH . 'config/constants.php';
require_once ROOT_PATH . 'config/database.php';
require_once ROOT_PATH . 'models/Notification.php';

class NotificationController {
    private $notificationModel;
    
    public function __construct() {
        $this->notificationModel = new Notification();
    }
    
    public function getUserNotifications($limit = NOTIFICATIONS_PER_PAGE, $unreadOnly = false) {
        requireLogin();
        return $this->notificationModel->getByUserId(getCurrentUserId(), $limit, $unreadOnly);
    }
    
    public function getUnreadCount() {
        requireLogin();
        return $this->notificationModel->countUnread(getCurrentUserId());
    }
    
    public function markAsRead($notificationId) {
        requireLogin();
        
        $notification = $this->notificationModel->findById($notificationId);
        if (!$notification || $notification['user_id'] !== getCurrentUserId()) {
            return ['success' => false, 'errors' => ['general' => 'Notification not found']];
        }
        
        $this->notificationModel->markAsRead($notificationId);
        
        return ['success' => true, 'message' => 'Notification marked as read'];
    }
    
    public function markAllAsRead() {
        requireLogin();
        $this->notificationModel->markAllAsRead(getCurrentUserId());
        return ['success' => true, 'message' => 'All notifications marked as read'];
    }
    
    public function delete($notificationId) {
        requireLogin();
        
        $notification = $this->notificationModel->findById($notificationId);
        if (!$notification || $notification['user_id'] !== getCurrentUserId()) {
            return ['success' => false, 'errors' => ['general' => 'Notification not found']];
        }
        
        $this->notificationModel->delete($notificationId);
        
        return ['success' => true, 'message' => 'Notification deleted'];
    }
}
