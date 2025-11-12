<?php

/**
 * Export Sales Report to CSV
 * Proxies requests to the Python microservice for CSV generation
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../utils/auth.php';

// Only admins can export reports
session_start();
$session = get_session_user();
$user_id = $session['id'];
$role = $session['role'];

if (!$user_id || $role !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Access denied']);
    exit;
}

try {
    // Get query parameters
    $start_date = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
    $end_date = $_GET['end_date'] ?? date('Y-m-d');
    $report_type = $_GET['report_type'] ?? 'orders'; // orders, products, or summary

    // Build Python microservice URL
    $python_service_url = 'http://127.0.0.1:5001/api/reports/sales/export';
    $query_params = http_build_query([
        'start_date' => $start_date,
        'end_date' => $end_date,
        'report_type' => $report_type
    ]);
    $url = $python_service_url . '?' . $query_params;

    // Initialize cURL
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

    // Execute request
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $content_type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
    $curl_error = curl_error($ch);
    curl_close($ch);

    // Check for cURL errors
    if ($response === false) {
        error_log("cURL Error: " . $curl_error);
        http_response_code(503);
        echo json_encode(['success' => false, 'message' => 'Unable to connect to reporting service']);
        exit;
    }

    // Check HTTP status
    if ($http_code !== 200) {
        error_log("Python service returned HTTP $http_code");
        http_response_code($http_code);
        echo $response;
        exit;
    }

    // Set headers for CSV download
    $filename = "sales_report_" . $report_type . "_" . $start_date . "_to_" . $end_date . ".csv";
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: no-cache, no-store, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');

    // Output the CSV content
    echo $response;
} catch (Exception $e) {
    error_log('Error in export_sales_report.php: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'An error occurred while exporting the report']);
}
