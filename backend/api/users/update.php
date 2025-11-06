<?php

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../utils/response.php';
require_once __DIR__ . '/../../utils/auth.php';

set_json_headers();
require_role(['admin']); // Only admins can update user profiles

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    error_response('Invalid request method.', null, 405);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (empty($input['user_id'])) {
    error_response('Missing required field: user_id.', null, 400);
    exit;
}

$user_id = $input['user_id'];

try {
    $pdo = get_db_connection();

    $fields = [];
    $params = [':user_id' => $user_id];

    if (isset($input['username'])) {
        // map `username` field expected by front-end to `full_name` in DB
        $fields[] = 'full_name = :full_name';
        $params[':full_name'] = trim($input['username']);
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
    if (isset($input['role'])) {
        if (!in_array($input['role'], ['customer', 'sales_manager', 'admin'])) {
            error_response('Invalid role specified.', null, 400);
            exit;
        }
        $fields[] = 'role = :role';
        $params[':role'] = $input['role'];
    }
    if (isset($input['state'])) {
        $fields[] = 'state = :state';
        $params[':state'] = trim($input['state']);
    }
    if (isset($input['lga'])) {
        // frontend may provide `lga`; map to DB `city`
        $fields[] = 'city = :city';
        $params[':city'] = trim($input['lga']);
    }
    if (isset($input['phone'])) {
        $fields[] = 'phone = :phone';
        $params[':phone'] = trim($input['phone']);
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
        success_response('User updated successfully');
    } else {
        success_response('No changes made.', null, 200);
    }
} catch (PDOException $e) {
    if ($e->errorInfo[1] == 1062) { // Duplicate entry
        error_response('A user with this username or email already exists.', null, 409);
    } else {
        error_log('Database error updating user: ' . $e->getMessage());
        error_response('A database error occurred.', null, 500);
    }
} catch (Exception $e) {
    error_log('Error updating user: ' . $e->getMessage());
    error_response($e->getMessage(), null, 500);
}
