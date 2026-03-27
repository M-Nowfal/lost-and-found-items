<?php
/**
 * Admin - Dashboard Stats API
 * GET /api/admin/dashboard.php
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
    
    $stats = $adminController->getDashboardStats();
    $activity = $adminController->getRecentActivity();
    
    jsonResponse([
        'success' => true,
        'stats' => $stats,
        'activity' => $activity
    ]);
} catch (Exception $e) {
    jsonResponse(['success' => false, 'message' => 'Unauthorized'], 403);
}
