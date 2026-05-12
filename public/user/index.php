<?php
session_start();
include('./includes/header.php');
include('./includes/tsbar.php');
?>

<div class="page-wrapper overflow-hidden bg-white">

  <!-- Hero Section -->
  <section class="banner-section position-relative d-flex align-items-center py-10"
    style="background: linear-gradient(135deg, rgba(0,0,0,0.9) 0%, rgba(0,0,0,0.6) 50%, rgba(255,255,255,0.1) 100%), url('assets/images/backgrounds/home.jpg'); background-size: cover; background-position: center; min-height: 45vh;">
    <div class="container">

      <div class="row">
        <div class="col-lg-7">
          <div class="position-relative z-1" data-aos="fade-up">
            <div class="d-flex align-items-center gap-2 mb-3">
              <iconify-icon icon="lucide:asterisk" style="color: #57cb57;" class="fs-7"></iconify-icon>
              <span class="text-white fw-bold tracking-wider text-uppercase fs-2">kmkdt-Library Portal</span>
            </div>
            <h1 class="display-3 fw-extrabold text-white mb-3"> Explore a World of <span style="color: #7ce87c;">Knowledge</span></h1>
            <p class="text-white-50 fs-5 mb-0">Your gateway to digital resources and academic management. All your active loans and profile tools in one place.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Dashboard Section -->
  <section class="py-5 py-lg-8 bg-white">
    <div class="container">
      <div class="row mb-5" data-aos="fade-right">
        <div class="col-12">
          <span class="badge rounded-pill px-3 py-2 mb-3"
            style="background: rgba(50, 205, 50, 0.1); color: #28a745; font-weight: 800; border: 1px solid #32cd32;">DASHBOARD
            OVERVIEW</span>
          <h2 class="fw-bold text-dark">Quick Access Tools</h2>
        </div>
      </div>

      <div class="row g-4">
        <!-- Profile -->
        <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
          <div class="card portal-card p-2">
            <div class="card-body d-flex flex-column">
              <div class="icon-box mb-4"><iconify-icon icon="lucide:user" class="fs-8 text-lime"></iconify-icon></div>
              <h4 class="fw-bold text-dark mb-2">My Profile</h4>
              <p class="text-muted small mb-4 flex-grow-1">Manage your account settings, contact details, and security
                credentials.</p>
              <a href="profile" class="btn btn-portal w-100 rounded-pill">Manage Account</a>
            </div>
          </div>
        </div>
        <!-- Borrowed -->
        <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
          <div class="card portal-card p-2">
            <div class="card-body d-flex flex-column">
              <div class="icon-box mb-4"><iconify-icon icon="lucide:book-open" class="fs-8 text-lime"></iconify-icon>
              </div>
              <h4 class="fw-bold text-dark mb-2">Borrowed Books</h4>
              <p class="text-muted small mb-4 flex-grow-1">Monitor your current loans, check due dates, and request
                extensions.</p>
              <a href="MBB" class="btn btn-portal w-100 rounded-pill">View Your Books</a>
            </div>
          </div>
        </div>
        <!-- Contact -->
        <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
          <div class="card portal-card p-2">
            <div class="card-body d-flex flex-column">
              <div class="icon-box mb-4"><iconify-icon icon="lucide:message-circle"
                  class="fs-8 text-lime"></iconify-icon></div>
              <h4 class="fw-bold text-dark mb-2">Contact Us</h4>
              <p class="text-muted small mb-4 flex-grow-1">Have a question or feedback? Reach out to our team for
                assistance.</p>
              <a href="contact" class="btn btn-portal w-100 rounded-pill">Start Chat</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Divider -->
  <section class="py-5 text-center bg-dark">
    <div class="container">
      <span class="text-uppercase fw-bold opacity-50 text-white ls-2">Digital Hub</span>
      <h1 class="display-2 fw-black text-white mt-2">Discover <span style="color: #28a745;">Resources</span></h1>
      <p class="text-white-50 mx-auto" style="max-width: 600px;">Access a streamlined collection of books, research
        materials, and digital archives designed for modern academic excellence.</p>
    </div>
  </section>

  <!-- Featured Library Collections Section -->
  <section class="py-5 py-lg-8 bg-light">
    <div class="container">
      <div class="row align-items-center mb-5" data-aos="fade-up">
        <div class="col-lg-12">
          <div class="section-label">
                <span class="round-36 flex-shrink-0 text-dark rounded-circle bg-primary hstack justify-content-center fw-medium">01</span>
                <hr class="border-line bg-white">
            <span class="badge bg-white text-dark px-3 py-2">Library</span>
          </div>
          <h2 class="display-6 fw-bold text-white">Featured Library Collections</h2>
          <p class="text-white-50 fs-4">Explore curated books and research materials available in our system.</p>
        </div>
      </div>

      <div class="row g-4">
        <!-- Item 1: General -->
        <div class="col-md-4">
          <div class="card portal-card border-0 shadow-sm overflow-hidden">
            <div class="img-container">
              <img src="assets/images/books/general.jpg" alt="General">
              <div
                class="portfolio-hover-overlay d-flex align-items-center justify-content-center position-absolute top-0 w-100 h-100"
                style="background:rgba(50,205,50,0.8); opacity:0; transition:0.3s;">
                <a href="BrowBoks" class="btn btn-light rounded-circle p-3"><iconify-icon icon="lucide:arrow-up-right"
                    class="fs-5"></iconify-icon></a>
              </div>
            </div>
            <div class="p-4">
              <h4 class="fw-bold h5 mb-3">General Reference Books</h4>
              <div class="d-flex gap-2"><span class="badge border text-muted fw-normal">Fiction</span><span
                  class="badge border text-muted fw-normal">Non-Fiction</span></div>
            </div>
          </div>
        </div>
        <!-- Item 2: Journals -->
        <div class="col-md-4">
          <div class="card portal-card border-0 shadow-sm overflow-hidden">
            <div class="img-container">
              <img src="assets/images/books/journal.jpg" alt="Journals">
              <div
                class="portfolio-hover-overlay d-flex align-items-center justify-content-center position-absolute top-0 w-100 h-100"
                style="background:rgba(50,205,50,0.8); opacity:0; transition:0.3s;">
                <a href="BrowBoks" class="btn btn-light rounded-circle p-3"><iconify-icon icon="lucide:arrow-up-right"
                    class="fs-5"></iconify-icon></a>
              </div>
            </div>
            <div class="p-4">
              <h4 class="fw-bold h5 mb-3">Academic Journals</h4>
              <div class="d-flex gap-2"><span class="badge border text-muted fw-normal">Research</span><span
                  class="badge border text-muted fw-normal">Science</span></div>
            </div>
          </div>
        </div>
        <!-- Item 3: E-Books -->
        <div class="col-md-4">
          <div class="card portal-card border-0 shadow-sm overflow-hidden">
            <div class="img-container">
              <img src="assets/images/books/ebok.jpg" alt="eBooks">
              <div
                class="portfolio-hover-overlay d-flex align-items-center justify-content-center position-absolute top-0 w-100 h-100"
                style="background:rgba(50,205,50,0.8); opacity:0; transition:0.3s;">
                <a href="BrowBoks" class="btn btn-light rounded-circle p-3"><iconify-icon icon="lucide:arrow-up-right"
                    class="fs-5"></iconify-icon></a>
              </div>
            </div>
            <div class="p-4">
              <h4 class="fw-bold h5 mb-3">Digital eBooks</h4>
              <div class="d-flex gap-2"><span class="badge border text-muted fw-normal">E-Library</span><span
                  class="badge border text-muted fw-normal">Online</span></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Testimonials -->
  <section class="py-5 py-lg-8 bg-white">
    <div class="container">
      <div class="section-label" data-aos="fade-up">
        <span class="round-36 flex-shrink-0 text-white rounded-circle bg-primary hstack justify-content-center fw-medium">02</span>
        <hr class="border-line bg-white">
        <span class="badge bg-dark text-white px-3 py-2">Testimonials</span>
      </div>
      <h2 class="display-6 fw-bold text-dark">Reader Feedback</h2>
       <p class="text-dark-50 fs-4"> Hear from our community about their reading experience, library services, and how we help them discover new books.</p>
      <div class="row g-4">
        <div class="col-lg-4">
          <div class="card border-0 rounded-4 p-4 h-100" style="background-color: #7ce87c;">
            <iconify-icon icon="ri:double-quotes-l" class="fs-1 text-dark opacity-25 mb-3"></iconify-icon>
            <h5 class="fw-bold mb-4">The library helped me find all my research materials easily.</h5>
            <div class="mt-auto d-flex align-items-center gap-3">
              <img src="assets/images/profile/user-1.jpg" class="rounded-circle" width="50" height="50">
              <div>
                <h6 class="mb-0 fw-bold">User</h6><small class="text-dark opacity-75">Member</small>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="card border-0 rounded-4 p-4 h-100 bg-dark text-white">
            <iconify-icon icon="ri:double-quotes-l" class="fs-1 text-white opacity-25 mb-3"></iconify-icon>
            <h5 class="text-white-50 mb-4">Access to digital books and journals made studying much easier.</h5>
            <div class="mt-auto d-flex align-items-center gap-3">
              <img src="assets/images/profile/user-2.jpg" class="rounded-circle border" width="50" height="50">
              <div>
                <h6 class="mb-0 fw-bold text-white">Library User</h6><small class="text-white-50">E-Learning
                  Student</small>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="card border-0 rounded-4 p-4 h-100" style="background-color: #7ce87c;">
            <iconify-icon icon="ri:double-quotes-l" class="fs-1 text-dark opacity-25 mb-3"></iconify-icon>
            <h5 class="fw-bold mb-4">Great place for studying and accessing professional resources.</h5>
            <div class="mt-auto d-flex align-items-center gap-3">
              <img src="assets/images/profile/user-3.jpg" class="rounded-circle" width="50" height="50">
              <div>
                <h6 class="mb-0 fw-bold">Reader</h6><small class="text-dark opacity-75">Library Member</small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- News Section -->
  <section class="py-5 py-lg-8 bg-light">
    <div class="container">
      <div class="section-label" data-aos="fade-up">
        <span class="round-36 flex-shrink-0 text-dark rounded-circle bg-primary hstack justify-content-center fw-medium">03</span>
        <hr class="border-line bg-white">
        <span class="badge bg-white text-dark px-3 py-2">Library News</span>
      </div>
      <h2 class="display-6 fw-bold text-white">Library Updates</h2>
      <p class="text-white-50 fs-4"> Stay informed about new book arrivals, upcoming events, and important announcements from our library.</p>
      <div class="row g-4">
        <div class="col-xl-6">
          <div class="card portal-card overflow-hidden">
            <img src="assets/images/news/news1.jpg" class="w-100" style="height: 300px; object-fit: cover;">
            <div class="p-4">
              <span class="text-lime fw-bold small">GROWTH • APRIL 17, 2026</span>
              <h3 class="fw-bold h4 mt-2"><a href="blog-detail.php"
                  class="text-decoration-none text-dark hover-lime">Popular book collections continue attracting active
                  student readers.</a></h3>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-md-6">
          <div class="card portal-card overflow-hidden">
            <img src="assets/images/news/news2.jpg" class="w-100" style="height: 180px; object-fit: cover;">
            <div class="p-4">
              <span class="text-muted small">April 28, 2026</span>
              <h5 class="fw-bold mt-2 h6">Breaking boundaries in our latest system redesign</h5>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-md-6">
          <div class="card portal-card overflow-hidden">
            <img src="assets/images/news/news3.jpg" class="w-100" style="height: 180px; object-fit: cover;">
            <div class="p-4">
              <span class="text-muted small">May 01, 2026</span>
              <h5 class="fw-bold mt-2 h6">Recognized for library innovation excellence</h5>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- FAQ Section -->
  <section class="py-5 py-lg-8 bg-white">
    <div class="container">
      <div class="section-label" data-aos="fade-up">
        <span class="round-36 flex-shrink-0 text-white rounded-circle bg-primary hstack justify-content-center fw-medium">04</span>
        <hr class="border-line bg-white">
        <span class="badge bg-dark text-white px-3 py-2">Library Support</span>
      </div>
      <h2 class="display-6 fw-bold text-dark mb-4">Frequently asked questions</h2>
      <p class="text-muted fs-3 mb-0">Discover how we organize our services to deliver accessible resources and
        personalized assistance.</p>

      <div class="row justify-content-end">
        <div class="col-xl-7">
          <div class="accordion custom-accordion" id="libraryFaq">
           
          <!-- Question 1 -->
            <div class="accordion-item border-top">
              <h2 class="accordion-header">
                <button class="accordion-button collapsed fw-bold py-4" type="button" data-bs-toggle="collapse"
                  data-bs-target="#faq1">
                  What services does the library offer?
                </button>
              </h2>

              <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#libraryFaq">
                <div class="accordion-body text-muted pb-4">
                  We provide comprehensive library support including research assistance, reading material procurement,
                  and 24/7 digital catalog access for all registered members.
                </div>
              </div>
            </div>


            <!-- Question 2 -->
            <div class="accordion-item">
              <h2 class="accordion-header">
                <button class="accordion-button collapsed fw-bold py-4" type="button" data-bs-toggle="collapse"
                  data-bs-target="#faq2">
                  How long can I borrow books or resources?
                </button>
              </h2>

              <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#libraryFaq">
                <div class="accordion-body text-muted pb-4">
                  Standard loan duration for physical books is 14 days. Fiction books may be borrowed for up to 30 days,
                  while nonfiction and educational materials are available for 14 days.
                  Renewals can be requested through your dashboard under the “Borrowed Books” section, up to 2 times.
                </div>
              </div>
            </div>



            <!-- Question 3 -->
            <div class="accordion-item">
              <h2 class="accordion-header">
                <button class="accordion-button collapsed fw-bold py-4" type="button" data-bs-toggle="collapse"
                  data-bs-target="#faq3">
                  Is there any membership fee for library access?
                </button>

              </h2>

              <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#libraryFaq">
                <div class="accordion-body text-muted pb-4">
                  Membership is free for all users.
                </div>
              </div>
            </div>



            <!-- Question 4 -->
            <div class="accordion-item border-bottom">
              <h2 class="accordion-header">
                <button class="accordion-button collapsed fw-bold py-4" type="button" data-bs-toggle="collapse"
                  data-bs-target="#faq4">
                  Do you provide support for research and study?
                </button>

              </h2>
              <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#libraryFaq">
                <div class="accordion-body text-muted pb-4">
                  Yes, our team offers one-on-one sessions to help you navigate databases, cite sources, and find
                  relevant academic literature.
                </div>
              </div>
            </div>


            <div class="mt-3">
              <a href="contact"
                class="text-dark fw-bold text-decoration-none d-inline-flex align-items-center gap-2 hover-lime">
                Have questions? Contact us
                <iconify-icon icon="lucide:arrow-right" class="fs-4"></iconify-icon>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>



  <!-- Footer Break -->
  <section class="py-1 bg-dark text-center">
    <div class="container"><span class="text-uppercase fw-bold opacity-50 text-white ls-2">you reach the end..</span>
    </div>
  </section>

</div>

<script>
  // Simple check for Portfolio card hover effect logic if needed
  document.querySelectorAll('.portal-card').forEach(card => {
    card.addEventListener('mouseenter', () => {
      const overlay = card.querySelector('.portfolio-hover-overlay');
      if (overlay) overlay.style.opacity = '1';
    });
    card.addEventListener('mouseleave', () => {
      const overlay = card.querySelector('.portfolio-hover-overlay');
      if (overlay) overlay.style.opacity = '0';
    });
  });
</script>

<?php include('./includes/footer.php'); ?>