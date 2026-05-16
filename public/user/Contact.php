<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include('./includes/header.php');
include('./includes/tsbar.php');

// Robust session extraction fallback block to catch all auth configurations
$sessionUserId = $_SESSION['user_id'] ?? $_SESSION['authUser']['user_id'] ?? 0;
?>
  <div class="page-wrapper overflow-hidden">

    <section class="banner-section position-relative d-flex align-items-center"
      style="background: linear-gradient(135deg, rgba(0,0,0,0.9) 0%, rgba(0,0,0,0.6) 50%, rgba(255,255,255,0.1) 100%), url('assets/images/backgrounds/contact.jpg'); background-size: cover; background-position: center; min-height: 45vh;">
      <div class="container">
        <div class="row">
          <div class="col-lg-7">
            <div class="position-relative z-1" data-aos="fade-up">
              <h1 class="display-3 fw-extrabold text-white mb-3">Support & Community</h1>
              <p class="text-white-50 fs-5 mb-0">Submit inquiries, report issues, and access support services for your account and platform needs.</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="get-in-touch py-5 py-lg-9 py-xl-10">
      <div class="container">
          
          <div id="messengerUserContext" data-user-id="<?php echo htmlspecialchars($sessionUserId); ?>">
            <div class="messenger-container" data-aos="fade-up" data-aos-delay="200" data-aos-duration="1000">
              
              <div class="conversations-panel">
                <div class="conversations-header">
                  <h4 class="conversations-title">Messages</h4>

                  <div class="library-support-chat" id="librarySupport">
                    <span class="support-badge">?</span>
                    <div class="support-info" style="flex: 1;">
                      <div class="support-title">Library Support</div>
                      <div class="support-subtitle">Get help anytime</div>
                    </div>
                    <span class="support-pin">📌</span>
                  </div>

                  
                  <button class="new-chat-btn text-white">
                    <span>+ New Chat</span>
                  </button>
                </div>
                <ul class="conversations-list">
                  <li style="padding: 16px; text-align: center; color: #999;">Loading...</li>
                </ul>
              </div>
              
              <div class="messages-panel">
                <div class="messages-header">
                  <div class="messages-header-info">
                    <h3>Select a conversation</h3>
                    <p>Start chatting with the library admin or other users</p>
                  </div> 
                </div>
                <div class="messages-container messages-empty">
                  <p>Select a conversation to start messaging</p>
                </div>
                <div class="message-input-area" style="display: none;">
                  <div class="message-input-wrapper">
                    <textarea placeholder="Type a message..." rows="1"></textarea>
                  </div>
                  <button class="send-btn">
                    <span>Send</span>
                  </button>
                </div>
              </div>
              
            </div>
          </div>
          
          <div class="new-chat-modal">
            <div class="new-chat-content">
              <h4>Start a New Chat</h4>
              <p>Enter the email address of the user you want to message:</p>
              <input type="email" placeholder="Enter user email...">
              <div class="new-chat-buttons">
                <button class="btn-start-chat">Start Chat</button>
                <button class="btn-cancel">Cancel</button>
              </div>
            </div>
          </div>
          
      </div>
    </section>

  </div>



<?php
include('./includes/footer.php');
?>