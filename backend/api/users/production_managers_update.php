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

    // Ensure the target user is a production_manager
    $check = $pdo->prepare("SELECT id FROM users WHERE id = :user_id AND role = 'production_manager'");
    $check->execute([':user_id' => $user_id]);
    if (!$check->fetch()) {
        error_response('Production manager not found.', null, 404);
        exit;
    }

    $fields = [];
    $params = [':user_id' => $user_id];

    if (isset($input['username']) || isset($input['name'])) {
        // map frontend username/name -> DB full_name
        $fields[] = 'full_name = :full_name';
        $params[':full_name'] = trim($input['username'] ?? $input['name']);
    }
    if (isset($input['email'])) {
        $email = filter_var($input['email'], FILTER_SANITIZE_EMAIL);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            error_response('Invalid email format.', null, 400);
            exit;
        }
        $fields[] = 'email = :email';
        $params[':email'] = $email;
    }
    if (isset($input['phone'])) {
        $fields[] = 'phone = :phone';
        $params[':phone'] = trim($input['phone']);
    }
    if (isset($input['lga']) || isset($input['city'])) {
        // map frontend lga -> DB city
        $fields[] = 'city = :city';
        $params[':city'] = trim($input['lga'] ?? $input['city']);
    }
    if (isset($input['state'])) {
        $fields[] = 'state = :state';
        $params[':state'] = trim($input['state']);
    }
    if (!empty($input['password'])) {
        $password_hash = password_hash($input['password'], PASSWORD_DEFAULT);
        $fields[] = 'password_hash = :password_hash';
        $params[':password_hash'] = $password_hash;
    }

    if (empty($fields)) {
        error_response('No fields to update.', null, 400);
        exit;
    }

    $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    if ($stmt->rowCount() > 0) {
        success_response('Production manager updated successfully');
    } else {
        success_response('No changes made.', null, 200);
    }
} catch (PDOException $e) {
    if ($e->errorInfo[1] == 1062) {
        error_response('A user with this username or email already exists.', null, 409);
    } else {
        error_log('Database error updating production manager: ' . $e->getMessage());
        error_response('A database error occurred.', null, 500);
    }
} catch (Exception $e) {
    error_log('Error updating production manager: ' . $e->getMessage());
    error_response($e->getMessage(), null, 500);
}
