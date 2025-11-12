<?php

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../utils/response.php';
require_once __DIR__ . '/../../utils/auth.php';

set_json_headers();

// Allow sales_manager, production_manager, and admin to delete products
require_role(['sales_manager', 'production_manager', 'admin']);

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { // Using POST for delete to include a body
    error_response('Invalid request method.', null, 405);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (empty($input['id'])) {
    error_response('Product ID is required.', null, 400);
    exit;
}

$id = trim($input['id']);

try {
    $pdo = get_db_connection();

    $stmt = $pdo->prepare("DELETE FROM products WHERE id = :id");
    $stmt->execute([':id' => $id]);

    if ($stmt->rowCount() === 0) {
        error_response('Product not found.', null, 404);
        exit;
    }

    success_response('Product deleted successfully');
} catch (PDOException $e) {
    error_log('Database error deleting product: ' . $e->getMessage());
    error_response('A database error occurred.', null, 500);
} catch (Exception $e) {
    error_log('Error deleting product: ' . $e->getMessage());
    error_response($e->getMessage(), null, $e->getCode() ?: 500);
}
