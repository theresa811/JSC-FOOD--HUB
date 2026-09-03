<?php
/* ============================================
   AUTHENTICATION API
   Handles user login and token generation
   ============================================ */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../config/database.php';
require_once '../utils/helpers.php';

$action = isset($_GET['action']) ? $_GET['action'] : '';
$input = json_decode(file_get_contents('php://input'), true);

if ($action === 'login') {
    handleLogin($input);
} elseif ($action === 'logout') {
    handleLogout();
} elseif ($action === 'verify') {
    verifyToken();
} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
}

function handleLogin($input) {
    global $conn;
    
    if (!isset($input['username']) || !isset($input['password']) || !isset($input['role'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        return;
    }
    
    $username = $conn->real_escape_string($input['username']);
    $password = $input['password'];
    $role = $conn->real_escape_string($input['role']);
    
    $query = "SELECT id, username, password, role, email, full_name FROM users WHERE username = '$username' AND role = '$role' AND is_active = TRUE";
    $result = $conn->query($query);
    
    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Verify password (using bcrypt)
        if (password_verify($password, $user['password'])) {
            // Generate token
            $token = generateToken($user['id'], $user['username'], $user['role']);
            
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'message' => 'Login successful',
                'token' => $token,
                'user' => [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'role' => $user['role'],
                    'email' => $user['email'],
                    'full_name' => $user['full_name']
                ]
            ]);
        } else {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Invalid password']);
        }
    } else {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'User not found']);
    }
}

function handleLogout() {
    // Token-based auth doesn't require server-side logout
    http_response_code(200);
    echo json_encode(['success' => true, 'message' => 'Logged out successfully']);
}

function verifyToken() {
    $headers = getallheaders();
    $authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : '';
    
    if (!$authHeader) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Missing authorization header']);
        return;
    }
    
    $token = str_replace('Bearer ', '', $authHeader);
    $decoded = verifyJWT($token);
    
    if ($decoded) {
        http_response_code(200);
        echo json_encode(['success' => true, 'user' => $decoded]);
    } else {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Invalid token']);
    }
}
?>