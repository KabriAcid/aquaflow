<?php
// Set the content type to application/json for API responses
header('Content-Type: application/json');

// Include necessary configuration and utility files
require_once '../../config/database.php';
require_once '../../utils/response.php';
require_once '../../utils/auth.php';

// Start the session to access user data
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Authenticate the user and check their role
// The user must be a production manager to access this endpoint
if (!is_authenticated() || $_SESSION['role'] !== 'production_manager') {
    send_response(403, "Access denied. You must be a production manager to access this data.");
    exit;
}

/**
 * Fetches dashboard data for the production manager.
 *
 * @param PDO $pdo The database connection object.
 * @return array An associative array containing production metrics.
 */
function get_production_dashboard_data($pdo) {
    // For demonstration purposes, this function returns hardcoded data.
    // In a real application, you would fetch this data from the database.
    return [
        'daily_output' => [
            'total' => 12500, // Total units produced today
            'bottled_water' => 8000,
            'sparkling_beverages' => 4500,
        ],
        'stock_levels' => [
            ['product_name' => 'Natural Spring Water - 1L', 'quantity' => 15000, 'reorder_point' => 5000],
            ['product_name' => 'Sparkling Beverage - 330ml', 'quantity' => 8000, 'reorder_point' => 3000],
            ['product_name' => 'Bulk Water Package - 24x500ml', 'quantity' => 2500, 'reorder_point' => 1000],
        ],
        'production_trends' => [
            'labels' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
            'data' => [11000, 11500, 12000, 12200, 12500],
        ],
    ];
}

// Get the database connection
$pdo = get_database_connection();

// Fetch the dashboard data
$dashboard_data = get_production_dashboard_data($pdo);

// Send a successful response with the fetched data
send_response(200, "Production dashboard data retrieved successfully.", $dashboard_data);
?>
