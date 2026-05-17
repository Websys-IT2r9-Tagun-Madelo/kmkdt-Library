<?php
include('../../app/middleware/admin.php');
include('./includes/header.php');
include('./includes/topbar.php');
include('./includes/sidebar.php');

// Consolidated session user lookup with standard fallback structure
$adminId = $_SESSION['authUser']['user_id'] ?? $_SESSION['user_id'] ?? 4;
?>

  <div class="pagetitle">
    <h1>Contacts & Inquiries</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index">Home</a></li>
        <li class="breadcrumb-item active">Contacts</li>
      </ol>
    </nav>
  </div>

  <section class="section contact" data-admin-id="<?php echo htmlspecialchars($adminId); ?>">
    
    <div class="row g-3 mb-4">
      <div class="col-sm-6 col-xl-3">
        <div class="info-box card p-3 d-flex flex-row align-items-center h-100 style-address">
          <div class="icon-avatar me-3 text-primary bg-primary-light">
            <i class="bi bi-geo-alt fs-4"></i>
          </div>
          <div>
            <span class="info-label">LIBRARY ADDRESS</span>
            <p class="info-data">USTP, Lapasan,<br>Cagayan de Oro City</p>
          </div>
        </div>
      </div>

      <div class="col-sm-6 col-xl-3">
        <div class="info-box card p-3 d-flex flex-row align-items-center h-100 style-phone">
          <div class="icon-avatar me-3 text-success bg-success-light">
            <i class="bi bi-telephone fs-4"></i>
          </div>
          <div>
            <span class="info-label">CONTACT NUMBER</span>
            <p class="info-data">+63 912 345 6789<br>+63 992 892 2973</p>
          </div>
        </div>
      </div>

      <div class="col-sm-6 col-xl-3">
        <div class="info-box card p-3 d-flex flex-row align-items-center h-100 style-email">
          <div class="icon-avatar me-3 text-danger bg-danger-light">
            <i class="bi bi-envelope fs-4"></i>
          </div>
          <div>
            <span class="info-label">LIBRARY EMAIL</span>
            <p class="info-data">library@school.edu<br>support@library.edu</p>
          </div>
        </div>
      </div>

      <div class="col-sm-6 col-xl-3">
        <div class="info-box card p-3 d-flex flex-row align-items-center h-100 style-hours">
          <div class="icon-avatar me-3 text-warning bg-warning-light">
            <i class="bi bi-clock fs-4"></i>
          </div>
          <div>
            <span class="info-label">LIBRARY HOURS</span>
            <p class="info-data">Monday - Saturday<br>8:00AM - 6:00PM</p>
          </div>
        </div>
      </div>
    </div>

<!-- MESSAGING SURFACE -->
<div class="row">
      <div class="col-12 col-xl-12 col-xxl-10 mx-auto">
        <div class="card p-0 overflow-hidden shadow-sm border-0">
          
          <div class="admin-messenger-container">
            
          <div class="admin-conversations-panel">
            <div class="panel-search-wrapper">
              <h6>Conversations</h6>
              <div class="search-input-group">
                <i class="bi bi-search search-icon"></i> <input type="text" id="adminConversationSearch" placeholder="Search users...">
                <button type="button" id="clearSearchBtn" class="clear-search-btn hidden">
                  <i class="bi bi-x-circle-fill"></i>
                </button>
              </div>
            </div>
            
            <ul class="admin-conversations-list">
              <li class="list-loading-placeholder">
                <div class="spinner-border spinner-border-sm text-success me-2" role="status"></div>
                <span>Loading conversations...</span>
              </li>
            </ul>
          </div>
            
            <div class="admin-messages-panel">
              <div class="panel-header-wrapper">
                <div class="admin-messages-header-info">
                  <h6 class="current-chat-title">Select a conversation</h6>
                  <p class="current-chat-subtitle">Choose a user from the left pane to view message history</p>
                </div>
              </div>
              
              <div class="admin-messages-container messages-empty">
                <div class="placeholder-wrapper">
                  <i class="bi bi-chat-left-dots display-4 mb-2 text-muted d-block"></i>
                  <p class="placeholder-text">Select a conversation to view messages</p>
                </div>
              </div>
              
              <div class="admin-message-input-area">
                <div class="admin-message-input-wrapper">
                  <textarea placeholder="Type a message..." rows="1"></textarea>
                </div>
                <button class="admin-send-btn">Send</button>
              </div>
            </div>
            
          </div> </div>
      </div>
    </div>

  </section>



<?php
include('./includes/footer.php');
?>