<?php
/**
 * Item Model
 * Lost & Found Portal
 */

class Item {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    public function create($data) {
        return $this->db->insert('items', $data);
    }
    
    public function findById($id) {
        $sql = "SELECT i.*, u.name as user_name, u.email as user_email 
                FROM items i 
                JOIN users u ON i.user_id = u.id 
                WHERE i.id = :id";
        return $this->db->fetchOne($sql, ['id' => $id]);
    }
    
    public function findByUserId($userId, $type = null) {
        if ($type) {
            return $this->db->fetchAll(
                "SELECT * FROM items WHERE user_id = :user_id AND type = :type ORDER BY created_at DESC",
                ['user_id' => $userId, 'type' => $type]
            );
        }
        return $this->db->fetchAll(
            "SELECT * FROM items WHERE user_id = :user_id ORDER BY created_at DESC",
            ['user_id' => $userId]
        );
    }
    
    public function update($id, $data) {
        $this->db->update('items', $data, 'id = :id', ['id' => $id]);
    }
    
    public function delete($id) {
        $this->db->delete('items', 'id = :id', ['id' => $id]);
    }
    
    public function verify($id, $verified = true) {
        $this->db->update('items', ['verified' => $verified], 'id = :id', ['id' => $id]);
    }
    
    public function updateStatus($id, $status) {
        $this->db->update('items', ['status' => $status], 'id = :id', ['id' => $id]);
    }
    
    public function getAll($limit = 100, $offset = 0, $filters = []) {
        $where = [];
        $params = [];
        
        if (!empty($filters['type'])) {
            $where[] = "i.type = :type";
            $params['type'] = $filters['type'];
        }
        
        if (!empty($filters['status'])) {
            $where[] = "i.status = :status";
            $params['status'] = $filters['status'];
        }
        
        if (!empty($filters['category'])) {
            $where[] = "i.category = :category";
            $params['category'] = $filters['category'];
        }
        
        if (!empty($filters['verified'])) {
            $where[] = "i.verified = :verified";
            $params['verified'] = $filters['verified'];
        }
        
        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
        
        $sql = "SELECT i.*, u.name as user_name, u.email as user_email 
                FROM items i 
                JOIN users u ON i.user_id = u.id 
                {$whereClause}
                ORDER BY i.created_at DESC 
                LIMIT :limit OFFSET :offset";
        
        $params['limit'] = $limit;
        $params['offset'] = $offset;
        
        return $this->db->fetchAll($sql, $params);
    }
    
    public function search($keyword, $filters = []) {
        $params = ['keyword' => '%' . $keyword . '%'];
        $where = "(i.title LIKE :keyword OR i.description LIKE :keyword OR i.location LIKE :keyword)";
        
        if (!empty($filters['type'])) {
            $where .= " AND i.type = :type";
            $params['type'] = $filters['type'];
        }
        
        if (!empty($filters['category'])) {
            $where .= " AND i.category = :category";
            $params['category'] = $filters['category'];
        }
        
        if (!empty($filters['date_from'])) {
            $where .= " AND i.date >= :date_from";
            $params['date_from'] = $filters['date_from'];
        }
        
        if (!empty($filters['date_to'])) {
            $where .= " AND i.date <= :date_to";
            $params['date_to'] = $filters['date_to'];
        }
        
        $sql = "SELECT i.*, u.name as user_name, u.email as user_email 
                FROM items i 
                JOIN users u ON i.user_id = u.id 
                WHERE {$where} AND i.verified = 1 AND i.status != 'claimed'
                ORDER BY i.created_at DESC";
        
        return $this->db->fetchAll($sql, $params);
    }
    
    public function findPotentialMatches($itemId) {
        $item = $this->findById($itemId);
        if (!$item) return [];
        
        $oppositeType = $item['type'] === 'lost' ? 'found' : 'lost';
        
        $sql = "SELECT i.*, u.name as user_name, 
                (CASE WHEN i.category = :category THEN 30 ELSE 0 END +
                 CASE WHEN i.location = :location THEN 40 ELSE 0 END +
                 CASE WHEN i.date BETWEEN DATE_SUB(:date1, INTERVAL 3 DAY) AND DATE_ADD(:date2, INTERVAL 3 DAY) THEN 30 ELSE 0 END) as similarity_score
                FROM items i 
                JOIN users u ON i.user_id = u.id 
                WHERE i.type = :opposite_type 
                AND i.category = :category2 
                AND i.verified = 1 
                AND i.status = 'pending'
                HAVING similarity_score >= :threshold
                ORDER BY similarity_score DESC";
        
        return $this->db->fetchAll($sql, [
            'category' => $item['category'],
            'category2' => $item['category'],
            'location' => $item['location'],
            'date1' => $item['date'],
            'date2' => $item['date'],
            'opposite_type' => $oppositeType,
            'threshold' => MATCH_SIMILARITY_THRESHOLD
        ]);
    }
    
    public function count($filters = []) {
        $where = [];
        $params = [];
        
        if (!empty($filters['type'])) {
            $where[] = "type = :type";
            $params['type'] = $filters['type'];
        }
        
        if (!empty($filters['status'])) {
            $where[] = "status = :status";
            $params['status'] = $filters['status'];
        }
        
        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
        
        $result = $this->db->fetchOne("SELECT COUNT(*) as count FROM items {$whereClause}", $params);
        return $result['count'];
    }
    
    public function getRecent($limit = 10) {
        return $this->db->fetchAll(
            "SELECT i.*, u.name as user_name 
             FROM items i 
             JOIN users u ON i.user_id = u.id 
             WHERE i.verified = 1 
             ORDER BY i.created_at DESC 
             LIMIT :limit",
            ['limit' => $limit]
        );
    }
    
    public function getStats() {
        $lost = $this->db->fetchOne("SELECT COUNT(*) as count FROM items WHERE type = 'lost'")['count'];
        $found = $this->db->fetchOne("SELECT COUNT(*) as count FROM items WHERE type = 'found'")['count'];
        $matched = $this->db->fetchOne("SELECT COUNT(*) as count FROM items WHERE status = 'matched'")['count'];
        $pending = $this->db->fetchOne("SELECT COUNT(*) as count FROM items WHERE status = 'pending'")['count'];
        
        return [
            'lost' => $lost,
            'found' => $found,
            'matched' => $matched,
            'pending' => $pending,
            'total' => $lost + $found
        ];
    }
}
