<?php
// backend/api/payments/verify.php
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../utils/response.php';

set_json_headers();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    error_response('Method not allowed', null, 405);
}

$raw = file_get_contents('php://input');
$data = json_decode($raw, true) ?: [];

$tx_ref = $data['tx_ref'] ?? null;
$transaction_id = $data['transaction_id'] ?? null;
$order_id = isset($data['order_id']) ? (int)$data['order_id'] : null;

if (!$tx_ref || !$transaction_id || !$order_id) {
    error_response('Missing verification parameters', null, 422);
}

try {
    // If FLW secret key is available, verify with Flutterwave API
    $flw_secret = $_ENV['FLW_SECRET_KEY'] ?? $_SERVER['FLW_SECRET_KEY'] ?? null;
    $verified = false;
    $verification_info = null;

    if ($flw_secret) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.flutterwave.com/v3/transactions/{$transaction_id}/verify");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer {$flw_secret}", 'Content-Type: application/json']);
        $resp = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $verification_info = json_decode($resp, true);
        if ($code === 200 && isset($verification_info['status']) && $verification_info['status'] === 'success') {
            $verified = true;
        }
    } else {
        // Development fallback: accept as verified but warn
        $verified = true;
        $verification_info = ['warning' => 'FLW_SECRET_KEY not set; skipping remote verification in dev mode.'];
    }

    if ($verified) {
        // mark order as paid in DB and record transaction
        $pdo = get_db_connection();

        // fetch order to get customer info and amount fallback
        $orderStmt = $pdo->prepare('SELECT id, customer_id, total_amount FROM orders WHERE id = :id LIMIT 1');
        $orderStmt->execute([':id' => $order_id]);
        $order = $orderStmt->fetch(PDO::FETCH_ASSOC) ?: null;

        $customer_id = $order['customer_id'] ?? null;
        $amount = null;
        $currency = 'NGN';

        // Try to extract amount/currency from verification response if present
        if (is_array($verification_info) && isset($verification_info['data']['amount'])) {
            $amount = (float)$verification_info['data']['amount'];
        }
        if (is_array($verification_info) && isset($verification_info['data']['currency'])) {
            $currency = $verification_info['data']['currency'];
        }

        // Fallback to order total_amount
        if (empty($amount) && !empty($order['total_amount'])) {
            $amount = (float)$order['total_amount'];
        }

        // Try to fetch user details
        $customer_name = null;
        $customer_email = null;
        $customer_phone = null;
        if ($customer_id) {
            $userStmt = $pdo->prepare('SELECT full_name, email, phone FROM users WHERE id = :id LIMIT 1');
            $userStmt->execute([':id' => $customer_id]);
            $user = $userStmt->fetch(PDO::FETCH_ASSOC);
            if ($user) {
                $customer_name = $user['full_name'] ?? null;
                $customer_email = $user['email'] ?? null;
                $customer_phone = $user['phone'] ?? null;
            }
        }

        // Determine payment method/type from verification response if available
        $payment_method = null;
        if (is_array($verification_info) && isset($verification_info['data']['payment_type'])) {
            $payment_method = $verification_info['data']['payment_type'];
        }

        // Insert transaction record
        $insertStmt = $pdo->prepare('INSERT INTO transactions (order_id, customer_id, customer_name, customer_email, customer_phone, transaction_id, tx_ref, amount, currency, status, payment_method, processor_response, created_at, updated_at) VALUES (:order_id, :customer_id, :customer_name, :customer_email, :customer_phone, :transaction_id, :tx_ref, :amount, :currency, :status, :payment_method, :processor_response, NOW(), NOW())');
        $procResp = json_encode($verification_info);
        $insertStmt->execute([
            ':order_id' => $order_id,
            ':customer_id' => $customer_id,
            ':customer_name' => $customer_name,
            ':customer_email' => $customer_email,
            ':customer_phone' => $customer_phone,
            ':transaction_id' => $transaction_id,
            ':tx_ref' => $tx_ref,
            ':amount' => $amount ?: 0,
            ':currency' => $currency,
            ':status' => 'paid',
            ':payment_method' => $payment_method,
            ':processor_response' => $procResp
        ]);

        // mark order as paid
        $stmt = $pdo->prepare('UPDATE orders SET payment_status = :payment_status, updated_at = NOW() WHERE id = :id');
        $stmt->execute([':payment_status' => 'paid', ':id' => $order_id]);

        success_response('Payment verified, order marked paid and transaction logged', ['verification' => $verification_info, 'transaction_id' => (int)$pdo->lastInsertId()]);
    } else {
        error_response('Payment verification failed', ['remote' => $verification_info], 400);
    }
} catch (Exception $e) {
    error_response('Verification error', ['exception' => $e->getMessage()], 500);
}
