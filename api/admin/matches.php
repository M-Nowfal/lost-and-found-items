<?php
/**
 * Admin - Get Matches API
 * GET /api/admin/matches.php
 */

require_once '../../config/constants.php';
require_once '../../config/database.php';
require_once '../../controllers/MatchController.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
}

$matchController = new MatchController();

try {
    requireAdmin();
    
    $status = $_GET['status'] ?? null;
    $matches = $matchController->getAll(100, 0, $status);
    $stats = $matchController->getStats();
    
    jsonResponse([
        'success' => true,
        'matches' => $matches,
        'stats' => $stats
    ]);
} catch (Exception $e) {
    jsonResponse(['success' => false, 'message' => 'Unauthorized'], 403);
}
