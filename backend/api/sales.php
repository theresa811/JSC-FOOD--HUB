<?php
/**
 * Sales Tracking API
 * Handles sales records and revenue tracking
 * GET /api/sales.php - Get all sales records
 * POST /api/sales.php - Record new sale
 * GET /api/sales.php?stats=1 - Get sales statistics
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/Database.php';

$method = $_SERVER['REQUEST_METHOD'];
$db = new Database();
$conn = $db->connect();

switch ($method) {
    case 'GET':
        if (isset($_GET['stats'])) {
            getSalesStats($conn);
        } else {
            getAllSales($conn);
        }
        break;
    case 'POST':
        recordSale($conn);
        break;
    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}

function getAllSales($conn) {
    try {
        $query = 'SELECT sr.*, mi.name as item_name FROM sales_records sr 
                  LEFT JOIN menu_items mi ON sr.menu_item_id = mi.id 
                  ORDER BY sr.sold_at DESC';
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $sales = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'data' => $sales,
            'total' => count($sales)
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function recordSale($conn) {
    try {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['menu_item_id']) || !isset($data['quantity']) || !isset($data['unit_price'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Missing required fields']);
            return;
        }

        $total_price = $data['quantity'] * $data['unit_price'];
        
        $query = 'INSERT INTO sales_records (menu_item_id, quantity, unit_price, total_price) 
                  VALUES (:menu_item_id, :quantity, :unit_price, :total_price)';
        $stmt = $conn->prepare($query);
        
        $stmt->bindParam(':menu_item_id', $data['menu_item_id']);
        $stmt->bindParam(':quantity', $data['quantity']);
        $stmt->bindParam(':unit_price', $data['unit_price']);
        $stmt->bindParam(':total_price', $total_price);
        
        $stmt->execute();
        
        echo json_encode([
            'success' => true,
            'message' => 'Sale recorded successfully',
            'id' => $conn->lastInsertId(),
            'total_price' => $total_price
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function getSalesStats($conn) {
    try {
        $query = 'SELECT 
                  COUNT(*) as total_sales,
                  SUM(quantity) as total_items_sold,
                  SUM(total_price) as total_revenue,
                  AVG(total_price) as avg_sale_value,
                  MAX(total_price) as highest_sale
                  FROM sales_records';
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $stats = $stmt->fetch(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'data' => $stats
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
?>