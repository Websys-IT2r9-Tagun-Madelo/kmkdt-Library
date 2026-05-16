// ===== MESSENGER CHAT SYSTEM =====
// Configuration
const MESSAGING_API = '/kmkdt-Library/app/controller/messagingController.php';
const REFRESH_INTERVAL = 3000; // 3 seconds - refresh messages

let currentConversationId = null;
let currentRecipientId = null;
let refreshInterval = null;

// ===== INITIALIZE MESSENGER =====
function initializeMessenger() {
    console.log('Initializing messenger...');
    
    // Auto-load or create admin conversation on init
    ensureAdminConversation();
    
    // Load conversations on page load
    loadConversations();
    
    // Set up event listeners
    setupEventListeners();
    
    // Start auto-refresh
    startAutoRefresh();
}

// ===== ENSURE ADMIN CONVERSATION EXISTS =====
function ensureAdminConversation() {
    fetch(`${MESSAGING_API}?action=getAdminConversation`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('Admin conversation ID:', data.conversation_id);
                // Admin conversation is now ready, will be loaded with other conversations
            }
        })
        .catch(error => console.error('Error creating admin conversation:', error));
}

// ===== LOAD CONVERSATIONS =====
function loadConversations() {
    fetch(`${MESSAGING_API}?action=getConversations`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderConversations(data.conversations);
            } else {
                console.error('Error loading conversations:', data.message);
            }
        })
        .catch(error => console.error('Error:', error));
}

// ===== RENDER CONVERSATIONS LIST =====
function renderConversations(conversations) {
    const conversationsList = document.querySelector('.conversations-list');
    
    if (!conversationsList) return;
    
    if (conversations.length === 0) {
        conversationsList.innerHTML = '<li style="padding: 16px; text-align: center; color: #999;">No conversations yet</li>';
        return;
    }
    
    // Sort conversations: admin (recipient_id=1) first, then others by date
    conversations.sort((a, b) => {
        // Admin (typically ID 1) always goes first
        if (a.recipient_id === 1 || a.recipient_id == 1) return -1;
        if (b.recipient_id === 1 || b.recipient_id == 1) return 1;
        // Then sort by most recent
        return new Date(b.created_at) - new Date(a.created_at);
    });
    
    conversationsList.innerHTML = conversations.map((conv, index) => {
        const isActive = currentConversationId === conv.id ? 'active' : '';
        const unreadClass = conv.unread_count > 0 ? '' : 'hidden';
        const unreadBadge = conv.unread_count > 0 ? `<span class="conversation-badge ${unreadClass}">${conv.unread_count}</span>` : '';
        const lastMessageTime = formatTime(conv.last_message_time);
        const lastMessage = conv.last_message || 'No messages yet';
        const isAdmin = conv.recipient_id === 1 || conv.recipient_id == 1;
        const adminBadge = isAdmin ? '<span class="admin-badge">Admin</span>' : '';
        const adminPin = isAdmin ? '<span style="font-size: 10px; color: #97ee5b; margin-left: 4px;">📌</span>' : '';
        
        return `
            <li class="conversation-item ${isActive}" data-conversation-id="${conv.id}" data-recipient-id="${conv.recipient_id}" style="${isAdmin ? 'border-top: 2px solid #97ee5b;' : ''}">
                <div class="conversation-avatar" style="${isAdmin ? 'background: #97ee5b; font-weight: 700;' : ''}">${conv.recipient_name.charAt(0).toUpperCase()}</div>
                <div class="conversation-info">
                    <div class="conversation-header">
                        <div>
                            <span class="conversation-name">${conv.recipient_name}</span>
                            ${adminBadge}
                            ${adminPin}
                        </div>
                        ${unreadBadge}
                    </div>
                    <div class="conversation-preview">${lastMessage}</div>
                </div>
                <div class="conversation-time">${lastMessageTime}</div>
            </li>
        `;
    }).join('');
    
    // Add click listeners to conversation items
    document.querySelectorAll('.conversation-item').forEach(item => {
        item.addEventListener('click', () => {
            const convId = item.getAttribute('data-conversation-id');
            const recipientId = item.getAttribute('data-recipient-id');
            selectConversation(convId, recipientId);
        });
    });
}

// ===== SELECT CONVERSATION =====
function selectConversation(conversationId, recipientId) {
    currentConversationId = conversationId;
    currentRecipientId = recipientId;
    
    // Update active state
    document.querySelectorAll('.conversation-item').forEach(item => {
        item.classList.remove('active');
    });
    document.querySelector(`[data-conversation-id="${conversationId}"]`)?.classList.add('active');
    
    // Load messages
    loadMessages(conversationId);
}

// ===== LOAD MESSAGES =====
function loadMessages(conversationId) {
    fetch(`${MESSAGING_API}?action=getMessages&conversation_id=${conversationId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderMessages(data.messages);
                scrollMessagesToBottom();
            } else {
                console.error('Error loading messages:', data.message);
            }
        })
        .catch(error => console.error('Error:', error));
}

// ===== RENDER MESSAGES =====
function renderMessages(messages) {
    const messagesContainer = document.querySelector('.messages-container');
    
    if (!messagesContainer) return;
    
    if (messages.length === 0) {
        messagesContainer.innerHTML = '<div class="messages-empty"><p>No messages yet. Start the conversation!</p></div>';
        return;
    }
    
    // Get current user ID from the page
    const currentUserId = getCurrentUserId();
    
    messagesContainer.innerHTML = messages.map(msg => {
        const isSent = parseInt(msg.sender_id) === parseInt(currentUserId);
        const messageClass = isSent ? 'sent' : 'received';
        const time = formatTime(msg.created_at);
        
        return `
            <div class="message ${messageClass}">
                <div>
                    <div class="message-bubble">${escapeHtml(msg.message)}</div>
                    <div class="message-time">${time}</div>
                </div>
            </div>
        `;
    }).join('');
    
    // Show message input area
    document.querySelector('.message-input-area').style.display = 'flex';
}

// ===== SEND MESSAGE =====
function sendMessage() {
    const textarea = document.querySelector('.message-input-wrapper textarea');
    const message = textarea.value.trim();
    
    if (!message || !currentConversationId) return;
    
    const formData = new FormData();
    formData.append('action', 'sendMessage');
    formData.append('conversation_id', currentConversationId);
    formData.append('message', message);
    
    fetch(MESSAGING_API, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            textarea.value = '';
            loadMessages(currentConversationId);
            loadConversations();
        } else {
            alert('Error sending message: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error sending message');
    });
}

// ===== SETUP EVENT LISTENERS =====
function setupEventListeners() {
    // Send button
    const sendBtn = document.querySelector('.send-btn');
    if (sendBtn) {
        sendBtn.addEventListener('click', sendMessage);
    }
    
    // Message input - send on Enter (Shift+Enter for new line)
    const textarea = document.querySelector('.message-input-wrapper textarea');
    if (textarea) {
        textarea.addEventListener('keypress', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });
        
        // Auto-expand textarea
        textarea.addEventListener('input', () => {
            textarea.style.height = 'auto';
            textarea.style.height = Math.min(textarea.scrollHeight, 100) + 'px';
        });
    }
    
    // Library Support button - click to open admin chat
    const librarySupportBtn = document.querySelector('#librarySupport');
    if (librarySupportBtn) {
        librarySupportBtn.addEventListener('click', openLibrarySupport);
        // Add hover effect
        librarySupportBtn.addEventListener('mouseenter', () => {
            librarySupportBtn.style.transform = 'translateY(-2px)';
            librarySupportBtn.style.boxShadow = '0 4px 12px rgba(151, 238, 91, 0.3)';
        });
        librarySupportBtn.addEventListener('mouseleave', () => {
            librarySupportBtn.style.transform = 'translateY(0)';
            librarySupportBtn.style.boxShadow = '0 2px 8px rgba(151, 238, 91, 0.2)';
        });
    }
    
    // New chat button
    const newChatBtn = document.querySelector('.new-chat-btn');
    if (newChatBtn) {
        newChatBtn.addEventListener('click', openNewChatModal);
    }
    
    // New chat modal buttons
    const startChatBtn = document.querySelector('.btn-start-chat');
    const cancelBtn = document.querySelector('.btn-cancel');
    
    if (startChatBtn) {
        startChatBtn.addEventListener('click', startNewChat);
    }
    
    if (cancelBtn) {
        cancelBtn.addEventListener('click', closeNewChatModal);
    }
    
    // Close modal on background click
    const modal = document.querySelector('.new-chat-modal');
    if (modal) {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                closeNewChatModal();
            }
        });
    }
}

// ===== NEW CHAT MODAL =====
function openNewChatModal() {
    const modal = document.querySelector('.new-chat-modal');
    if (modal) {
        modal.classList.add('active');
        const input = modal.querySelector('input');
        if (input) input.focus();
    }
}

function closeNewChatModal() {
    const modal = document.querySelector('.new-chat-modal');
    if (modal) {
        modal.classList.remove('active');
        modal.querySelector('input').value = '';
    }
}

function startNewChat() {
    const modal = document.querySelector('.new-chat-modal');
    const emailInput = modal.querySelector('input');
    const email = emailInput.value.trim();
    
    if (!email) {
        alert('Please enter an email address');
        return;
    }
    
    const formData = new FormData();
    formData.append('action', 'startConversation');
    formData.append('recipient_email', email);
    
    fetch(MESSAGING_API, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeNewChatModal();
            loadConversations();
            setTimeout(() => {
                selectConversation(data.conversation_id, null);
            }, 300);
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error creating conversation');
    });
}

// ===== OPEN LIBRARY SUPPORT CHAT =====
function openLibrarySupport() {
    fetch(`${MESSAGING_API}?action=getAdminConversation`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                selectConversation(data.conversation_id, 1);
            } else {
                alert('Error opening support chat');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error opening support chat');
        });
}

// ===== AUTO-REFRESH MESSAGES =====
function startAutoRefresh() {
    // Refresh messages every 3 seconds if a conversation is selected
    setInterval(() => {
        if (currentConversationId) {
            loadMessages(currentConversationId);
        }
        loadConversations();
    }, REFRESH_INTERVAL);
}

// ===== UTILITY FUNCTIONS =====
function formatTime(dateString) {
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

function scrollMessagesToBottom() {
    const messagesContainer = document.querySelector('.messages-container');
    if (messagesContainer) {
        setTimeout(() => {
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }, 100);
    }
}

function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, m => map[m]);
}

function getCurrentUserId() {
    // Get user ID from hidden element or data attribute
    const userIdElement = document.querySelector('[data-user-id]');
    if (userIdElement) {
        return userIdElement.getAttribute('data-user-id');
    }
    
    // Alternative: extract from page or session
    const userElement = document.querySelector('.current-user-id');
    if (userElement) {
        return userElement.textContent;
    }
    
    return 0;
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', initializeMessenger);
