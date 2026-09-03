<?php
/* ============================================
   INVENTORY API
   CRUD operations for stock items
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
if (!$user || $user['role'] !== 'admin') {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Admin access required']);
    exit();
}

if ($method === 'GET') {
    getInventory();
} elseif ($method === 'POST') {
    addStock($input);
} elseif ($method === 'PUT') {
    updateStock($input);
} elseif ($method === 'DELETE') {
    deleteStock($_GET['id'] ?? null);
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}

function getInventory() {
    global $conn;
    
    $query = "SELECT id, name, quantity, unit, low_stock_threshold, cost_per_unit, status, last_updated FROM inventory_stocks ORDER BY name ASC";
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

function addStock($input) {
    global $conn;
    
    $required = ['name', 'quantity', 'unit', 'low_stock_threshold', 'cost_per_unit'];
    foreach ($required as $field) {
        if (!isset($input[$field])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => "Missing field: $field"]);
            return;
        }
    }
    
    $name = $conn->real_escape_string($input['name']);
    $quantity = (float)$input['quantity'];
    $unit = $conn->real_escape_string($input['unit']);
    $threshold = (float)$input['low_stock_threshold'];
    $cost = (float)$input['cost_per_unit'];
    $status = $quantity <= $threshold ? 'Low' : 'Normal';
    
    $query = "INSERT INTO inventory_stocks (name, quantity, unit, low_stock_threshold, cost_per_unit, status) 
              VALUES ('$name', $quantity, '$unit', $threshold, $cost, '$status')";
    
    if ($conn->query($query)) {
        http_response_code(201);
        echo json_encode(['success' => true, 'message' => 'Stock item added', 'id' => $conn->insert_id]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    }
}

function updateStock($input) {
    global $conn;
    
    if (!isset($input['id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Missing stock ID']);
        return;
    }
    
    $id = (int)$input['id'];
    $updates = [];
    
    if (isset($input['quantity'])) {
        $quantity = (float)$input['quantity'];
        $updates[] = "quantity = $quantity";
        
        // Check threshold and update status
        $result = $conn->query("SELECT low_stock_threshold FROM inventory_stocks WHERE id = $id");
        if ($result && $row = $result->fetch_assoc()) {
            $status = $quantity <= $row['low_stock_threshold'] ? 'Low' : 'Normal';
            $updates[] = "status = '$status'";
        }
    }
    
    if (isset($input['low_stock_threshold'])) $updates[] = "low_stock_threshold = " . (float)$input['low_stock_threshold'];
    if (isset($input['cost_per_unit'])) $updates[] = "cost_per_unit = " . (float)$input['cost_per_unit'];
    
    if (empty($updates)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'No fields to update']);
        return;
    }
    
    $query = "UPDATE inventory_stocks SET " . implode(', ', $updates) . " WHERE id = $id";
    
    if ($conn->query($query)) {
        http_response_code(200);
        echo json_encode(['success' => true, 'message' => 'Stock updated']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
}

function deleteStock($id) {
    global $conn;
    
    if (!$id) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Missing stock ID']);
        return;
    }
    
    $id = (int)$id;
    $query = "DELETE FROM inventory_stocks WHERE id = $id";
    
    if ($conn->query($query)) {
        http_response_code(200);
        echo json_encode(['success' => true, 'message' => 'Stock deleted']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
}
?>