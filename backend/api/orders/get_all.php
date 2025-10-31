<?php
// backend/api/orders/get_all.php
// Returns a list of orders for the authenticated user (or all orders for admin)

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../utils/response.php';

set_json_headers();

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
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

try {
    $pdo = get_db_connection();

    // Allow admin and sales_manager to view all orders; customers only their own
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 0;

    if (in_array($role, ['admin', 'sales_manager'])) {
        $sql = 'SELECT id, order_number, order_date, customer_id, subtotal, delivery_fee, total_amount, status, payment_status FROM orders ORDER BY order_date DESC';
        if ($limit > 0) {
            $sql .= ' LIMIT :limit';
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
        } else {
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
        }
    } else {
        if (!$userId) {
            error_response('Not authenticated', null, 401);
        }
        $sql = 'SELECT id, order_number, order_date, customer_id, subtotal, delivery_fee, total_amount, status, payment_status FROM orders WHERE customer_id = :cid ORDER BY order_date DESC';
        if ($limit > 0) {
            $sql .= ' LIMIT :limit';
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':cid', $userId, PDO::PARAM_INT);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
        } else {
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':cid' => $userId]);
        }
    }

    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // return simple list; frontend will group/filter by status
    success_response('Orders retrieved', $orders);
} catch (PDOException $ex) {
    error_log('orders/get_all.php error: ' . $ex->getMessage());
    error_response('Server error', null, 500);
}
