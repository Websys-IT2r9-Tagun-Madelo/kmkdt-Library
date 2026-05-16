<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (ob_get_length()) ob_clean();
header('Content-Type: application/json; charset=UTF-8');

// ===== CONFIGURATION RESOLVER =====
$configPath = null;
$possiblePaths = [
    dirname(__DIR__) . '/app/config/config.php',                                
    dirname(__DIR__, 2) . '/app/config/config.php',                            
    dirname(__DIR__, 3) . '/app/config/config.php',                            
    $_SERVER['DOCUMENT_ROOT'] . '/kmkdt-Library/app/config/config.php'         
];

foreach ($possiblePaths as $path) {
    $normalizedPath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
    if (file_exists($normalizedPath)) {
        $configPath = $normalizedPath;
        break;
    }
}

if ($configPath) {
    include_once($configPath);
} else {
    http_response_code(500);
    die(json_encode([
        'success' => false, 
        'message' => 'Config file could not be found locally.',
        'debug_executed_from' => __FILE__ 
    ]));
}

// Secure session parsing extraction layer
$current_user_id = $_SESSION['user_id'] ?? $_SESSION['authUser']['user_id'] ?? 0;

if ($current_user_id === 0) {
    http_response_code(401);
    die(json_encode(['success' => false, 'message' => 'Unauthorized: Please log into your account.']));
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';
$conn = $GLOBALS['conn'] ?? $conn;

if (!$conn) {
    http_response_code(500);
    die(json_encode(['success' => false, 'message' => 'Database connector context unavailable.']));
}

// ===== GET USER INFO =====
if ($action === 'getUser') {
    $user_id = (int)($_GET['user_id'] ?? 0);
    
    $query = "SELECT id, fullName, emailAddress, username FROM user WHERE id = ?";
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

// ===== GET OR CREATE CONVERSATION WITH ADMIN (LIBRARY SUPPORT CARD) =====
if ($action === 'getAdminConversation' || $action === 'support') {
    $admin_id = 4;
    
    if ((int)$current_user_id === $admin_id) {
        echo json_encode(['success' => false, 'message' => 'Administrators cannot spawn self-support channels.']);
        exit;
    }
    
    // Check if conversation exists
    $query = "SELECT id FROM conversations WHERE user_id = ? AND admin_id = ? LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $current_user_id, $admin_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $conversation = $result->fetch_assoc();
        echo json_encode(['success' => true, 'conversation_id' => $conversation['id']]);
    } else {
        // Create new conversation link
        $insert_query = "INSERT INTO conversations (user_id, admin_id, created_at, updated_at) VALUES (?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)";
        $insert_stmt = $conn->prepare($insert_query);
        $insert_stmt->bind_param("ii", $current_user_id, $admin_id);
        
        if ($insert_stmt->execute()) {
            echo json_encode(['success' => true, 'conversation_id' => $insert_stmt->insert_id]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to create support conversation connection channel']);
        }
    }
    exit;
}

// ===== GET USER CONVERSATIONS (SIDEBAR LIST) =====
// ===== GET USER CONVERSATIONS (SIDEBAR LIST) =====
if ($action === 'getConversations' || $action === 'get_conversations') {
    $query = "
        SELECT 
            c.id,
            c.created_at,
            c.user_id as creator_id,
            c.admin_id as assign_id,
            CASE 
                WHEN c.user_id = ? THEN u2.fullName
                ELSE u1.fullName
            END as recipient_name,
            CASE 
                WHEN c.user_id = ? THEN u2.emailAddress
                ELSE u1.emailAddress
            END as recipient_email,
            CASE 
                WHEN c.user_id = ? THEN u2.id
                ELSE u1.id
            END as recipient_id,
            COALESCE((SELECT message FROM messages WHERE conversation_id = c.id ORDER BY created_at DESC LIMIT 1), 'No messages yet') as last_message,
            COALESCE((SELECT DATE_FORMAT(created_at, '%h:%i %p') FROM messages WHERE conversation_id = c.id ORDER BY created_at DESC LIMIT 1), DATE_FORMAT(c.created_at, '%h:%i %p')) as last_message_time,
            COALESCE((SELECT created_at FROM messages WHERE conversation_id = c.id ORDER BY created_at DESC LIMIT 1), c.updated_at) as raw_sort_time,
            (SELECT COUNT(*) FROM messages WHERE conversation_id = c.id AND sender_id != ? AND status = 'sent') as unread_count
        FROM conversations c
        LEFT JOIN user u1 ON c.user_id = u1.id
        LEFT JOIN user u2 ON c.admin_id = u2.id
        WHERE c.user_id = ? OR c.admin_id = ?
        ORDER BY c.updated_at DESC
    ";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iiiiii", $current_user_id, $current_user_id, $current_user_id, $current_user_id, $current_user_id, $current_user_id);
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
if ($action === 'getMessages' || $action === 'get_messages') {
    $conversation_id = (int)($_GET['conversation_id'] ?? 0);
    
    $query = "SELECT sender_id, message, status, DATE_FORMAT(created_at, '%h:%i %p') as time_stamp, created_at FROM messages WHERE conversation_id = ? ORDER BY id ASC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $conversation_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $messages = [];
    while ($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }
    
    // Mark incoming messages as read safely
    $update_query = "UPDATE messages SET status = 'read' WHERE conversation_id = ? AND sender_id != ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("ii", $conversation_id, $current_user_id);
    $update_stmt->execute();
    
    echo json_encode(['success' => true, 'messages' => $messages]);
    exit;
}

// ===== SEND MESSAGE =====
if ($action === 'sendMessage' || $action === 'send_message') {
    $conversation_id = $_POST['conversation_id'] ?? 0;
    $message = trim($_POST['message'] ?? '');
    
    if (empty($message)) {
        echo json_encode(['success' => false, 'message' => 'Message content cannot be blank.']);
        exit;
    }

    if ($conversation_id === 'support') {
        $admin_id = 4;
        $check = "SELECT id FROM conversations WHERE user_id = ? AND admin_id = ? LIMIT 1";
        $cStmt = $conn->prepare($check);
        $cStmt->bind_param("ii", $current_user_id, $admin_id);
        $cStmt->execute();
        $res = $cStmt->get_result()->fetch_assoc();
        
        if ($res) {
            $conversation_id = (int)$res['id'];
        } else {
            $insert = "INSERT INTO conversations (user_id, admin_id, created_at, updated_at) VALUES (?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)";
            $iStmt = $conn->prepare($insert);
            $iStmt->bind_param("ii", $current_user_id, $admin_id);
            $iStmt->execute();
            $conversation_id = (int)$conn->insert_id;
        }
    } else {
        $conversation_id = (int)$conversation_id;
    }
    
    // Insert new message record
    $insert_query = "INSERT INTO messages (conversation_id, sender_id, message, status, created_at) VALUES (?, ?, ?, 'sent', CURRENT_TIMESTAMP)";
    $insert_stmt = $conn->prepare($insert_query);
    $insert_stmt->bind_param("iis", $conversation_id, $current_user_id, $message);
    
    if ($insert_stmt->execute()) {
        $msg_id = $insert_stmt->insert_id;
        
        // Update handling to notify parent thread changes
        $update_conv = "UPDATE conversations SET updated_at = CURRENT_TIMESTAMP WHERE id = ?";
        $update_stmt = $conn->prepare($update_conv);
        $update_stmt->bind_param("i", $conversation_id);
        $update_stmt->execute();
        
        echo json_encode(['success' => true, 'conversation_id' => $conversation_id, 'message_id' => $msg_id]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to save message context to database record logs.']);
    }
    exit;
}

// ===== START NEW CONVERSATION WITH USER =====
if ($action === 'startConversation' || $action === 'create_by_email') {
    $recipient_email = trim($_POST['recipient_email'] ?? $_POST['email'] ?? '');
    
    if (empty($recipient_email)) {
        echo json_encode(['success' => false, 'message' => 'A valid user email address target is required.']);
        exit;
    }
    
    $find_query = "SELECT id FROM user WHERE emailAddress = ? LIMIT 1";
    $find_stmt = $conn->prepare($find_query);
    $find_stmt->bind_param("s", $recipient_email);
    $find_stmt->execute();
    $find_result = $find_stmt->get_result();
    
    if ($find_result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'No active system account matches that email address.']);
        exit;
    }
    
    $recipient = $find_result->fetch_assoc();
    $recipient_id = (int)$recipient['id'];
    
    if ($recipient_id === (int)$current_user_id) {
        echo json_encode(['success' => false, 'message' => 'You cannot initiate chat threads with yourself.']);
        exit;
    }
    
    // Check if conversation already exists in either structural mapping direction
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
    
    // Create new structural conversation record link
    $create_query = "INSERT INTO conversations (user_id, admin_id, created_at, updated_at) VALUES (?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)";
    $create_stmt = $conn->prepare($create_query);
    $create_stmt->bind_param("ii", $current_user_id, $recipient_id);
    
    if ($create_stmt->execute()) {
        echo json_encode(['success' => true, 'conversation_id' => $create_stmt->insert_id]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to initialize database conversation container.']);
    }
    exit;
}

// ===== ADMIN: GET ALL USER CONVERSATIONS =====
if ($action === 'adminGetConversations') {
    $current_role = $_SESSION['role'] ?? $_SESSION['authUser']['role'] ?? '';
    if (strtolower($current_role) !== 'admin') {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Access Denied: Administrative privileges required.']);
        exit;
    }
    
    $query = "
        SELECT 
            c.id,
            c.user_id,
            u.fullName,
            u.emailAddress as email,
            COALESCE((SELECT message FROM messages WHERE conversation_id = c.id ORDER BY created_at DESC LIMIT 1), 'No messages yet') as last_message,
            COALESCE((SELECT created_at FROM messages WHERE conversation_id = c.id ORDER BY created_at DESC LIMIT 1), c.created_at) as last_message_time,
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

// Invalid fallback handling 
http_response_code(400);
die(json_encode([
    'success' => false, 
    'message' => 'Invalid action handling format requested.',
    'debug_received_action' => $action,
    'debug_request_method' => $_SERVER['REQUEST_METHOD']
]));
?>