<?php

/**
 * Return currently authenticated user based on PHP session
 * GET /backend/api/auth/me.php
 * Returns { success: true, data: { id, full_name, email, role } }
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../utils/response.php';

set_json_headers();

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if (session_status() === PHP_SESSION_NONE) session_start();

// Determine role and role-specific id key
$role = $_SESSION['user_role'] ?? null;
$userId = null;
if ($role) {
    $roleKey = preg_replace('/[^a-z0-9_]/', '', strtolower($role)) . '_id';
    if (!empty($_SESSION[$roleKey])) {
        $userId = (int)$_SESSION[$roleKey];
    }
}

// Fallback: some setups may still set 'user_id'
if (!$userId && !empty($_SESSION['user_id'])) {
    $userId = (int)$_SESSION['user_id'];
}

if (!$userId) {
    error_response('Not authenticated', null, 401);
}

try {
    $pdo = get_db_connection();
    $stmt = $pdo->prepare('SELECT id, full_name, email, role FROM users WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $userId]);
    $user = $stmt->fetch();
    if (!$user) {
        error_response('User not found', null, 404);
    }

    // return minimal profile
    $payload = [
        'id' => (int)$user['id'],
        'full_name' => $user['full_name'],
        'email' => $user['email'],
        'role' => $user['role']
    ];
    success_response('Authenticated', $payload);
} catch (PDOException $ex) {
    error_log('me.php error: ' . $ex->getMessage());
    error_response('Server error', null, 500);
}
