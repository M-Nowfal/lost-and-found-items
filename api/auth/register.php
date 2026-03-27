<?php
/**
 * User Registration API
 * POST /api/auth/register.php
 */

require_once '../../config/constants.php';
require_once '../../config/database.php';
require_once '../../controllers/AuthController.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
}

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    $data = $_POST;
}

$auth = new AuthController();
$result = $auth->register($data);

jsonResponse($result, $result['success'] ? 201 : 400);
