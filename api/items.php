<?php
/**
 * Items API
 * GET /api/items.php - Get items
 * POST /api/items.php - Create item
 */

require_once '../config/constants.php';
require_once '../config/database.php';
require_once '../controllers/ItemController.php';

header('Content-Type: application/json');

$itemController = new ItemController();

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        requireLogin();
        
        $userId = $_GET['user_id'] ?? null;
        $myItems = $_GET['my_items'] ?? false;
        
        if ($myItems && isLoggedIn()) {
            $items = $itemController->getUserItems();
        } else {
            $filters = [];
            if (!empty($_GET['type'])) $filters['type'] = $_GET['type'];
            if (!empty($_GET['category'])) $filters['category'] = $_GET['category'];
            
            $items = $itemController->getAll($filters);
        }
        
        jsonResponse(['success' => true, 'items' => $items]);
        break;
        
    case 'POST':
        requireLogin();
        
        $data = $_POST;
        $result = $itemController->create($data, $_FILES);
        
        jsonResponse($result, $result['success'] ? 201 : 400);
        break;
        
    default:
        jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
}
