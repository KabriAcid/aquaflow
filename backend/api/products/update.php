<?php

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../utils/response.php';
require_once __DIR__ . '/../../utils/auth.php';

set_json_headers();

// Allow admin, sales_manager, and production_manager to update products
require_role(['admin', 'sales_manager', 'production_manager']);

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    error_response('Invalid request method.', null, 405);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (empty($input['id'])) {
    error_response('Missing required field: id.', null, 400);
    exit;
}

$product_id = trim($input['id']);

try {
    $pdo = get_db_connection();

    // Fetch the existing product to ensure it exists
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
    $stmt->execute([':id' => $product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        error_response('Product not found.', null, 404);
        exit;
    }

    $fields = [];
    $params = [':id' => $product_id];

    if (isset($input['name'])) {
        $fields[] = 'name = :name';
        $params[':name'] = trim($input['name']);
    }
    if (isset($input['category'])) {
        $fields[] = 'category = :category';
        $params[':category'] = trim($input['category']);
    }
    if (isset($input['product_type'])) {
        $fields[] = 'product_type = :product_type';
        $params[':product_type'] = trim($input['product_type']);
    }
    if (isset($input['size'])) {
        $fields[] = 'size = :size';
        $params[':size'] = trim($input['size']);
    }
    if (isset($input['volume'])) {
        $fields[] = 'volume = :volume';
        $params[':volume'] = trim($input['volume']);
    }
    if (isset($input['unit_price'])) {
        $price = filter_var($input['unit_price'], FILTER_VALIDATE_FLOAT);
        if ($price === false || $price < 0) {
            error_response('Invalid unit price.', null, 400);
            exit;
        }
        $fields[] = 'unit_price = :unit_price';
        $params[':unit_price'] = $price;
    }
    if (isset($input['minimum_order_quantity'])) {
        $moq = filter_var($input['minimum_order_quantity'], FILTER_VALIDATE_INT);
        if ($moq === false || $moq < 1) {
            error_response('Invalid minimum order quantity.', null, 400);
            exit;
        }
        $fields[] = 'minimum_order_quantity = :minimum_order_quantity';
        $params[':minimum_order_quantity'] = $moq;
    }
    if (isset($input['description'])) {
        $fields[] = 'description = :description';
        $params[':description'] = trim($input['description']);
    }
    if (isset($input['image_url'])) {
        $fields[] = 'image_url = :image_url';
        $params[':image_url'] = trim($input['image_url']) ?: 'default.png';
    }

    if (empty($fields)) {
        error_response('No fields to update.', null, 400);
        exit;
    }

    $sql = "UPDATE products SET " . implode(', ', $fields) . " WHERE id = :id";
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
