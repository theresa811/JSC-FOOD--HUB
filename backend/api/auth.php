<?php
/**
 * Authentication API
 * Handles user login and session management
 * POST /api/auth.php?action=login
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/Database.php';

$action = isset($_GET['action']) ? $_GET['action'] : '';
$db = new Database();
$conn = $db->connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'login') {
        login($conn);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if ($action === 'verify') {
        verifyToken();
    }
}

function login($conn) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['username']) || !isset($data['password']) || !isset($data['role'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Missing credentials']);
        return;
    }

    try {
        $query = 'SELECT * FROM users WHERE username = :username AND role = :role LIMIT 1';
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':username', $data['username']);
        $stmt->bindParam(':role', $data['role']);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($data['password'], $user['password'])) {
            // Generate session token
            $token = bin2hex(random_bytes(32));
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['token'] = $token;

            echo json_encode([
                'success' => true,
                'message' => 'Login successful',
                'user' => [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'role' => $user['role'],
                    'email' => $user['email']
                ],
                'token' => $token
            ]);
        } else {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
}

function verifyToken() {
    if (isset($_SESSION['token'])) {
        echo json_encode([
            'success' => true,
            'user' => [
                'id' => $_SESSION['user_id'],
                'username' => $_SESSION['username'],
                'role' => $_SESSION['role']
            ]
        ]);
    } else {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    }
}
?>