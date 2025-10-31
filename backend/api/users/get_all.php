<?php
// backend/api/users/get_all.php

include_once '../../config/database.php';
include_once '../../utils/response.php';
include_once '../../utils/auth.php';

// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$conn = connect_db();

// Require admin role
require_role('admin');

// Get all users
$query = "SELECT id, name, email, role FROM users";
$stmt = $conn->prepare($query);
$stmt->execute();

$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($users) > 0) {
    success_response($users);
} else {
    success_response([]);
}
