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
        $verification_info = @json_decode($resp, true);
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

        if (!$order) {
            // log and return a 404 if the order isn't present
            $logDir = __DIR__ . '/../../../logs';
            if (!is_dir($logDir)) @mkdir($logDir, 0755, true);
            $logFile = $logDir . '/payments_verify_errors.log';
            $msg = date('Y-m-d H:i:s') . " - verify.php: order not found for order_id={$order_id} payload=" . json_encode($data) . " response=" . substr(($resp ?? ''), 0, 1000) . "\n";
            @file_put_contents($logFile, $msg, FILE_APPEND | LOCK_EX);
            error_response('Order not found', null, 404);
        }

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
        // fallback
        if (empty($payment_method)) $payment_method = 'card';

        // Insert a record into payments table with verification info
        $insertStmt = $pdo->prepare('INSERT INTO payments (order_id, payment_method, amount, transaction_reference, payment_status, payment_date, notes, receipt_url) VALUES (:order_id, :payment_method, :amount, :transaction_reference, :payment_status, NOW(), :notes, :receipt_url)');
        $procResp = json_encode($verification_info);
        // Determine a reasonable transaction reference: prefer provider transaction id if available, else tx_ref
        $txRefValue = $transaction_id ?: $tx_ref;
        $receiptUrl = null;

        $insertStmt->execute([
            ':order_id' => $order_id,
            ':payment_method' => $payment_method,
            ':amount' => $amount ?: 0,
            ':transaction_reference' => $txRefValue,
            ':payment_status' => 'completed',
            ':notes' => $procResp,
            ':receipt_url' => $receiptUrl
        ]);

        // mark order as paid
        $stmt = $pdo->prepare('UPDATE orders SET payment_status = :payment_status, updated_at = NOW() WHERE id = :id');
        $stmt->execute([':payment_status' => 'paid', ':id' => $order_id]);

        success_response('Payment verified, order marked paid and payment recorded', ['verification' => $verification_info, 'payment_id' => (int)$pdo->lastInsertId()]);
    } else {
        error_response('Payment verification failed', ['remote' => $verification_info], 400);
    }
} catch (Exception $e) {
    // Log detailed error to logs/payments_verify_errors.log
    $logDir = __DIR__ . '/../../../logs';
    if (!is_dir($logDir)) @mkdir($logDir, 0755, true);
    $logFile = $logDir . '/payments_verify_errors.log';
    $trace = $e->getTraceAsString();
    $payload = isset($data) ? json_encode($data) : '';
    $respSnippet = isset($resp) ? substr($resp, 0, 2000) : '';
    $verificationSnippet = isset($verification_info) ? json_encode($verification_info) : '';
    $msg = date('Y-m-d H:i:s') . " - verify.php exception: " . $e->getMessage() . "\npayload=" . $payload . "\nresponse_snippet=" . $respSnippet . "\nverification_info=" . $verificationSnippet . "\ntrace=" . $trace . "\n\n";
    @file_put_contents($logFile, $msg, FILE_APPEND | LOCK_EX);

    error_response('Verification error', ['exception' => $e->getMessage()], 500);
}
