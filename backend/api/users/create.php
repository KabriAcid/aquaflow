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

$full_name = trim($input['full_name'] ?? '');
$email = trim($input['email'] ?? '');
$password = $input['password'] ?? '';
$state = trim($input['state'] ?? '');
$city = trim($input['city'] ?? '');
$phone = trim($input['phone'] ?? '');
$status = $input['status'] ?? 'active';
$role = trim($input['role'] ?? 'customer'); // Allow role to be specified, default to 'customer'

if (empty($full_name) || empty($email) || empty($password)) {
    error_response('Missing required fields: full_name, email, password.', null, 400);
    exit;
}

// Validate role
$allowed_roles = ['customer', 'sales_manager', 'production_manager', 'admin'];
if (!in_array($role, $allowed_roles)) {
    error_response('Invalid role specified.', null, 400);
    exit;
}

$password_hash = password_hash($password, PASSWORD_DEFAULT);

try {
    $pdo = get_db_connection();
    $stmt = $pdo->prepare(
        "INSERT INTO users (full_name, email, password_hash, role, city, state, phone, status) VALUES (:full_name, :email, :password_hash, :role, :city, :state, :phone, :status)"
    );
    $stmt->execute([
        ':full_name' => $full_name,
        ':email' => $email,
        ':password_hash' => $password_hash,
        ':role' => $role,
        ':city' => $city,
        ':state' => $state,
        ':phone' => $phone,
        ':status' => $status
    ]);

    $user_id = $pdo->lastInsertId();
    $newUser = [
        'id' => $user_id,
        'full_name' => $full_name,
        'email' => $email,
        'role' => $role,
        'city' => $city,
        'state' => $state,
        'phone' => $phone,
        'status' => $status
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
