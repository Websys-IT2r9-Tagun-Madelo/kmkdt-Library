/**
 * Real-Time Administrative System Polling Engine
 */
document.addEventListener("DOMContentLoaded", function () {
    console.log("🚀 Admin Notification Engine active on dashboard view.");

    // Absolute routing path mapping contexts
    const MESSAGING_API = window.location.origin + '/kmkdt-Library/app/controller/messagingController.php';
    const ADMIN_API     = window.location.origin + '/kmkdt-Library/app/controller/adminController.php';

    function checkGlobalLiveUpdates() {
        console.log("📡 Polling system engines for live updates...");
        
        // 1. ROUTE TO MESSAGING CONTROLLER FOR ADMINISTRATIVE SUPPORT CHATS
        fetch(`${MESSAGING_API}?action=adminGetConversations`)
            .then(res => {
                if (!res.ok) throw new Error(`HTTP Session Error! Status: ${res.status}`);
                return res.json();
            })
            .then(data => {
                if (!data.success) {
                    console.error("❌ Messaging Engine Refused Session Data:", data);
                    return;
                }

                let totalUnreadMessages = 0;
                let messageDropdownHTML = '';

                if (data.conversations && Array.isArray(data.conversations)) {
                    data.conversations.forEach(convo => {
                        const unread = parseInt(convo.unread_count || 0);
                        totalUnreadMessages += unread;

                        if (unread > 0) {
                            const initial = convo.recipient_name ? convo.recipient_name.charAt(0).toUpperCase() : '?';
                            messageDropdownHTML += `
                                <li class="message-item px-3 py-2">
                                    <a href="messageHub" class="d-flex align-items-center text-decoration-none text-dark">
                                        <div class="rounded-circle bg-success text-white fw-bold d-flex align-items-center justify-content-center me-2" style="width: 35px; height: 35px; min-width: 35px; font-size:12px;">
                                            ${initial}
                                        </div>
                                        <div class="overflow-hidden flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="fw-bold small mb-0">${convo.recipient_name}</span>
                                                <span class="badge bg-success rounded-pill" style="font-size:9px;">${unread} new</span>
                                            </div>
                                            <p class="text-muted small mb-0 text-truncate" style="font-size: 11px;">${convo.last_message}</p>
                                        </div>
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                            `;
                        }
                    });
                }

                // Update Message Topbar UI Elements
                const msgBadge = document.getElementById('global-message-badge');
                const msgHeader = document.getElementById('global-message-header');
                const msgPreviewList = document.getElementById('global-messages-preview-list');

                if (totalUnreadMessages > 0) {
                    if (msgBadge) {
                        msgBadge.className = "badge bg-success badge-number";
                        msgBadge.textContent = totalUnreadMessages;
                        msgBadge.style.display = "inline-block";
                    }
                    if (msgHeader) msgHeader.innerHTML = `You have ${totalUnreadMessages} new message threads <a href="messageHub"><span class="badge rounded-pill bg-success p-2 ms-2">View all</span></a>`;
                    if (msgPreviewList) msgPreviewList.innerHTML = messageDropdownHTML;
                } else {
                    if (msgBadge) {
                        msgBadge.className = "";
                        msgBadge.textContent = "";
                        msgBadge.style.display = "none";
                    }
                    if (msgHeader) msgHeader.innerHTML = `Support Chat Center <a href="messageHub"><span class="badge rounded-pill bg-success p-2 ms-2">Open Chat</span></a>`;
                    if (msgPreviewList) msgPreviewList.innerHTML = `<li class="text-center py-3 text-muted small">No unread threads.</li>`;
                }
            })
            .catch(err => console.error("⚠️ Messaging Endpoint Connection Refused:", err));


        // 2. ROUTE TO ADMIN CONTROLLER FOR LIVE OVERDUE REPORT ALERTS
        fetch(`${ADMIN_API}?action=getNotifications`)
            .then(res => {
                if (!res.ok) throw new Error(`HTTP Request Error! Status: ${res.status}`);
                return res.json();
            })
            .then(data => {
                if (!data.success) {
                    console.error("❌ Admin Notification Query Blocked:", data);
                    return;
                }

                const notiBadge = document.getElementById('global-notification-badge');
                const notiHeader = document.getElementById('global-notification-header');
                const notiPreviewList = document.getElementById('global-notifications-preview-list');

                let totalNotifications = 0;
                let notiDropdownHTML = '';
                const systemNotifications = data.notifications || [];

                totalNotifications = systemNotifications.length;

                if (totalNotifications > 0) {
                    systemNotifications.forEach(noti => {
                        let iconClass = "bi-exclamation-circle text-warning";
                        if (noti.type === 'danger' || noti.type === 'overdue') iconClass = "bi-x-circle text-danger";
                        if (noti.type === 'success') iconClass = "bi-check-circle text-success";

                        notiDropdownHTML += `
                            <li class="notification-item px-3 py-2 d-flex align-items-start" style="list-style: none;">
                                <i class="bi ${iconClass} me-2" style="font-size:16px;"></i>
                                <div class="overflow-hidden flex-grow-1">
                                    <p class="mb-0 small text-dark fw-bold">${noti.title || 'System Alert'}</p>
                                    <p class="mb-0 text-muted text-truncate" style="font-size: 11px;">${noti.message}</p>
                                </div>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                        `;
                    });

                    if (notiBadge) {
                        notiBadge.className = "badge badge-number";
                        notiBadge.style.backgroundColor = "#32cd32"; // Administrative Theme Lime Hex Match
                        notiBadge.style.color = "#ffffff";
                        notiBadge.style.display = "inline-block";
                        notiBadge.textContent = totalNotifications;
                    }
                    
                    if (notiHeader) {
                        notiHeader.innerHTML = `You have ${totalNotifications} new notifications <a href="Reports"><span class="badge rounded-pill p-2 ms-2" style="background-color: #32cd32; color: white;">View all</span></a>`;
                    }
                    
                    if (notiPreviewList) {
                        notiPreviewList.innerHTML = notiDropdownHTML;
                    }
                } else {
                    if (notiBadge) {
                        notiBadge.className = "";
                        notiBadge.style.backgroundColor = "";
                        notiBadge.textContent = "";
                        notiBadge.style.display = "none";
                    }
                    if (notiHeader) {
                        notiHeader.innerHTML = `You have 0 new notifications <a href="Reports"><span class="badge rounded-pill p-2 ms-2" style="background-color: #32cd32; color: white;">View all</span></a>`;
                    }
                    if (notiPreviewList) {
                        notiPreviewList.innerHTML = `<li class="text-center py-3 text-muted small" style="list-style: none;">No new system alerts.</li>`;
                    }
                }
            })
            .catch(err => console.error("⚠️ Admin Notification Target Connection Error:", err));
    }

    // Initialize immediate execution and establish loop cycle
    checkGlobalLiveUpdates();
    setInterval(checkGlobalLiveUpdates, 5000);
});