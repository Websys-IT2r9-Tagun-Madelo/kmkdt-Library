<?php
include('./includes/header.php');
include('./includes/tsbar.php');
?>
  <!--  Page Wrapper -->
  <div class="page-wrapper overflow-hidden">

    <!--  Banner Section -->
    <section class="banner-section banner-inner-section position-relative overflow-hidden d-flex align-items-end"
       style="background-image: url('assets/images/backgrounds/contact.jpg');">

      <div class="container">
        <div class="d-flex flex-column gap-4 pb-5 pb-xl-10 position-relative z-1">
          <div class="row align-items-center">
            <div class="col-xl-4">
              <div class="d-flex align-items-center gap-4" data-aos="fade-up" data-aos-delay="100"
                data-aos-duration="1000">
                <img src="../assets/images/svgs/primary-leaf.svg" alt="" class="img-fluid animate-spin">
              </div>
            </div>
          </div>
          <div class="d-flex align-items-end gap-3" data-aos="fade-up" data-aos-delay="200" data-aos-duration="1000">
            <h1 class="mb-0 fs-16 text-white lh-1">Contact Us</h1>
            <a href="javascript:void(0)" class="p-1 ps-7 bg-primary rounded-pill">
              <span class="bg-white round-52 rounded-circle d-flex align-items-center justify-content-center">
                <iconify-icon icon="lucide:arrow-up-right" class="fs-8 text-dark"></iconify-icon>
              </span>
            </a>
          </div>
        </div>
      </div>
    </section>

    <!--  Get in touch Section -->
    <section class="get-in-touch py-5 py-lg-11 py-xl-12">
      <div class="container">
        <div class="d-flex flex-column gap-5 gap-xl-10">
          <div class="row gap-7 gap-xl-0">
            <div class="col-xl-4 col-xxl-4">
              <div class="d-flex align-items-center gap-7 py-2" data-aos="fade-right" data-aos-delay="100"
                data-aos-duration="1000">
                <span
                  class="round-36 flex-shrink-0 text-dark rounded-circle bg-primary hstack justify-content-center fw-medium"></span>
                <hr class="border-line bg-white">
                <span class="badge text-bg-dark">Messenger</span>
              </div>
            </div>
            <div class="col-xl-8 col-xxl-7">
              <div class="row">
                <div class="col-xxl-8">
                  <div class="d-flex flex-column gap-6" data-aos="fade-up" data-aos-delay="100"
                    data-aos-duration="1000">
                    <h2 class="mb-0">Send a Message</h2>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <!-- MESSENGER INTERFACE -->
          <div data-user-id="<?php echo $_SESSION['authUser']['user_id']; ?>">
            <div class="messenger-container" data-aos="fade-up" data-aos-delay="200" data-aos-duration="1000">
              
              <!-- CONVERSATIONS PANEL (LEFT) -->
              <div class="conversations-panel">
                <div class="conversations-header">
                  <h4 class="conversations-title">Messages</h4>
                  
                  <!-- Built-in Library Support Chat -->
                  <div class="library-support-chat" id="librarySupport" style="
                    background: linear-gradient(135deg, #97ee5b 0%, #85d84d 100%);
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
