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

$username = trim($input['username'] ?? $input['name'] ?? '');
$email = trim($input['email'] ?? '');
$password = $input['password'] ?? '';
$phone = trim($input['phone'] ?? '');
// map city/lga
$city = trim($input['city'] ?? $input['lga'] ?? '');
// map state if provided
$state = trim($input['state'] ?? '');

if (empty($username) || empty($email) || empty($password)) {
    error_response('Missing required fields: username, email, password.', null, 400);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    error_response('Invalid email format.', null, 400);
    exit;
}

$password_hash = password_hash($password, PASSWORD_DEFAULT);

try {
    $pdo = get_db_connection();
    // Insert using current schema: full_name, email, password_hash, role, phone, city, state
    $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password_hash, role, phone, city, state) VALUES (:full_name, :email, :password_hash, 'production_manager', :phone, :city, :state)");
    $stmt->execute([
        ':full_name' => $username,
        ':email' => $email,
        ':password_hash' => $password_hash,
        ':phone' => $phone,
        ':city' => $city,
        ':state' => $state
    ]);

    $user_id = $pdo->lastInsertId();
    $newUser = [
        'user_id' => $user_id,
        'name' => $username,
        'email' => $email,
        'phone' => $phone,
        'city' => $city,
        'state' => $state
    ];
    success_response('Production manager created successfully', $newUser, 201);
} catch (PDOException $e) {
    if ($e->errorInfo[1] == 1062) {
        error_response('A user with this username or email already exists.', null, 409);
    } else {
        error_log('Database error creating production manager: ' . $e->getMessage());
        error_response('A database error occurred.', null, 500);
    }
} catch (Exception $e) {
    error_log('Error creating production manager: ' . $e->getMessage());
    error_response($e->getMessage(), null, 500);
}
