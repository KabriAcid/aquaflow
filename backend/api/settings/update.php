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

try {
    $pdo = get_db_connection();

    // Mapping of input keys to setting_key in database
    $settingMappings = [
        'company_name' => 'company_name',
        'company_email' => 'company_email',
        'company_phone' => 'company_phone',
        'company_address' => 'company_address',
        'delivery_fee' => 'delivery_fee',
        'minimum_order' => 'minimum_order_amount'
    ];

    $updatedSettings = [];

    foreach ($settingMappings as $inputKey => $dbKey) {
        if (isset($input[$inputKey])) {
            $value = $input[$inputKey];

            // Try to update existing setting
            $stmt = $pdo->prepare("UPDATE settings SET setting_value = :value WHERE setting_key = :key");
            $stmt->execute([
                ':value' => $value,
                ':key' => $dbKey
            ]);

            // If no rows were updated, insert new setting
            if ($stmt->rowCount() === 0) {
                $insertStmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value, setting_type) VALUES (:key, :value, :type)");
                $insertStmt->execute([
                    ':key' => $dbKey,
                    ':value' => $value,
                    ':type' => 'general'
                ]);
            }

            $updatedSettings[$inputKey] = $value;
        }
    }

    success_response('Settings updated successfully', $updatedSettings);
} catch (PDOException $e) {
    error_log('Database error updating settings: ' . $e->getMessage());
    error_response('A database error occurred.', null, 500);
} catch (Exception $e) {
    error_log('Error updating settings: ' . $e->getMessage());
    error_response($e->getMessage(), null, 500);
}
