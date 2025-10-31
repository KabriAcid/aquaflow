<?php
// backend/api/sales/summary.php

include_once '../../config/database.php';
include_once '../../utils/response.php';
include_once '../../utils/auth.php';

// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$conn = connect_db();

// Require admin role for this endpoint
require_role('admin');

try {
    // --- Get Total Sales --- //
    // It's assumed that there is a table named 'orders' with a column 'total_amount'
    $query_sales = "SELECT SUM(total_amount) as total_sales FROM orders";
    $stmt_sales = $conn->prepare($query_sales);
    $stmt_sales->execute();
    $sales_row = $stmt_sales->fetch(PDO::FETCH_ASSOC);
    $total_sales = $sales_row['total_sales'] ?? 0;

    // --- Get Total Orders --- //
    $query_orders = "SELECT COUNT(id) as total_orders FROM orders";
    $stmt_orders = $conn->prepare($query_orders);
    $stmt_orders->execute();
    $orders_row = $stmt_orders->fetch(PDO::FETCH_ASSOC);
    $total_orders = $orders_row['total_orders'] ?? 0;

    // Prepare the summary data
    $summary = [
        'total_sales' => number_format((float)$total_sales, 2, '.', ''),
        'total_orders' => (int)$total_orders,
    ];

    // Return a success response with the data
    success_response($summary);

} catch (Exception $e) {
    // Return an error response if something goes wrong
    error_response('Failed to retrieve sales summary: ' . $e->getMessage());
}
