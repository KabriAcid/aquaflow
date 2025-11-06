<?php

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../utils/response.php';
require_once __DIR__ . '/../../utils/auth.php';

set_json_headers();
require_role(['admin']);

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    error_response('Invalid request method.', null, 405);
    exit;
}

try {
    $pdo = get_db_connection();
    $stmt = $pdo->query("SELECT id AS user_id, full_name AS username, email, role, state, city, phone, created_at FROM users ORDER BY created_at DESC");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    success_response('Users fetched successfully', $users);
} catch (PDOException $e) {
    error_log('Database error fetching users: ' . $e->getMessage());
    error_response('A database error occurred.', null, 500);
} catch (Exception $e) {
    error_log('Error fetching users: ' . $e->getMessage());
    error_response($e->getMessage(), null, 500);
}
