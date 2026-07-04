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
if (!isset($data['email']) || !isset($data['googleId']) || !isset($data['signupMethod'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

$email = trim($data['email']);
$googleId = trim($data['googleId']);
$name = isset($data['name']) ? trim($data['name']) : '';
$picture = isset($data['picture']) ? trim($data['picture']) : '';
$signupMethod = $data['signupMethod'];

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
    exit;
}

// Validate Google ID
if (empty($googleId)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid Google ID']);
    exit;
}

try {
    // Check if user with this Google ID already exists
    $checkGoogle = $conn->prepare("SELECT id, email, username FROM users WHERE google_id = ?");
    $checkGoogle->bind_param("s", $googleId);
    $checkGoogle->execute();
    $googleResult = $checkGoogle->get_result();
    
    if ($googleResult->num_rows > 0) {
        // User already exists, update last login and return existing user
        $row = $googleResult->fetch_assoc();
        $userId = $row['id'];
        $username = $row['username'];
        
        // Update last activity
        $updateUser = $conn->prepare("UPDATE users SET updated_at = NOW() WHERE id = ?");
        $updateUser->bind_param("i", $userId);
        $updateUser->execute();
        $updateUser->close();
        
        $googleResult->close();
        $checkGoogle->close();
        
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Login successful',
            'user' => [
                'id' => $userId,
                'email' => $row['email'],
                'username' => $username
            ]
        ]);
        exit;
    }
    $checkGoogle->close();

    // Check if email already exists with different signup method
    $checkEmail = $conn->prepare("SELECT id, username, signup_method FROM users WHERE email = ?");
    $checkEmail->bind_param("s", $email);
    $checkEmail->execute();
    $emailResult = $checkEmail->get_result();
    
    if ($emailResult->num_rows > 0) {
        // Email exists with different method, link Google account to existing user
        $row = $emailResult->fetch_assoc();
        $userId = $row['id'];
        $username = $row['username'];
        
        $linkGoogle = $conn->prepare("UPDATE users SET google_id = ?, profile_picture = ? WHERE id = ?");
        $linkGoogle->bind_param("ssi", $googleId, $picture, $userId);
        $linkGoogle->execute();
        $linkGoogle->close();
        
        $emailResult->close();
        $checkEmail->close();
        
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Google account linked successfully',
            'user' => [
                'id' => $userId,
                'email' => $email,
                'username' => $username
            ]
        ]);
        exit;
    }
    $checkEmail->close();

    // Create new user with Google credentials
    // Generate username from email if not provided
    $username = explode('@', $email)[0];
    $originalUsername = $username;
    $counter = 1;
    
    // Ensure username is unique
    while (true) {
        $checkUsernameExists = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $checkUsernameExists->bind_param("s", $username);
        $checkUsernameExists->execute();
        $usernameCheckResult = $checkUsernameExists->get_result();
        
        if ($usernameCheckResult->num_rows === 0) {
            $usernameCheckResult->close();
            $checkUsernameExists->close();
            break;
        }
        
        $usernameCheckResult->close();
        $checkUsernameExists->close();
        
        $username = $originalUsername . $counter;
        $counter++;
    }

    // Insert new user
    $stmt = $conn->prepare("INSERT INTO users (email, username, google_id, profile_picture, full_name, signup_method, is_verified) VALUES (?, ?, ?, ?, ?, ?, ?)");
    
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    $isVerified = 1; // Google verified email
    $stmt->bind_param("ssssssi", $email, $username, $googleId, $picture, $name, $signupMethod, $isVerified);

    if ($stmt->execute()) {
        $userId = $stmt->insert_id;
        $stmt->close();

        http_response_code(201);
        echo json_encode([
            'success' => true,
            'message' => 'Account created successfully with Google',
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
