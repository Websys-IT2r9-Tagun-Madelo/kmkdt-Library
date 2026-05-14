<?php
session_start();

// Include config
$appPath = dirname(__DIR__) . '/app';
$configPath = $appPath . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php';

if (file_exists($configPath)) {
    include_once($configPath);
} else {
    die(json_encode(['success' => false, 'message' => 'Config not found']));
}

// Get current user from session
if (!isset($_SESSION['authUser'])) {
    http_response_code(401);
    die(json_encode(['success' => false, 'message' => 'Unauthorized']));
}

$current_user_id = $_SESSION['authUser']['user_id'];
$action = $_GET['action'] ?? '';

$conn = $GLOBALS['conn'];

// ===== GET USER INFO =====
if ($action === 'getUser') {
    $user_id = $_GET['user_id'] ?? 0;
    $query = "SELECT id, fullName, email, username FROM user WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo json_encode(['success' => true, 'user' => $result->fetch_assoc()]);
    } else {
        echo json_encode(['success' => false, 'message' => 'User not found']);
    }
    exit;
}

// ===== GET OR CREATE CONVERSATION WITH ADMIN =====
if ($action === 'getAdminConversation') {
    // Get or create conversation with admin (user_id = 1, typically the first admin)
    $admin_id = 1;
    
    // Check if conversation exists
    $query = "SELECT * FROM conversations WHERE user_id = ? AND admin_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $current_user_id, $admin_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $conversation = $result->fetch_assoc();
        echo json_encode(['success' => true, 'conversation_id' => $conversation['id']]);
    } else {
        // Create new conversation
        $insert_query = "INSERT INTO conversations (user_id, admin_id) VALUES (?, ?)";
        $insert_stmt = $conn->prepare($insert_query);
        $insert_stmt->bind_param("ii", $current_user_id, $admin_id);
        
        if ($insert_stmt->execute()) {
            $conv_id = $insert_stmt->insert_id;
            echo json_encode(['success' => true, 'conversation_id' => $conv_id]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to create conversation']);
        }
    }
    exit;
}

// ===== GET USER CONVERSATIONS =====
if ($action === 'getConversations') {
    $query = "
        SELECT 
            c.id,
            c.created_at,
            CASE 
                WHEN c.admin_id IS NOT NULL THEN u.fullName
                ELSE (SELECT fullName FROM user WHERE id = admin_id LIMIT 1)
            END as recipient_name,
            CASE 
                WHEN c.admin_id IS NOT NULL THEN u.email
                ELSE (SELECT email FROM user WHERE id = admin_id LIMIT 1)
            END as recipient_email,
            CASE 
                WHEN c.admin_id IS NOT NULL THEN u.id
                ELSE admin_id
            END as recipient_id,
            (SELECT message FROM messages WHERE conversation_id = c.id ORDER BY created_at DESC LIMIT 1) as last_message,
            (SELECT created_at FROM messages WHERE conversation_id = c.id ORDER BY created_at DESC LIMIT 1) as last_message_time,
            (SELECT COUNT(*) FROM messages WHERE conversation_id = c.id AND sender_id != ? AND status = 'sent') as unread_count
        FROM conversations c
        LEFT JOIN user u ON c.admin_id = u.id
        WHERE c.user_id = ?
        ORDER BY c.updated_at DESC
    ";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $current_user_id, $current_user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $conversations = [];
    while ($row = $result->fetch_assoc()) {
        $conversations[] = $row;
    }
    
    echo json_encode(['success' => true, 'conversations' => $conversations]);
    exit;
}

// ===== GET MESSAGES FOR CONVERSATION =====
if ($action === 'getMessages') {
    $conversation_id = $_GET['conversation_id'] ?? 0;
    
    $query = "SELECT * FROM messages WHERE conversation_id = ? ORDER BY created_at ASC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $conversation_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $messages = [];
    while ($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }
    
    // Mark messages as read
    $update_query = "UPDATE messages SET status = 'read' WHERE conversation_id = ? AND sender_id != ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("ii", $conversation_id, $current_user_id);
    $update_stmt->execute();
    
    echo json_encode(['success' => true, 'messages' => $messages]);
    exit;
}

// ===== SEND MESSAGE =====
if ($action === 'sendMessage') {
    $conversation_id = $_POST['conversation_id'] ?? 0;
    $message = $_POST['message'] ?? '';
    
    if (empty($message)) {
        echo json_encode(['success' => false, 'message' => 'Message cannot be empty']);
        exit;
    }
    
    // Insert message
    $insert_query = "INSERT INTO messages (conversation_id, sender_id, message, status) VALUES (?, ?, ?, 'sent')";
    $insert_stmt = $conn->prepare($insert_query);
    $insert_stmt->bind_param("iis", $conversation_id, $current_user_id, $message);
    
    if ($insert_stmt->execute()) {
        $msg_id = $insert_stmt->insert_id;
        
        // Update conversation updated_at
        $update_conv = "UPDATE conversations SET updated_at = CURRENT_TIMESTAMP WHERE id = ?";
        $update_stmt = $conn->prepare($update_conv);
        $update_stmt->bind_param("i", $conversation_id);
        $update_stmt->execute();
        
        echo json_encode(['success' => true, 'message_id' => $msg_id]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to send message']);
    }
    exit;
}

// ===== START NEW CONVERSATION WITH USER =====
if ($action === 'startConversation') {
    $recipient_email = $_POST['recipient_email'] ?? '';
    
    if (empty($recipient_email)) {
        echo json_encode(['success' => false, 'message' => 'Email is required']);
        exit;
    }
    
    // Find user by email
    $find_query = "SELECT id FROM user WHERE email = ? LIMIT 1";
    $find_stmt = $conn->prepare($find_query);
    $find_stmt->bind_param("s", $recipient_email);
    $find_stmt->execute();
    $find_result = $find_stmt->get_result();
    
    if ($find_result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'User not found']);
        exit;
    }
    
    $recipient = $find_result->fetch_assoc();
    $recipient_id = $recipient['id'];
    
    if ($recipient_id === $current_user_id) {
        echo json_encode(['success' => false, 'message' => 'Cannot message yourself']);
        exit;
    }
    
    // Check if conversation already exists (either direction)
    $check_query = "
        SELECT id FROM conversations 
        WHERE (user_id = ? AND admin_id = ?) 
           OR (user_id = ? AND admin_id = ?)
        LIMIT 1
    ";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("iiii", $current_user_id, $recipient_id, $recipient_id, $current_user_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        $conversation = $check_result->fetch_assoc();
        echo json_encode(['success' => true, 'conversation_id' => $conversation['id']]);
        exit;
    }
    
    // Create new conversation (use admin_id field for user-to-user)
    $create_query = "INSERT INTO conversations (user_id, admin_id) VALUES (?, ?)";
    $create_stmt = $conn->prepare($create_query);
    $create_stmt->bind_param("ii", $current_user_id, $recipient_id);
    
    if ($create_stmt->execute()) {
        $conv_id = $create_stmt->insert_id;
        echo json_encode(['success' => true, 'conversation_id' => $conv_id]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to create conversation']);
    }
    exit;
}

// ===== ADMIN: GET ALL USER CONVERSATIONS =====
if ($action === 'adminGetConversations') {
    // Only admins can access this
    if ($_SESSION['authUser']['userRole'] !== 'admin') {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Forbidden']);
        exit;
    }
    
    $query = "
        SELECT 
            c.id,
            c.user_id,
            u.fullName,
            u.email,
            (SELECT message FROM messages WHERE conversation_id = c.id ORDER BY created_at DESC LIMIT 1) as last_message,
            (SELECT created_at FROM messages WHERE conversation_id = c.id ORDER BY created_at DESC LIMIT 1) as last_message_time,
            (SELECT COUNT(*) FROM messages WHERE conversation_id = c.id AND sender_id = c.user_id AND status = 'sent') as unread_count
        FROM conversations c
        JOIN user u ON c.user_id = u.id
        WHERE c.admin_id IS NOT NULL
        ORDER BY c.updated_at DESC
    ";
    
    $result = $conn->query($query);
    $conversations = [];
    
    while ($row = $result->fetch_assoc()) {
        $conversations[] = $row;
    }
    
    echo json_encode(['success' => true, 'conversations' => $conversations]);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid action']);
?>
