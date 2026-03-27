<?php
/**
 * Admin - Get Items API
 * GET /api/admin/items.php
 */

require_once '../../config/constants.php';
require_once '../../config/database.php';
require_once '../../controllers/AdminController.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
}

$adminController = new AdminController();

try {
    requireAdmin();
    
    $filters = [];
    if (!empty($_GET['type'])) $filters['type'] = $_GET['type'];
    if (!empty($_GET['status'])) $filters['status'] = $_GET['status'];
    if (isset($_GET['verified'])) $filters['verified'] = $_GET['verified'] === 'true';
    
    $items = $adminController->getItems(100, 0, $filters);
    jsonResponse(['success' => true, 'items' => $items]);
} catch (Exception $e) {
    jsonResponse(['success' => false, 'message' => 'Unauthorized'], 403);
}
