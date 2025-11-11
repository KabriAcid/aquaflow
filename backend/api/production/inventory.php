<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../utils/response.php';
require_once __DIR__ . '/../../utils/auth.php';

set_json_headers();

// Authenticate and check for production_manager role
require_role(['production_manager']);

// Get database connection
$pdo = get_db_connection();

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if ($method === 'GET') {
    try {
        $query = "
            SELECT 
                i.id,
                i.product_id,
                p.name AS product_name,
                i.current_stock AS quantity,
                i.last_updated,
                i.minimum_stock_level,
                i.reorder_point
            FROM inventory i
            INNER JOIN products p ON i.product_id = p.id
            ORDER BY p.name ASC
        ";

        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $inventory = $stmt->fetchAll(PDO::FETCH_ASSOC);

        success_response("Inventory data retrieved successfully.", $inventory);
    } catch (PDOException $e) {
        error_response("Failed to retrieve inventory data: " . $e->getMessage(), null, 500);
    }
} elseif ($method === 'POST') {
    try {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['product_id']) || !isset($data['quantity'])) {
            error_response("Invalid input. Product ID and quantity are required.", null, 400);
            exit;
        }

        $product_id = (int) $data['product_id'];
        $quantity = (int) $data['quantity'];

        // Verify the product exists
        $check_query = "SELECT id FROM products WHERE id = :product_id";
        $check_stmt = $pdo->prepare($check_query);
        $check_stmt->execute(['product_id' => $product_id]);

        if (!$check_stmt->fetch()) {
            error_response("Product not found.", null, 404);
            exit;
        }

        // Update inventory
        $update_query = "
            UPDATE inventory 
            SET current_stock = :quantity 
            WHERE product_id = :product_id
        ";

        $update_stmt = $pdo->prepare($update_query);
        $update_stmt->execute([
            'quantity' => $quantity,
            'product_id' => $product_id
        ]);

        success_response("Inventory updated successfully for product ID: " . $product_id);
    } catch (PDOException $e) {
        error_response("Failed to update inventory: " . $e->getMessage(), null, 500);
    }
} else {
    error_response("Method Not Allowed", null, 405);
}
