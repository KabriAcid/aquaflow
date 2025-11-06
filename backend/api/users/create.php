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

$username = trim($input['username'] ?? '');
$email = trim($input['email'] ?? '');
$password = $input['password'] ?? '';
$role = $input['role'] ?? 'customer';
$state = trim($input['state'] ?? '');
$lga = trim($input['lga'] ?? '');
$phone = trim($input['phone'] ?? '');

if (empty($username) || empty($email) || empty($password) || empty($role)) {
    error_response('Missing required fields: username, email, password, role.', null, 400);
    exit;
}

if (!in_array($role, ['customer', 'sales_manager', 'admin'])) {
    error_response('Invalid role specified.', null, 400);
    exit;
}

$password_hash = password_hash($password, PASSWORD_DEFAULT);

try {
    $pdo = get_db_connection();
    // Map to current schema: `full_name`, `email`, `password_hash`, `role`, `city`, `state`, `phone`
    $stmt = $pdo->prepare(
        "INSERT INTO users (full_name, email, password_hash, role, city, state, phone) VALUES (:full_name, :email, :password_hash, :role, :city, :state, :phone)"
    );
    $stmt->execute([
        ':full_name' => $username,
        ':email' => $email,
        ':password_hash' => $password_hash,
        ':role' => $role,
        ':city' => $lga,
        ':state' => $state,
        ':phone' => $phone
    ]);

    $user_id = $pdo->lastInsertId();
    $newUser = [
        'user_id' => $user_id,
        'full_name' => $username,
        'email' => $email,
        'role' => $role,
        'city' => $lga,
        'state' => $state,
        'phone' => $phone
    ];
    success_response('User created successfully', $newUser, 201);
} catch (PDOException $e) {
    if ($e->errorInfo[1] == 1062) { // Duplicate entry
        error_response('A user with this username or email already exists.', null, 409);
    } else {
        error_log('Database error creating user: ' . $e->getMessage());
        error_response('A database error occurred.', null, 500);
    }
} catch (Exception $e) {
    error_log('Error creating user: ' . $e->getMessage());
    error_response($e->getMessage(), null, 500);
}
