/**
 * Real-Time User Notification Engine
 */
document.addEventListener("DOMContentLoaded", function () {
    console.log("🚀 User Notification Engine initialized.");

    const BASE_URL = window.location.origin + '/kmkdt-Library';
    const USER_API = `${BASE_URL}/app/controller/userController.php`;
    
    const container = document.getElementById('userNotiContainer');
    const badge = document.getElementById('userNotiBadge');

    if (!container || !badge) {
        console.warn("⚠️ Notification DOM elements missing from layout view.");
        return;
    }

    function checkLiveUserNotifications() {
        // Ensure elements are still in the DOM before executing fetch
        if (!document.getElementById('userNotiContainer')) {
            clearInterval(pollingInterval);
            return;
        }

        console.log("📡 Sending fetch request to:", `${USER_API}?action=get_live_user_updates`);
        
        fetch(`${USER_API}?action=get_live_user_updates`)
            .then(response => {
                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                return response.json(); // Direct conversion to JSON safely handles syntax exceptions
            })
            .then(data => {
                if (data && data.notifications && data.notifications.length > 0) {
                    console.log(`✅ Found ${data.notifications.length} active notifications! Rendering...`);
                    
                    badge.textContent = data.notifications.length;
                    badge.classList.remove('d-none');

                    let htmlContent = '';
                    data.notifications.forEach(item => {
                        const isDanger = item.type === 'danger';
                        const badgeClass = isDanger ? 'noti-icon-wrapper-danger' : 'noti-icon-wrapper-warning';
                        const iconClass = isDanger ? 'bi-exclamation-triangle-fill' : 'bi-envelope-fill';
                        
                        const lowerTitle = (item.title || "").toLowerCase();

                        // 🗺️ Dynamic Routing Logic
                        let targetUrl = `${BASE_URL}/public/user/profile`; 
                        if (lowerTitle.includes("message") || (item.type === 'warning' && !lowerTitle.includes("book"))) {
                            targetUrl = `${BASE_URL}/public/user/contact`;
                        }

                        htmlContent += `
                            <li class="border-bottom dropdown-item p-0 noti-item-row" style="list-style: none;">
                                <a href="${targetUrl}" class="d-flex align-items-start gap-3 p-3 text-decoration-none text-wrap w-100 h-100">
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
                } else {
                    console.log("ℹ️ Backend returned zero notifications. Showing empty bell state.");
                    badge.classList.add('d-none');
                    badge.textContent = '0';
                    container.innerHTML = `
                        <div class="text-center py-5 text-muted small">
                            <i class="bi bi-bell-fill d-block text-secondary opacity-50 mb-2 empty-bell-icon"></i>
                            <span class="fw-medium">No current notifications</span>
                        </div>
                    `;
                }
            })
            .catch(err => console.error("❌ Notification Engine Crash:", err));
    }

    // Run immediately on page mount
    checkLiveUserNotifications();
    
    // Establish interval cadence loop
    const pollingInterval = setInterval(checkLiveUserNotifications, 8000);
});