<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Include database connection
require_once 'database.php';

// Get POST data
$data = json_decode(file_get_contents("php://input"), true);

// Validate input
if (!isset($data['emailOrUsername']) || !isset($data['password'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Email/Username and password required']);
    exit;
}

$emailOrUsername = trim($data['emailOrUsername']);
$password = $data['password'];

// Validate input is not empty
if (empty($emailOrUsername) || empty($password)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Email/Username and password cannot be empty']);
    exit;
}

try {
    // Check if input is email or username
    $isEmail = filter_var($emailOrUsername, FILTER_VALIDATE_EMAIL);
    
    if ($isEmail) {
        // Search by email
        $stmt = $conn->prepare("SELECT id, email, username, password_hash, is_active, is_verified, profile_picture FROM users WHERE email = ? AND password_hash IS NOT NULL");
    } else {
        // Search by username
        $stmt = $conn->prepare("SELECT id, email, username, password_hash, is_active, is_verified, profile_picture FROM users WHERE username = ? AND password_hash IS NOT NULL");
    }
    
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("s", $emailOrUsername);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
        exit;
    }

    $user = $result->fetch_assoc();
    $stmt->close();

    // Check if account is active
    if ($user['is_active'] === 0) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Account is deactivated']);
        exit;
    }

    // Verify password
    if (!password_verify($password, $user['password_hash'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
        exit;
    }

    // Generate session token
    $sessionToken = bin2hex(random_bytes(32));
    $tokenExpiry = date('Y-m-d H:i:s', strtotime('+24 hours'));

    // Store session token in database
    $updateSession = $conn->prepare("UPDATE users SET session_token = ?, token_expiry = ?, updated_at = NOW() WHERE id = ?");
    $updateSession->bind_param("ssi", $sessionToken, $tokenExpiry, $user['id']);
    $updateSession->execute();
    $updateSession->close();

    // Check if email is verified
    $isVerified = $user['is_verified'] === 1;

    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Login successful',
        'user' => [
            'id' => $user['id'],
            'email' => $user['email'],
            'username' => $user['username'],
            'is_verified' => $isVerified,
            'profile_picture' => $user['profile_picture']
        ],
        'token' => $sessionToken,
        'expiresIn' => 86400 // 24 hours in seconds
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
} finally {
    $conn->close();
}
?>
