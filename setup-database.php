<?php
// Database setup script for messenger chat system
$servername = "localhost";
$username = "root";
$password = "";
$database = "library";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "<h2>Setting up Messenger Database Tables...</h2>";

// SQL queries
$queries = [
    // Conversations Table
    "CREATE TABLE IF NOT EXISTS conversations (
        id INT PRIMARY KEY AUTO_INCREMENT,
        user_id INT NOT NULL,
        admin_id INT DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE,
        FOREIGN KEY (admin_id) REFERENCES user(id) ON DELETE SET NULL,
        INDEX idx_user_id (user_id),
        INDEX idx_admin_id (admin_id)
    )",
    
    // Messages Table
    "CREATE TABLE IF NOT EXISTS messages (
        id INT PRIMARY KEY AUTO_INCREMENT,
        conversation_id INT NOT NULL,
        sender_id INT NOT NULL,
        message TEXT NOT NULL,
        status ENUM('sent', 'read') DEFAULT 'sent',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (conversation_id) REFERENCES conversations(id) ON DELETE CASCADE,
        FOREIGN KEY (sender_id) REFERENCES user(id) ON DELETE CASCADE,
        INDEX idx_conversation_id (conversation_id),
        INDEX idx_sender_id (sender_id)
    )"
];

// Execute queries
foreach ($queries as $i => $sql) {
    if ($conn->query($sql) === TRUE) {
        echo "<p style='color: green;'>✓ Table " . ($i === 0 ? "conversations" : "messages") . " created successfully or already exists.</p>";
    } else {
        echo "<p style='color: red;'>✗ Error creating table: " . $conn->error . "</p>";
    }
}

echo "<h3 style='color: green;'>Database setup completed successfully!</h3>";
echo "<p><a href='/kmkdt-Library/public/admin/index'>Go back to admin dashboard</a></p>";

$conn->close();
?>
