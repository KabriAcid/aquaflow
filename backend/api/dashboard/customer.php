<?php

/**
 * Return dashboard data for the currently authenticated customer
 * Uses PHP session to determine the customer id
 */
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../utils/response.php';

set_json_headers();

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if (session_status() === PHP_SESSION_NONE) session_start();

// determine user id from role-scoped session key or fallback
$role = $_SESSION['user_role'] ?? null;
$userId = null;
if ($role) {
    $roleKey = preg_replace('/[^a-z0-9_]/', '', strtolower($role)) . '_id';
    if (!empty($_SESSION[$roleKey])) $userId = (int)$_SESSION[$roleKey];
}
if (!$userId && !empty($_SESSION['user_id'])) $userId = (int)$_SESSION['user_id'];

if (!$userId) {
    error_response('Not authenticated', null, 401);
}

try {
    $pdo = get_db_connection();

    // Fetch recent orders for this customer
    $stmt = $pdo->prepare('SELECT id, order_number, order_date, total_amount, status FROM orders WHERE customer_id = :cid ORDER BY order_date DESC LIMIT 50');
    $stmt->execute([':cid' => $userId]);
    $orders = $stmt->fetchAll();

    // Also compute simple aggregates: total orders, pending count, total spent
    $stmt2 = $pdo->prepare('SELECT COUNT(*) as total_orders, SUM(total_amount) as total_spent FROM orders WHERE customer_id = :cid');
    $stmt2->execute([':cid' => $userId]);
    $agg = $stmt2->fetch();

    $stmt3 = $pdo->prepare('SELECT COUNT(*) as pending_orders FROM orders WHERE customer_id = :cid AND status = "pending"');
    $stmt3->execute([':cid' => $userId]);
    $pending = $stmt3->fetch();

    $payload = [
        'orders' => $orders,
        'stats' => [
            'total_orders' => (int)($agg['total_orders'] ?? 0),
            'pending_orders' => (int)($pending['pending_orders'] ?? 0),
            'total_spent' => (float)($agg['total_spent'] ?? 0),
        ]
    ];

    success_response('Dashboard data', $payload);
} catch (PDOException $ex) {
    error_log('dashboard/customer.php error: ' . $ex->getMessage());
    error_response('Server error fetching dashboard', null, 500);
}
