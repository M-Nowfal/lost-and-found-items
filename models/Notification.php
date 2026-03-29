<?php
/**
 * Notification Model
 * Lost & Found Portal
 */

class Notification {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    public function create($data) {
        return $this->db->insert('notifications', $data);
    }
    
    public function createForUser($userId, $message, $type = 'system', $link = null) {
        return $this->create([
            'user_id' => $userId,
            'message' => $message,
            'type' => $type,
            'link' => $link
        ]);
    }
    
    public function findById($id) {
        return $this->db->fetchOne("SELECT * FROM notifications WHERE id = :id", ['id' => $id]);
    }
    
    public function getByUserId($userId, $limit = 50, $unreadOnly = false) {
        $sql = "SELECT * FROM notifications WHERE user_id = :user_id";
        
        if ($unreadOnly) {
            $sql .= " AND `read` = 0";
        }
        
        $sql .= " ORDER BY created_at DESC LIMIT :limit";
        
        return $this->db->fetchAll($sql, ['user_id' => $userId, 'limit' => $limit]);
    }
    
    public function markAsRead($id) {
        $this->db->query(
            "UPDATE notifications SET `read` = 1 WHERE id = :id",
            ['id' => $id]
        );
    }
    
    public function markAllAsRead($userId) {
        $this->db->query(
            "UPDATE notifications SET `read` = 1 WHERE user_id = :user_id AND `read` = 0",
            ['user_id' => $userId]
        );
    }
    
    public function delete($id) {
        $this->db->delete('notifications', 'id = :id', ['id' => $id]);
    }
    
    public function deleteOld($days = 30) {
        $this->db->query(
            "DELETE FROM notifications WHERE created_at < DATE_SUB(NOW(), INTERVAL :days DAY) AND `read` = 1",
            ['days' => $days]
        );
    }
    
    public function countUnread($userId) {
        $result = $this->db->fetchOne(
            "SELECT COUNT(*) as count FROM notifications WHERE user_id = :user_id AND `read` = 0",
            ['user_id' => $userId]
        );
        return $result['count'];
    }
    
    public function notifyMatchFound($lostUserId, $foundUserId, $lostItemTitle, $foundItemTitle, $lostItemId, $foundItemId) {
        $this->createForUser(
            $lostUserId,
            "A potential match was found for your lost item: {$lostItemTitle}",
            'match',
            "item-detail.php?id={$lostItemId}"
        );
        
        $this->createForUser(
            $foundUserId,
            "A potential match was found for the item you found: {$foundItemTitle}",
            'match',
            "item-detail.php?id={$foundItemId}"
        );
    }
    
    public function notifyMatchApproved($lostUserId, $foundUserId, $lostItemTitle, $foundItemTitle, $lostUserContact = [], $foundUserContact = [], $lostItemId = null, $foundItemId = null) {
        $lostMsg = "Your lost item '{$lostItemTitle}' has been matched! Reach out to the finder: ";
        $lostMsg .= "{$foundUserContact['name']} ({$foundUserContact['email']}" . (!empty($foundUserContact['phone']) ? ", {$foundUserContact['phone']}" : "") . ")";
        
        $this->createForUser($lostUserId, $lostMsg, 'match', "item-detail.php?id={$lostItemId}");
        
        $foundMsg = "Your found item '{$foundItemTitle}' has been approved! Reach out to the owner: ";
        $foundMsg .= "{$lostUserContact['name']} ({$lostUserContact['email']}" . (!empty($lostUserContact['phone']) ? ", {$lostUserContact['phone']}" : "") . ")";
        
        $this->createForUser($foundUserId, $foundMsg, 'match', "item-detail.php?id={$foundItemId}");
    }
    
    public function notifyUserVerified($userId, $verified = true) {
        if ($verified) {
            $this->createForUser($userId, "Your account has been verified. You can now report and search items!", 'verification');
        } else {
            $this->createForUser($userId, "Your account verification is pending review.", 'verification');
        }
    }
    
    public function notifyItemVerified($userId, $itemTitle, $itemId, $verified = true) {
        if ($verified) {
            $this->createForUser($userId, "Your item '{$itemTitle}' has been verified and is now visible to others.", 'verification', "item-detail.php?id={$itemId}");
        } else {
            $this->createForUser($userId, "Your item '{$itemTitle}' is pending verification by an administrator.", 'verification', "item-detail.php?id={$itemId}");
        }
    }
}
