<?php
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../utils/response.php';
require_once __DIR__ . '/../../utils/auth.php';

set_json_headers();

// Allow preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// Require admin role for this endpoint
require_role('admin');

try {
    $pdo = get_db_connection();

    // --- Get Total Sales --- //
    $query_sales = "SELECT IFNULL(SUM(total_amount),0) as total_sales FROM orders";
    $stmt_sales = $pdo->prepare($query_sales);
    $stmt_sales->execute();
    $sales_row = $stmt_sales->fetch(PDO::FETCH_ASSOC);
    $total_sales = $sales_row['total_sales'] ?? 0;

    // --- Get Total Orders --- //
    $query_orders = "SELECT COUNT(id) as total_orders FROM orders";
    $stmt_orders = $pdo->prepare($query_orders);
    $stmt_orders->execute();
    $orders_row = $stmt_orders->fetch(PDO::FETCH_ASSOC);
    $total_orders = $orders_row['total_orders'] ?? 0;

    // Prepare the summary data
    $summary = [
        'total_sales' => number_format((float)$total_sales, 2, '.', ''),
        'total_orders' => (int)$total_orders,
    ];

    // Return a success response with the data
    success_response('Sales summary', $summary);
} catch (PDOException $e) {
    error_log('sales/summary.php DB error: ' . $e->getMessage());
    error_response('Failed to retrieve sales summary.', null, 500);
} catch (Exception $e) {
    error_log('sales/summary.php error: ' . $e->getMessage());
    error_response('Failed to retrieve sales summary.', null, 500);
}
