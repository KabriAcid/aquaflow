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

if (empty($input['user_id'])) {
    error_response('Missing required field: user_id.', null, 400);
    exit;
}

$user_id = (int)$input['user_id'];

try {
    $pdo = get_db_connection();

    // Only delete if the role is production_manager to avoid accidental deletions
    $stmt = $pdo->prepare("DELETE FROM users WHERE user_id = :user_id AND role = 'production_manager'");
    $stmt->execute([':user_id' => $user_id]);

    if ($stmt->rowCount() > 0) {
        success_response('Production manager deleted successfully');
    } else {
        error_response('Production manager not found or could not be deleted.', null, 404);
    }
} catch (PDOException $e) {
    error_log('Database error deleting production manager: ' . $e->getMessage());
    error_response('A database error occurred.', null, 500);
} catch (Exception $e) {
    error_log('Error deleting production manager: ' . $e->getMessage());
    error_response($e->getMessage(), null, 500);
}
