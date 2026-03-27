<?php
/**
 * Update Item API
 * POST /api/items/update.php
 */

require_once '../../config/constants.php';
require_once '../../config/database.php';
require_once '../../controllers/ItemController.php';

header('Content-Type: application/json');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
}

// Require login
requireLogin();

$id = $_POST['id'] ?? null;
if (!$id) {
    jsonResponse(['success' => false, 'message' => 'Item ID is required'], 400);
}

$itemController = new ItemController();
$data = $_POST;
$result = $itemController->update($id, $data);

// Return response or redirect
if ($result['success']) {
    // Check if it's an AJAX request
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        jsonResponse($result, 200);
    } else {
        // Normal form submission - redirect with success message
        $_SESSION['success_msg'] = "Your item has been updated successfully!";
        redirect(BASE_URL . 'views/user/dashboard.php?success=1');
    }
} else {
    jsonResponse($result, 400);
}
