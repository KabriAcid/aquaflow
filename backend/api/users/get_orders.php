<?php
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../utils/response.php';
require_once __DIR__ . '/../../utils/auth.php';

set_json_headers();

// allow sales_manager and admin to fetch orders for a specific customer
require_role(['admin', 'sales_manager']);

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$customer_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$customer_id) {
    error_response('Customer id required', null, 400);
}

try {
    $pdo = get_db_connection();
    $stmt = $pdo->prepare('SELECT id, order_number, order_date, subtotal, delivery_fee, total_amount, status, payment_status FROM orders WHERE customer_id = :cid ORDER BY order_date DESC');
    $stmt->execute([':cid' => $customer_id]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Optionally attach items per order (small result set expected)
    foreach ($orders as &$o) {
        $stmt2 = $pdo->prepare('SELECT id, product_id, product_name, quantity, unit_price, subtotal FROM order_items WHERE order_id = :oid');
        $stmt2->execute([':oid' => $o['id']]);
        $o['items'] = $stmt2->fetchAll(PDO::FETCH_ASSOC);
    }

    success_response('Orders fetched', $orders);
} catch (PDOException $e) {
    error_log('users/get_orders.php DB error: ' . $e->getMessage());
    error_response('Failed to fetch orders', null, 500);
}
