<?php
// Create a COD transaction record for an order and set order status accordingly
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../utils/response.php';

set_json_headers();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    error_response('Method not allowed', null, 405);
}

$raw = file_get_contents('php://input');
$data = json_decode($raw, true) ?: [];

if (session_status() === PHP_SESSION_NONE) session_start();

$customer_id = $_SESSION['customer_id'] ?? null;
if (!$customer_id) {
    error_response('Not authenticated', null, 401);
}

$order_id = isset($data['order_id']) ? (int)$data['order_id'] : null;
if (!$order_id) {
    error_response('Missing order_id', null, 422);
}

try {
    $pdo = get_db_connection();

    // fetch the order and ensure it belongs to the customer (or exists)
    $orderStmt = $pdo->prepare('SELECT id, customer_id, total_amount, status FROM orders WHERE id = :id LIMIT 1');
    $orderStmt->execute([':id' => $order_id]);
    $order = $orderStmt->fetch(PDO::FETCH_ASSOC);
    if (!$order) {
        error_response('Order not found', null, 404);
    }
    if ((int)$order['customer_id'] !== (int)$customer_id) {
        error_response('Forbidden: order does not belong to you', null, 403);
    }

    // start DB transaction
    $pdo->beginTransaction();

    // Create a tx reference for COD
    $tx_ref = 'COD-' . $order_id . '-' . time();

    // fetch customer details for transaction record
    $userStmt = $pdo->prepare('SELECT id, full_name, email, phone FROM users WHERE id = :id LIMIT 1');
    $userStmt->execute([':id' => $customer_id]);
    $user = $userStmt->fetch(PDO::FETCH_ASSOC) ?: null;

    $amount = isset($order['total_amount']) ? (float)$order['total_amount'] : 0.0;
    $currency = 'NGN';

    // Insert a payment record into the payments table instead of transactions
    $insertStmt = $pdo->prepare('INSERT INTO payments (order_id, payment_method, amount, transaction_reference, payment_status, payment_date, notes) VALUES (:order_id, :payment_method, :amount, :transaction_reference, :payment_status, NOW(), :notes)');
    $procResp = json_encode(['note' => 'Cash on Delivery created via confirm_cod endpoint', 'created_by' => $customer_id]);
    $insertStmt->execute([
        ':order_id' => $order_id,
        ':payment_method' => 'cash_on_delivery',
        ':amount' => $amount,
        ':transaction_reference' => $tx_ref,
        ':payment_status' => 'pending',
        ':notes' => $procResp
    ]);

    $payment_id = (int)$pdo->lastInsertId();

    // Update order status and payment status
    $update = $pdo->prepare('UPDATE orders SET status = :status, payment_status = :payment_status, updated_at = NOW() WHERE id = :id');
    $update->execute([
        ':status' => 'processing',
        ':payment_status' => 'unpaid',
        ':id' => $order_id
    ]);

    $pdo->commit();

    success_response('COD confirmed and payment recorded', ['order_id' => $order_id, 'payment_id' => $payment_id, 'transaction_reference' => $tx_ref]);
} catch (Exception $e) {
    if ($pdo && $pdo->inTransaction()) $pdo->rollBack();
    error_response('Failed to confirm COD order', ['exception' => $e->getMessage()], 500);
}
