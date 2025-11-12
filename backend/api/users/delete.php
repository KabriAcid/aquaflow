<?php

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../utils/response.php';
require_once __DIR__ . '/../../utils/auth.php';

set_json_headers();
require_role(['admin']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    error_response('Invalid request method.', null, 405);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (empty($input['id'])) {
    error_response('Missing required field: id.', null, 400);
    exit;
}

$user_id = $input['id'];

try {
    $pdo = get_db_connection();
    
    // First get the user details to verify it exists
    $selectStmt = $pdo->prepare("SELECT id, full_name FROM users WHERE id = :id");
    $selectStmt->execute([':id' => $user_id]);
    $user = $selectStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        error_response('User not found.', null, 404);
        exit;
    }
    
    // Delete the user
    $deleteStmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
    $deleteStmt->execute([':id' => $user_id]);
    
    success_response('User deleted successfully', [
        'deleted_user_id' => $user_id,
        'deleted_user_name' => $user['full_name']
    ]);
} catch (PDOException $e) {
    error_log('Database error deleting user: ' . $e->getMessage());
    error_response('A database error occurred.', null, 500);
} catch (Exception $e) {
    error_log('Error deleting user: ' . $e->getMessage());
    error_response($e->getMessage(), null, 500);
}
