<?php
// backend/api/orders/create.php
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../utils/response.php';

set_json_headers();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    error_response('Method not allowed', null, 405);
}

$raw = file_get_contents('php://input');
$data = json_decode($raw, true) ?: [];

if (session_status() === PHP_SESSION_NONE) session_start();

// Determine customer id from session
$customer_id = $_SESSION['customer_id'] ?? null;
if (!$customer_id) {
    error_response('Not authenticated', null, 401);
}

$items = $data['items'] ?? [];
$subtotal = isset($data['subtotal']) ? (float)$data['subtotal'] : 0.0;
$delivery_fee = isset($data['delivery_fee']) ? (float)$data['delivery_fee'] : 0.0;
$total_amount = isset($data['total_amount']) ? (float)$data['total_amount'] : ($subtotal + $delivery_fee);
$delivery_address = $data['delivery_address'] ?? '';
$delivery_date = $data['delivery_date'] ?? null;
$special_instructions = $data['special_instructions'] ?? null;
$payment_method = $data['payment_method'] ?? 'card';

if (empty($items) || $subtotal <= 0) {
    error_response('Invalid order data', null, 422);
}

try {
    $pdo = get_db_connection();
    $pdo->beginTransaction();

    // generate simple order number
    $order_number = 'AF' . time() . rand(100, 999);

    // note: avoid referencing columns that may not exist in some DB instances (e.g., delivery_postal_code)
    $stmt = $pdo->prepare('INSERT INTO orders (order_number, customer_id, delivery_address, delivery_city, delivery_state, delivery_date, special_instructions, subtotal, delivery_fee, total_amount, status, payment_status, created_at) VALUES (:order_number, :customer_id, :delivery_address, :delivery_city, :delivery_state, :delivery_date, :special_instructions, :subtotal, :delivery_fee, :total_amount, :status, :payment_status, NOW())');

    // try to split delivery_address into components if comma-separated
    $delivery_city = '';
    $delivery_state = '';
    $parts = array_map('trim', explode(',', $delivery_address));
    if (count($parts) >= 2) {
        $delivery_city = $parts[count($parts) - 2];
        $delivery_state = $parts[count($parts) - 1];
    }

    $status = 'pending';
    $payment_status = ($payment_method === 'cash_on_delivery') ? 'unpaid' : 'unpaid';

    $stmt->execute([
        ':order_number' => $order_number,
        ':customer_id' => $customer_id,
        ':delivery_address' => $delivery_address,
        ':delivery_city' => $delivery_city,
        ':delivery_state' => $delivery_state,
        ':delivery_date' => $delivery_date,
        ':special_instructions' => $special_instructions,
        ':subtotal' => $subtotal,
        ':delivery_fee' => $delivery_fee,
        ':total_amount' => $total_amount,
        ':status' => $status,
        ':payment_status' => $payment_status
    ]);

    $order_id = (int)$pdo->lastInsertId();

    // insert order items
    $stmtItem = $pdo->prepare('INSERT INTO order_items (order_id, product_id, product_name, quantity, unit_price, subtotal) VALUES (:order_id, :product_id, :product_name, :quantity, :unit_price, :subtotal)');
    foreach ($items as $it) {
        $pid = $it['id'] ?? null;
        $pname = $it['name'] ?? '';
        $qty = isset($it['quantity']) ? (int)$it['quantity'] : 1;
        $price = isset($it['price']) ? (float)$it['price'] : 0.0;
        $sub = $price * $qty;
        $stmtItem->execute([
            ':order_id' => $order_id,
            ':product_id' => $pid,
            ':product_name' => $pname,
            ':quantity' => $qty,
            ':unit_price' => $price,
            ':subtotal' => $sub
        ]);
    }

    $pdo->commit();

    // Return user info for Flutterwave customer prefill (try to fetch)
    $userStmt = $pdo->prepare('SELECT id, full_name, email, phone FROM users WHERE id = :id LIMIT 1');
    $userStmt->execute([':id' => $customer_id]);
    $user = $userStmt->fetch(PDO::FETCH_ASSOC) ?: null;

    $orderPayload = ['order_id' => $order_id, 'order_number' => $order_number, 'total_amount' => $total_amount];
    success_response('Order created', ['order' => $orderPayload, 'user' => $user], 201);
} catch (Exception $e) {
    // attempt rollback if transaction is active
    if (isset($pdo) && $pdo && $pdo->inTransaction()) $pdo->rollBack();

    // log exception to a dedicated file to help debugging on development machine
    $logDir = __DIR__ . '/../../../logs';
    if (!is_dir($logDir)) {
        @mkdir($logDir, 0755, true);
    }
    $logFile = $logDir . '/create_order_errors.log';
    $msg = date('Y-m-d H:i:s') . " - create.php exception: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n";
    @file_put_contents($logFile, $msg, FILE_APPEND | LOCK_EX);

    // return error to client (include exception message to aid debugging in dev)
    error_response('Failed to create order', ['exception' => $e->getMessage()], 500);
}
