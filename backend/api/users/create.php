<?php
// backend/api/users/create.php

include_once '../../config/database.php';
include_once '../../utils/response.php';
include_once '../../utils/auth.php';

// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

$conn = connect_db();

// Require admin role
require_role('admin');

// Get raw posted data
$data = json_decode(file_get_contents("php://input"));

if (empty($data->name) || empty($data->email) || empty($data->password)) {
    error_response('Missing required fields');
}

// Create user
$query = "INSERT INTO users (name, email, password, role) VALUES (:name, :email, :password, :role)";
$stmt = $conn->prepare($query);

// Sanitize data
$name = htmlspecialchars(strip_tags($data->name));
$email = htmlspecialchars(strip_tags($data->email));
$password = password_hash($data->password, PASSWORD_BCRYPT);
$role = 'sales_manager'; // Default to sales_manager

// Bind data
$stmt->bindParam(':name', $name);
$stmt->bindParam(':email', $email);
$stmt->bindParam(':password', $password);
$stmt->bindParam(':role', $role);

if ($stmt->execute()) {
    success_response('User created successfully');
} else {
    error_response('User could not be created');
}
