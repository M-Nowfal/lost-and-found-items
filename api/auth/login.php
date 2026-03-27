<?php
/**
 * User Login API
 * POST /api/auth/login.php
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
$result = $auth->login($data['email'] ?? '', $data['password'] ?? '');

if ($result['success']) {
    jsonResponse([
        'success' => true,
        'message' => 'Login successful',
        'user' => $result['user'],
        'redirect' => $result['user']['role'] === 'admin' ? 'admin/dashboard.php' : 'user/dashboard.php'
    ]);
} else {
    jsonResponse($result, 401);
}
