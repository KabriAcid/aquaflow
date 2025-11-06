<?php

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../utils/response.php';
require_once __DIR__ . '/../../utils/auth.php';

session_start();
set_json_headers();

// Any authenticated user can fetch their own profile
if (!isset($_SESSION['user_id'])) {
    error_response('User not authenticated.', null, 401);
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    $pdo = get_db_connection();
    $stmt = $pdo->prepare("SELECT id AS user_id, full_name AS username, email, role, created_at FROM users WHERE id = :user_id");
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
