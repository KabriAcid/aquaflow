<?php

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../utils/response.php';
require_once __DIR__ . '/../../utils/auth.php';

session_start();
set_json_headers();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    error_response('Invalid request method.', null, 405);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    error_response('User not authenticated.', null, 401);
    exit;
}

$user_id = $_SESSION['user_id'];
$input = json_decode(file_get_contents('php://input'), true);

try {
    $pdo = get_db_connection();
    
    // Handle email update
    if (isset($input['email'])) {
        $email = filter_var($input['email'], FILTER_SANITIZE_EMAIL);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            error_response('Invalid email format.', null, 400);
            exit;
        }

        $stmt = $pdo->prepare("UPDATE users SET email = :email WHERE user_id = :user_id");
        $stmt->execute([':email' => $email, ':user_id' => $user_id]);
    }

    // Handle password update
    if (!empty($input['current_password']) && !empty($input['new_password'])) {
        if ($input['new_password'] !== $input['confirm_password']) {
            error_response('New passwords do not match.', null, 400);
            exit;
        }

        $stmt = $pdo->prepare("SELECT password_hash FROM users WHERE user_id = :user_id");
        $stmt->execute([':user_id' => $user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || !password_verify($input['current_password'], $user['password_hash'])) {
            error_response('Incorrect current password.', null, 403);
            exit;
        }

        $new_password_hash = password_hash($input['new_password'], PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password_hash = :password_hash WHERE user_id = :user_id");
        $stmt->execute([':password_hash' => $new_password_hash, ':user_id' => $user_id]);
    }

    success_response('Profile updated successfully');

} catch (PDOException $e) {
    error_log('Database error updating profile: ' . $e->getMessage());
    error_response('A database error occurred.', null, 500);
} catch (Exception $e) {
    error_log('Error updating profile: ' . $e->getMessage());
    error_response($e->getMessage(), null, 500);
}
