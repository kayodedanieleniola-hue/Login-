<?php
// Database Migration - Add Session Token Fields
require_once 'database.php';

try {
    // Check if columns exist, if not add them
    $checkSessionToken = $conn->query("SHOW COLUMNS FROM users LIKE 'session_token'");
    
    if ($checkSessionToken->num_rows === 0) {
        // Add session_token column
        $sql1 = "ALTER TABLE users ADD COLUMN session_token VARCHAR(64) UNIQUE AFTER is_active";
        if ($conn->query($sql1) === FALSE) {
            throw new Exception("Error adding session_token column: " . $conn->error);
        }
        echo "✓ Added session_token column\n";
    }

    // Check if token_expiry exists
    $checkTokenExpiry = $conn->query("SHOW COLUMNS FROM users LIKE 'token_expiry'");
    
    if ($checkTokenExpiry->num_rows === 0) {
        // Add token_expiry column
        $sql2 = "ALTER TABLE users ADD COLUMN token_expiry DATETIME AFTER session_token";
        if ($conn->query($sql2) === FALSE) {
            throw new Exception("Error adding token_expiry column: " . $conn->error);
        }
        echo "✓ Added token_expiry column\n";
    }

    // Create login_attempts table for security (prevent brute force)
    $sql3 = "CREATE TABLE IF NOT EXISTS login_attempts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        email_or_username VARCHAR(255) NOT NULL,
        ip_address VARCHAR(45),
        attempt_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        success INT DEFAULT 0,
        INDEX idx_email_username (email_or_username),
        INDEX idx_attempt_time (attempt_time)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

    if ($conn->query($sql3) === FALSE) {
        throw new Exception("Error creating login_attempts table: " . $conn->error);
    }
    echo "✓ Login attempts table ready\n";

    // Create password_resets table for password recovery
    $sql4 = "CREATE TABLE IF NOT EXISTS password_resets (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        reset_token VARCHAR(64) UNIQUE NOT NULL,
        token_expiry DATETIME NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        used INT DEFAULT 0,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        INDEX idx_token (reset_token),
        INDEX idx_user_id (user_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

    if ($conn->query($sql4) === FALSE) {
        throw new Exception("Error creating password_resets table: " . $conn->error);
    }
    echo "✓ Password resets table ready\n";

    // Create email_verifications table
    $sql5 = "CREATE TABLE IF NOT EXISTS email_verifications (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        verification_token VARCHAR(64) UNIQUE NOT NULL,
        token_expiry DATETIME NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        verified INT DEFAULT 0,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        INDEX idx_token (verification_token),
        INDEX idx_user_id (user_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

    if ($conn->query($sql5) === FALSE) {
        throw new Exception("Error creating email_verifications table: " . $conn->error);
    }
    echo "✓ Email verifications table ready\n";

    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Database migration completed successfully',
        'tables_updated' => [
            'users - added session_token and token_expiry columns',
            'login_attempts - created for security monitoring',
            'password_resets - created for password recovery',
            'email_verifications - created for email verification'
        ]
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Migration error: ' . $e->getMessage()
    ]);
} finally {
    $conn->close();
}
?>
