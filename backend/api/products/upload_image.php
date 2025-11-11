<?php
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../utils/response.php';
require_once __DIR__ . '/../../utils/auth.php';

set_json_headers();

// Allow sales_manager to upload images
require_role(['sales_manager', 'admin', 'production_manager']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    error_response('Invalid request method. POST required.', null, 405);
    exit;
}

// Check if file was uploaded
if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    error_response('No image file uploaded or upload error occurred.', null, 400);
    exit;
}

$file = $_FILES['image'];

// Validate file type
$allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$file_type = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

if (!in_array($file_type, $allowed_types)) {
    error_response('Invalid file type. Only JPEG, PNG, GIF, and WebP images are allowed.', null, 400);
    exit;
}

// Validate file size (max 5MB)
$max_size = 5 * 1024 * 1024; // 5MB
if ($file['size'] > $max_size) {
    error_response('File size exceeds 5MB limit.', null, 400);
    exit;
}

try {
    // Create upload directory if it doesn't exist
    $upload_dir = __DIR__ . '/../../../frontend/uploads/products/';
    if (!is_dir($upload_dir)) {
        if (!mkdir($upload_dir, 0755, true)) {
            throw new Exception('Failed to create upload directory');
        }
    }

    // Generate unique filename with hash
    $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $hash_name = bin2hex(random_bytes(16)) . '.' . $file_extension;
    $file_path = $upload_dir . $hash_name;

    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $file_path)) {
        throw new Exception('Failed to move uploaded file');
    }

    // Set proper permissions
    chmod($file_path, 0644);

    // Return success with relative URL
    $relative_url = '/aquaflow/frontend/uploads/products/' . $hash_name;
    success_response('Image uploaded successfully', [
        'filename' => $hash_name,
        'image_url' => $relative_url,
        'size' => filesize($file_path)
    ], 200);
} catch (Exception $e) {
    error_log('Image upload error: ' . $e->getMessage());
    error_response('An error occurred while uploading the image: ' . $e->getMessage(), null, 500);
}
