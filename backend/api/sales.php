<?php
/* ============================================
   SALES API
   Handle sales records and tracking
   ============================================ */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
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
    getSalesRecords();
} elseif ($method === 'POST') {
    recordSale($input, $user['id']);
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}

function getSalesRecords() {
    global $conn;
    
    $query = "SELECT sr.id, sr.menu_item_id, mi.name, sr.quantity, sr.price, sr.total_amount, sr.timestamp 
              FROM sales_records sr 
              LEFT JOIN menu_items mi ON sr.menu_item_id = mi.id 
              ORDER BY sr.timestamp DESC";
    
    $result = $conn->query($query);
    
    if ($result) {
        $records = [];
        while ($row = $result->fetch_assoc()) {
            $records[] = $row;
        }
        http_response_code(200);
        echo json_encode(['success' => true, 'data' => $records]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
}

function recordSale($input, $userId) {
    global $conn;
    
    $required = ['menu_item_id', 'quantity', 'price'];
    foreach ($required as $field) {
        if (!isset($input[$field])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => "Missing field: $field"]);
            return;
        }
    }
    
    $menuItemId = (int)$input['menu_item_id'];
    $quantity = (int)$input['quantity'];
    $price = (float)$input['price'];
    $totalAmount = $price * $quantity;
    
    $query = "INSERT INTO sales_records (menu_item_id, quantity, price, total_amount, recorded_by) 
              VALUES ($menuItemId, $quantity, $price, $totalAmount, $userId)";
    
    if ($conn->query($query)) {
        http_response_code(201);
        echo json_encode(['success' => true, 'message' => 'Sale recorded', 'id' => $conn->insert_id]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    }
}
?>