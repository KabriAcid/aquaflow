<?php

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../utils/response.php';
require_once __DIR__ . '/../../utils/auth.php';

set_json_headers();

// Only sales_manager can update products
require_role(['sales_manager']);

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    error_response('Invalid request method.', null, 405);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (empty($input['id']) || empty($input['name']) || empty($input['description']) || !isset($input['unit_price']) || !isset($input['current_stock'])) {
    error_response('Missing required fields: id, name, description, unit_price, current_stock.', null, 400);
    exit;
}

$id = trim($input['id']);
$name = trim($input['name']);
$description = trim($input['description']);
$unit_price = filter_var($input['unit_price'], FILTER_VALIDATE_FLOAT);
$current_stock = filter_var($input['current_stock'], FILTER_VALIDATE_INT);
$image_url = isset($input['image_url']) ? trim($input['image_url']) : null;

if ($unit_price === false || $unit_price < 0) {
    error_response('Invalid unit price.', null, 400);
    exit;
}
if ($current_stock === false || $current_stock < 0) {
    error_response('Invalid stock quantity.', null, 400);
    exit;
}
if (!empty($image_url) && !filter_var($image_url, FILTER_VALIDATE_URL)) {
    error_response('Invalid image URL.', null, 400);
    exit;
}

try {
    $pdo = get_db_connection();

    $stmt = $pdo->prepare(
        "UPDATE products SET name = :name, description = :description, unit_price = :unit_price, current_stock = :current_stock, image_url = :image_url WHERE id = :id"
    );

    $stmt->execute([
        ':id' => $id,
        ':name' => $name,
        ':description' => $description,
        ':unit_price' => $unit_price,
        ':current_stock' => $current_stock,
        ':image_url' => $image_url
    ]);

    if ($stmt->rowCount() === 0) {
        error_response('Product not found or no changes made.', null, 404);
        exit;
    }

    success_response('Product updated successfully');

} catch (PDOException $e) {
    if ($e->errorInfo[1] == 1062) { // Duplicate entry
        error_response('A product with this name already exists.', null, 409);
    } else {
        error_log('Database error updating product: ' . $e->getMessage());
        error_response('A database error occurred.', null, 500);
    }
} catch (Exception $e) {
    error_log('Error updating product: ' . $e->getMessage());
    error_response($e->getMessage(), null, $e->getCode() ?: 500);
}
