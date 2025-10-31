<?php
header('Content-Type: application/json');
session_start();

// Basic security check - ensure user is logged in as an admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Database connection (replace with your actual connection details)
try {
    // $pdo = new PDO("mysql:host=localhost;dbname=aquaflow", "root", "");
    // $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => "Database connection failed: " . $e->getMessage()]);
    exit;
}

$action = isset($_GET['action']) ? $_GET['action'] : '';

switch ($action) {
    case 'get_customers':
        // TODO: Replace with real database query
        $customers = [
            ['id' => 1, 'name' => 'John Doe', 'email' => 'john.doe@example.com', 'created_at' => '2023-10-27T10:00:00Z'],
            ['id' => 2, 'name' => 'Jane Smith', 'email' => 'jane.smith@example.com', 'created_at' => '2023-10-26T14:30:00Z'],
        ];
        echo json_encode(['success' => true, 'data' => $customers]);
        break;

    case 'get_customer_details':
        $customerId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        // TODO: Replace with real database query
        $customer = ['id' => $customerId, 'name' => 'John Doe', 'email' => 'john.doe@example.com', 'created_at' => '2023-10-27T10:00:00Z'];
        echo json_encode(['success' => true, 'data' => $customer]);
        break;

    case 'get_customer_orders':
        $customerId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        // TODO: Replace with real database query
        $orders = [
            ['id' => 101, 'created_at' => '2023-10-28T11:00:00Z', 'total_amount' => 150.00, 'status' => 'Shipped'],
            ['id' => 102, 'created_at' => '2023-10-29T12:30:00Z', 'total_amount' => 75.50, 'status' => 'Processing'],
        ];
        echo json_encode(['success' => true, 'data' => $orders]);
        break;

    case 'get_order_details':
        $orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        // TODO: Replace with real database query
        $order = [
            'id' => $orderId,
            'created_at' => '2023-10-28T11:00:00Z',
            'total_amount' => 150.00,
            'status' => 'Shipped',
            'shipping_address' => '123 Main St, Anytown, USA',
            'customer' => ['name' => 'John Doe', 'email' => 'john.doe@example.com'],
            'items' => [
                ['name' => 'Premium Water Bottle', 'quantity' => 2, 'price' => 25.00],
                ['name' => 'Water Filter', 'quantity' => 1, 'price' => 100.00],
            ],
        ];
        echo json_encode(['success' => true, 'data' => $order]);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}
