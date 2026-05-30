<?php

include('./includes/header.php');
include('./includes/tsbar.php');

// Robust session extraction fallback block to catch all auth configurations
$sessionUserId = $_SESSION['user_id'] ?? $_SESSION['authUser']['user_id'] ?? 0;
?>
  <div class="page-wrapper overflow-hidden">

    <section class="banner-section position-relative d-flex align-items-center messagehub-banner">
      <div class="container">
        <div class="row">
          <div class="col-lg-7">
            <div class="position-relative z-1" data-aos="fade-up">
              <h1 class="display-3 fw-extrabold text-white mb-3">Message Hub</h1>
              <p class="text-white-50 fs-5 mb-0">Submit inquiries, report issues, and access support services for your account and platform needs.</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="messagehub-info-section py-5" data-admin-id="<?php echo htmlspecialchars($adminId ?? ''); ?>">
        <div class="container">
            <div class="row g-4">
                
                <div class="col-sm-6 col-xl-3" data-aos="fade-up" data-aos-delay="100">
                    <div class="card border-0 shadow-sm h-100 p-4 transition-hover">
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-avatar me-3 bg-primary bg-opacity-10 text-primary rounded-3 d-flex align-items-center justify-content-center">
                                <i class="bi bi-geo-alt-fill fs-4"></i>
                            </div>
                            <span class="text-muted fw-bold small tracking-wider">LIBRARY ADDRESS</span>
                        </div>
                        <p class="mb-0 text-secondary fw-semibold"> Lapasan <br> Cagayan de Oro City</p>
                    </div>
                </div>

                <div class="col-sm-6 col-xl-3" data-aos="fade-up" data-aos-delay="200">
                    <div class="card border-0 shadow-sm h-100 p-4 transition-hover">
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-avatar me-3 bg-success bg-opacity-10 text-success rounded-3 d-flex align-items-center justify-content-center">
                                <i class="bi bi-telephone-fill fs-4"></i>
                            </div>
                            <span class="text-muted fw-bold small tracking-wider">CONTACT NUMBER</span>
                        </div>
                        <p class="mb-0 text-secondary fw-semibold">+63 912 345 6789<br>+63 992 892 2973</p>
                    </div>
                </div>

                <div class="col-sm-6 col-xl-3" data-aos="fade-up" data-aos-delay="300">
                    <div class="card border-0 shadow-sm h-100 p-4 transition-hover">
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-avatar me-3 bg-danger bg-opacity-10 text-danger rounded-3 d-flex align-items-center justify-content-center">
                                <i class="bi bi-envelope-fill fs-4"></i>
                            </div>
                            <span class="text-muted fw-bold small tracking-wider">OTHER EMAILs</span>
                        </div>
                        <p class="mb-0 text-secondary fw-semibold text-break">admin2@kmkdtlibrary.edu.ph<br>admin@kmkdtlibrary.edu.ph</p>
                    </div>
                </div>

                <div class="col-sm-6 col-xl-3" data-aos="fade-up" data-aos-delay="400">
                    <div class="card border-0 shadow-sm h-100 p-4 transition-hover">
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-avatar me-3 bg-warning bg-opacity-10 text-warning rounded-3 d-flex align-items-center justify-content-center">
                                <i class="bi bi-clock-fill fs-4"></i>
                            </div>
                            <span class="text-muted fw-bold small tracking-wider">LIBRARY HOURS</span>
                        </div>
                        <p class="mb-0 text-secondary fw-semibold">Monday - Saturday<br>8:00 AM - 6:00 PM</p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <section class="get-in-touch py-5">
      <div class="container">
          
          <div id="messengerUserContext" data-user-id="<?php echo htmlspecialchars($sessionUserId); ?>">
            <div class="messenger-container shadow-sm border" data-aos="fade-up" data-aos-delay="200" data-aos-duration="1000">
              
              <div class="conversations-panel">
                <div class="conversations-header">
                  <h4 class="conversations-title">Messages</h4>

                  <div class="library-support-chat" id="librarySupport">
                    <span class="support-badge"><i class="bi bi-info-circle fs-4"></i></span>
                    <div class="support-info">
                      <div class="support-title">Library Support</div>
                      <div class="support-subtitle">Get help anytime</div>
                    </div>
                    <span class="support-pin"><i class="bi bi-pin-angle-fill"></i></span>
                  </div>

                  <button class="new-chat-btn text-white">
                    <span>+ New Chat</span>
                  </button>
                </div>
                
                <ul class="conversations-list">
                  <li class="loading-item">Loading...</li>
                </ul>
              </div>
              
              <div class="messages-panel bg-light-subtle">
                <div class="messages-header border-bottom">
                  <div class="messages-header-info">
                    <h3>Select a conversation</h3>
                    <p>Start chatting with the library admin or other users</p>
                  </div> 
                </div>
                
                <div class="messages-container messages-empty">
                  <p>Select a conversation to start messaging</p>
                </div>
                
                <div class="message-input-area border-top bg-white">
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
            <div class="new-chat-content shadow border">
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