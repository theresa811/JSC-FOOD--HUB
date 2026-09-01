<?php
/**
 * Inventory API
 * Handles CRUD operations for inventory/stock items
 * GET /api/inventory.php - Get all inventory
 * GET /api/inventory.php?low=1 - Get low stock items
 * POST /api/inventory.php - Add stock item
 * PUT /api/inventory.php?id=X - Update stock
 * DELETE /api/inventory.php?id=X - Delete stock item
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/Database.php';

$method = $_SERVER['REQUEST_METHOD'];
$db = new Database();
$conn = $db->connect();

switch ($method) {
    case 'GET':
        if (isset($_GET['low'])) {
            getLowStockItems($conn);
        } else {
            getAllInventory($conn);
        }
        break;
    case 'POST':
        addStock($conn);
        break;
    case 'PUT':
        updateStock($conn);
        break;
    case 'DELETE':
        deleteStock($conn);
        break;
    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}

function getAllInventory($conn) {
    try {
        $query = 'SELECT * FROM inventory ORDER BY status DESC, name ASC';
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'data' => $items,
            'total' => count($items)
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function getLowStockItems($conn) {
    try {
        $query = 'SELECT * FROM inventory WHERE quantity <= low_stock_threshold ORDER BY quantity ASC';
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'data' => $items,
            'total' => count($items)
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function addStock($conn) {
    try {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['name']) || !isset($data['quantity']) || !isset($data['low_stock_threshold'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Missing required fields']);
            return;
        }

        $status = $data['quantity'] <= $data['low_stock_threshold'] ? 'low' : 'normal';
        
        $query = 'INSERT INTO inventory (name, quantity, unit, low_stock_threshold, cost_per_unit, status) 
                  VALUES (:name, :quantity, :unit, :threshold, :cost, :status)';
        $stmt = $conn->prepare($query);
        
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':quantity', $data['quantity']);
        $stmt->bindParam(':unit', $data['unit'] ?? 'kg');
        $stmt->bindParam(':threshold', $data['low_stock_threshold']);
        $stmt->bindParam(':cost', $data['cost_per_unit'] ?? 0);
        $stmt->bindParam(':status', $status);
        
        $stmt->execute();
        
        echo json_encode([
            'success' => true,
            'message' => 'Stock item added successfully',
            'id' => $conn->lastInsertId()
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function updateStock($conn) {
    try {
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        if (!$id) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'ID parameter required']);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $status = $data['quantity'] <= $data['low_stock_threshold'] ? 'low' : 'normal';
        
        $query = 'UPDATE inventory SET quantity = :quantity, status = :status WHERE id = :id';
        $stmt = $conn->prepare($query);
        
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':quantity', $data['quantity']);
        $stmt->bindParam(':status', $status);
        
        $stmt->execute();
        
        echo json_encode(['success' => true, 'message' => 'Stock updated successfully']);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function deleteStock($conn) {
    try {
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        if (!$id) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'ID parameter required']);
            return;
        }

        $query = 'DELETE FROM inventory WHERE id = :id';
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        echo json_encode(['success' => true, 'message' => 'Stock deleted successfully']);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
?>