<?php

/*
 * Session-based login endpoint
 * Expects JSON body: { email, password, remember } or form fields.
 * On success: starts session and sets only $_SESSION['user_id'].
 * The API response returns only the user id so dashboard pages can query the users table.
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../utils/response.php';

set_json_headers();

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// Read input
$raw = file_get_contents('php://input');
$data = [];
if ($raw) {
    $decoded = json_decode($raw, true);
    if (is_array($decoded)) $data = $decoded;
}
if (empty($data)) $data = $_POST;

$email = trim(strtolower($data['email'] ?? ''));
$password = $data['password'] ?? '';
$remember = !empty($data['remember']);

$errors = [];
if ($email === '') $errors['email'] = 'Email is required';
if ($password === '') $errors['password'] = 'Password is required';
if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Invalid email';
if (!empty($errors)) {
    error_response('Validation failed', $errors, 422);
}

try {
    $pdo = get_db_connection();

    $stmt = $pdo->prepare('SELECT id, full_name, email, password_hash, role, status FROM users WHERE email = :email LIMIT 1');
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch();
    if (!$user) {
        error_response('Invalid credentials', null, 401);
    }

    if ($user['status'] !== 'active') {
        error_response('Account not active', null, 403);
    }

    if (!password_verify($password, $user['password_hash'])) {
        error_response('Invalid credentials', null, 401);
    }

    // Start session and set session variables
    // If remember requested, extend cookie lifetime (e.g., 30 days)
    if ($remember) {
        $lifetime = 60 * 60 * 24 * 30; // 30 days
        session_set_cookie_params(['lifetime' => $lifetime, 'path' => '/', 'httponly' => true, 'samesite' => 'Lax']);
    } else {
        session_set_cookie_params(['lifetime' => 0, 'path' => '/', 'httponly' => true, 'samesite' => 'Lax']);
    }

    if (session_status() === PHP_SESSION_NONE) session_start();
    session_regenerate_id(true);

    // Determine role and store a role-specific session key (e.g. customer_id, admin_id)
    $role = strtolower(trim($user['role'] ?? 'customer'));
    // sanitize role to safe identifier (letters, numbers, underscore)
    $roleKey = preg_replace('/[^a-z0-9_]/', '', $role) . '_id';

    // Store only a role-scoped id in session and keep the role name so we can later destroy the right key
    $_SESSION[$roleKey] = (int)$user['id'];
    $_SESSION['user_role'] = $role;

    // Return success with the user id and role so the frontend can perform a role-based redirect
    $payload = ['id' => (int)$user['id'], 'role' => $role];
    success_response('Login successful', $payload);
} catch (PDOException $ex) {
    error_log('Login error: ' . $ex->getMessage());
    error_response('Server error during login', null, 500);
}
