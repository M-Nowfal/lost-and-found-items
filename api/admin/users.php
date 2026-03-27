<?php
/**
 * Admin - Get Users API
 * GET /api/admin/users.php
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
    $users = $adminController->getUsers();
    jsonResponse(['success' => true, 'users' => $users]);
} catch (Exception $e) {
    jsonResponse(['success' => false, 'message' => 'Unauthorized'], 403);
}
