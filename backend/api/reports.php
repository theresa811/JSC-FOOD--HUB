<?php
/**
 * Reports API
 * Generates comprehensive reports for inventory, sales, and alerts
 * GET /api/reports.php?type=inventory - Inventory report
 * GET /api/reports.php?type=sales - Sales report
 * GET /api/reports.php?type=alerts - Alerts report
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

require_once '../config/Database.php';

$type = isset($_GET['type']) ? $_GET['type'] : 'dashboard';
$db = new Database();
$conn = $db->connect();

switch ($type) {
    case 'inventory':
        generateInventoryReport($conn);
        break;
    case 'sales':
        generateSalesReport($conn);
        break;
    case 'alerts':
        generateAlertsReport($conn);
        break;
    case 'dashboard':
        generateDashboardReport($conn);
        break;
    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid report type']);
}

function generateInventoryReport($conn) {
    try {
        $query = 'SELECT 
                  COUNT(*) as total_items,
                  SUM(quantity) as total_quantity,
                  SUM(quantity * cost_per_unit) as total_inventory_value,
                  COUNT(CASE WHEN status = "low" THEN 1 END) as low_stock_count
                  FROM inventory';
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $summary = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $query = 'SELECT * FROM inventory ORDER BY status DESC, name ASC';
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $details = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'report_type' => 'Inventory Report',
            'generated_at' => date('Y-m-d H:i:s'),
            'summary' => $summary,
            'details' => $details
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function generateSalesReport($conn) {
    try {
        $query = 'SELECT 
                  COUNT(*) as total_transactions,
                  SUM(quantity) as total_items_sold,
                  SUM(total_price) as total_revenue,
                  AVG(total_price) as avg_transaction,
                  MAX(total_price) as max_transaction
                  FROM sales_records';
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $summary = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $query = 'SELECT sr.*, mi.name as item_name FROM sales_records sr 
                  LEFT JOIN menu_items mi ON sr.menu_item_id = mi.id 
                  ORDER BY sr.sold_at DESC';
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $details = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'report_type' => 'Sales Report',
            'generated_at' => date('Y-m-d H:i:s'),
            'summary' => $summary,
            'details' => $details
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function generateAlertsReport($conn) {
    try {
        $query = 'SELECT * FROM inventory WHERE quantity <= low_stock_threshold ORDER BY quantity ASC';
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $low_stocks = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $query = 'SELECT * FROM stock_alerts WHERE is_resolved = FALSE ORDER BY created_at DESC';
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $alerts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'report_type' => 'Alerts Report',
            'generated_at' => date('Y-m-d H:i:s'),
            'low_stock_items' => $low_stocks,
            'alert_count' => count($low_stocks),
            'system_alerts' => $alerts
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function generateDashboardReport($conn) {
    try {
        // Get overall statistics
        $queries = [
            'menu_count' => 'SELECT COUNT(*) as count FROM menu_items',
            'inventory_count' => 'SELECT COUNT(*) as count FROM inventory',
            'low_stock_count' => 'SELECT COUNT(*) as count FROM inventory WHERE status = "low"',
            'total_sales' => 'SELECT COUNT(*) as count, SUM(total_price) as total FROM sales_records',
            'inventory_value' => 'SELECT SUM(quantity * cost_per_unit) as total_value FROM inventory'
        ];
        
        $dashboard = [];
        foreach ($queries as $key => $query) {
            $stmt = $conn->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $dashboard[$key] = $result;
        }
        
        echo json_encode([
            'success' => true,
            'report_type' => 'Dashboard Overview',
            'generated_at' => date('Y-m-d H:i:s'),
            'dashboard' => $dashboard
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
?>