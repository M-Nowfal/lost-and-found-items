<?php
/**
 * Item Search API
 * GET /api/search.php
 */

require_once '../config/constants.php';
require_once '../config/database.php';
require_once '../controllers/ItemController.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
}

requireLogin();

$keyword = $_GET['q'] ?? '';
$filters = [];

if (!empty($_GET['type'])) $filters['type'] = $_GET['type'];
if (!empty($_GET['category'])) $filters['category'] = $_GET['category'];
if (!empty($_GET['date_from'])) $filters['date_from'] = $_GET['date_from'];
if (!empty($_GET['date_to'])) $filters['date_to'] = $_GET['date_to'];

if (empty($keyword) && empty($filters)) {
    jsonResponse(['success' => true, 'items' => []]);
}

$itemController = new ItemController();
$items = $itemController->search($keyword, $filters);

jsonResponse(['success' => true, 'items' => $items, 'count' => count($items)]);
