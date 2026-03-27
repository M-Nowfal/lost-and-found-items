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
    
    public function createForUser($userId, $message, $type = 'system') {
        return $this->create([
            'user_id' => $userId,
            'message' => $message,
            'type' => $type
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
    
    public function notifyMatchFound($lostUserId, $foundUserId, $lostItemTitle, $foundItemTitle) {
        $this->createForUser(
            $lostUserId,
            "A potential match was found for your lost item: {$lostItemTitle}",
            'match'
        );
        
        $this->createForUser(
            $foundUserId,
            "A potential match was found for the item you found: {$foundItemTitle}",
            'match'
        );
    }
    
    public function notifyMatchApproved($lostUserId, $foundUserId, $lostItemTitle, $foundItemTitle) {
        $this->createForUser(
            $lostUserId,
            "Your lost item '{$lostItemTitle}' has been matched with a found item. Contact the finder to claim it!",
            'match'
        );
        
        $this->createForUser(
            $foundUserId,
            "Your found item '{$foundItemTitle}' has been approved. Contact the owner to return it!",
            'match'
        );
    }
    
    public function notifyUserVerified($userId, $verified = true) {
        if ($verified) {
            $this->createForUser($userId, "Your account has been verified. You can now report and search items!", 'verification');
        } else {
            $this->createForUser($userId, "Your account verification is pending review.", 'verification');
        }
    }
    
    public function notifyItemVerified($userId, $itemTitle, $verified = true) {
        if ($verified) {
            $this->createForUser($userId, "Your item '{$itemTitle}' has been verified and is now visible to others.", 'verification');
        } else {
            $this->createForUser($userId, "Your item '{$itemTitle}' is pending verification by an administrator.", 'verification');
        }
    }
}
