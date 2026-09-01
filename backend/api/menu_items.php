<?php
/**
 * Menu Items API
 * Handles CRUD operations for menu items
 * GET /api/menu_items.php - Get all items
 * POST /api/menu_items.php - Create new item
 * PUT /api/menu_items.php?id=X - Update item
 * DELETE /api/menu_items.php?id=X - Delete item
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
        getAllMenuItems($conn);
        break;
    case 'POST':
        createMenuItem($conn);
        break;
    case 'PUT':
        updateMenuItem($conn);
        break;
    case 'DELETE':
        deleteMenuItem($conn);
        break;
    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}

function getAllMenuItems($conn) {
    try {
        $query = 'SELECT * FROM menu_items ORDER BY created_at DESC';
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

function createMenuItem($conn) {
    try {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['name']) || !isset($data['category']) || !isset($data['price'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Missing required fields']);
            return;
        }

        $query = 'INSERT INTO menu_items (name, category, price, description, is_available, created_by) 
                  VALUES (:name, :category, :price, :description, :available, :created_by)';
        $stmt = $conn->prepare($query);
        
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':category', $data['category']);
        $stmt->bindParam(':price', $data['price']);
        $stmt->bindParam(':description', $data['description'] ?? null);
        $stmt->bindParam(':available', $data['is_available'] ?? true, PDO::PARAM_BOOL);
        $stmt->bindParam(':created_by', $data['created_by'] ?? 1);
        
        $stmt->execute();
        
        echo json_encode([
            'success' => true,
            'message' => 'Menu item created successfully',
            'id' => $conn->lastInsertId()
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function updateMenuItem($conn) {
    try {
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        if (!$id) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'ID parameter required']);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        
        $query = 'UPDATE menu_items SET name = :name, category = :category, price = :price, 
                  description = :description, is_available = :available WHERE id = :id';
        $stmt = $conn->prepare($query);
        
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':category', $data['category']);
        $stmt->bindParam(':price', $data['price']);
        $stmt->bindParam(':description', $data['description'] ?? null);
        $stmt->bindParam(':available', $data['is_available'] ?? true, PDO::PARAM_BOOL);
        
        $stmt->execute();
        
        echo json_encode(['success' => true, 'message' => 'Menu item updated successfully']);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function deleteMenuItem($conn) {
    try {
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        if (!$id) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'ID parameter required']);
            return;
        }

        $query = 'DELETE FROM menu_items WHERE id = :id';
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        echo json_encode(['success' => true, 'message' => 'Menu item deleted successfully']);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
?>