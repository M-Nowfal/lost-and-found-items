<?php
/**
 * Notifications API
 * GET /api/notifications.php - Get notifications
 * POST /api/notifications.php - Mark as read
 */

require_once '../config/constants.php';
require_once '../config/database.php';
require_once '../controllers/NotificationController.php';

header('Content-Type: application/json');

$notificationController = new NotificationController();

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        requireLogin();
        
        $unreadOnly = isset($_GET['unread_only']) && $_GET['unread_only'] === 'true';
        $notifications = $notificationController->getUserNotifications(50, $unreadOnly);
        $unreadCount = $notificationController->getUnreadCount();
        
        jsonResponse([
            'success' => true,
            'notifications' => $notifications,
            'unread_count' => $unreadCount
        ]);
        break;
        
    case 'POST':
        requireLogin();
        
        $data = json_decode(file_get_contents('php://input'), true);
        $action = $data['action'] ?? '';
        
        if ($action === 'mark_all_read') {
            $result = $notificationController->markAllAsRead();
        } else {
            $result = $notificationController->markAsRead($data['id'] ?? 0);
        }
        
        jsonResponse($result);
        break;
        
    default:
        jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
}
