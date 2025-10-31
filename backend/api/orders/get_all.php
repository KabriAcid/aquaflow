<?php

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../utils/response.php';
require_once __DIR__ . '/../../utils/auth.php';

set_json_headers();

// A sales_manager can see all orders.
// A customer can only see their own orders.
$user_id = require_role(['sales_manager', 'customer']);
$user_role = $_SESSION['user_role'];

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

try {
    $pdo = get_db_connection();
    $customer_id_filter = $_GET['customer_id'] ?? null;

    $sql = "SELECT o.*, u.full_name as customer_name FROM orders o JOIN users u ON o.customer_id = u.id";
    $params = [];

    if ($user_role === 'customer') {
        $sql .= " WHERE o.customer_id = :customer_id";
        $params[':customer_id'] = $user_id;
    } elseif ($user_role === 'sales_manager' && $customer_id_filter) {
        $sql .= " WHERE o.customer_id = :customer_id";
        $params[':customer_id'] = $customer_id_filter;
    }

    $sql .= " ORDER BY o.order_date DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    success_response('Orders fetched successfully', $orders);

} catch (PDOException $e) {
    error_log('Database error fetching orders: ' . $e->getMessage());
    error_response('A database error occurred.', null, 500);
} catch (Exception $e) {
    error_log('Error fetching orders: ' . $e->getMessage());
    error_response($e->getMessage(), null, $e->getCode() ?: 500);
}
