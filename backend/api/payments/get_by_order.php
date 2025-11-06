<?php
// backend/api/payments/get_by_order.php
// Returns payment(s) for a given order id (reads from payments table)

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../utils/response.php';

set_json_headers();

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    error_response('Method not allowed', null, 405);
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

$orderId = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
if ($orderId <= 0) {
    error_response('order_id is required', null, 400);
}

try {
    $pdo = get_db_connection();

    // fetch order and check ownership (customers can only access their orders)
    $stmt = $pdo->prepare('SELECT id, customer_id FROM orders WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $orderId]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$order) {
        error_response('Order not found', null, 404);
    }

    if ($role !== 'admin' && $userId && (int)$order['customer_id'] !== (int)$userId) {
        error_response('Unauthorized to view payments for this order', null, 403);
    }

    // Use the payments table (single payment per order or multiple payment attempts)
    $pstmt = $pdo->prepare('SELECT id, order_id, payment_method, amount, transaction_reference, payment_status, payment_date, receipt_url, notes FROM payments WHERE order_id = :order_id ORDER BY payment_date DESC');
    $pstmt->execute([':order_id' => $orderId]);
    $payments = $pstmt->fetchAll(PDO::FETCH_ASSOC);

    success_response('Payments fetched', $payments);
} catch (PDOException $ex) {
    error_log('payments/get_by_order.php error: ' . $ex->getMessage());
    error_response('Server error', null, 500);
}
