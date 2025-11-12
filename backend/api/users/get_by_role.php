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

$role = $_GET['role'] ?? null;

if (!$role || !in_array($role, ['sales_manager', 'production_manager'])) {
    error_response('Invalid role specified.', null, 400);
    exit;
}

try {
    $pdo = get_db_connection();
    $stmt = $pdo->prepare("SELECT id, full_name, email, phone, role, status FROM users WHERE role = :role ORDER BY created_at DESC");
    $stmt->execute([':role' => $role]);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    success_response('Users fetched successfully', $users);
} catch (PDOException $e) {
    error_log('Database error fetching users: ' . $e->getMessage());
    error_response('A database error occurred.', null, 500);
} catch (Exception $e) {
    error_log('Error fetching users: ' . $e->getMessage());
    error_response($e->getMessage(), null, 500);
}
