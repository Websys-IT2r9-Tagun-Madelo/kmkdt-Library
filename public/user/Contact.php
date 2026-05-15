<?php
include('./includes/header.php');
include('./includes/tsbar.php');
?>
  <!--  Page Wrapper -->
  <div class="page-wrapper overflow-hidden">

  <!-- Banner Section -->
    <section class="banner-section position-relative d-flex align-items-center"
      style="background: linear-gradient(135deg, rgba(0,0,0,0.9) 0%, rgba(0,0,0,0.6) 50%, rgba(255,255,255,0.1) 100%), url('assets/images/backgrounds/contact.jpg'); background-size: cover; background-position: center; min-height: 45vh;">
      <div class="container">

        <div class="row">
          <div class="col-lg-7">
            <div class="position-relative z-1" data-aos="fade-up">
              <h1 class="display-3 fw-extrabold text-white mb-3"> Support & Community</h1>
              <p class="text-white-50 fs-5 mb-0"> Submit inquiries, report issues, and access support services for your account and platform needs. </p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!--  Get in touch Section -->
    <section class="get-in-touch py-5 py-lg-9 py-xl-10">
      <div class="container">
          
          <!-- MESSENGER INTERFACE -->
          <div data-user-id="<?php echo $_SESSION['authUser']['user_id']; ?>">
            <div class="messenger-container" data-aos="fade-up" data-aos-delay="200" data-aos-duration="1000">
              
              <!-- CONVERSATIONS PANEL (LEFT) -->
              <div class="conversations-panel">
                <div class="conversations-header">
                  <h4 class="conversations-title">Messages</h4>


                  <!-- Built-in Library Support Chat -->
                  <div class="library-support-chat" id="librarySupport" style="
                    background: linear-gradient(135deg, #57cb57 0%, #356635 100%);
                    border-radius: 12px;
                    padding: 12px 14px;
                    margin-bottom: 12px;
                    cursor: pointer;
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    transition: all 0.3s;
                    box-shadow: 0 2px 8px rgba(151, 238, 91, 0.2);
                  ">
                    <span style="
                      display: flex;
                      align-items: center;
                      justify-content: center;
                      width: 32px;
                      height: 32px;
                      background: white;
                      border-radius: 50%;
                      font-weight: 700;
                      color: #333;
                      font-size: 14px;
                    ">?</span>
                    <div style="flex: 1;">
                      <div style="font-weight: 600; color: #333; font-size: 13px;">Library Support</div>
                      <div style="font-size: 11px; color: rgba(51, 51, 51, 0.7);">Get help anytime</div>
                    </div>
                    <span style="font-size: 12px; color: #333;">📌</span>
                  </div>
                  
                  <button class="new-chat-btn">
                    <span>+ New Chat</span>
                  </button>
                </div>
                <ul class="conversations-list">
                  <li style="padding: 16px; text-align: center; color: #999;">Loading...</li>
                </ul>
              </div>
              
              <!-- MESSAGES PANEL (RIGHT) -->
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
          
          <!-- NEW CHAT MODAL -->
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
      </div>
    </section>

  </div>

<?php
include('./includes/footer.php');
?>

<!-- Load Messenger Styles and Scripts -->
<link rel="stylesheet" href="../assets/css/messenger.css">
<script src="../assets/js/messenger.js"></script>
