<?php

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../utils/response.php';
require_once __DIR__ . '/../../utils/auth.php';

set_json_headers();

// Only sales_manager can get all customers
require_role(['sales_manager']);

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

try {
    $pdo = get_db_connection();
    
    $stmt = $pdo->prepare("SELECT id, full_name, email, phone, address, city, state, postal_code, created_at FROM users WHERE role = 'customer' ORDER BY created_at DESC");
    $stmt->execute();
    $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    success_response('Customers fetched successfully', $customers);

} catch (PDOException $e) {
    error_log('Database error fetching customers: ' . $e->getMessage());
    error_response('A database error occurred.', null, 500);
} catch (Exception $e) {
    error_log('Error fetching customers: ' . $e->getMessage());
    error_response($e->getMessage(), null, $e->getCode() ?: 500);
}
