<?php

/**
 * Logout endpoint - destroy PHP session and return JSON
 */
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../utils/response.php';

set_json_headers();

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if (session_status() === PHP_SESSION_NONE) session_start();

// Determine role-key to unset
$role = $_SESSION['user_role'] ?? null;
if ($role) {
    $roleKey = preg_replace('/[^a-z0-9_]/', '', strtolower($role)) . '_id';
    if (isset($_SESSION[$roleKey])) unset($_SESSION[$roleKey]);
}

// Unset generic keys too
if (isset($_SESSION['user_id'])) unset($_SESSION['user_id']);
if (isset($_SESSION['user_role'])) unset($_SESSION['user_role']);

// Clear session array and destroy
$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'] ?? '', $params['secure'] ?? false, $params['httponly'] ?? true);
}
session_destroy();

success_response('Logged out', null);
