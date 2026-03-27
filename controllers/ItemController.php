<?php
/**
 * Item Controller
 * Lost & Found Portal
 */

require_once ROOT_PATH . 'config/constants.php';
require_once ROOT_PATH . 'config/database.php';
require_once ROOT_PATH . 'models/Item.php';
require_once ROOT_PATH . 'models/Match.php';
require_once ROOT_PATH . 'models/Notification.php';

class ItemController {
    private $itemModel;
    private $matchModel;
    private $notificationModel;
    
    public function __construct() {
        $this->itemModel = new Item();
        $this->matchModel = new MatchModel();
        $this->notificationModel = new Notification();
    }
    
    public function create($data, $files = null) {
        $errors = $this->validateItem($data);
        
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }
        
        $imagePath = null;
        if ($files && isset($files['image']) && $files['image']['error'] === UPLOAD_ERR_OK) {
            $imagePath = $this->uploadImage($files['image']);
            if (!$imagePath) {
                return ['success' => false, 'errors' => ['image' => 'Failed to upload image']];
            }
        }
        
        $itemId = $this->itemModel->create([
            'title' => sanitize($data['title']),
            'description' => sanitize($data['description'] ?? ''),
            'category' => sanitize($data['category']),
            'location' => sanitize($data['location']),
            'date' => $data['date'],
            'type' => $data['type'],
            'status' => 'pending',
            'user_id' => getCurrentUserId(),
            'image_path' => $imagePath,
            'verified' => false
        ]);
        
        if ($itemId) {
            $this->notificationModel->createForUser(
                getCurrentUserId(),
                "Your " . $data['type'] . " item '{$data['title']}' has been reported. It's pending verification.",
                'system'
            );
            
            $potentialMatches = $this->findAndCreateMatches($itemId);
            
            return [
                'success' => true,
                'message' => 'Item reported successfully!',
                'item_id' => $itemId,
                'potential_matches' => $potentialMatches
            ];
        }
        
        return ['success' => false, 'errors' => ['general' => 'Failed to create item']];
    }
    
    public function getById($id) {
        return $this->itemModel->findById($id);
    }
    
    public function getUserItems($userId = null, $type = null) {
        $userId = $userId ?? getCurrentUserId();
        return $this->itemModel->findByUserId($userId, $type);
    }
    
    public function getAll($filters = [], $limit = ITEMS_PER_PAGE, $offset = 0) {
        $filters['verified'] = 1;
        return $this->itemModel->getAll($limit, $offset, $filters);
    }
    
    public function search($keyword, $filters = []) {
        return $this->itemModel->search($keyword, $filters);
    }
    
    public function update($id, $data) {
        $item = $this->itemModel->findById($id);
        
        if (!$item) {
            return ['success' => false, 'errors' => ['general' => 'Item not found']];
        }
        
        if ($item['user_id'] !== getCurrentUserId() && !isAdmin()) {
            return ['success' => false, 'errors' => ['general' => 'Unauthorized']];
        }
        
        $errors = $this->validateItem($data);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }
        
        $this->itemModel->update($id, [
            'title' => sanitize($data['title']),
            'description' => sanitize($data['description'] ?? ''),
            'category' => sanitize($data['category']),
            'location' => sanitize($data['location']),
            'date' => $data['date']
        ]);
        
        return ['success' => true, 'message' => 'Item updated successfully'];
    }
    
    public function delete($id) {
        $item = $this->itemModel->findById($id);
        
        if (!$item) {
            return ['success' => false, 'errors' => ['general' => 'Item not found']];
        }
        
        if ($item['user_id'] !== getCurrentUserId() && !isAdmin()) {
            return ['success' => false, 'errors' => ['general' => 'Unauthorized']];
        }
        
        if ($item['image_path'] && file_exists(ROOT_PATH . $item['image_path'])) {
            unlink(ROOT_PATH . $item['image_path']);
        }
        
        $this->itemModel->delete($id);
        
        return ['success' => true, 'message' => 'Item deleted successfully'];
    }
    
    public function getStats() {
        return $this->itemModel->getStats();
    }
    
    public function getRecent($limit = 10) {
        return $this->itemModel->getRecent($limit);
    }
    
    public function findPotentialMatches($itemId) {
        return $this->itemModel->findPotentialMatches($itemId);
    }
    
    private function findAndCreateMatches($itemId) {
        $potentialMatches = $this->itemModel->findPotentialMatches($itemId);
        $matchesCreated = 0;
        
        foreach ($potentialMatches as $match) {
            $lostId = $itemId;
            $foundId = $match['id'];
            
            if ($match['type'] === 'lost') {
                $lostId = $match['id'];
                $foundId = $itemId;
            }
            
            if (!$this->matchModel->exists($lostId, $foundId)) {
                $lostItem = $this->itemModel->findById($lostId);
                $foundItem = $this->itemModel->findById($foundId);
                
                $similarity = $this->matchModel->calculateSimilarity($lostItem, $foundItem);
                
                $this->matchModel->create([
                    'lost_item_id' => $lostId,
                    'found_item_id' => $foundId,
                    'status' => 'pending',
                    'similarity_score' => $similarity
                ]);
                
                $matchesCreated++;
                
                $this->notificationModel->notifyMatchFound(
                    $lostItem['user_id'],
                    $foundItem['user_id'],
                    $lostItem['title'],
                    $foundItem['title']
                );
            }
        }
        
        return $matchesCreated;
    }
    
    private function uploadImage($file) {
        $allowedTypes = UPLOAD_ALLOWED_TYPES;
        $allowedExts = UPLOAD_ALLOWED_EXTENSIONS;
        
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        if (!in_array($mimeType, $allowedTypes) || !in_array($ext, $allowedExts)) {
            return false;
        }
        
        if ($file['size'] > UPLOAD_MAX_SIZE) {
            return false;
        }
        
        $uploadDir = UPLOAD_PATH . 'items/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $filename = time() . '_' . uniqid() . '.' . $ext;
        $destination = $uploadDir . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return 'uploads/items/' . $filename;
        }
        
        return false;
    }
    
    private function validateItem($data) {
        $errors = [];
        
        if (empty($data['title'])) {
            $errors['title'] = 'Title is required';
        } elseif (strlen($data['title']) < 3) {
            $errors['title'] = 'Title must be at least 3 characters';
        }
        
        if (empty($data['category'])) {
            $errors['category'] = 'Category is required';
        }
        
        if (empty($data['location'])) {
            $errors['location'] = 'Location is required';
        }
        
        if (empty($data['date'])) {
            $errors['date'] = 'Date is required';
        } elseif (strtotime($data['date']) > time()) {
            $errors['date'] = 'Date cannot be in the future';
        }
        
        if (!in_array($data['type'], ['lost', 'found'])) {
            $errors['type'] = 'Invalid item type';
        }
        
        return $errors;
    }
}
