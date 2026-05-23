<?php
if (session_status() === PHP_SESSION_NONE) {
    session_name('KMKDT_ADMIN_SESSION'); 
    session_set_cookie_params(0, '/'); 
    session_start();
}

$sessionUser = $_SESSION['authUser'] ?? $_SESSION ?? [];
$loggedInUserName = htmlspecialchars($sessionUser['fullName'] ?? $sessionUser['username'] ?? 'System User');
$loggedInRole = htmlspecialchars($sessionUser['role'] ?? 'Staff/Admin');

$unreadNotificationCount = 0; 
$unreadMessageCount = 0;      

$sessionRoleClean = strtolower($sessionUser['role'] ?? 'user');
$reportRedirectUrl = ($sessionRoleClean === 'admin') ? 'reports' : '../admin/reports';
?>

<header id="header" class="header fixed-top d-flex align-items-center">

  <div class="d-flex align-items-center justify-content-between">
    <a href="index" class="logo d-flex align-items-center">
      <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" fill="#32cd32" class="me-2" viewBox="0 0 16 16">
        <path d="M1 2.828c.885-.37 2.154-.769 3.388-.893 1.33-.134 2.458.063 3.112.752v9.746c-.935-.53-2.12-.603-3.213-.493-1.18.12-2.37.461-3.287.811V2.828zm7.5-.141c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492V2.687zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783z"/>
      </svg>
      <span class="d-none d-lg-block">kmkdt-Library</span>
    </a>
    <i class="bi bi-list toggle-sidebar-btn"></i>
  </div>
    


  <nav class="header-nav ms-auto">
    <ul class="d-flex align-items-center">

      <li class="nav-item d-block d-lg-none">
        <a class="nav-link nav-icon search-bar-toggle " href="#">
          <i class="bi bi-search"></i>
        </a>
      </li>

      <li class="nav-item dropdown">
        <a class="nav-link nav-icon" href="reports" data-bs-toggle="dropdown">
          <i class="bi bi-bell"></i>
          <span id="global-notification-badge" class=""></span>
        </a>

        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow notifications">
          <li class="dropdown-header" id="global-notification-header">
            You have 0 new notifications
            <a href="reports"><span class="badge rounded-pill p-2 ms-2" style="background-color: #32cd32; color: white;">View all</span></a>
          </li>
          <li><hr class="dropdown-divider"></li>

          <div id="global-notifications-preview-list">
            <li class="text-center py-3 text-muted small" style="list-style: none;">No new system alerts.</li>
          </div>

          <li><hr class="dropdown-divider"></li>
          <li class="dropdown-footer text-center py-2">
            <a href="reports">Show all notifications</a>
          </li>
        </ul>
      </li>

      <li class="nav-item dropdown">
        <a class="nav-link nav-icon" href="messageHub" data-bs-toggle="dropdown">
          <i class="bi bi-chat-left-text"></i>
          <span id="global-message-badge"></span>
        </a>

        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow messages">
          <li class="dropdown-header" id="global-message-header">
            Support Chat Center
            <a href="messageHub"><span class="badge rounded-pill bg-success p-2 ms-2">Open Chat</span></a>
          </li>
          <li><hr class="dropdown-divider"></li>
          
          <span id="global-messages-preview-list">
             <li class="text-center py-3 text-muted small">No unread threads.</li>
          </span>
          
          <li><hr class="dropdown-divider"></li>
          <li class="dropdown-footer"><a href="messageHub">Go to Support Inbox</a></li>
        </ul>
      </li>

      <li class="nav-item dropdown pe-3">
        <a class="nav-link nav-profile d-flex align-items-center pe-0" href="Aprofile" data-bs-toggle="dropdown">
          <div class="rounded-circle d-flex align-items-center justify-content-center bg-secondary text-white fw-bold" style="width: 36px; height: 36px; font-size: 14px;">
            <?= strtoupper(substr($loggedInUserName, 0, 1)) ?>
          </div>
          <span class="d-none d-md-block dropdown-toggle ps-2"><?= $loggedInUserName ?></span>
        </a>

        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile border-0 shadow-lg p-2" style="min-width: 230px; border-radius: 12px;">
          <li class="px-3 py-2mb-1 bg-light rounded-3 text-center mx-1">
            <div class="fw-bold text-dark fs-6" style="letter-spacing: -0.3px;"><?= htmlspecialchars($loggedInUserName) ?></div>
            <span class="badge bg-dark-subtle text-dark border border-secondary-subtle px-2 py-0.5 mt-1 small font-monospace text-capitalize" style="font-size: 0.75rem;">
              <i class="bi bi-shield-lock me-1"></i><?= htmlspecialchars($loggedInRole) ?>
            </span>
          </li>

          <div class="my-1.5"></div>

          <li>
            <a class="dropdown-item d-flex align-items-center gap-2 rounded-2 py-2 text-secondary" href="Aprofile">
              <div class="d-flex align-items-center justify-content-center text-muted" style="width: 24px; font-size: 1.1rem;">
                <i class="bi bi-gear"></i>
              </div>
              <span class="fw-medium text-dark" style="font-size: 0.9rem;">Account Settings</span>
            </a>
          </li>

          <li>
            <form action="/kmkdt-Library/app/controller/adminController.php" method="post" class="m-0">
              <button type="submit" name="logoutButton" class="dropdown-item d-flex align-items-center gap-2 text-danger border-0 bg-transparent w-100 text-start rounded-2 py-2" style="padding: var(--bs-dropdown-item-padding-y) var(--bs-dropdown-item-padding-x);">
                <div class="d-flex align-items-center justify-content-center text-danger" style="width: 24px; font-size: 1.1rem;">
                  <i class="bi bi-box-arrow-right"></i>
                </div>
                <span class="fw-semibold" style="font-size: 0.9rem;">Sign Out</span>
              </button>
            </form>
          </li>
        </ul>
      </li>
    </ul>
  </nav>
</header>