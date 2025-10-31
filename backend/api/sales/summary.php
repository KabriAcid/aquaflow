<?php

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../utils/response.php';
require_once __DIR__ . '/../../utils/auth.php';

set_json_headers();

// Require sales_manager role
$user_id = require_role(['sales_manager']);

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

try {
    $pdo = get_db_connection();
    
    // 1. Total Sales (Delivered Orders)
    $stmt = $pdo->prepare("SELECT SUM(total_amount) as total_sales FROM orders WHERE status = 'delivered'");
    $stmt->execute();
    $total_sales = $stmt->fetchColumn() ?: 0;

    // 2. Total Orders
    $stmt = $pdo->prepare("SELECT COUNT(*) as total_orders FROM orders");
    $stmt->execute();
    $total_orders = $stmt->fetchColumn() ?: 0;

    // 3. New Customers (e.g., in the last 30 days)
    $stmt = $pdo->prepare("SELECT COUNT(*) as new_customers FROM users WHERE role = 'customer' AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
    $stmt->execute();
    $new_customers = $stmt->fetchColumn() ?: 0;

    // 4. Pending Deliveries
    $stmt = $pdo->prepare("SELECT COUNT(*) as pending_deliveries FROM orders WHERE status = 'out_for_delivery'");
    $stmt->execute();
    $pending_deliveries = $stmt->fetchColumn() ?: 0;

    $summary_data = [
        'total_sales' => $total_sales,
        'total_orders' => $total_orders,
        'new_customers' => $new_customers,
        'pending_deliveries' => $pending_deliveries
    ];

    success_response('Sales summary fetched successfully', $summary_data);

} catch (PDOException $e) {
    error_log('Database error fetching sales summary: ' . $e->getMessage());
    error_response('A database error occurred while fetching the summary.', null, 500);
} catch (Exception $e) {
    error_log('Error fetching sales summary: ' . $e->getMessage());
    error_response($e->getMessage(), null, $e->getCode() ?: 500);
}
