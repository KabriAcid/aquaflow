<?php

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../utils/response.php';
require_once __DIR__ . '/../../utils/auth.php';

set_json_headers();

// Only admins may list users
require_role('admin');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

try {
    $pdo = get_db_connection();

    $stmt = $pdo->prepare('SELECT id, full_name, email, role FROM users ORDER BY full_name ASC');
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    success_response('Users fetched', $users);
} catch (PDOException $e) {
    error_log('users/get_all.php DB error: ' . $e->getMessage());
    error_response('Failed to fetch users', null, 500);
}
