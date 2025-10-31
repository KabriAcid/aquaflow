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

$order_id = $_GET['id'] ?? null;

if (!$order_id) {
    error_response('Order ID is required', null, 400);
    exit;
}

try {
    $pdo = get_db_connection();
    
    $sql = "SELECT o.*, u.full_name as customer_name FROM orders o JOIN users u ON o.customer_id = u.id WHERE o.id = :order_id";
    $params = [':order_id' => $order_id];

    if ($user_role === 'customer') {
        $sql .= " AND o.customer_id = :customer_id";
        $params[':customer_id'] = $user_id;
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        error_response('Order not found or you do not have permission to view it.', null, 404);
        exit;
    }

    // Fetch order items
    $stmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = :order_id");
    $stmt->execute([':order_id' => $order_id]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $order['items'] = $items;

    success_response('Order fetched successfully', $order);

} catch (PDOException $e) {
    error_log('Database error fetching order: ' . $e->getMessage());
    error_response('A database error occurred.', null, 500);
} catch (Exception $e) {
    error_log('Error fetching order: ' . $e->getMessage());
    error_response($e->getMessage(), null, $e->getCode() ?: 500);
}
