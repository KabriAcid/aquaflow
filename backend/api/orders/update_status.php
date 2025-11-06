<?php
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../utils/response.php';

set_json_headers();

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if (session_status() === PHP_SESSION_NONE) session_start();

$role = $_SESSION['user_role'] ?? null;
if (!in_array($role, ['admin', 'sales_manager'])) {
    error_response('Forbidden', null, 403);
}

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!$data) {
    error_response('Invalid payload', null, 400);
}

$order_id = isset($data['order_id']) ? (int)$data['order_id'] : 0;
$status = isset($data['status']) ? trim($data['status']) : '';

$allowed = ['pending', 'processing', 'out_for_delivery', 'delivered', 'cancelled'];
if (!$order_id || !in_array($status, $allowed)) {
    error_response('Missing or invalid fields', null, 400);
}

try {
    $pdo = get_db_connection();

    $stmt = $pdo->prepare('UPDATE orders SET status = :status, updated_at = current_timestamp() WHERE id = :id');
    $stmt->execute([':status' => $status, ':id' => $order_id]);

    if ($stmt->rowCount() === 0) {
        error_response('Order not found or status unchanged', null, 404);
    }

    $stmt = $pdo->prepare('SELECT id, order_number, order_date, customer_id, subtotal, delivery_fee, total_amount, status, payment_status FROM orders WHERE id = :id');
    $stmt->execute([':id' => $order_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    success_response('Order status updated', $order);
} catch (PDOException $ex) {
    error_log('orders/update_status.php error: ' . $ex->getMessage());
    error_response('Server error', null, 500);
}
