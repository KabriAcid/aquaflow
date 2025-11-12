<?php

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../utils/response.php';
require_once __DIR__ . '/../../utils/auth.php';

set_json_headers();

// Allow sales_manager, production_manager, and admin to create products
require_role(['sales_manager', 'production_manager', 'admin']);

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    error_response('Invalid request method.', null, 405);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (empty($input['name']) || !isset($input['unit_price'])) {
    error_response('Missing required fields: name, unit_price.', null, 400);
    exit;
}

$name = trim($input['name']);
$description = isset($input['description']) ? trim($input['description']) : '';
$category = isset($input['category']) ? trim($input['category']) : 'beverage';
$size = isset($input['size']) ? trim($input['size']) : null;
$volume = isset($input['volume']) ? trim($input['volume']) : null;
$unit_price = filter_var($input['unit_price'], FILTER_VALIDATE_FLOAT);
$minimum_order_quantity = isset($input['minimum_order_quantity']) ? filter_var($input['minimum_order_quantity'], FILTER_VALIDATE_INT) : 1;
$image_url = isset($input['image_url']) ? trim($input['image_url']) : 'default.png';

if ($unit_price === false || $unit_price < 0) {
    error_response('Invalid unit price.', null, 400);
    exit;
}
if ($minimum_order_quantity === false || $minimum_order_quantity < 1) {
    $minimum_order_quantity = 1;
}
if (!empty($image_url) && $image_url !== 'default.png' && !filter_var($image_url, FILTER_VALIDATE_URL) && !file_exists(__DIR__ . '/../../uploads/products/' . $image_url)) {
    error_response('Invalid image URL.', null, 400);
    exit;
}
if (!in_array($category, ['bottled_water', 'beverage', 'package'])) {
    $category = 'beverage';
}

try {
    $pdo = get_db_connection();

    $stmt = $pdo->prepare(
        "INSERT INTO products (name, category, size, volume, unit_price, minimum_order_quantity, description, image_url, status) 
         VALUES (:name, :category, :size, :volume, :unit_price, :minimum_order_quantity, :description, :image_url, 'active')"
    );

    $stmt->execute([
        ':name' => $name,
        ':category' => $category,
        ':size' => $size,
        ':volume' => $volume,
        ':unit_price' => $unit_price,
        ':minimum_order_quantity' => $minimum_order_quantity,
        ':description' => $description,
        ':image_url' => $image_url
    ]);

    $new_product_id = $pdo->lastInsertId();

    success_response('Product created successfully', ['id' => $new_product_id], 201);
} catch (PDOException $e) {
    if ($e->errorInfo[1] == 1062) { // Duplicate entry
        error_response('A product with this name already exists.', null, 409);
    } else {
        error_log('Database error creating product: ' . $e->getMessage());
        error_response('A database error occurred.', null, 500);
    }
} catch (Exception $e) {
    error_log('Error creating product: ' . $e->getMessage());
    error_response($e->getMessage(), null, $e->getCode() ?: 500);
}
