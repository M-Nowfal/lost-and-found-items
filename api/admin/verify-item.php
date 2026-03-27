<?php
/**
 * Admin - Verify Item API
 * PUT /api/admin/verify-item.php
 */

require_once '../../config/constants.php';
require_once '../../config/database.php';
require_once '../../controllers/AdminController.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
}

$adminController = new AdminController();

try {
    requireAdmin();
    
    $data = json_decode(file_get_contents('php://input'), true);
    $itemId = $data['item_id'] ?? 0;
    $verified = $data['verified'] ?? true;
    
    $result = $adminController->verifyItem($itemId, $verified);
    jsonResponse($result, $result['success'] ? 200 : 400);
} catch (Exception $e) {
    jsonResponse(['success' => false, 'message' => 'Unauthorized'], 403);
}
