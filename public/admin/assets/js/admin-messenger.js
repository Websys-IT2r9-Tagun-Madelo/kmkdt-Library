const ADMIN_MESSAGING_API = '../../app/controller/messagingController.php';
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
        const lastMessageTime = conv.last_message_time || '';
        const lastMessage = conv.last_message || 'No messages yet';
        
        // SAFELY FALLBACK IF NAMES MISMATCH VIA THE CONTROLLER
        const displayName = conv.recipient_name || conv.fullName || 'Unknown User';
        const displayEmail = conv.recipient_email || conv.email || '';
        const displayUserId = conv.recipient_id || conv.user_id;
        
        return `
            <li class="conversation-item ${isActive}" data-conversation-id="${conv.id}" data-user-id="${displayUserId}">
                <div class="conversation-avatar">${displayName.charAt(0).toUpperCase()}</div>
                <div class="conversation-info">
                    <div class="conversation-header">
                        <span class="conversation-name">${displayName}</span>
                        ${unreadBadge}
                    </div>
                    <div style="font-size: 12px; color: #999; margin-bottom: 4px;">${displayEmail}</div>
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
    
    // Align with global admin assignment definitions
    const currentAdminId = getAdminUserId();
    
    messagesContainer.innerHTML = messages.map(msg => {
        const isSent = parseInt(msg.sender_id) === parseInt(currentAdminId);
        const messageClass = isSent ? 'sent' : 'received';
        const time = formatTimeAdmin(msg.created_at);
        
        // REMOVED THE WRAPPING DIV TAGS INSIDE THE MESSAGE WRAPPER
        return `
            <div class="message ${messageClass}">
                <div class="message-bubble">${escapeHtmlAdmin(msg.message)}</div>
                <div class="message-time">${time}</div>
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
    if (adminRefreshInterval) clearInterval(adminRefreshInterval);

    adminRefreshInterval = setInterval(() => {
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
    
    if (diff < 60000) return 'just now';
    
    if (diff < 3600000) {
        const mins = Math.floor(diff / 60000);
        return mins + 'm ago';
    }
    
    if (diff < 86400000) {
        const hours = Math.floor(diff / 3600000);
        return hours + 'h ago';
    }
    
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
    const adminIdElement = document.querySelector('[data-admin-id]');
    if (adminIdElement) {
        return adminIdElement.getAttribute('data-admin-id');
    }
    
    const adminElement = document.querySelector('.current-admin-id');
    if (adminElement) {
        return adminElement.textContent;
    }
    
    return 4; // Normalized Admin ID reference alignment
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', initializeAdminMessenger);

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

    // ==========================================
    // ADDED: LIVE SEARCH FILTER FOR CONVERSATIONS
    // ==========================================
    const searchInput = document.getElementById('adminConversationSearch');
    const clearBtn = document.getElementById('clearSearchBtn');

    if (searchInput) {
        searchInput.addEventListener('input', () => {
            const query = searchInput.value.toLowerCase().trim();
            const items = document.querySelectorAll('.admin-conversations-list .conversation-item');
            
            // Toggle the 'X' clear button visibility if it exists
            if (clearBtn) {
                if (query.length > 0) {
                    clearBtn.classList.remove('hidden');
                } else {
                    clearBtn.classList.add('hidden');
                }
            }

            items.forEach(item => {
                // Grab the name and email text content from the item elements
                const name = item.querySelector('.conversation-name')?.textContent.toLowerCase() || '';
                const email = item.style.cssText ? '' : item.querySelector('div[style*="font-size: 12px"]')?.textContent.toLowerCase() || ''; 
                
                // If the name or email matches the query, show it; otherwise hide it
                if (name.includes(query) || email.includes(query)) {
                    item.style.setProperty('display', 'flex', 'important');
                } else {
                    item.style.setProperty('display', 'none', 'important');
                }
            });

            // Handle the "No results found" visual placeholder safely
            const visibleItems = document.querySelectorAll('.admin-conversations-list .conversation-item[style*="display: flex"]');
            const listContainer = document.querySelector('.admin-conversations-list');
            let noResultPlaceholder = document.getElementById('search-no-results');

            if (visibleItems.length === 0 && items.length > 0) {
                if (!noResultPlaceholder) {
                    noResultPlaceholder = document.createElement('li');
                    noResultPlaceholder.id = 'search-no-results';
                    noResultPlaceholder.style.cssText = 'padding: 24px; text-align: center; color: #94a3b8; font-size: 13.5px;';
                    noResultPlaceholder.textContent = 'No matching users found';
                    listContainer.appendChild(noResultPlaceholder);
                }
            } else if (noResultPlaceholder) {
                noResultPlaceholder.remove();
            }
        });
    }

    // Optional: Make the clear button clear the text out when clicked
    if (clearBtn && searchInput) {
        clearBtn.addEventListener('click', () => {
            searchInput.value = '';
            clearBtn.classList.add('hidden');
            searchInput.dispatchEvent(new Event('input')); // Re-trigger filtering to reset list
            searchInput.focus();
        });
    }
}