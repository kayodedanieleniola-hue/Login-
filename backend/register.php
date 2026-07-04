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
if (!isset($data['email']) || !isset($data['username']) || !isset($data['password']) || !isset($data['signupMethod'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

$email = trim($data['email']);
$username = trim($data['username']);
$password = $data['password'];
$signupMethod = $data['signupMethod'];

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
    exit;
}

// Validate username
if (strlen($username) < 3) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Username must be at least 3 characters']);
    exit;
}

if (strlen($username) > 100) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Username must be less than 100 characters']);
    exit;
}

// Validate username characters (alphanumeric and underscore only)
if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Username can only contain letters, numbers, and underscores']);
    exit;
}

// Validate password
if (strlen($password) < 6) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters']);
    exit;
}

if (strlen($password) > 128) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Password must be less than 128 characters']);
    exit;
}

try {
    // Check if email already exists
    $checkEmail = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $checkEmail->bind_param("s", $email);
    $checkEmail->execute();
    $emailResult = $checkEmail->get_result();
    
    if ($emailResult->num_rows > 0) {
        http_response_code(409);
        echo json_encode(['success' => false, 'message' => 'Email already registered']);
        exit;
    }
    $checkEmail->close();

    // Check if username already exists
    $checkUsername = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $checkUsername->bind_param("s", $username);
    $checkUsername->execute();
    $usernameResult = $checkUsername->get_result();
    
    if ($usernameResult->num_rows > 0) {
        http_response_code(409);
        echo json_encode(['success' => false, 'message' => 'Username already taken']);
        exit;
    }
    $checkUsername->close();

    // Hash password with bcrypt
    $passwordHash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

    // Insert new user
    $stmt = $conn->prepare("INSERT INTO users (email, username, password_hash, signup_method, is_verified) VALUES (?, ?, ?, ?, ?)");
    
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    $isVerified = 0; // Email needs verification
    $stmt->bind_param("ssssi", $email, $username, $passwordHash, $signupMethod, $isVerified);

    if ($stmt->execute()) {
        $userId = $stmt->insert_id;
        $stmt->close();

        // TODO: Send verification email here
        // For now, we'll just return success

        http_response_code(201);
        echo json_encode([
            'success' => true,
            'message' => 'Account created successfully',
            'user' => [
                'id' => $userId,
                'email' => $email,
                'username' => $username
            ]
        ]);
    } else {
        throw new Exception("Execute failed: " . $stmt->error);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
} finally {
    $conn->close();
}
?>
