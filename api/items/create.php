<?php
/**
 * Create Item API
 * POST /api/items/create.php
 */

require_once '../../config/constants.php';
require_once '../../config/database.php';
require_once '../../controllers/ItemController.php';

header('Content-Type: application/json');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
}

// Require login for item creation
requireLogin();

$itemController = new ItemController();

// Use $_POST for text data and $_FILES for image uploads
$data = $_POST;
$result = $itemController->create($data, $_FILES);

// Return response or redirect
if ($result['success']) {
    // Check if it's an AJAX request
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        jsonResponse($result, 201);
    } else {
        // Normal form submission - redirect to dashboard with success message
        $type = $_POST['type'] ?? 'lost';
        $_SESSION['success_msg'] = "Your " . $type . " item has been reported successfully! Our AI is now looking for matches.";
        redirect(BASE_URL . 'views/user/dashboard.php?success=1');
    }
} else {
    jsonResponse($result, 400);
}
