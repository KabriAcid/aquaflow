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
        // mark order as paid in DB
        $pdo = get_db_connection();
        $stmt = $pdo->prepare('UPDATE orders SET payment_status = :payment_status, updated_at = NOW() WHERE id = :id');
        $stmt->execute([':payment_status' => 'paid', ':id' => $order_id]);

        success_response('Payment verified and order marked paid', ['verification' => $verification_info]);
    } else {
        error_response('Payment verification failed', ['remote' => $verification_info], 400);
    }
} catch (Exception $e) {
    error_response('Verification error', ['exception' => $e->getMessage()], 500);
}
