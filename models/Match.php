<?php
/**
 * Match Model
 * Lost & Found Portal
 */

class MatchModel {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    public function create($data) {
        return $this->db->insert('`matches`', $data);
    }
    
    public function findById($id) {
        $sql = "SELECT m.*, 
                l.title as lost_title, l.description as lost_description, l.location as lost_location, l.date as lost_date,
                f.title as found_title, f.description as found_description, f.location as found_location, f.date as found_date,
                lu.name as lost_user_name, lu.email as lost_user_email,
                fu.name as found_user_name, fu.email as found_user_email
                FROM `matches` m
                JOIN items l ON m.lost_item_id = l.id
                JOIN items f ON m.found_item_id = f.id
                JOIN users lu ON l.user_id = lu.id
                JOIN users fu ON f.user_id = fu.id
                WHERE m.id = :id";
        return $this->db->fetchOne($sql, ['id' => $id]);
    }
    
    public function getByUserId($userId, $status = null) {
        $sql = "SELECT m.*, 
                l.title as lost_title, l.description as lost_description, l.location as lost_location, l.image_path as lost_image,
                f.title as found_title, f.description as found_description, f.location as found_location, f.image_path as found_image
                FROM `matches` m
                JOIN items l ON m.lost_item_id = l.id
                JOIN items f ON m.found_item_id = f.id
                WHERE (l.user_id = :user_id1 OR f.user_id = :user_id2)";
        
        $params = ['user_id1' => $userId, 'user_id2' => $userId];
        
        if ($status) {
            $sql .= " AND m.status = :status";
            $params['status'] = $status;
        }
        
        $sql .= " ORDER BY m.created_at DESC";
        
        return $this->db->fetchAll($sql, $params);
    }
    
    public function getAll($limit = 100, $offset = 0, $status = null) {
        $sql = "SELECT m.*, 
                l.title as lost_title, l.description as lost_description, l.location as lost_location, l.image_path as lost_image,
                f.title as found_title, f.description as found_description, f.location as found_location, f.image_path as found_image,
                lu.name as lost_user_name, lu.email as lost_user_email, lu.id as lost_user_id,
                fu.name as found_user_name, fu.email as found_user_email, fu.id as found_user_id
                FROM `matches` m
                JOIN items l ON m.lost_item_id = l.id
                JOIN items f ON m.found_item_id = f.id
                JOIN users lu ON l.user_id = lu.id
                JOIN users fu ON f.user_id = fu.id";
        
        $params = [];
        
        if ($status) {
            $sql .= " WHERE m.status = :status";
            $params['status'] = $status;
        }
        
        $sql .= " ORDER BY m.created_at DESC LIMIT :limit OFFSET :offset";
        $params['limit'] = $limit;
        $params['offset'] = $offset;
        
        return $this->db->fetchAll($sql, $params);
    }
    
    public function updateStatus($id, $status) {
        $this->db->update('matches', ['status' => $status], 'id = :id', ['id' => $id]);
    }
    
    public function approve($id) {
        $match = $this->findById($id);
        if (!$match) return false;
        
        $this->db->update('matches', ['status' => 'approved'], 'id = :id', ['id' => $id]);
        
        $itemModel = new Item();
        $itemModel->updateStatus($match['lost_item_id'], 'matched');
        $itemModel->updateStatus($match['found_item_id'], 'matched');
        
        return true;
    }
    
    public function reject($id) {
        $this->updateStatus($id, 'rejected');
    }
    
    public function exists($lostItemId, $foundItemId) {
        $result = $this->db->fetchOne(
            "SELECT COUNT(*) as count FROM matches WHERE (lost_item_id = :li AND found_item_id = :fi) OR (lost_item_id = :fi2 AND found_item_id = :li2)",
            ['li' => $lostItemId, 'fi' => $foundItemId, 'li2' => $lostItemId, 'fi2' => $foundItemId]
        );
        return $result['count'] > 0;
    }
    
    public function count($status = null) {
        if ($status) {
            return $this->db->fetchOne("SELECT COUNT(*) as count FROM `matches` WHERE status = :status", ['status' => $status])['count'];
        }
        return $this->db->fetchOne("SELECT COUNT(*) as count FROM `matches`")['count'];
    }
    
    public function getPending() {
        return $this->getAll(100, 0, 'pending');
    }
    
    public function calculateSimilarity($lostItem, $foundItem) {
        $score = 0;
        
        if ($lostItem['category'] === $foundItem['category']) {
            $score += 30;
        }
        
        similar_text(strtolower($lostItem['location']), strtolower($foundItem['location']), $locationSimilarity);
        if ($locationSimilarity > 70) {
            $score += 40;
        } elseif ($locationSimilarity > 50) {
            $score += 20;
        }
        
        $dateDiff = abs(strtotime($lostItem['date']) - strtotime($foundItem['date'])) / (60 * 60 * 24);
        if ($dateDiff <= 1) {
            $score += 30;
        } elseif ($dateDiff <= 3) {
            $score += 15;
        } elseif ($dateDiff <= 7) {
            $score += 5;
        }
        
        if (!empty($lostItem['description']) && !empty($foundItem['description'])) {
            similar_text(strtolower($lostItem['description']), strtolower($foundItem['description']), $descSimilarity);
            if ($descSimilarity > 50) {
                $score += 20;
            } elseif ($descSimilarity > 30) {
                $score += 10;
            }
        }
        
        similar_text(strtolower($lostItem['title']), strtolower($foundItem['title']), $titleSimilarity);
        if ($titleSimilarity > 60) {
            $score += 20;
        } elseif ($titleSimilarity > 40) {
            $score += 10;
        }
        
        return min(100, $score);
    }
}
