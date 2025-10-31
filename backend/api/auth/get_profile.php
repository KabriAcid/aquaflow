<?php
// backend/api/auth/get_profile.php
// Returns detailed profile information for the authenticated user

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
    $stmt = $pdo->prepare('SELECT id, full_name, email, phone, address, city, state, postal_code FROM users WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$user) {
        error_response('User not found', null, 404);
    }

    // Normalize keys and return
    $payload = [
        'id' => (int)$user['id'],
        'full_name' => $user['full_name'] ?? '',
        'email' => $user['email'] ?? '',
        'phone' => $user['phone'] ?? '',
        'address' => $user['address'] ?? '',
        'city' => $user['city'] ?? '',
        'state' => $user['state'] ?? '',
        'postal_code' => $user['postal_code'] ?? ''
    ];

    success_response('Profile retrieved', $payload);
} catch (PDOException $ex) {
    error_log('get_profile.php error: ' . $ex->getMessage());
    error_response('Server error', null, 500);
}
