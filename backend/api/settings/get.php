<?php

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../utils/response.php';
require_once __DIR__ . '/../../utils/auth.php';

set_json_headers();
require_role(['admin']);

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    error_response('Invalid request method.', null, 405);
    exit;
}

try {
    $pdo = get_db_connection();
    $stmt = $pdo->query("SELECT setting_key, setting_value FROM settings");
    $settings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $settingsArray = [];
    foreach ($settings as $setting) {
        $settingsArray[$setting['setting_key']] = $setting['setting_value'];
    }

    success_response('Settings fetched successfully', $settingsArray);
} catch (PDOException $e) {
    error_log('Database error fetching settings: ' . $e->getMessage());
    error_response('A database error occurred.', null, 500);
} catch (Exception $e) {
    error_log('Error fetching settings: ' . $e->getMessage());
    error_response($e->getMessage(), null, 500);
}
