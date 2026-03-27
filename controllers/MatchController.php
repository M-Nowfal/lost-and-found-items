<?php
/**
 * Match Controller
 * Lost & Found Portal
 */

require_once ROOT_PATH . 'config/constants.php';
require_once ROOT_PATH . 'config/database.php';
require_once ROOT_PATH . 'config/mail.php';
require_once ROOT_PATH . 'models/Match.php';
require_once ROOT_PATH . 'models/Item.php';
require_once ROOT_PATH . 'models/Notification.php';

class MatchController {
    private $matchModel;
    private $itemModel;
    private $notificationModel;
    
    public function __construct() {
        $this->matchModel = new MatchModel();
        $this->itemModel = new Item();
        $this->notificationModel = new Notification();
    }
    
    public function create($lostItemId, $foundItemId) {
        requireLogin();
        
        $lostItem = $this->itemModel->findById($lostItemId);
        $foundItem = $this->itemModel->findById($foundItemId);
        
        if (!$lostItem || !$foundItem) {
            return ['success' => false, 'errors' => ['general' => 'Item not found']];
        }
        
        if ($lostItem['type'] !== 'lost' || $foundItem['type'] !== 'found') {
            return ['success' => false, 'errors' => ['general' => 'Invalid item types for matching']];
        }
        
        if ($this->matchModel->exists($lostItemId, $foundItemId)) {
            return ['success' => false, 'errors' => ['general' => 'Match already exists']];
        }
        
        $similarity = $this->matchModel->calculateSimilarity($lostItem, $foundItem);
        
        $matchId = $this->matchModel->create([
            'lost_item_id' => $lostItemId,
            'found_item_id' => $foundItemId,
            'status' => 'pending',
            'similarity_score' => $similarity
        ]);
        
        if ($matchId) {
            $this->notificationModel->notifyMatchFound(
                $lostItem['user_id'],
                $foundItem['user_id'],
                $lostItem['title'],
                $foundItem['title']
            );
            
            return [
                'success' => true,
                'message' => 'Match request created successfully!',
                'match_id' => $matchId
            ];
        }
        
        return ['success' => false, 'errors' => ['general' => 'Failed to create match']];
    }
    
    public function getUserMatches($status = null) {
        requireLogin();
        return $this->matchModel->getByUserId(getCurrentUserId(), $status);
    }
    
    public function getAll($limit = 100, $offset = 0, $status = null) {
        requireAdmin();
        return $this->matchModel->getAll($limit, $offset, $status);
    }
    
    public function getPending() {
        requireAdmin();
        return $this->matchModel->getPending();
    }
    
    public function getById($id) {
        return $this->matchModel->findById($id);
    }
    
    public function approve($id) {
        requireAdmin();
        
        $match = $this->matchModel->findById($id);
        if (!$match) {
            return ['success' => false, 'errors' => ['general' => 'Match not found']];
        }
        
        if ($match['status'] !== 'pending') {
            return ['success' => false, 'errors' => ['general' => 'Match is not pending']];
        }
        
        $this->matchModel->approve($id);
        
        $this->itemModel->updateStatus($match['lost_item_id'], 'matched');
        $this->itemModel->updateStatus($match['found_item_id'], 'matched');
        
        $this->notificationModel->notifyMatchApproved(
            $match['lost_user_id'],
            $match['found_user_id'],
            $match['lost_title'],
            $match['found_title']
        );
        
        try {
            $mailer = getMailer();
            $mailer->sendMatchNotification(
                $match['lost_user_email'],
                $match['lost_user_name'],
                $match['lost_title'],
                'Lost'
            );
            $mailer->sendMatchNotification(
                $match['found_user_email'],
                $match['found_user_name'],
                $match['found_title'],
                'Found'
            );
        } catch (Exception $e) {
            error_log("Email notification failed: " . $e->getMessage());
        }
        
        return ['success' => true, 'message' => 'Match approved successfully!'];
    }
    
    public function reject($id) {
        requireAdmin();
        
        $match = $this->matchModel->findById($id);
        if (!$match) {
            return ['success' => false, 'errors' => ['general' => 'Match not found']];
        }
        
        $this->matchModel->reject($id);
        
        $this->notificationModel->createForUser(
            $match['lost_user_id'],
            "Your potential match for '{$match['lost_title']}' was not approved.",
            'system'
        );
        
        return ['success' => true, 'message' => 'Match rejected'];
    }
    
    public function getStats() {
        return [
            'pending' => $this->matchModel->count('pending'),
            'approved' => $this->matchModel->count('approved'),
            'rejected' => $this->matchModel->count('rejected'),
            'total' => $this->matchModel->count()
        ];
    }
}
