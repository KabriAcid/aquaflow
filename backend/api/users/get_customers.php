<?php
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../utils/response.php';
require_once __DIR__ . '/../../utils/auth.php';

set_json_headers();

// allow sales_manager and admin to list customers
require_role(['admin', 'sales_manager']);

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

try {
    $pdo = get_db_connection();
    $stmt = $pdo->prepare('SELECT id, full_name, email, phone, city, state, status FROM users WHERE role = :role ORDER BY full_name ASC');
    $stmt->execute([':role' => 'customer']);
    $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    success_response('Customers fetched', $customers);
} catch (PDOException $e) {
    error_log('users/get_customers.php DB error: ' . $e->getMessage());
    error_response('Failed to fetch customers', null, 500);
}
