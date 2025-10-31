<?php
header('Content-Type: application/json');
session_start();

require_once '../config.php';

// Basic security check - ensure user is logged in as an admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Database connection
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => "Database connection failed: " . $e->getMessage()]);
    exit;
}

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

switch ($action) {

    // PRODUCT ACTIONS
    case 'get_products':
        try {
            $stmt = $pdo->query("SELECT id, name, price, stock FROM products ORDER BY name");
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'data' => $products]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Query failed: ' . $e->getMessage()]);
        }
        break;

    case 'get_product':
        try {
            $productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
            $stmt->execute([$productId]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'data' => $product]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Query failed: ' . $e->getMessage()]);
        }
        break;

    case 'add_product':
        try {
            $stmt = $pdo->prepare("INSERT INTO products (name, price, stock) VALUES (?, ?, ?)");
            $stmt->execute([$_POST['product-name'], $_POST['product-price'], $_POST['product-stock']]);
            echo json_encode(['success' => true, 'message' => 'Product added successfully']);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Query failed: ' . $e->getMessage()]);
        }
        break;

    case 'update_product':
        try {
            $stmt = $pdo->prepare("UPDATE products SET name = ?, price = ?, stock = ? WHERE id = ?");
            $stmt->execute([$_POST['product-name'], $_POST['product-price'], $_POST['product-stock'], $_POST['id']]);
            echo json_encode(['success' => true, 'message' => 'Product updated successfully']);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Query failed: ' . $e->getMessage()]);
        }
        break;

    case 'delete_product':
        try {
            $productId = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
            $stmt->execute([$productId]);
            echo json_encode(['success' => true, 'message' => 'Product deleted successfully']);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Query failed: ' . $e->getMessage()]);
        }
        break;

    // CUSTOMER ACTIONS
    case 'get_customers':
        try {
            $searchTerm = isset($_GET['search']) ? '%' . $_GET['search'] . '%' : '%';
            $stmt = $pdo->prepare("SELECT id, name, email, created_at FROM users WHERE role = 'customer' AND (name LIKE ? OR email LIKE ?) ORDER BY name");
            $stmt->execute([$searchTerm, $searchTerm]);
            $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'data' => $customers]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Query failed: ' . $e->getMessage()]);
        }
        break;

    case 'get_customer_details':
        try {
            $customerId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            $stmt = $pdo->prepare("SELECT id, name, email, created_at FROM users WHERE id = ? AND role = 'customer'");
            $stmt->execute([$customerId]);
            $customer = $stmt->fetch(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'data' => $customer]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Query failed: ' . $e->getMessage()]);
        }
        break;

    case 'get_customer_orders':
        try {
            $customerId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            $stmt = $pdo->prepare("SELECT id, created_at, total_amount, status FROM orders WHERE user_id = ? ORDER BY created_at DESC");
            $stmt->execute([$customerId]);
            $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'data' => $orders]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Query failed: ' . $e->getMessage()]);
        }
        break;

    // ORDER ACTIONS
    case 'get_orders':
        try {
            $searchTerm = isset($_GET['search']) ? '%' . $_GET['search'] . '%' : '%';
            $statusFilter = isset($_GET['status']) ? $_GET['status'] : 'all';

            $sql = "SELECT o.id, u.name as customer_name, o.created_at, o.total_amount, o.status FROM orders o JOIN users u ON o.user_id = u.id WHERE (u.name LIKE ? OR o.id LIKE ?)";
            $params = [$searchTerm, $searchTerm];

            if ($statusFilter !== 'all') {
                $sql .= " AND o.status = ?";
                $params[] = $statusFilter;
            }

            $sql .= " ORDER BY o.created_at DESC";

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'data' => $orders]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Query failed: ' . $e->getMessage()]);
        }
        break;

    case 'get_order_details':
        try {
            $orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            
            // Fetch main order details
            $orderStmt = $pdo->prepare("SELECT o.id, o.created_at, o.total_amount, o.status, o.shipping_address, u.name as customer_name, u.email as customer_email FROM orders o JOIN users u ON o.user_id = u.id WHERE o.id = ?");
            $orderStmt->execute([$orderId]);
            $order = $orderStmt->fetch(PDO::FETCH_ASSOC);

            if (!$order) {
                echo json_encode(['success' => false, 'message' => 'Order not found']);
                exit;
            }

            // Fetch order items
            $itemsStmt = $pdo->prepare("SELECT p.name, oi.quantity, oi.price FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
            $itemsStmt->execute([$orderId]);
            $items = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);

            $order['customer'] = [
                'name' => $order['customer_name'],
                'email' => $order['customer_email'],
            ];
            $order['items'] = $items;

            echo json_encode(['success' => true, 'data' => $order]);

        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Query failed: ' . $e->getMessage()]);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}
