<?php

/**
 * Sales Report Endpoint
 * Proxies requests to the Python microservice for report generation
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../utils/response.php';
require_once __DIR__ . '/../../utils/auth.php';

set_json_headers();

// Only admins can generate reports
require_role(['admin']);

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    error_response('Invalid request method.', null, 405);
    exit;
}

try {
    // Get query parameters
    $start_date = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
    $end_date = $_GET['end_date'] ?? date('Y-m-d');
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;

    // Build Python microservice URL
    $python_service_url = 'http://127.0.0.1:5001/api/reports/sales';
    $query_params = http_build_query([
        'start_date' => $start_date,
        'end_date' => $end_date,
        'limit' => $limit
    ]);
    $url = $python_service_url . '?' . $query_params;

    // Initialize cURL
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json'
    ]);

    // Execute request
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    curl_close($ch);

    // Check for cURL errors
    if ($response === false) {
        error_log("cURL Error: " . $curl_error);
        error_response('Unable to connect to reporting service. Please ensure the Python microservice is running.', null, 503);
        exit;
    }

    // Check HTTP status
    if ($http_code !== 200) {
        error_log("Python service returned HTTP $http_code: $response");
        error_response('Reporting service returned an error.', null, $http_code);
        exit;
    }

    // Decode and validate response
    $data = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("JSON decode error: " . json_last_error_msg());
        error_response('Invalid response from reporting service.', null, 500);
        exit;
    }

    // Return the data from Python service
    if (isset($data['success']) && $data['success'] === true) {
        success_response($data['message'] ?? 'Report generated successfully', $data['data'] ?? null);
    } else {
        error_response($data['message'] ?? 'Failed to generate report', null, 500);
    }
} catch (Exception $e) {
    error_log('Error in get_sales_report.php: ' . $e->getMessage());
    error_response('An error occurred while generating the report.', null, 500);
}
