<?php
// Set the content type to application/json for API responses
header('Content-Type: application/json');

// Include necessary configuration and utility files
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../utils/response.php';
require_once __DIR__ . '/../../utils/auth.php';

// Start the session to access user data
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Fetches dashboard data for the production manager.
 *
 * @param PDO $pdo The database connection object.
 * @return array An associative array containing production metrics.
 */
function get_production_dashboard_data($pdo)
{
    try {
        $today = date('Y-m-d');

        // Try to fetch from production_logs table first
        $daily_output = [
            'bottled_water' => 0,
            'sparkling_beverages' => 0,
            'total' => 0,
        ];

        $stock_levels = [];
        $production_trends = [
            'labels' => [],
            'data' => [],
        ];

        // Check if production_logs table exists
        try {
            $productionQuery = "
                SELECT 
                    COALESCE(SUM(CASE WHEN product_type = 'bottled_water' THEN quantity_produced ELSE 0 END), 0) as bottled_water,
                    COALESCE(SUM(CASE WHEN product_type = 'sparkling_beverages' THEN quantity_produced ELSE 0 END), 0) as sparkling_beverages,
                    COALESCE(SUM(quantity_produced), 0) as total
                FROM production_logs
                WHERE DATE(production_date) = ?
            ";
            $stmt = $pdo->prepare($productionQuery);
            $stmt->execute([$today]);
            $productionData = $stmt->fetch(PDO::FETCH_ASSOC);

            $daily_output = [
                'bottled_water' => (int)($productionData['bottled_water'] ?? 0),
                'sparkling_beverages' => (int)($productionData['sparkling_beverages'] ?? 0),
                'total' => (int)($productionData['total'] ?? 0),
            ];

            // Fetch production trends for the last 7 days
            $trendsQuery = "
                SELECT 
                    DATE(production_date) as date,
                    SUM(quantity_produced) as total_produced
                FROM production_logs
                WHERE production_date >= DATE_SUB(?, INTERVAL 6 DAY)
                GROUP BY DATE(production_date)
                ORDER BY DATE(production_date) ASC
            ";
            $stmt = $pdo->prepare($trendsQuery);
            $stmt->execute([$today]);
            $trendsData = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $labels = [];
            $data = [];
            foreach ($trendsData as $trend) {
                $date = new DateTime($trend['date']);
                $labels[] = $date->format('D, M d');
                $data[] = (int)$trend['total_produced'];
            }

            if (!empty($labels)) {
                $production_trends = [
                    'labels' => $labels,
                    'data' => $data,
                ];
            }
        } catch (PDOException $e) {
            // If table doesn't exist, provide default data
            error_log("production_logs table not found, using default data");
        }

        // Fetch current stock levels from inventory
        try {
            $stockQuery = "
                SELECT 
                    p.id,
                    p.name as product_name,
                    COALESCE(i.current_stock, 0) as quantity,
                    COALESCE(i.minimum_stock_level, 50) as reorder_point
                FROM products p
                LEFT JOIN inventory i ON p.id = i.product_id
                WHERE p.category IN ('bottled_water', 'beverage')
                ORDER BY i.current_stock ASC
                LIMIT 10
            ";
            $stmt = $pdo->prepare($stockQuery);
            $stmt->execute();
            $stock_levels = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching stock levels: " . $e->getMessage());
        }

        return [
            'daily_output' => $daily_output,
            'stock_levels' => $stock_levels,
            'production_trends' => $production_trends,
        ];
    } catch (PDOException $e) {
        error_log("Database error in get_production_dashboard_data: " . $e->getMessage());
        // Return default data if database fails
        return [
            'daily_output' => [
                'total' => 0,
                'bottled_water' => 0,
                'sparkling_beverages' => 0,
            ],
            'stock_levels' => [],
            'production_trends' => [
                'labels' => [],
                'data' => [],
            ],
        ];
    }
} // Get the database connection
$pdo = get_db_connection();

// Fetch the dashboard data
$dashboard_data = get_production_dashboard_data($pdo);

// Send a successful response with the fetched data
success_response("Production dashboard data retrieved successfully.", $dashboard_data, 200);
