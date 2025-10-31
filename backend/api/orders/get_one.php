<?php
// backend/api/orders/get_one.php
// Returns a single order and its items. GET /backend/api/orders/get_one.php?id={order_id}

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../utils/response.php';

set_json_headers();

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if (session_status() === PHP_SESSION_NONE) session_start();

// Determine role and role-specific id key
$role = $_SESSION['user_role'] ?? null;
$userId = null;
if ($role) {
    $roleKey = preg_replace('/[^a-z0-9_]/', '', strtolower($role)) . '_id';
    if (!empty($_SESSION[$roleKey])) {
        $userId = (int)$_SESSION[$roleKey];
    }
}
if (!$userId && !empty($_SESSION['user_id'])) {
    $userId = (int)$_SESSION['user_id'];
}

$orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($orderId <= 0) {
    error_response('Order id is required', null, 400);
}

try {
    $pdo = get_db_connection();

    $stmt = $pdo->prepare('SELECT * FROM orders WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $orderId]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$order) {
        error_response('Order not found', null, 404);
    }

    // authorization: customers can only view their own orders
    if ($role !== 'admin' && $userId && (int)$order['customer_id'] !== (int)$userId) {
        error_response('Unauthorized to view this order', null, 403);
    }

    // fetch order items
    $stmtItems = $pdo->prepare('SELECT product_name, quantity, unit_price, subtotal FROM order_items WHERE order_id = :order_id');
    $stmtItems->execute([':order_id' => $orderId]);
    $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

    // normalize payload
    $payload = [
        'order_id' => (int)$order['id'],
        'order_number' => $order['order_number'],
        'order_date' => $order['order_date'],
        'delivery_date' => $order['delivery_date'],
        'delivery_address' => $order['delivery_address'],
        'payment_method' => $order['payment_method'] ?? null,
        'payment_status' => $order['payment_status'],
        'status' => $order['status'],
        'subtotal' => (float)$order['subtotal'],
        'delivery_fee' => (float)$order['delivery_fee'],
        'total_amount' => (float)$order['total_amount'],
        'items' => $items
    ];

    success_response('Order retrieved', $payload);
} catch (PDOException $ex) {
    error_log('get_one.php error: ' . $ex->getMessage());
    error_response('Server error', null, 500);
}
