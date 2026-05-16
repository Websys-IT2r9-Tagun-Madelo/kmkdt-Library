// ===== ADMIN MESSENGER CHAT SYSTEM =====
// Configuration
const ADMIN_MESSAGING_API = '/kmkdt-Library/app/controller/messagingController.php';
const ADMIN_REFRESH_INTERVAL = 3000; // 3 seconds - refresh messages

let adminCurrentConversationId = null;
let adminCurrentUserId = null;
let adminRefreshInterval = null;

// ===== INITIALIZE ADMIN MESSENGER =====
function initializeAdminMessenger() {
    console.log('Initializing admin messenger...');
    
    // Load conversations on page load
    adminLoadConversations();
    
    // Set up event listeners
    adminSetupEventListeners();
    
    // Start auto-refresh
    adminStartAutoRefresh();
}

// ===== LOAD ADMIN CONVERSATIONS =====
function adminLoadConversations() {
    fetch(`${ADMIN_MESSAGING_API}?action=adminGetConversations`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                adminRenderConversations(data.conversations);
            } else {
                console.error('Error loading conversations:', data.message);
            }
        })
        .catch(error => console.error('Error:', error));
}

// ===== RENDER ADMIN CONVERSATIONS LIST =====
function adminRenderConversations(conversations) {
    const conversationsList = document.querySelector('.admin-conversations-list');
    
    if (!conversationsList) return;
    
    if (conversations.length === 0) {
        conversationsList.innerHTML = '<li style="padding: 16px; text-align: center; color: #999;">No conversations yet</li>';
        return;
    }
    
    conversationsList.innerHTML = conversations.map(conv => {
        const isActive = adminCurrentConversationId === conv.id ? 'active' : '';
        const unreadClass = conv.unread_count > 0 ? '' : 'hidden';
        const unreadBadge = conv.unread_count > 0 ? `<span class="conversation-badge ${unreadClass}">${conv.unread_count}</span>` : '';
        const lastMessageTime = formatTimeAdmin(conv.last_message_time);
        const lastMessage = conv.last_message || 'No messages yet';
        
        return `
            <li class="conversation-item ${isActive}" data-conversation-id="${conv.id}" data-user-id="${conv.user_id}">
                <div class="conversation-avatar">${conv.fullName.charAt(0).toUpperCase()}</div>
                <div class="conversation-info">
                    <div class="conversation-header">
                        <span class="conversation-name">${conv.fullName}</span>
                        ${unreadBadge}
                    </div>
                    <div style="font-size: 12px; color: #999; margin-bottom: 4px;">${conv.email}</div>
                    <div class="conversation-preview">${lastMessage}</div>
                </div>
                <div class="conversation-time">${lastMessageTime}</div>
            </li>
        `;
    }).join('');
    
    // Add click listeners to conversation items
    document.querySelectorAll('.admin-conversations-list .conversation-item').forEach(item => {
        item.addEventListener('click', () => {
            const convId = item.getAttribute('data-conversation-id');
            const userId = item.getAttribute('data-user-id');
            adminSelectConversation(convId, userId);
        });
    });
}

// ===== SELECT CONVERSATION (ADMIN) =====
function adminSelectConversation(conversationId, userId) {
    adminCurrentConversationId = conversationId;
    adminCurrentUserId = userId;
    
    // Update active state
    document.querySelectorAll('.admin-conversations-list .conversation-item').forEach(item => {
        item.classList.remove('active');
    });
    document.querySelector(`.admin-conversations-list [data-conversation-id="${conversationId}"]`)?.classList.add('active');
    
    // Load messages
    adminLoadMessages(conversationId);
}

// ===== LOAD MESSAGES (ADMIN) =====
function adminLoadMessages(conversationId) {
    fetch(`${ADMIN_MESSAGING_API}?action=getMessages&conversation_id=${conversationId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                adminRenderMessages(data.messages);
                adminScrollMessagesToBottom();
            } else {
                console.error('Error loading messages:', data.message);
            }
        })
        .catch(error => console.error('Error:', error));
}

// ===== RENDER MESSAGES (ADMIN) =====
function adminRenderMessages(messages) {
    const messagesContainer = document.querySelector('.admin-messages-container');
    
    if (!messagesContainer) return;
    
    if (messages.length === 0) {
        messagesContainer.innerHTML = '<div class="messages-empty"><p>No messages yet. Start the conversation!</p></div>';
        return;
    }
    
    // Get current admin ID (Admin should be user ID 1 typically)
    const currentAdminId = getAdminUserId();
    
    messagesContainer.innerHTML = messages.map(msg => {
        const isSent = parseInt(msg.sender_id) === parseInt(currentAdminId);
        const messageClass = isSent ? 'sent' : 'received';
        const time = formatTimeAdmin(msg.created_at);
        
        return `
            <div class="message ${messageClass}">
                <div>
                    <div class="message-bubble">${escapeHtmlAdmin(msg.message)}</div>
                    <div class="message-time">${time}</div>
                </div>
            </div>
        `;
    }).join('');
    
    // Show message input area
    document.querySelector('.admin-message-input-area').style.display = 'flex';
}

// ===== SEND MESSAGE (ADMIN) =====
function adminSendMessage() {
    const textarea = document.querySelector('.admin-message-input-wrapper textarea');
    const message = textarea.value.trim();
    
    if (!message || !adminCurrentConversationId) return;
    
    const formData = new FormData();
    formData.append('action', 'sendMessage');
    formData.append('conversation_id', adminCurrentConversationId);
    formData.append('message', message);
    
    fetch(ADMIN_MESSAGING_API, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            textarea.value = '';
            adminLoadMessages(adminCurrentConversationId);
            adminLoadConversations();
        } else {
            alert('Error sending message: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error sending message');
    });
}

// ===== SETUP EVENT LISTENERS (ADMIN) =====
function adminSetupEventListeners() {
    // Send button
    const sendBtn = document.querySelector('.admin-send-btn');
    if (sendBtn) {
        sendBtn.addEventListener('click', adminSendMessage);
    }
    
    // Message input - send on Enter (Shift+Enter for new line)
    const textarea = document.querySelector('.admin-message-input-wrapper textarea');
    if (textarea) {
        textarea.addEventListener('keypress', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                adminSendMessage();
            }
        });
        
        // Auto-expand textarea
        textarea.addEventListener('input', () => {
            textarea.style.height = 'auto';
            textarea.style.height = Math.min(textarea.scrollHeight, 100) + 'px';
        });
    }
}

// ===== AUTO-REFRESH MESSAGES (ADMIN) =====
function adminStartAutoRefresh() {
    // Refresh messages every 3 seconds if a conversation is selected
    setInterval(() => {
        if (adminCurrentConversationId) {
            adminLoadMessages(adminCurrentConversationId);
        }
        adminLoadConversations();
    }, ADMIN_REFRESH_INTERVAL);
}

// ===== UTILITY FUNCTIONS =====
function formatTimeAdmin(dateString) {
    if (!dateString) return '';
    
    const date = new Date(dateString);
    const now = new Date();
    const diff = now - date;
    
    // Less than 1 minute
    if (diff < 60000) return 'just now';
    
    // Less than 1 hour
    if (diff < 3600000) {
        const mins = Math.floor(diff / 60000);
        return mins + 'm ago';
    }
    
    // Less than 1 day
    if (diff < 86400000) {
        const hours = Math.floor(diff / 3600000);
        return hours + 'h ago';
    }
    
    // Today vs Yesterday
    const dateOnly = date.toLocaleDateString();
    const todayOnly = now.toLocaleDateString();
    
    if (dateOnly === todayOnly) {
        return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    }
    
    const yesterday = new Date(now);
    yesterday.setDate(yesterday.getDate() - 1);
    if (dateOnly === yesterday.toLocaleDateString()) {
        return 'Yesterday';
    }
    
    // Older dates
    return date.toLocaleDateString();
}

function adminScrollMessagesToBottom() {
    const messagesContainer = document.querySelector('.admin-messages-container');
    if (messagesContainer) {
        setTimeout(() => {
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }, 100);
    }
}

function escapeHtmlAdmin(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, m => map[m]);
}

function getAdminUserId() {
    // Get admin user ID from hidden element
    const adminIdElement = document.querySelector('[data-admin-id]');
    if (adminIdElement) {
        return adminIdElement.getAttribute('data-admin-id');
    }
    
    // Alternative: extract from page
    const adminElement = document.querySelector('.current-admin-id');
    if (adminElement) {
        return adminElement.textContent;
    }
    
    return 1; // Default to admin ID 1
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', initializeAdminMessenger);
