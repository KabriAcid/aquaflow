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

	if ($role === 'admin') {
		$stmt = $pdo->prepare('SELECT id, order_number, order_date, subtotal, delivery_fee, total_amount, status, payment_status FROM orders ORDER BY order_date DESC');
		$stmt->execute();
	} else {
		if (!$userId) {
			error_response('Not authenticated', null, 401);
		}
		$stmt = $pdo->prepare('SELECT id, order_number, order_date, subtotal, delivery_fee, total_amount, status, payment_status FROM orders WHERE customer_id = :cid ORDER BY order_date DESC');
		$stmt->execute([':cid' => $userId]);
	}

	$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

	// return simple list; frontend will group/filter by status
	success_response('Orders retrieved', $orders);
} catch (PDOException $ex) {
	error_log('orders/get_all.php error: ' . $ex->getMessage());
	error_response('Server error', null, 500);
}

 
