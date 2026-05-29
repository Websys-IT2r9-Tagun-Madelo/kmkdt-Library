/**
 * Real-Time User Notification Engine
 */
document.addEventListener("DOMContentLoaded", function () {
    console.log(" User Notification Engine initialized.");

    const BASE_URL = window.location.origin + '/kmkdt-Library';
    const USER_API = `${BASE_URL}/app/controller/userController.php`;
    
    const container = document.getElementById('userNotiContainer');
    const badge = document.getElementById('userNotiBadge');

    if (!container || !badge) {
        console.warn(" Notification DOM elements missing from layout view.");
        return;
    }

    function checkLiveUserNotifications() {
        if (!document.getElementById('userNotiContainer')) {
            clearInterval(pollingInterval);
            return;
        }

        console.log(" Sending fetch request to:", `${USER_API}?action=get_live_user_updates`);
        
        fetch(`${USER_API}?action=get_live_user_updates`)
            .then(response => {
                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                return response.json();
            })
            .then(data => {
                if (data && data.notifications && data.notifications.length > 0) {
                    console.log(`✅ Found ${data.notifications.length} active notifications! Rendering...`);
                    
                    badge.textContent = data.notifications.length;
                    badge.classList.remove('d-none');

                    let htmlContent = '';
                    data.notifications.forEach(item => {
                        const lowerTitle = (item.title || "").toLowerCase();
                        const lowerMessage = (item.message || "").toLowerCase();

                        //  Determine if notification concerns a book deadline
                        const isBookAlert = lowerTitle.includes("due") || lowerTitle.includes("overdue") || lowerTitle.includes("book") || lowerMessage.includes("due") || lowerMessage.includes("overdue");
                        const isDanger = item.type === 'danger' || lowerTitle.includes("overdue");

                        // Visual Identity Routing & Icon Tweaks
                        let badgeClass = 'noti-icon-wrapper-warning';
                        let iconClass = 'bi-envelope-fill';

                        if (isBookAlert) {
                            badgeClass = isDanger ? 'noti-icon-wrapper-book-danger' : 'noti-icon-wrapper-book-warning';
                            iconClass = isDanger ? 'bi-exclamation-octagon-fill' : 'bi-exclamation-triangle-fill';
                        } else if (isDanger) {
                            badgeClass = 'noti-icon-wrapper-danger';
                            iconClass = 'bi-exclamation-circle-fill';
                        }

                        // Dynamic Routing Logic
                        let targetUrl = `${BASE_URL}/public/user/profile`; 
                        if (isBookAlert) {
                            targetUrl = `${BASE_URL}/public/user/myBooks.php`;
                        } else if (lowerTitle.includes("message") || item.type === 'warning') {
                            targetUrl = `${BASE_URL}/public/user/messageHub`;
                        }

                        htmlContent += `
                            <li class="border-bottom dropdown-item p-0 noti-item-row" style="list-style: none;">
                                <a href="${targetUrl}" class="d-flex align-items-start gap-3 p-3 text-decoration-none text-wrap w-100 h-100 legacy-noti-link">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 style-icon-box ${badgeClass}" style="width: 35px; height: 35px;">
                                        <i class="bi ${iconClass}"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <strong class="d-block text-dark small fw-bold mb-1">${item.title || 'Notification'}</strong>
                                        <span class="text-muted d-block small">${item.message || ''}</span>
                                    </div>
                                </a>
                            </li>
                        `;
                    });
                    container.innerHTML = htmlContent;
                    attachInstantClickInterceptors();
                } else {
                    renderEmptyState();
                }
            })
            .catch(err => console.error("❌ Notification Engine Crash:", err));
    }

    // Optimistic UI Update: Clear notification from view instantly upon click action
    function attachInstantClickInterceptors() {
        const links = container.querySelectorAll('.legacy-noti-link');
        links.forEach(link => {
            link.addEventListener('click', function (e) {
                const currentCount = parseInt(badge.textContent, 10) || 0;
                const newCount = currentCount - 1;

                if (newCount <= 0) {
                    badge.classList.add('d-none');
                    badge.textContent = '0';
                    renderEmptyState();
                } else {
                    badge.textContent = newCount;
                    // Safely drop the specific element container row
                    const row = this.closest('.noti-item-row');
                    if (row) row.remove();
                }
            });
        });
    }

    function renderEmptyState() {
        console.log("ℹ️ Showing empty bell state.");
        badge.classList.add('d-none');
        badge.textContent = '0';
        container.innerHTML = `
            <div class="text-center py-5 text-muted small">
                <i class="bi bi-bell-fill d-block text-secondary opacity-50 mb-2 empty-bell-icon"></i>
                <span class="fw-medium">No current notifications</span>
            </div>
        `;
    }

    // Run loops
    checkLiveUserNotifications();
    const pollingInterval = setInterval(checkLiveUserNotifications, 8000);
});