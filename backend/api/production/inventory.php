<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../utils/response.php';
require_once __DIR__ . '/../../utils/auth.php';

set_json_headers();

// Authenticate and check for production_manager role
require_role(['production_manager']);

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if ($method === 'GET') {
    // For now, return static data. Later, this will fetch from the database.
    $inventory = [
        ['id' => 1, 'product_name' => 'Natural Spring Water - 1L', 'quantity' => 15000, 'last_updated' => '2024-07-28 10:00:00'],
        ['id' => 2, 'product_name' => 'Sparkling Beverage - 330ml', 'quantity' => 8000, 'last_updated' => '2024-07-28 11:30:00'],
        ['id' => 3, 'product_name' => 'Bulk Water Package - 24x500ml', 'quantity' => 2500, 'last_updated' => '2024-07-28 09:00:00'],
        ['id' => 4, 'product_name' => 'Flavored Water - Berry 500ml', 'quantity' => 6000, 'last_updated' => '2024-07-27 15:00:00'],
    ];
    send_response(200, "Inventory data retrieved successfully.", $inventory);
} elseif ($method === 'POST') {
    // This part will handle inventory updates. 
    // For now, we simulate an update.
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['product_id']) || !isset($data['quantity'])) {
        send_response(400, "Invalid input. Product ID and quantity are required.");
        exit;
    }

    // In a real application, you would update the database here.
    // e.g., UPDATE products SET quantity = :quantity WHERE id = :product_id

    send_response(200, "Inventory updated successfully for product ID: " . htmlspecialchars($data['product_id']));
} else {
    send_response(405, "Method Not Allowed");
}
