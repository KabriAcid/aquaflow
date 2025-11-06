<?php

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../utils/response.php';
require_once __DIR__ . '/../../utils/auth.php';

set_json_headers();
require_role(['admin']);

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    error_response('Invalid request method.', null, 405);
    exit;
}

$user_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : null;

try {
    $pdo = get_db_connection();

    if ($user_id) {
        $stmt = $pdo->prepare("SELECT user_id, username AS name, email, phone, state, lga, role, created_at FROM users WHERE user_id = :user_id AND role = 'production_manager' LIMIT 1");
        $stmt->execute([':user_id' => $user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$user) {
            error_response('Production manager not found.', null, 404);
            exit;
        }
        success_response('Production manager fetched', $user);
        exit;
    }

    $stmt = $pdo->query("SELECT user_id, username AS name, email, phone, created_at FROM users WHERE role = 'production_manager' ORDER BY created_at DESC");
    $managers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    success_response('Production managers fetched', $managers);
} catch (PDOException $e) {
    error_log('Database error fetching production managers: ' . $e->getMessage());
    error_response('A database error occurred.', null, 500);
} catch (Exception $e) {
    error_log('Error fetching production managers: ' . $e->getMessage());
    error_response($e->getMessage(), null, 500);
}
