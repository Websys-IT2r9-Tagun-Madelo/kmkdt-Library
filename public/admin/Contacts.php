<?php
include('../../app/middleware/admin.php');
include('./includes/header.php');
include('./includes/topbar.php');
include('./includes/sidebar.php');
?>

    <div class="pagetitle">
      <h1>Contacts</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index">Home</a></li>
          <li class="breadcrumb-item active">Contacts</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->
 <div class="pagetitle">

<section class="section contact">
  
  <!-- Store current admin ID for messaging -->
  <input type="hidden" data-admin-id="<?php echo $_SESSION['authUser']['user_id']; ?>" class="current-admin-id" value="<?php echo $_SESSION['authUser']['user_id']; ?>">
  
  <!-- TOP ROW - LIBRARY INFO CARDS (HORIZONTAL LAYOUT) -->
  <div class="row g-3 mb-4">
    <div class="col-lg-6 col-xl-3">
      <div class="info-box card p-3 d-flex flex-row align-items-center h-100">
        <i class="bi bi-geo-alt fs-3 me-3 text-primary"></i>
        <div>
          <h6 class="mb-1"><strong>LIBRARY ADDRESS</strong></h6>
          <p class="mb-0 small">USTP, Lapasan,<br>Cagayan de Oro City</p>
        </div>
      </div>
    </div>

    <div class="col-lg-6 col-xl-3">
      <div class="info-box card p-3 d-flex flex-row align-items-center h-100">
        <i class="bi bi-telephone fs-3 me-3 text-success"></i>
        <div>
          <h6 class="mb-1"><strong>CONTACT NUMBER</strong></h6>
          <p class="mb-0 small">+63 912 345 6789<br>+63 992 892 2973</p>
        </div>
      </div>
    </div>

    <div class="col-lg-6 col-xl-3">
      <div class="info-box card p-3 d-flex flex-row align-items-center h-100">
        <i class="bi bi-envelope fs-3 me-3 text-danger"></i>
        <div>
          <h6 class="mb-1"><strong>LIBRARY EMAIL</strong></h6>
          <p class="mb-0 small">library@school.edu<br>support@library.edu</p>
        </div>
      </div>
    </div>

    <div class="col-lg-6 col-xl-3">
      <div class="info-box card p-3 d-flex flex-row align-items-center h-100">
        <i class="bi bi-clock fs-3 me-3 text-warning"></i>
        <div>
          <h6 class="mb-1"><strong>LIBRARY HOURS</strong></h6>
          <p class="mb-0 small">Monday - Saturday<br>8:00AM - 6:00PM</p>
        </div>
      </div>
    </div>
  </div>

  <!-- BOTTOM ROW - FULL-WIDTH MESSENGER INTERFACE -->
  <div class="row">
    <div class="col-12">
      <div style="margin-bottom: 20px;">
        <h5 class="card-title" style="margin: 0; padding-bottom: 10px;">User Inquiries</h5>
        <p style="color: #999; font-size: 12px; margin: 0;">Real-time messaging with registered users</p>
      </div>
      
      <div class="admin-messenger-container" style="
        display: flex;
        gap: 0;
        background: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        height: 600px;
        border: 1px solid #e0e0e0;
      ">
        
        <!-- CONVERSATIONS PANEL (LEFT) -->
        <div class="admin-conversations-panel" style="
          width: 100%;
          max-width: 280px;
          border-right: 1px solid #e0e0e0;
          display: flex;
          flex-direction: column;
          background: white;
        ">
          <div style="padding: 16px; border-bottom: 1px solid #e0e0e0;">
            <h6 style="font-size: 16px; font-weight: 600; color: #333; margin: 0 0 12px 0;">Conversations</h6>
            <input type="text" id="adminConversationSearch" placeholder="Search users..." style="
              width: 100%;
              padding: 10px 12px;
              border: 1px solid #e0e0e0;
              border-radius: 20px;
              font-size: 14px;
              outline: none;
            ">
          </div>
          <ul class="admin-conversations-list" style="
            flex: 1;
            overflow-y: auto;
            padding: 0;
            list-style: none;
            margin: 0;
          ">
            <li style="padding: 16px; text-align: center; color: #999;">Loading conversations...</li>
          </ul>
        </div>
        
        <!-- MESSAGES PANEL (RIGHT) -->
        <div class="admin-messages-panel" style="
          flex: 1;
          display: flex;
          flex-direction: column;
          background: white;
        ">
          <div style="padding: 16px; border-bottom: 1px solid #e0e0e0; display: flex; justify-content: space-between; align-items: center;">
            <div class="admin-messages-header-info">
              <h6 style="margin: 0; font-size: 16px; font-weight: 600; color: #333;">Select a conversation</h6>
              <p style="margin: 4px 0 0 0; font-size: 12px; color: #999;">Choose a user to view messages</p>
            </div>
          </div>
          <div class="admin-messages-container" style="
            flex: 1;
            overflow-y: auto;
            padding: 16px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            align-items: center;
            justify-content: center;
            color: #999;
          ">
            <p style="margin: 0; text-align: center;">Select a conversation to view messages</p>
          </div>
          <div class="admin-message-input-area" style="
            padding: 16px;
            border-top: 1px solid #e0e0e0;
            display: none;
            gap: 12px;
          ">
            <div class="admin-message-input-wrapper" style="
              flex: 1;
              display: flex;
              gap: 8px;
            ">
              <textarea placeholder="Type a message..." rows="1" style="
                flex: 1;
                padding: 12px 16px;
                border: 1px solid #e0e0e0;
                border-radius: 20px;
                font-size: 14px;
                font-family: inherit;
                resize: none;
                max-height: 100px;
                outline: none;
              "></textarea>
            </div>
            <button class="admin-send-btn" style="
              padding: 12px 16px;
              background: #97ee5b;
              border: none;
              border-radius: 20px;
              color: #333;
              font-weight: 600;
              cursor: pointer;
              transition: all 0.3s;
            ">
              Send
            </button>
          </div>
        </div>
        
      </div>
    </div>
    </div>
  </div>
</section>

<?php
include('./includes/footer.php');
?>

<!-- Load Messenger Styles and Scripts for Admin -->

