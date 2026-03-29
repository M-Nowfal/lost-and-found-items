<?php
/**
 * Admin - DB Sync Script
 * GET /api/admin/db-sync.php
 * Automatically adds missing columns to the database.
 */

require_once '../../config/constants.php';
require_once '../../config/database.php';

header('Content-Type: application/json');

try {
    requireAdmin();
    
    $db = getDB();
    $messages = [];
    
    // Check for "phone" in users
    $userCols = $db->fetchAll("SHOW COLUMNS FROM users LIKE 'phone'");
    if (empty($userCols)) {
        $db->query("ALTER TABLE users ADD COLUMN phone VARCHAR(20) AFTER password");
        $messages[] = "Added 'phone' column to users table.";
    } else {
        $messages[] = "'phone' column already exists in users table.";
    }
    
    // Check for "link" in notifications
    $notifCols = $db->fetchAll("SHOW COLUMNS FROM notifications LIKE 'link'");
    if (empty($notifCols)) {
        $db->query("ALTER TABLE notifications ADD COLUMN link VARCHAR(255) AFTER `read` ");
        $messages[] = "Added 'link' column to notifications table.";
    } else {
        $messages[] = "'link' column already exists in notifications table.";
    }
    
    jsonResponse([
        'success' => true,
        'message' => "Database synchronized successfully.",
        'details' => $messages
    ]);
    
} catch (Exception $e) {
    jsonResponse(['success' => false, 'message' => $e->getMessage()], 403);
}
