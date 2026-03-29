<?php
/**
 * User Match Rejection API
 * POST /api/matches/user-reject.php
 * Allows a user to reject an approved match if it's incorrect.
 */

require_once '../../config/constants.php';
require_once '../../config/database.php';
require_once '../../controllers/MatchController.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
}

requireLogin();

$data = json_decode(file_get_contents('php://input'), true);
$matchId = $data['id'] ?? 0;

if (!$matchId) {
    jsonResponse(['success' => false, 'message' => 'Match ID is required'], 400);
}

$matchController = new MatchController();
$result = $matchController->userReject($matchId);

jsonResponse($result);
