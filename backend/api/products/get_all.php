<?php
// backend/api/products/get_all.php

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../utils/response.php';

try {
    $pdo = get_db_connection();

    $stmt = $pdo->prepare(
        "SELECT p.id, p.name, p.category, p.size, p.volume, p.unit_price, p.minimum_order_quantity, p.description, p.image_url, p.status, IFNULL(i.current_stock, 0) AS current_stock
		 FROM products p
		 LEFT JOIN inventory i ON i.product_id = p.id
		 WHERE p.status = 'active'
		 ORDER BY p.created_at DESC"
    );

    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    success_response('Products retrieved', $products);
} catch (Exception $e) {
    error_response('Failed to fetch products', ['exception' => $e->getMessage()], 500);
}
