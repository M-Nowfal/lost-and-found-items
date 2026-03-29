<?php
/**
 * Admin - Fix Notifications Script
 * POST /api/admin/fix-notifications.php
 * Generates missing notifications for previously approved matches.
 */

require_once '../../config/constants.php';
require_once '../../config/database.php';
require_once '../../models/Match.php';
require_once '../../models/Notification.php';

header('Content-Type: application/json');

try {
    requireAdmin();
    
    $db = getDB();
    $matchModel = new MatchModel();
    $notificationModel = new Notification();
    
    // Find all approved matches
    $matches = $matchModel->getAll(500, 0, 'approved');
    $fixedCount = 0;
    
    foreach ($matches as $match) {
        $lostUserId = $match['lost_user_id'];
        $foundUserId = $match['found_user_id'];
        
        // Check if lost user has an approval notification for this item
        $lostNotifExists = $db->fetchOne(
            "SELECT id FROM notifications WHERE user_id = :uid AND message LIKE :msg",
            ['uid' => $lostUserId, 'msg' => "%{$match['lost_title']}%matched%"]
        );
        
        // Check if found user has an approval notification for this item
        $foundNotifExists = $db->fetchOne(
            "SELECT id FROM notifications WHERE user_id = :uid AND message LIKE :msg",
            ['uid' => $foundUserId, 'msg' => "%{$match['found_title']}%approved%"]
        );

        // Find if any link is missing for existing notifications
        $missingLinkNotif = $db->fetchOne(
            "SELECT id FROM notifications WHERE user_id IN (:u1, :u2) AND (message LIKE :m1 OR message LIKE :m2) AND link IS NULL",
            ['u1' => $lostUserId, 'u2' => $foundUserId, 'm1' => "%{$match['lost_title']}%matched%", 'm2' => "%{$match['found_title']}%approved%"]
        );
        
        if (!$lostNotifExists || !$foundNotifExists || $missingLinkNotif) {
            $notificationModel->notifyMatchApproved(
                $lostUserId,
                $foundUserId,
                $match['lost_title'],
                $match['found_title'],
                [
                    'name' => $match['lost_user_name'],
                    'email' => $match['lost_user_email'],
                    'phone' => $match['lost_user_phone'] ?? null
                ],
                [
                    'name' => $match['found_user_name'],
                    'email' => $match['found_user_email'],
                    'phone' => $match['found_user_phone'] ?? null
                ],
                $match['lost_item_id'],
                $match['found_item_id']
            );
            $fixedCount++;
        }
    }
    
    jsonResponse([
        'success' => true, 
        'message' => "Successfully synchronized notifications for {$fixedCount} matches.",
        'total_scanned' => count($matches)
    ]);
    
} catch (Exception $e) {
    jsonResponse(['success' => false, 'message' => $e->getMessage()], 403);
}
