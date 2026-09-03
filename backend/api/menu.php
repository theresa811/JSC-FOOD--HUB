<?php
/* ============================================
   MENU ITEMS API
   CRUD operations for menu items
   ============================================ */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../config/database.php';
require_once '../utils/helpers.php';

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

// Verify authentication
$user = verifyToken();
if (!$user) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if ($method === 'GET') {
    getMenuItems();
} elseif ($method === 'POST' && $user['role'] === 'chef') {
    addMenuItem($input, $user['id']);
} elseif ($method === 'PUT' && $user['role'] === 'chef') {
    updateMenuItem($input, $user['id']);
} elseif ($method === 'DELETE' && $user['role'] === 'chef') {
    deleteMenuItem($_GET['id'] ?? null, $user['id']);
} else {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Forbidden']);
}

function getMenuItems() {
    global $conn;
    
    $query = "SELECT id, name, category, price, description, availability, created_at FROM menu_items ORDER BY created_at DESC";
    $result = $conn->query($query);
    
    if ($result) {
        $items = [];
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }
        http_response_code(200);
        echo json_encode(['success' => true, 'data' => $items]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
}

function addMenuItem($input, $userId) {
    global $conn;
    
    if (!isset($input['name']) || !isset($input['category']) || !isset($input['price'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        return;
    }
    
    $name = $conn->real_escape_string($input['name']);
    $category = $conn->real_escape_string($input['category']);
    $price = (float)$input['price'];
    $description = $conn->real_escape_string($input['description'] ?? '');
    $availability = isset($input['availability']) ? (int)$input['availability'] : 1;
    
    $query = "INSERT INTO menu_items (name, category, price, description, availability, created_by) 
              VALUES ('$name', '$category', $price, '$description', $availability, $userId)";
    
    if ($conn->query($query)) {
        http_response_code(201);
        echo json_encode(['success' => true, 'message' => 'Menu item added', 'id' => $conn->insert_id]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    }
}

function updateMenuItem($input, $userId) {
    global $conn;
    
    if (!isset($input['id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Missing item ID']);
        return;
    }
    
    $id = (int)$input['id'];
    $updates = [];
    
    if (isset($input['name'])) $updates[] = "name = '" . $conn->real_escape_string($input['name']) . "'";
    if (isset($input['category'])) $updates[] = "category = '" . $conn->real_escape_string($input['category']) . "'";
    if (isset($input['price'])) $updates[] = "price = " . (float)$input['price'];
    if (isset($input['description'])) $updates[] = "description = '" . $conn->real_escape_string($input['description']) . "'";
    if (isset($input['availability'])) $updates[] = "availability = " . (int)$input['availability'];
    
    if (empty($updates)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'No fields to update']);
        return;
    }
    
    $query = "UPDATE menu_items SET " . implode(', ', $updates) . " WHERE id = $id AND created_by = $userId";
    
    if ($conn->query($query)) {
        http_response_code(200);
        echo json_encode(['success' => true, 'message' => 'Menu item updated']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
}

function deleteMenuItem($id, $userId) {
    global $conn;
    
    if (!$id) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Missing item ID']);
        return;
    }
    
    $id = (int)$id;
    $query = "DELETE FROM menu_items WHERE id = $id AND created_by = $userId";
    
    if ($conn->query($query)) {
        http_response_code(200);
        echo json_encode(['success' => true, 'message' => 'Menu item deleted']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
}
?>