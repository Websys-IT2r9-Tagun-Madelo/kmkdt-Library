// ===== MESSENGER CHAT SYSTEM =====
// Configuration
const MESSAGING_API = '/kmkdt-Library/app/controller/messagingController.php';
const REFRESH_INTERVAL = 3000; // 3 seconds - refresh messages
const ADMIN_ID = 4; // Normalized Admin ID reference 

let currentConversationId = null;
let currentRecipientId = null;
let refreshInterval = null; 
let isPolling = false; // Flag to prevent stacking asynchronous polling cycles

// Cached DOM Elements (Populated on setup/run)
let domElements = {};

function cacheElements() {
    domElements = {
        conversationsList: document.querySelector('.conversations-list'),
        messagesContainer: document.querySelector('.messages-panel .messages-container'),
        inputArea: document.querySelector('.message-input-area'),
        headerTitle: document.querySelector(".messages-header-info h3"),
        headerSub: document.querySelector(".messages-header-info p"),
        textarea: document.querySelector('.message-input-wrapper textarea'),
        sendBtn: document.querySelector('.send-btn'),
        newChatModal: document.querySelector('.new-chat-modal'),
        newChatInput: document.querySelector('.new-chat-modal input'),
        startChatBtn: document.querySelector('.btn-start-chat'),
        cancelBtn: document.querySelector('.btn-cancel'),
        librarySupportBtn: document.querySelector('#librarySupport')
    };
}

// ===== INITIALIZE MESSENGER =====
function initializeMessenger() {
    console.log('Initializing messenger...');
    cacheElements();
    
    ensureAdminConversation();
    loadConversations();
    setupEventListeners();
    startAutoRefresh();
}

// ===== SAFE HTML ESCAPING UTILITY =====
function escapeHtml(text) {
    if (!text) return '';
    return text
        .toString()
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

// ===== CURRENT USER SESSION FALLBACK =====
function getCurrentUserId() {
    const userElement = document.querySelector('[data-user-id]');
    return userElement ? (parseInt(userElement.getAttribute('data-user-id'), 10) || 0) : 0;
}

// ===== ENSURE ADMIN CONVERSATION EXISTS =====
async function ensureAdminConversation() {
    try {
        const response = await fetch(`${MESSAGING_API}?action=getAdminConversation`);
        const data = await response.json();
        if (data.success) {
            console.log('Admin conversation ID:', data.conversation_id);
        }
    } catch (error) {
        console.error('Error creating admin conversation:', error);
    }
}

// ===== LOAD CONVERSATIONS =====
async function loadConversations() {
    try {
        const response = await fetch(`${MESSAGING_API}?action=getConversations`);
        const data = await response.json();
        if (data.success) {
            renderConversations(data.conversations);
        } else {
            console.error('Error loading conversations:', data.message);
        }
    } catch (error) {
        console.error('Network error loading conversations:', error);
    }
}

// ===== RENDER CONVERSATIONS LIST =====
function renderConversations(conversations) {
    const listContainer = domElements.conversationsList;
    if (!listContainer) return;
    
    if (!conversations || conversations.length === 0) {
        listContainer.innerHTML = '<li style="padding: 16px; text-align: center; color: #999;">No conversations yet</li>';
        return;
    }
    
    // 1. Find the admin conversation dynamically to update the green pinned card
    const adminConv = conversations.find(c => parseInt(c.recipient_id, 10) === ADMIN_ID);
    const librarySupportBtn = domElements.librarySupportBtn;
    
    let isAdminActive = false;

    if (adminConv && librarySupportBtn) {
        // Attach the real conversation ID to the green HTML button container
        librarySupportBtn.setAttribute('data-conversation-id', adminConv.id);
        librarySupportBtn.classList.add('library-support-pinned-card');
        
        // Update its text preview and time dynamically if messages exist
        const supportSubtitle = librarySupportBtn.querySelector('.support-subtitle');
        if (supportSubtitle && adminConv.last_message) {
            supportSubtitle.innerText = adminConv.last_message;
        }
        
        // Check if user currently has the support chat open
        if (currentConversationId == adminConv.id) {
            isAdminActive = true;
            librarySupportBtn.style.background = 'linear-gradient(135deg, #1e7e34 5%, #57cb57 95%)';
        } else {
            librarySupportBtn.style.background = ''; // Resets cleanly to layout base CSS
        }
    }
    
    // 2. FILTER OUT THE ADMIN from the lower list so they only live in the green box
    const filteredConversations = conversations.filter(c => parseInt(c.recipient_id, 10) !== ADMIN_ID);
    
    // Sort remaining regular users by descending date
    const sorted = [...filteredConversations].sort((a, b) => {
        const timeA = a.raw_sort_time ? new Date(a.raw_sort_time.replace(/-/g, "/")) : new Date(a.created_at.replace(/-/g, "/"));
        const timeB = b.raw_sort_time ? new Date(b.raw_sort_time.replace(/-/g, "/")) : new Date(b.created_at.replace(/-/g, "/"));
        return timeB - timeA;
    });
    
    // Render only the non-admin rows
    listContainer.innerHTML = sorted.map((conv) => {
        // If Admin is active, regular users must NEVER look active
        const isActive = !isAdminActive && (currentConversationId == conv.id);
        const unreadBadge = conv.unread_count > 0 ? `<span class="badge bg-danger" style="float: right; margin-left: 8px;">${conv.unread_count}</span>` : '';
        const lastMessageTime = formatTime(conv.last_message_time);
        const lastMessage = conv.last_message || 'No messages yet';
        
        const backgroundStyle = isActive ? 'background: #f1f5f9;' : 'background: transparent;';
        
        return `
            <li class="conversation-item ${isActive ? 'active' : ''}" 
                data-conversation-id="${conv.id}" 
                data-recipient-id="${conv.recipient_id}" 
                data-recipient-name="${escapeHtml(conv.recipient_name)}"
                style="cursor: pointer; padding: 12px 16px; border-bottom: 1px solid rgba(0,0,0,0.05); ${backgroundStyle}">
                <div class="conversation-info" style="display: flex; flex-direction: column; width: 100%;">
                    <div class="conversation-header" style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                        <div>
                            <span class="conversation-name" style="font-weight: 600; color: #1e293b;">${escapeHtml(conv.recipient_name)}</span>
                        </div>
                        <div class="conversation-time" style="font-size: 11px; color: #94a3b8;">${lastMessageTime}</div>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 4px;">
                        <div class="conversation-preview" style="font-size: 12px; color: #64748b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 85%;">${escapeHtml(lastMessage)}</div>
                        ${unreadBadge}
                    </div>
                </div>
            </li>
        `;
    }).join('');
    
    // Event delegation listeners
    listContainer.querySelectorAll('.conversation-item').forEach(item => {
        item.addEventListener('click', () => {
            selectConversation(
                item.getAttribute('data-conversation-id'),
                item.getAttribute('data-recipient-id'),
                item.getAttribute('data-recipient-name')
            );
        });
    });
}

// ===== SELECT CONVERSATION =====
function selectConversation(conversationId, recipientId, recipientName) {
    currentConversationId = conversationId;
    currentRecipientId = recipientId;
    
    if (domElements.inputArea) {
        domElements.inputArea.style.setProperty('display', 'flex', 'important');
    }
    
    if (recipientName) {
        if (domElements.headerTitle) domElements.headerTitle.innerText = recipientName;
        if (domElements.headerSub) domElements.headerSub.innerText = "Direct Chat Session Activity Open";
    }
    
    // Clear styles across all standard items first
    document.querySelectorAll('.conversation-item').forEach(item => {
        item.classList.remove('active');
        item.style.background = 'transparent';
    });
    
    // Explicitly check if the recipient IS NOT the admin before rendering active gray backgrounds
    if (parseInt(recipientId, 10) !== ADMIN_ID) {
        const activeEl = document.querySelector(`.conversation-item[data-conversation-id="${conversationId}"]`);
        if (activeEl) {
            activeEl.classList.add('active');
            activeEl.style.background = '#f1f5f9';
        }
        // Ensure support card drops its selection layout
        if (domElements.librarySupportBtn) domElements.librarySupportBtn.style.background = '';
    } else {
        // If admin is chosen, explicitly apply the vibrant green gradient onto support button
        if (domElements.librarySupportBtn) {
            domElements.librarySupportBtn.style.background = 'linear-gradient(135deg, #1e7e34 5%, #57cb57 95%)';
        }
    }
    
    loadMessages(conversationId, true);
}

// ===== LOAD MESSAGES =====
async function loadMessages(conversationId, forceScroll = false) {
    try {
        const response = await fetch(`${MESSAGING_API}?action=getMessages&conversation_id=${conversationId}`);
        const data = await response.json();
        if (data.success) {
            renderMessages(data.messages, forceScroll);
        }
    } catch (error) {
        console.error('Error loading messages:', error);
    }
}

// ===== RENDER MESSAGES =====
function renderMessages(messages, forceScroll = false) {
    const container = domElements.messagesContainer;
    if (!container) return;
    
    container.classList.remove('messages-empty');
    const isAtBottom = (container.scrollHeight - container.scrollTop <= container.clientHeight + 100);
    
    if (!messages || messages.length === 0) {
        container.innerHTML = '<div class="messages-empty" style="text-align: center; color: #94a3b8; padding: 40px 0;"><p>No messages yet. Start the conversation!</p></div>';
        return;
    }
    
    const currentUserId = getCurrentUserId();
    
    container.innerHTML = messages.map(msg => {
        const isSent = parseInt(msg.sender_id, 10) === parseInt(currentUserId, 10);
        const textAlignment = isSent ? 'flex-end' : 'flex-start';
        const bubbleBg = isSent ? '#57cb57' : '#f1f5f9'; 
        const bubbleColor = isSent ? '#000000' : '#1e293b';
        const formattedDisplayTime = msg.time_stamp ? msg.time_stamp : formatTime(msg.created_at);
        
        return `
            <div style="margin-bottom: 14px; display: flex; flex-direction: column; align-items: ${textAlignment}; width: 100%;">
                <div style="background: ${bubbleBg}; color: ${bubbleColor}; padding: 10px 14px; border-radius: 12px; max-width: 75%; font-size: 13px; line-height: 1.4; word-break: break-word;">
                    ${escapeHtml(msg.message)}
                </div>
                <div style="font-size: 10px; color: #94a3b8; margin-top: 2px; padding: 0 4px;">${formattedDisplayTime}</div>
            </div>
        `;
    }).join('');

    if (forceScroll || isAtBottom) {
        scrollMessagesToBottom(forceScroll ? 'smooth' : 'auto');
    }
}

// ===== SEND MESSAGE =====
async function sendMessage() {
    const textarea = domElements.textarea;
    if (!textarea || !currentConversationId) return;

    const message = textarea.value.trim();
    if (!message) return;
    
    const formData = new FormData();
    formData.append('conversation_id', currentConversationId);
    formData.append('message', message);
    
    textarea.value = '';
    textarea.style.height = 'auto';
    
    try {
        const response = await fetch(`${MESSAGING_API}?action=sendMessage`, {
            method: 'POST',
            body: formData
        });
        const data = await response.json();
        
        if (data.success) {
            await Promise.all([
                loadMessages(currentConversationId, true),
                loadConversations()
            ]);
        } else {
            alert('Error sending message: ' + data.message);
        }
    } catch (error) {
        console.error('Error sending message dispatch:', error);
    }
}

// ===== SETUP EVENT LISTENERS =====
function setupEventListeners() {
    if (domElements.sendBtn) {
        domElements.sendBtn.addEventListener('click', sendMessage);
    }
    
    const textarea = domElements.textarea;
    if (textarea) {
        textarea.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });
        
        textarea.addEventListener('input', () => {
            textarea.style.height = 'auto';
            textarea.style.height = Math.min(textarea.scrollHeight, 100) + 'px';
        });
    }
    
    if (domElements.librarySupportBtn) {
        domElements.librarySupportBtn.style.cursor = 'pointer';
        domElements.librarySupportBtn.addEventListener('click', openLibrarySupport);
    }
    
    const newChatBtn = document.querySelector('.new-chat-btn');
    if (newChatBtn) newChatBtn.addEventListener('click', openNewChatModal);
    if (domElements.startChatBtn) domElements.startChatBtn.addEventListener('click', startNewChat);
    if (domElements.cancelBtn) domElements.cancelBtn.addEventListener('click', closeNewChatModal);
    
    if (domElements.newChatModal) {
        domElements.newChatModal.addEventListener('click', (e) => {
            if (e.target === domElements.newChatModal) closeNewChatModal();
        });
    }
}

// ===== NEW CHAT MODAL =====
function openNewChatModal() {
    const modal = domElements.newChatModal;
    if (modal) {
        modal.style.display = 'flex';
        modal.classList.add('active');
        if (domElements.newChatInput) domElements.newChatInput.focus();
    }
}

// ===== CLOSE MODAL =====
function closeNewChatModal() {
    const modal = domElements.newChatModal;
    if (modal) {
        modal.style.display = 'none';
        modal.classList.remove('active');
        if (domElements.newChatInput) domElements.newChatInput.value = '';
    }
}

// ===== START NEW CHAT ROUTINE =====
async function startNewChat() {
    const email = domElements.newChatInput ? domElements.newChatInput.value.trim() : '';
    
    if (!email) {
        alert('Please enter an email address');
        return;
    }
    
    const formData = new FormData();
    formData.append('recipient_email', email);
    
    try {
        const response = await fetch(`${MESSAGING_API}?action=startConversation`, {
            method: 'POST',
            body: formData
        });
        const data = await response.json();
        
        if (data.success) {
            closeNewChatModal();
            
            const convResponse = await fetch(`${MESSAGING_API}?action=getConversations`);
            const cData = await convResponse.json();
            
            if (cData.success) {
                renderConversations(cData.conversations);
                
                const newConv = cData.conversations
                    .filter(c => parseInt(c.recipient_id, 10) !== ADMIN_ID)
                    .find(c => c.id == data.conversation_id);
                
                if (newConv) {
                    selectConversation(data.conversation_id, newConv.recipient_id, newConv.recipient_name);
                } else {
                    openLibrarySupport();
                }
            }
        } else {
            alert('Error: ' + data.message);
        }
    } catch (error) {
        console.error('Error creating new conversation:', error);
    }
}

// ===== OPEN LIBRARY SUPPORT =====
async function openLibrarySupport() {
    try {
        const response = await fetch(`${MESSAGING_API}?action=getAdminConversation`);
        const data = await response.json();
        if (data.success) {
            document.querySelectorAll('.conversation-item').forEach(item => {
                item.classList.remove('active');
                item.style.background = 'transparent';
            });
            
            selectConversation(data.conversation_id, ADMIN_ID, "Library Support Admin");
            
            if (domElements.conversationsList.innerHTML !== 'Loading...') {
                const convResponse = await fetch(`${MESSAGING_API}?action=getConversations`);
                const cData = await convResponse.json();
                if (cData.success) renderConversations(cData.conversations);
            }
        } else {
            alert('Error opening support chat');
        }
    } catch (error) {
        console.error('Error opening support channel connection:', error);
    }
}

// ===== AUTO-REFRESH MESSAGES =====
function startAutoRefresh() {
    if (refreshInterval) clearInterval(refreshInterval);
    
    refreshInterval = setInterval(async () => {
        if (isPolling) return; 
        isPolling = true;
        
        try {
            const tasks = [loadConversations()];
            if (currentConversationId) {
                tasks.push(loadMessages(currentConversationId, false));
            }
            await Promise.all(tasks);
        } catch (error) {
            console.error("Polling instance error: ", error);
        } finally {
            isPolling = false;
        }
    }, REFRESH_INTERVAL);
}

// ===== UTILITY FUNCTIONS =====
function formatTime(dateString) {
    if (!dateString) return '';
    if (dateString.includes('AM') || dateString.includes('PM')) return dateString;
    
    const date = new Date(dateString.replace(/-/g, "/"));
    const now = new Date();
    
    if (isNaN(date.getTime())) return dateString; 
    
    const diff = now - date;
    if (diff < 60000) return 'just now';
    if (diff < 3600000) return Math.floor(diff / 60000) + 'm ago';
    if (diff < 86400000) return Math.floor(diff / 3600000) + 'h ago';
    
    return date.toLocaleDateString([], { hour: '2-digit', minute: '2-digit' });
}

function scrollMessagesToBottom(behavior = 'auto') {
    const container = domElements.messagesContainer;
    if (container) {
        container.scrollTo({
            top: container.scrollHeight,
            behavior: behavior
        });
    }
}

// Initialize on Document Load
document.addEventListener('DOMContentLoaded', initializeMessenger);