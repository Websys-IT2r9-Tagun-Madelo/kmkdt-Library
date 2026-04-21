<?php
include('./includes/header.php');
include('./includes/tsbar.php');
?>

<div class="page-wrapper overflow-hidden">

<!-- Banner Section -->
<section class="banner-section banner-inner-section position-relative overflow-hidden d-flex align-items-end"
  style="background-image: url(../assets/images/backgrounds/blog-banner.jpg);">

  <div class="container">

    <div class="d-flex flex-column gap-4 pb-5 pb-xl-10 position-relative z-1">

      <div class="row align-items-center">
        <div class="col-xl-4">

          <div class="d-flex align-items-center gap-4" data-aos="fade-up" data-aos-delay="100"
            data-aos-duration="1000">

            <img src="../assets/images/svgs/primary-leaf.svg" alt="" class="img-fluid animate-spin">

            <p class="mb-0 text-white fs-5 text-opacity-70">
              Explore our <span class="text-primary">library collection</span> and discover your next favorite book.
            </p>

          </div>

        </div>
      </div>

      <div class="d-flex align-items-end gap-3" data-aos="fade-up" data-aos-delay="200" data-aos-duration="1000">

        <h1 class="mb-0 fs-16 text-white lh-1">Browse Books</h1>

        <a href="javascript:void(0)" class="p-1 ps-7 bg-primary rounded-pill">
          <span class="bg-white round-52 rounded-circle d-flex align-items-center justify-content-center">
            <iconify-icon icon="lucide:arrow-up-right" class="fs-8 text-dark"></iconify-icon>
          </span>
        </a>

      </div>

    </div>

  </div>

</section>

    <section class="filter-section pt-5">
        <div class="container">
            <div class="row g-4 align-items-center mb-5">
                <div class="col-md-5">
                    <form action="" method="GET" class="position-relative">
                        <input type="text" name="search" class="form-control rounded-pill py-3 ps-5" placeholder="Search titles..." style="border: 1px solid #32cd32;">
                        <button type="submit" class="btn position-absolute end-0 top-50 translate-middle-y me-2 bg-primary rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <iconify-icon icon="lucide:search" class="text-white"></iconify-icon>
                        </button>
                    </form>
                </div>
                <div class="col-md-7">
                    <div class="d-flex gap-2 justify-content-md-end flex-wrap">
                        <span class="align-self-center me-2 fw-bold">Filter By:</span>
                        <a href="#" class="btn rounded-pill px-4" style="background-color: #32cd32; color: white;">All</a>
                        <a href="#" class="btn btn-outline-dark rounded-pill px-4">Fiction</a>
                        <a href="#" class="btn btn-outline-dark rounded-pill px-4">Non-Fiction</a>
                    </div>
                </div>
            </div>

            <div class="row">
                
                <div class="col-12 mb-4">
                    <div class="p-4 rounded-4 shadow-sm d-flex justify-content-between align-items-center border-start border-5" style="border-color: #32cd32 !important; background-color: #fff;">
                        <div>
                            <div class="d-flex gap-2 mb-2">
                                <small class="text-uppercase fw-bold text-muted" style="letter-spacing: 1px;">Non-Fiction</small>
                                <span class="badge rounded-pill px-3" style="background-color: #f0fdf4; color: #32cd32; border: 1px solid #32cd32;">Technology</span>
                            </div>
                            <h3 class="mb-1 h4">Introduction to Programming</h3>
                            <p class="mb-0 text-secondary">By <span class="fw-semibold">John Smith</span></p>
                        </div>
                        <div class="text-end">
                            <a href="#" class="btn btn-dark rounded-pill px-4">Borrow Book</a>
                        </div>
                    </div>
                </div>

                <div class="col-12 mb-4">
                    <div class="p-4 rounded-4 shadow-sm d-flex justify-content-between align-items-center border-start border-5" style="border-color: #32cd32 !important; background-color: #fff;">
                        <div>
                            <div class="d-flex gap-2 mb-2">
                                <small class="text-uppercase fw-bold text-muted" style="letter-spacing: 1px;">Non-Fiction</small>
                                <span class="badge rounded-pill px-3" style="background-color: #f0fdf4; color: #32cd32; border: 1px solid #32cd32;">History</span>
                            </div>
                            <h3 class="mb-1 h4">World History</h3>
                            <p class="mb-0 text-secondary">By <span class="fw-semibold">Maria Lopez</span></p>
                        </div>
                        <div class="text-end">
                            <a href="#" class="btn btn-dark rounded-pill px-4">Borrow Book</a>
                        </div>
                    </div>
                </div>

                <div class="col-12 mb-4">
                    <div class="p-4 rounded-4 shadow-sm d-flex justify-content-between align-items-center border-start border-5" style="border-color: #32cd32 !important; background-color: #fff;">
                        <div>
                            <div class="d-flex gap-2 mb-2">
                                <small class="text-uppercase fw-bold text-muted" style="letter-spacing: 1px;">Non-Fiction</small>
                                <span class="badge rounded-pill px-3" style="background-color: #f0fdf4; color: #32cd32; border: 1px solid #32cd32;">Sports</span>
                            </div>
                            <h3 class="mb-1 h4">Formula 1 Racing Guide</h3>
                            <p class="mb-0 text-secondary">By <span class="fw-semibold">Various Authors</span></p>
                        </div>
                        <div class="text-end">
                            <a href="#" class="btn btn-dark rounded-pill px-4">Borrow Book</a>
                        </div>
                    </div>
                </div>

                <div class="col-12 mb-4">
                    <div class="p-4 rounded-4 shadow-sm d-flex justify-content-between align-items-center border-start border-5" style="border-color: #32cd32 !important; background-color: #fff;">
                        <div>
                            <div class="d-flex gap-2 mb-2">
                                <small class="text-uppercase fw-bold text-muted" style="letter-spacing: 1px;">Fiction</small>
                                <span class="badge rounded-pill px-3" style="background-color: #f0fdf4; color: #32cd32; border: 1px solid #32cd32;">Manga</span>
                            </div>
                            <h3 class="mb-1 h4">One Piece Vol. 1</h3>
                            <p class="mb-0 text-secondary">By <span class="fw-semibold">Eiichiro Oda</span></p>
                        </div>
                        <div class="text-end">
                            <a href="#" class="btn btn-dark rounded-pill px-4">Borrow Book</a>
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