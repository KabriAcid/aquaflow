<?php
// backend/api/payments/get_by_order.php
// Returns transaction(s) for a given order id

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
        error_response('Unauthorized to view transactions for this order', null, 403);
    }

    $tstmt = $pdo->prepare('SELECT id, transaction_id, tx_ref, amount, currency, status, payment_method, processor_response, created_at FROM transactions WHERE order_id = :order_id ORDER BY created_at DESC');
    $tstmt->execute([':order_id' => $orderId]);
    $transactions = $tstmt->fetchAll(PDO::FETCH_ASSOC);

    success_response('Transactions fetched', $transactions);
} catch (PDOException $ex) {
    error_log('payments/get_by_order.php error: ' . $ex->getMessage());
    error_response('Server error', null, 500);
}
