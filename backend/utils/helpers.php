<?php
/* ============================================
   HELPER FUNCTIONS
   Utility functions for authentication and validation
   ============================================ */

// JWT Secret Key (Change this to a strong secret)
define('JWT_SECRET', 'your_super_secret_key_change_this');

/**
 * Generate JWT Token
 */
function generateToken($userId, $username, $role) {
    $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
    $payload = json_encode([
        'userId' => $userId,
        'username' => $username,
        'role' => $role,
        'iat' => time(),
        'exp' => time() + (24 * 60 * 60) // 24 hours
    ]);
    
    $base64Header = rtrim(strtr(base64_encode($header), '+/', '-_'), '=');
    $base64Payload = rtrim(strtr(base64_encode($payload), '+/', '-_'), '=');
    
    $signature = hash_hmac('sha256', "$base64Header.$base64Payload", JWT_SECRET, true);
    $base64Signature = rtrim(strtr(base64_encode($signature), '+/', '-_'), '=');
    
    return "$base64Header.$base64Payload.$base64Signature";
}

/**
 * Verify JWT Token
 */
function verifyToken() {
    $headers = getallheaders();
    $authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : '';
    
    if (!$authHeader) {
        return false;
    }
    
    $token = str_replace('Bearer ', '', $authHeader);
    return verifyJWT($token);
}

/**
 * Verify JWT
 */
function verifyJWT($token) {
    $parts = explode('.', $token);
    
    if (count($parts) !== 3) {
        return false;
    }
    
    list($base64Header, $base64Payload, $base64Signature) = $parts;
    
    // Verify signature
    $signature = hash_hmac('sha256', "$base64Header.$base64Payload", JWT_SECRET, true);
    $base64SignatureCheck = rtrim(strtr(base64_encode($signature), '+/', '-_'), '=');
    
    if ($base64Signature !== $base64SignatureCheck) {
        return false;
    }
    
    // Decode payload
    $payload = json_decode(
        base64_decode(strtr($base64Payload, '-_', '+/')),
        true
    );
    
    // Check expiration
    if ($payload['exp'] < time()) {
        return false;
    }
    
    return $payload;
}

/**
 * Hash Password (using bcrypt)
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
}

/**
 * Verify Password
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Sanitize Input
 */
function sanitizeInput($input) {
    if (is_array($input)) {
        return array_map('sanitizeInput', $input);
    }
    return htmlspecialchars(strip_tags($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Validate Email
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Send JSON Response
 */
function sendResponse($success, $message, $data = null, $statusCode = 200) {
    http_response_code($statusCode);
    $response = ['success' => $success, 'message' => $message];
    if ($data !== null) {
        $response['data'] = $data;
    }
    echo json_encode($response);
    exit();
}
?>