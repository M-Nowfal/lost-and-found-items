<?php
/**
 * User Model
 * Lost & Found Portal
 */

class User {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    public function create($data) {
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        return $this->db->insert('users', $data);
    }
    
    public function findById($id) {
        return $this->db->fetchOne("SELECT * FROM users WHERE id = :id", ['id' => $id]);
    }
    
    public function findByEmail($email) {
        return $this->db->fetchOne("SELECT * FROM users WHERE email = :email", ['email' => $email]);
    }
    
    public function findByEmailAndPassword($email, $password) {
        $user = $this->findByEmail($email);
        
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        
        return null;
    }
    
    public function update($id, $data) {
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        $this->db->update('users', $data, 'id = :id', ['id' => $id]);
    }
    
    public function verify($id, $verified = true) {
        $this->db->update('users', ['verified' => $verified], 'id = :id', ['id' => $id]);
    }
    
    public function delete($id) {
        $this->db->delete('users', 'id = :id', ['id' => $id]);
    }
    
    public function getAll($limit = 100, $offset = 0) {
        return $this->db->fetchAll(
            "SELECT id, name, email, role, verified, created_at FROM users ORDER BY created_at DESC LIMIT :limit OFFSET :offset",
            ['limit' => $limit, 'offset' => $offset]
        );
    }
    
    public function count($role = null) {
        if ($role) {
            return $this->db->fetchOne("SELECT COUNT(*) as count FROM users WHERE role = :role", ['role' => $role])['count'];
        }
        return $this->db->fetchOne("SELECT COUNT(*) as count FROM users")['count'];
    }
    
    public function getUnverified() {
        return $this->db->fetchAll("SELECT * FROM users WHERE verified = 0 AND role = 'user'");
    }
    
    public function emailExists($email) {
        $result = $this->db->fetchOne("SELECT COUNT(*) as count FROM users WHERE email = :email", ['email' => $email]);
        return $result['count'] > 0;
    }
}
