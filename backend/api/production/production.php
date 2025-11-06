<?php
header('Content-Type: application/json');
require_once '../../config/database.php';
require_once '../../utils/response.php';
require_once '../../utils/auth.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Authenticate and check for production_manager role
if (!is_authenticated() || $_SESSION['role'] !== 'production_manager') {
    send_response(403, "Access Denied: You do not have permission to perform this action.");
    exit;
}

$pdo = get_database_connection();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // This will fetch a list of products to populate the production form
    // For now, return static data.
    $products = [
        ['id' => 1, 'product_name' => 'Natural Spring Water - 1L'],
        ['id' => 2, 'product_name' => 'Sparkling Beverage - 330ml'],
        ['id' => 3, 'product_name' => 'Bulk Water Package - 24x500ml'],
        ['id' => 4, 'product_name' => 'Flavored Water - Berry 500ml'],
    ];
    send_response(200, "Products retrieved successfully.", $products);

} elseif ($method === 'POST') {
    // This will handle the submission of the production log
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['product_id']) || !isset($data['quantity']) || !isset($data['shift'])) {
        send_response(400, "Invalid input. Product ID, quantity, and shift are required.");
        exit;
    }

    // In a real application, you would insert the production log into the database
    // and update the inventory quantity for the corresponding product.
    // e.g., INSERT INTO production_logs (product_id, quantity, shift, production_date) VALUES (...)
    // e.g., UPDATE products SET quantity = quantity + :quantity WHERE id = :product_id

    send_response(200, "Production log recorded successfully for product ID: " . htmlspecialchars($data['product_id']));

} else {
    send_response(405, "Method Not Allowed");
}
?>
