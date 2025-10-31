<?php

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../utils/response.php';
require_once __DIR__ . '/../../utils/auth.php';

set_json_headers();

// Only sales_manager and admin should access inventory alerts
require_role(['sales_manager', 'admin']);

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

try {
    $pdo = get_db_connection();

    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 0;

    // Select only columns that exist in the products table per schema (no `sku` column)
    $sql = "SELECT p.id, p.name, p.category, p.size, p.volume, p.unit_price,
           IFNULL(i.current_stock,0) AS current_stock, IFNULL(i.minimum_stock_level,0) AS minimum_stock_level
        FROM products p
        LEFT JOIN inventory i ON i.product_id = p.id
        WHERE IFNULL(i.current_stock,0) <= IFNULL(i.minimum_stock_level,0)
        ORDER BY IFNULL(i.current_stock,0) ASC";

    if ($limit > 0) {
        $sql .= " LIMIT :limit";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
    } else {
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
    }

    $alerts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    success_response('Inventory alerts fetched', $alerts);
} catch (PDOException $e) {
    error_log('Database error fetching inventory alerts: ' . $e->getMessage());
    error_response('A database error occurred.', null, 500);
} catch (Exception $e) {
    error_log('Error fetching inventory alerts: ' . $e->getMessage());
    error_response($e->getMessage(), null, $e->getCode() ?: 500);
}
