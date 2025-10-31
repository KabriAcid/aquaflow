<?php

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../utils/response.php';
require_once __DIR__ . '/../../utils/auth.php';

set_json_headers();

$user_id = require_role(['sales_manager', 'customer']);
$user_role = $_SESSION['user_role'];

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$customer_id_to_fetch = $_GET['id'] ?? null;

if (!$customer_id_to_fetch) {
    error_response('Customer ID is required', null, 400);
    exit;
}

// A customer can only view their own details, unless they are a sales manager
if ($user_role === 'customer' && $user_id != $customer_id_to_fetch) {
    error_response('Forbidden: You can only view your own profile.', null, 403);
    exit;
}

try {
    $pdo = get_db_connection();
    
    $stmt = $pdo->prepare("SELECT id, full_name, email, phone, address, city, state, postal_code, created_at FROM users WHERE id = :id AND role = 'customer'");
    $stmt->execute([':id' => $customer_id_to_fetch]);
    $customer = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$customer) {
        error_response('Customer not found', null, 404);
        exit;
    }

    success_response('Customer fetched successfully', $customer);

} catch (PDOException $e) {
    error_log('Database error fetching customer: ' . $e->getMessage());
    error_response('A database error occurred.', null, 500);
} catch (Exception $e) {
    error_log('Error fetching customer: ' . $e->getMessage());
    error_response($e->getMessage(), null, $e->getCode() ?: 500);
}
