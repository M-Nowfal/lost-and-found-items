<?php
/**
 * Matches API
 * GET /api/matches.php - Get matches
 * POST /api/matches.php - Create match
 */

require_once '../config/constants.php';
require_once '../config/database.php';
require_once '../controllers/MatchController.php';

header('Content-Type: application/json');

$matchController = new MatchController();

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        requireLogin();
        
        $status = $_GET['status'] ?? null;
        $matches = $matchController->getUserMatches($status);
        
        jsonResponse(['success' => true, 'matches' => $matches]);
        break;
        
    case 'POST':
        requireLogin();
        
        $data = json_decode(file_get_contents('php://input'), true);
        $result = $matchController->create($data['lost_item_id'] ?? 0, $data['found_item_id'] ?? 0);
        
        jsonResponse($result, $result['success'] ? 201 : 400);
        break;
        
    default:
        jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
}
