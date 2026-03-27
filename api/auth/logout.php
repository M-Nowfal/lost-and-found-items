<?php
/**
 * User Logout API
 * POST /api/auth/logout.php
 */

require_once '../../config/constants.php';
require_once '../../config/database.php';
require_once '../../controllers/AuthController.php';

header('Content-Type: application/json');

$auth = new AuthController();
$result = $auth->logout();

jsonResponse($result);
