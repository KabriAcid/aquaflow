<?php

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../utils/response.php';
require_once __DIR__ . '/../../utils/auth.php';

set_json_headers();

// Allow both customers and sales managers to see products
require_role(['customer', 'sales_manager']);

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

try {
    $pdo = get_db_connection();
    
    // The query is simple and doesn't need role-based adjustments
    $stmt = $pdo->prepare("SELECT * FROM products ORDER BY name");
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    success_response('Products fetched successfully', $products);

} catch (PDOException $e) {
    error_log('Database error fetching products: ' . $e->getMessage());
    error_response('A database error occurred.', null, 500);
} catch (Exception $e) {
    error_log('Error fetching products: ' . $e->getMessage());
    error_response($e->getMessage(), null, $e->getCode() ?: 500);
}
