<?php
/**
 * Admin - Approve Match API
 * POST /api/admin/approve-match.php
 */

require_once '../../config/constants.php';
require_once '../../config/database.php';
require_once '../../controllers/MatchController.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
}

$matchController = new MatchController();

try {
    requireAdmin();
    
    $data = json_decode(file_get_contents('php://input'), true);
    $matchId = $data['match_id'] ?? 0;
    
    $result = $matchController->approve($matchId);
    jsonResponse($result, $result['success'] ? 200 : 400);
} catch (Exception $e) {
    jsonResponse(['success' => false, 'message' => 'Unauthorized'], 403);
}
