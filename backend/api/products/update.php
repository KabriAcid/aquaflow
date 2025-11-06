<?php

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../utils/response.php';
require_once __DIR__ . '/../../utils/auth.php';

set_json_headers();

// Allow admin or sales_manager to update products
require_role(['admin', 'sales_manager']);

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    error_response('Invalid request method.', null, 405);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (empty($input['product_id'])) {
    error_response('Missing required field: product_id.', null, 400);
    exit;
}

$product_id = trim($input['product_id']);

try {
    $pdo = get_db_connection();
    
    // Fetch the existing product to ensure it exists
    $stmt = $pdo->prepare("SELECT * FROM products WHERE product_id = :product_id");
    $stmt->execute([':product_id' => $product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        error_response('Product not found.', null, 404);
        exit;
    }

    $fields = [];
    $params = [':product_id' => $product_id];

    if (isset($input['name'])) {
        $fields[] = 'name = :name';
        $params[':name'] = trim($input['name']);
    }
    if (isset($input['description'])) {
        $fields[] = 'description = :description';
        $params[':description'] = trim($input['description']);
    }
    if (isset($input['price'])) {
        $price = filter_var($input['price'], FILTER_VALIDATE_FLOAT);
        if ($price === false || $price < 0) {
            error_response('Invalid price.', null, 400);
            exit;
        }
        $fields[] = 'price = :price';
        $params[':price'] = $price;
    }
    if (isset($input['stock_quantity'])) {
        $stock = filter_var($input['stock_quantity'], FILTER_VALIDATE_INT);
        if ($stock === false || $stock < 0) {
            error_response('Invalid stock quantity.', null, 400);
            exit;
        }
        $fields[] = 'stock_quantity = :stock_quantity';
        $params[':stock_quantity'] = $stock;
    }

    if (empty($fields)) {
        error_response('No fields to update.', null, 400);
        exit;
    }

    $sql = "UPDATE products SET " . implode(', ', $fields) . " WHERE product_id = :product_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    if ($stmt->rowCount() === 0) {
        success_response('No changes made to the product.', null, 200);
    } else {
        success_response('Product updated successfully');
    }

} catch (PDOException $e) {
    error_log('Database error updating product: ' . $e->getMessage());
    error_response('A database error occurred.', null, 500);
} catch (Exception $e) {
    error_log('Error updating product: ' . $e->getMessage());
    error_response($e->getMessage(), null, $e->getCode() ?: 500);
}
