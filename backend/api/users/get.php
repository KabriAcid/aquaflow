<?php

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../utils/response.php';
require_once __DIR__ . '/../../utils/auth.php';

set_json_headers();

// Get current session user (handles both user_id and role_id variants)
$session = get_session_user();
$user_id = $session['id'];

if (!$user_id) {
    error_response('User not authenticated.', null, 401);
    exit;
}

try {
    $pdo = get_db_connection();
    $stmt = $pdo->prepare("SELECT id, full_name, email, role, status, created_at FROM users WHERE id = :user_id");
    $stmt->execute([':user_id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        success_response('User profile fetched successfully', $user);
    } else {
        error_response('User not found.', null, 404);
    }
} catch (PDOException $e) {
    error_log('Database error fetching user: ' . $e->getMessage());
    error_response('A database error occurred.', null, 500);
} catch (Exception $e) {
    error_log('Error fetching user: ' . $e->getMessage());
    error_response($e->getMessage(), null, 500);
}
