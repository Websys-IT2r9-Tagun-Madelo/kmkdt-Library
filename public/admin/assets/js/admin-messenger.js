// ===== MESSENGER ADMIN PANEL CONTROLLER =====
const ADMIN_MESSAGING_API = '../../app/controller/messagingController.php';
const ADMIN_REFRESH_INTERVAL = 3000; // 3 seconds - refresh messages
const ADMIN_DEFAULT_ID = 14;          // Fixed fallback admin ID configuration

let adminCurrentConversationId = null;
let adminCurrentUserId = null;
let adminRefreshInterval = null;
let isAdminPolling = false;          // Flag to prevent stacking asynchronous polling cycles

// Cached DOM Elements 
let adminDomElements = {};

function cacheAdminElements() {
    adminDomElements = {
        conversationsList: document.querySelector('.admin-conversations-list'),
        messagesContainer: document.querySelector('.admin-messages-container'),
        inputArea: document.querySelector('.admin-message-input-area'),
        headerTitle: document.querySelector(".current-chat-title"),
        headerSub: document.querySelector(".current-chat-subtitle"),
        textarea: document.querySelector('.admin-message-input-wrapper textarea'),
        sendBtn: document.querySelector('.admin-send-btn')
    };
}

// ===== INITIALIZE ADMIN MESSENGER =====
function initializeAdminMessenger() {
    console.log('Initializing admin messenger...');
    cacheAdminElements();
    
    // Load conversations on page load
    adminLoadConversations();
    
    // Set up event listeners
    adminSetupEventListeners();
    
    // Start auto-refresh
    adminStartAutoRefresh();
}

// ===== SAFE HTML ESCAPING UTILITY =====
function escapeHtmlAdmin(text) {
    if (!text) return '';
    return text
        .toString()
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

// ===== GET CURRENT ADMIN USER SESSION ID =====
function getAdminUserId() {
    const adminIdElement = document.querySelector('[data-admin-id]');
    if (adminIdElement) {
        return parseInt(adminIdElement.getAttribute('data-admin-id'), 10) || ADMIN_DEFAULT_ID;
    }
    return ADMIN_DEFAULT_ID; 
}

// ===== LOAD ADMIN CONVERSATIONS =====
async function adminLoadConversations() {
    try {
        const response = await fetch(`${ADMIN_MESSAGING_API}?action=adminGetConversations`);
        const data = await response.json();
        if (data.success) {
            adminRenderConversations(data.conversations);
        } else {
            console.error('Error loading conversations:', data.message);
        }
    } catch (error) {
        console.error('Network transport error loading admin list views:', error);
    }
}

// ===== RENDER ADMIN CONVERSATIONS LIST =====
function adminRenderConversations(conversations) {
    const listContainer = adminDomElements.conversationsList;
    if (!listContainer) return;
    
    if (!conversations || conversations.length === 0) {
        listContainer.innerHTML = '<li style="padding: 16px; text-align: center; color: #999; list-style: none;">No conversations yet</li>';
        return;
    }
    
    // Sort incoming conversations chronologically
    const sortedConversations = [...conversations].sort((a, b) => {
        const timeA = a.last_message_time ? new Date(a.last_message_time.replace(/-/g, "/")) : new Date(0);
        const timeB = b.last_message_time ? new Date(b.last_message_time.replace(/-/g, "/")) : new Date(0);
        return timeB - timeA;
    });
    
    listContainer.innerHTML = sortedConversations.map(conv => {
        const isActive = adminCurrentConversationId && parseInt(adminCurrentConversationId, 10) === parseInt(conv.id, 10);
        const unreadBadge = conv.unread_count > 0 ? `<span class="conversation-badge">${conv.unread_count}</span>` : '';
        const lastMessageTime = formatTimeAdmin(conv.last_message_time || conv.created_at);
        const lastMessage = conv.last_message || 'No messages yet';
        
        const displayName = conv.recipient_name || conv.fullName || 'Unknown User';
        const displayEmail = conv.recipient_email || conv.email || '';
        const displayUserId = conv.recipient_id || conv.user_id;
        
        return `
            <li class="conversation-item ${isActive ? 'active' : ''}" 
                data-conversation-id="${conv.id}" 
                data-user-id="${displayUserId}"
                data-recipient-name="${escapeHtmlAdmin(displayName)}">
                <div class="conversation-avatar">${escapeHtmlAdmin(displayName.charAt(0).toUpperCase())}</div>
                <div class="conversation-info">
                    <div class="conversation-header">
                        <span class="conversation-name">${escapeHtmlAdmin(displayName)}</span>
                        ${unreadBadge}
                    </div>
                    <div class="user-email-row" style="font-size: 12px; color: #94a3b8; margin-bottom: 4px;">${escapeHtmlAdmin(displayEmail)}</div>
                    <div class="conversation-preview">${escapeHtmlAdmin(lastMessage)}</div>
                </div>
                <div class="conversation-time">${lastMessageTime}</div>
            </li>
        `;
    }).join('');
    
    // Re-bind listeners
    listContainer.querySelectorAll('.conversation-item').forEach(item => {
        item.addEventListener('click', () => {
            adminSelectConversation(
                item.getAttribute('data-conversation-id'),
                item.getAttribute('data-user-id'),
                item.getAttribute('data-recipient-name')
            );
        });
    });
}

// ===== SELECT CONVERSATION (ADMIN) =====
function adminSelectConversation(conversationId, userId, recipientName) {
    adminCurrentConversationId = conversationId;
    adminCurrentUserId = userId;
    
    if (adminDomElements.inputArea) {
        adminDomElements.inputArea.style.setProperty('display', 'flex', 'important');
    }

    if (recipientName) {
        if (adminDomElements.headerTitle) adminDomElements.headerTitle.innerText = recipientName;
        if (adminDomElements.headerSub) adminDomElements.headerSub.innerText = "Direct Support Chat Session Open";
    }
    
    document.querySelectorAll('.conversation-item').forEach(item => item.classList.remove('active'));
    const activeItem = document.querySelector(`.conversation-item[data-conversation-id="${conversationId}"]`);
    if (activeItem) activeItem.classList.add('active');

    adminDomElements.messagesContainer.classList.remove('messages-empty');
    adminLoadMessages(conversationId, true);
}

// ===== LOAD MESSAGES (ADMIN) =====
async function adminLoadMessages(conversationId, forceScroll = false) {
    if (!conversationId) return;

    try {
        const response = await fetch(`${ADMIN_MESSAGING_API}?action=getMessages&conversation_id=${conversationId}`);
        const data = await response.json();
        if (data.success) {
            adminRenderMessages(data.messages, forceScroll);
        } else {
            console.error('Error loading messages:', data.message);
        }
    } catch (error) {
        console.error('Error fetching chat payload sync:', error);
    }
}

// ===== RENDER MESSAGES (ADMIN) =====
function adminRenderMessages(messages, forceScroll = false) {
    const container = adminDomElements.messagesContainer;
    if (!container) return;
    
    const isAtBottom = (container.scrollHeight - container.scrollTop <= container.clientHeight + 100);
    
    if (!messages || messages.length === 0) {
        container.innerHTML = '<div class="messages-empty" style="text-align: center; color: #94a3b8; padding: 40px 0;"><p>No messages yet. Start the conversation!</p></div>';
        return;
    }
    
    const currentAdminId = getAdminUserId();
    
    container.innerHTML = messages.map(msg => {
        const isSent = String(msg.sender_id).trim() === String(currentAdminId).trim();
        const messageClass = isSent ? 'sent' : 'received';
        const time = msg.time_stamp ? msg.time_stamp : formatTimeAdmin(msg.created_at);
        
        return `
            <div class="message ${messageClass}">
                <div>
                    <div class="message-bubble">${escapeHtmlAdmin(msg.message)}</div>
                    <div class="message-time">${time}</div>
                </div>
            </div>
        `;
    }).join('');
    
    if (forceScroll || isAtBottom) {
        adminScrollMessagesToBottom();
    }
}

// ===== SEND MESSAGE (ADMIN) =====
async function adminSendMessage() {
    const textarea = adminDomElements.textarea;
    if (!textarea || !adminCurrentConversationId) return;

    const message = textarea.value.trim();
    if (!message) return;
    
    const formData = new FormData();
    formData.append('action', 'sendMessage');
    formData.append('conversation_id', adminCurrentConversationId);
    formData.append('message', message);
    
    textarea.value = '';
    textarea.style.height = 'auto'; 
    
    try {
        const response = await fetch(ADMIN_MESSAGING_API, {
            method: 'POST',
            body: formData
        });
        const data = await response.json();
        if (data.success) {
            await Promise.all([
                adminLoadMessages(adminCurrentConversationId, true),
                adminLoadConversations()
            ]);
        } else {
            alert('Error sending message: ' + data.message);
        }
    } catch (error) {
        console.error('Error execution failed:', error);
    }
}

// ===== LIVE SEARCH CONVERSATIONS REGISTRATION =====
function setupLiveSearchFilters() {
    const searchInput = document.getElementById('adminConversationSearch');
    const clearBtn = document.getElementById('clearSearchBtn');

    if (!searchInput) return;

    searchInput.addEventListener('input', () => {
        const query = searchInput.value.toLowerCase().trim();
        const items = document.querySelectorAll('.conversation-item');
        
        if (clearBtn) {
            if (query.length > 0) clearBtn.classList.remove('hidden');
            else clearBtn.classList.add('hidden');
        }

        items.forEach(item => {
            const name = item.querySelector('.conversation-name')?.textContent.toLowerCase() || '';
            const emailRow = item.querySelector('.user-email-row');
            const email = emailRow ? emailRow.textContent.toLowerCase() : '';
            
            if (name.includes(query) || email.includes(query)) {
                item.style.setProperty('display', 'flex', 'important');
            } else {
                item.style.setProperty('display', 'none', 'important');
            }
        });

        const visibleItems = Array.from(items).filter(item => item.style.display !== 'none');
        const listContainer = adminDomElements.conversationsList;
        let noResultPlaceholder = document.getElementById('search-no-results');

        if (visibleItems.length === 0 && items.length > 0) {
            if (!noResultPlaceholder && listContainer) {
                noResultPlaceholder = document.createElement('li');
                noResultPlaceholder.id = 'search-no-results';
                noResultPlaceholder.style.cssText = 'padding: 24px; text-align: center; color: #94a3b8; font-size: 13.5px; list-style: none;';
                noResultPlaceholder.textContent = 'No matching users found';
                listContainer.appendChild(noResultPlaceholder);
            }
        } else if (noResultPlaceholder) {
            noResultPlaceholder.remove();
        }
    });

    if (clearBtn) {
        clearBtn.addEventListener('click', () => {
            searchInput.value = '';
            clearBtn.classList.add('hidden');
            searchInput.dispatchEvent(new Event('input'));
            searchInput.focus();
        });
    }
}

// ===== EVENT LISTENERS REGISTRATION =====
function adminSetupEventListeners() {
    if (adminDomElements.sendBtn) {
        adminDomElements.sendBtn.addEventListener('click', adminSendMessage);
    }

    const textarea = adminDomElements.textarea;
    if (textarea) {
        textarea.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                adminSendMessage();
            }
        });

        textarea.addEventListener('input', () => {
            textarea.style.height = 'auto';
            textarea.style.height = Math.min(textarea.scrollHeight, 100) + 'px';
        });
    }

    setupLiveSearchFilters();
}

// ===== AUTO-REFRESH MESSAGES (ADMIN) =====
function adminStartAutoRefresh() {
    if (adminRefreshInterval) clearInterval(adminRefreshInterval);

    adminRefreshInterval = setInterval(async () => {
        if (isAdminPolling) return;
        isAdminPolling = true;
        
        try {
            const tasks = [adminLoadConversations()];
            if (adminCurrentConversationId) {
                tasks.push(adminLoadMessages(adminCurrentConversationId, false));
            }
            await Promise.all(tasks);
        } catch (error) {
            console.error("Polling sequence frame dropped: ", error);
        } finally {
            isAdminPolling = false;
        }
    }, ADMIN_REFRESH_INTERVAL);
}

function formatTimeAdmin(dateString) {
    if (!dateString) return '';
    if (dateString.includes('AM') || dateString.includes('PM')) return dateString;
    
    const date = new Date(dateString.replace(/-/g, "/"));
    const now = new Date();
    if (isNaN(date.getTime())) return dateString; 
    
    const diff = now - date;
    if (diff < 60000) return 'just now';
    if (diff < 3600000) return Math.floor(diff / 60000) + 'm ago';
    if (diff < 86400000) return Math.floor(diff / 3600000) + 'h ago';
    
    const dateOnly = date.toLocaleDateString();
    if (dateOnly === now.toLocaleDateString()) {
        return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    }
    
    const yesterday = new Date(now);
    yesterday.setDate(yesterday.getDate() - 1);
    if (dateOnly === yesterday.toLocaleDateString()) return 'Yesterday';
    
    return date.toLocaleDateString();
}

function adminScrollMessagesToBottom() {
    const container = adminDomElements.messagesContainer;
    if (container) {
        setTimeout(() => { container.scrollTop = container.scrollHeight; }, 30);
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeAdminMessenger);
} else {
    initializeAdminMessenger();
}