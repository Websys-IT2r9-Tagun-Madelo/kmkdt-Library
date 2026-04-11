<?php
include('./includes/header.php');
include('./includes/tsbar.php');
?>
<!doctype html>
<html lang="en">

  <!--  Page Wrapper -->
  <div class="page-wrapper overflow-hidden">

    <!--  Banner Section -->
    <section class="banner-section banner-inner-section position-relative overflow-hidden d-flex align-items-end"
      style="background-image: url(../assets/images/backgrounds/blog-banner.jpg);">
      <div class="container">
        <div class="d-flex flex-column gap-4 pb-5 pb-xl-10 position-relative z-1">
          <div class="row align-items-center">
            <div class="col-xl-4">
              <div class="d-flex align-items-center gap-4" data-aos="fade-up" data-aos-delay="100"
                data-aos-duration="1000">
                <img src="../assets/images/svgs/primary-leaf.svg" alt="" class="img-fluid animate-spin">
                <p class="mb-0 text-white fs-5 text-opacity-70">Excited to <span class="text-primary">begin something
                    amazing?</span>Get in touch—we'd love to connect with you!</p>
              </div>
            </div>
          </div>
          <div class="d-flex align-items-end gap-3" data-aos="fade-up" data-aos-delay="200" data-aos-duration="1000">
            <h1 class="mb-0 fs-16 text-white lh-1">Browsed Books</h1>
            <a href="javascript:void(0)" class="p-1 ps-7 bg-primary rounded-pill">
              <span class="bg-white round-52 rounded-circle d-flex align-items-center justify-content-center">
                <iconify-icon icon="lucide:arrow-up-right" class="fs-8 text-dark"></iconify-icon>
              </span>
            </a>
          </div>
        </div>
      </div>
    </section>

    <!--  Blog Section -->
    <section class="blog-section py-5 py-lg-11 py-xl-12">
      <div class="container">
        <div class="row">
          <div class="col-lg-6 mb-7">
            <div class="resources d-flex flex-column gap-6" data-aos="fade-up" data-aos-delay="100"
              data-aos-duration="1000">
              <a href="blog-detail.html"
                class="resources-img resources-img-blog position-relative overflow-hidden d-block">
                <img src="../assets/images/resources/resources-1.jpg" alt="resources" class="img-fluid">
              </a>
              <div class="resources-details">
                <p class="mb-0">Dec 24, 2025</p>
                <h4 class="mb-0">A campaign that connects</h4>
              </div>
            </div>
          </div>
          <div class="col-lg-6 mb-7">
            <div class="resources d-flex flex-column gap-6" data-aos="fade-up" data-aos-delay="200"
              data-aos-duration="1000">
              <a href="blog-detail.html"
                class="resources-img resources-img-blog position-relative overflow-hidden d-block">
                <img src="../assets/images/resources/resources-2.jpg" alt="resources" class="img-fluid">
              </a>
              <div class="resources-details">
                <p class="mb-0">Dec 24, 2025</p>
                <h4 class="mb-0">An breaking boundaries our latest brand redesign</h4>
              </div>
            </div>
          </div>
          <div class="col-lg-6 mb-7">
            <div class="resources d-flex flex-column gap-6" data-aos="fade-up" data-aos-delay="300"
              data-aos-duration="1000">
              <a href="blog-detail.html"
                class="resources-img resources-img-blog position-relative overflow-hidden d-block">
                <img src="../assets/images/resources/resources-3.jpg" alt="resources" class="img-fluid">
              </a>
              <div class="resources-details">
                <p class="mb-0">Dec 24, 2025</p>
                <h4 class="mb-0">Recognized for design</h4>
              </div>
            </div>
          </div>
          <div class="col-lg-6 mb-7">
            <div class="resources d-flex flex-column gap-6" data-aos="fade-up" data-aos-delay="400"
              data-aos-duration="1000">
              <a href="blog-detail.html"
                class="resources-img resources-img-blog position-relative overflow-hidden d-block">
                <img src="../assets/images/services/services-img-1.jpg" alt="resources" class="img-fluid">
              </a>
              <div class="resources-details">
                <p class="mb-0">Dec 24, 2025</p>
                <h4 class="mb-0">The Modern Lens Perspectives on Culture & Trends</h4>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

  </div>
</body>

</html>


<?php
include('./includes/footer.php');
?>