<?php
/*
 * Registration endpoint using shared DB and response helpers
 * - Uses session-based auth: on successful registration user session will be started and user_id set
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../utils/response.php';

set_json_headers();

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// Read input (JSON preferred)
$raw = file_get_contents('php://input');
$data = [];
if ($raw) {
    $decoded = json_decode($raw, true);
    if (is_array($decoded)) $data = $decoded;
}
if (empty($data)) $data = $_POST;

// Basic validation
$required = ['full_name', 'email', 'password'];
$errors = [];
foreach ($required as $f) {
    if (empty($data[$f]) || trim($data[$f]) === '') {
        $errors[$f] = ucfirst(str_replace('_', ' ', $f)) . ' is required';
    }
}
if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'Invalid email format';
}
if (!empty($data['password']) && strlen($data['password']) < 8) {
    $errors['password'] = 'Password must be at least 8 characters';
}
if (!empty($errors)) {
    error_response('Validation failed', $errors, 422);
}

$full_name = trim($data['full_name']);
$email = strtolower(trim($data['email']));
$phone = trim($data['phone'] ?? '');
$address = trim($data['address'] ?? '');
$city = trim($data['city'] ?? '');
$state = trim($data['state'] ?? '');
$postal_code = trim($data['postal_code'] ?? '');
$password = $data['password'];

try {
    $pdo = get_db_connection();

    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
    $stmt->execute([':email' => $email]);
    if ($stmt->fetch()) {
        error_response('Email already registered', ['email' => 'Email already in use'], 409);
    }

    $password_hash = password_hash($password, PASSWORD_BCRYPT);

    $insert = $pdo->prepare('INSERT INTO users (full_name, email, phone, password_hash, role, status, address, city, state, postal_code, created_at, updated_at) VALUES (:full_name, :email, :phone, :password_hash, :role, :status, :address, :city, :state, :postal_code, NOW(), NOW())');

    $insert->execute([
        ':full_name' => $full_name,
        ':email' => $email,
        ':phone' => $phone,
        ':password_hash' => $password_hash,
        ':role' => 'customer',
        ':status' => 'active',
        ':address' => $address,
        ':city' => $city,
        ':state' => $state,
        ':postal_code' => $postal_code,
    ]);

    $userId = $pdo->lastInsertId();


    success_response('Registration successful', ['id' => $userId, 'email' => $email], 201);
} catch (PDOException $ex) {
    error_log('Register error: ' . $ex->getMessage());
    error_response('Server error while creating account', null, 500);
}
