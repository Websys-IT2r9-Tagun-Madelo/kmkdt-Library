<?php

session_start();

include('./includes/header.php');
include('./includes/tsbar.php');
?>

<!--  Page Wrapper -->
<div class="page-wrapper overflow-hidden">

  <!--  Banner Section -->
  <section class="banner-section position-relative d-flex align-items-end min-vh-100">
    <video class="position-absolute top-0 start-0 w-100 h-100 object-fit-cover" autoplay muted loop playsinline>
      <source src="../assets/images/backgrounds/" type="video/mp4" />
    </video>
    <div class="container">
      <div class="d-flex flex-column gap-4 pb-8 position-relative z-1">
        <div class="row align-items-center">
          <div class="col-xl-4">
            <div class="d-flex align-items-center gap-4" data-aos="fade-up" data-aos-delay="100"
              data-aos-duration="1000">
              <img src="../assets/images/svgs/primary-leaf.svg" alt="" class="img-fluid animate-spin">
              <!-- <p class="mb-0 text-white fs-5 text-opacity-70">We create <span
                    class="text-primary">high-performing</span> digital designs that elevate brands and enhance
                  conversions.</p> -->
            </div>
          </div>
        </div>
        <div class="d-flex align-items-end gap-3" data-aos="fade-up" data-aos-delay="200" data-aos-duration="1000">
          <h1 class="mb-0 fs-16 text-white lh-1">kmkdt-Library</h1>
          <a href="javascript:void(0)" class="p-1 ps-7 bg-primary rounded-pill">
            <span class="bg-white round-52 rounded-circle d-flex align-items-center justify-content-center">
              <iconify-icon icon="lucide:arrow-up-right" class="fs-8 text-dark"></iconify-icon>
            </span>
          </a>
        </div>
      </div>
    </div>
  </section>

  <!--  Library Stats & Facts Section -->
  <section class="stats-facts py-5 py-lg-11 py-xl-12 position-relative overflow-hidden">
    <div class="container">
      <div class="row gap-7 gap-xl-0">
        <div class="col-xl-4 col-xxl-4">
          <div class="d-flex align-items-center gap-7 py-2" data-aos="fade-right" data-aos-delay="100"
            data-aos-duration="1000">
            <span
              class="round-36 flex-shrink-0 text-dark rounded-circle bg-primary hstack justify-content-center fw-medium">01</span>
            <hr class="border-line">
            <span class="badge text-bg-dark">Library Stats</span>
          </div>
        </div>

        <div class="col-xl-8 col-xxl-7">
          <div class="d-flex flex-column gap-9">

            <div class="row">
              <div class="col-xxl-8">
                <div class="d-flex flex-column gap-6" data-aos="fade-up" data-aos-delay="100" data-aos-duration="1000">
                  <h2 class="mb-0">Modern library services and resources you can trust.</h2>
                  <p class="fs-5 mb-0">
                    A library system provides access to books, digital resources, and learning materials designed to
                    support education and research needs.
                  </p>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 col-lg-4 mb-7 mb-lg-0">
                <div class="d-flex flex-column gap-6 pt-9 border-top" data-aos="fade-up" data-aos-delay="200"
                  data-aos-duration="1000">
                  <h2 class="mb-0 fs-14"><span class="count" data-target="40">40</span>K+</h2>
                  <p class="mb-0">Registered library members and users</p>
                </div>
              </div>

              <div class="col-md-6 col-lg-4 mb-7 mb-lg-0">
                <div class="d-flex flex-column gap-6 pt-9 border-top" data-aos="fade-up" data-aos-delay="300"
                  data-aos-duration="1000">
                  <h2 class="mb-0 fs-14"><span class="count" data-target="238">238</span>+</h2>
                  <p class="mb-0">Available book collections and resources</p>
                </div>
              </div>

              <div class="col-md-6 col-lg-4 mb-7 mb-lg-0">
                <div class="d-flex flex-column gap-6 pt-9 border-top" data-aos="fade-up" data-aos-delay="400"
                  data-aos-duration="1000">
                  <h2 class="mb-0 fs-14"><span class="count" data-target="3">3</span>M+</h2>
                  <p class="mb-0">Digital library searches and access requests</p>
                </div>
              </div>
            </div>

            <a href="about-us.html" class="btn" data-aos="fade-up" data-aos-delay="500" data-aos-duration="1000">
              <span class="btn-text">About the Library</span>
              <iconify-icon icon="lucide:arrow-up-right"
                class="btn-icon bg-white text-dark round-52 rounded-circle hstack justify-content-center fs-7 shadow-sm"></iconify-icon>
            </a>

          </div>
        </div>
      </div>
    </div>

    <div class="position-absolute bottom-0 start-0" data-aos="zoom-in" data-aos-delay="100" data-aos-duration="1000">
      <img src="../assets/images/backgrounds/stats-facts-bg.svg" alt="" class="img-fluid">
    </div>
  </section>

  <!--  Featured Library Collections Section -->
  <section class="featured-projects py-5 py-lg-11 py-xl-12 bg-light-gray">
    <div class="d-flex flex-column gap-5 gap-xl-11">

      <div class="container">
        <div class="row gap-7 gap-xl-0">

          <div class="col-xl-4 col-xxl-4">
            <div class="d-flex align-items-center gap-7 py-2" data-aos="fade-right" data-aos-delay="100"
              data-aos-duration="1000">
              <span
                class="round-36 flex-shrink-0 text-dark rounded-circle bg-primary hstack justify-content-center fw-medium">02</span>
              <hr class="border-line">
              <span class="badge text-bg-dark">Library</span>
            </div>
          </div>

          <div class="col-xl-8 col-xxl-7">
            <div class="row">
              <div class="col-xxl-8">
                <div class="d-flex flex-column gap-6" data-aos="fade-up" data-aos-delay="100" data-aos-duration="1000">
                  <h2 class="mb-0">Featured library collections</h2>
                  <p class="fs-5 mb-0">
                    Explore curated books, research materials, and digital resources available in our library system.
                  </p>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>

      <div class="featured-projects-slider px-3">
        <div class="owl-carousel owl-theme">

          <div class="item">
            <div class="portfolio d-flex flex-column gap-6">
              <div class="portfolio-img position-relative overflow-hidden">
                <img src="../assets/images/portfolio/portfolio-img-1.jpg" alt="" class="img-fluid">
                <div class="portfolio-overlay">
                  <a href="projects-detail.html"
                    class="position-absolute top-50 start-50 translate-middle bg-primary round-64 rounded-circle hstack justify-content-center">
                    <iconify-icon icon="lucide:arrow-up-right" class="fs-8 text-dark"></iconify-icon>
                  </a>
                </div>
              </div>
              <div class="portfolio-details d-flex flex-column gap-3">
                <h3 class="mb-0">General Reference Books</h3>
                <div class="hstack gap-2">
                  <span class="badge text-dark border">Fiction</span>
                  <span class="badge text-dark border">Non-Fiction</span>
                </div>
              </div>
            </div>
          </div>

          <div class="item">
            <div class="portfolio d-flex flex-column gap-6">
              <div class="portfolio-img position-relative overflow-hidden">
                <img src="../assets/images/portfolio/portfolio-img-2.jpg" alt="" class="img-fluid">
                <div class="portfolio-overlay">
                  <a href="projects-detail.html"
                    class="position-absolute top-50 start-50 translate-middle bg-primary round-64 rounded-circle hstack justify-content-center">
                    <iconify-icon icon="lucide:arrow-up-right" class="fs-8 text-dark"></iconify-icon>
                  </a>
                </div>
              </div>
              <div class="portfolio-details d-flex flex-column gap-3">
                <h3 class="mb-0">Academic Journals</h3>
                <div class="hstack gap-2">
                  <span class="badge text-dark border">Research</span>
                  <span class="badge text-dark border">Science</span>
                </div>
              </div>
            </div>
          </div>

          <div class="item">
            <div class="portfolio d-flex flex-column gap-6">
              <div class="portfolio-img position-relative overflow-hidden">
                <img src="../assets/images/portfolio/portfolio-img-3.jpg" alt="" class="img-fluid">
                <div class="portfolio-overlay">
                  <a href="projects-detail.html"
                    class="position-absolute top-50 start-50 translate-middle bg-primary round-64 rounded-circle hstack justify-content-center">
                    <iconify-icon icon="lucide:arrow-up-right" class="fs-8 text-dark"></iconify-icon>
                  </a>
                </div>
              </div>
              <div class="portfolio-details d-flex flex-column gap-3">
                <h3 class="mb-0">Digital eBooks</h3>
                <div class="hstack gap-2">
                  <span class="badge text-dark border">E-Library</span>
                  <span class="badge text-dark border">Online Access</span>
                </div>
              </div>
            </div>
          </div>

          <div class="item">
            <div class="portfolio d-flex flex-column gap-6">
              <div class="portfolio-img position-relative overflow-hidden">
                <img src="../assets/images/portfolio/portfolio-img-4.jpg" alt="" class="img-fluid">
                <div class="portfolio-overlay">
                  <a href="projects-detail.html"
                    class="position-absolute top-50 start-50 translate-middle bg-primary round-64 rounded-circle hstack justify-content-center">
                    <iconify-icon icon="lucide:arrow-up-right" class="fs-8 text-dark"></iconify-icon>
                  </a>
                </div>
              </div>
              <div class="portfolio-details d-flex flex-column gap-3">
                <h3 class="mb-0">Archived Documents</h3>
                <div class="hstack gap-2">
                  <span class="badge text-dark border">History</span>
                  <span class="badge text-dark border">Records</span>
                </div>
              </div>
            </div>
          </div>

          <div class="item">
            <div class="portfolio d-flex flex-column gap-6">
              <div class="portfolio-img position-relative overflow-hidden">
                <img src="../assets/images/portfolio/portfolio-img-5.jpg" alt="" class="img-fluid">
                <div class="portfolio-overlay">
                  <a href="projects-detail.html"
                    class="position-absolute top-50 start-50 translate-middle bg-primary round-64 rounded-circle hstack justify-content-center">
                    <iconify-icon icon="lucide:arrow-up-right" class="fs-8 text-dark"></iconify-icon>
                  </a>
                </div>
              </div>
              <div class="portfolio-details d-flex flex-column gap-3">
                <h3 class="mb-0">Library Media Resources</h3>
                <div class="hstack gap-2">
                  <span class="badge text-dark border">Audio</span>
                  <span class="badge text-dark border">Video</span>
                </div>
              </div>
            </div>
          </div>

          <div class="item">
            <div class="portfolio d-flex flex-column gap-6">
              <div class="portfolio-img position-relative overflow-hidden">
                <img src="../assets/images/portfolio/portfolio-img-6.jpg" alt="" class="img-fluid">
                <div class="portfolio-overlay">
                  <a href="projects-detail.html"
                    class="position-absolute top-50 start-50 translate-middle bg-primary round-64 rounded-circle hstack justify-content-center">
                    <iconify-icon icon="lucide:arrow-up-right" class="fs-8 text-dark"></iconify-icon>
                  </a>
                </div>
              </div>
              <div class="portfolio-details d-flex flex-column gap-3">
                <h3 class="mb-0">Special Collections</h3>
                <div class="hstack gap-2">
                  <span class="badge text-dark border">Rare Books</span>
                  <span class="badge text-dark border">Archives</span>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>

    </div>
  </section>

  <!--  Library Services Section -->
  <section class="services py-5 py-lg-11 py-xl-12 bg-dark" id="services">
    <div class="container">
      <div class="d-flex flex-column gap-5 gap-xl-10">

        <div class="row gap-7 gap-xl-0">

          <div class="col-xl-4 col-xxl-4">
            <div class="d-flex align-items-center gap-7 py-2" data-aos="fade-right" data-aos-delay="100"
              data-aos-duration="1000">
              <span
                class="round-36 flex-shrink-0 text-dark rounded-circle bg-primary hstack justify-content-center fw-medium">03</span>
              <hr class="border-line bg-white">
              <span class="badge text-dark bg-white">Library Services</span>
            </div>
          </div>

          <div class="col-xl-8 col-xxl-7">
            <div class="row">
              <div class="col-xxl-8">
                <div class="d-flex flex-column gap-6" data-aos="fade-up" data-aos-delay="100" data-aos-duration="1000">
                  <h2 class="mb-0 text-white">What our library offers</h2>
                  <p class="fs-5 mb-0 text-white text-opacity-70">
                    A modern library system providing access to physical books, digital resources, research materials,
                    and learning support services.
                  </p>
                </div>
              </div>
            </div>
          </div>

        </div>

        <div class="services-tab">

          <div class="row gap-5 gap-xl-0">

            <div class="col-xl-4">
              <div class="tab-content" data-aos="zoom-in" data-aos-delay="100" data-aos-duration="1000">

                <div class="tab-pane active" id="one" role="tabpanel" aria-labelledby="one-tab" tabindex="0">
                  <img src="../assets/images/services/services-img-1.jpg" alt="services" class="img-fluid">
                </div>

                <div class="tab-pane" id="two" role="tabpanel" aria-labelledby="two-tab" tabindex="0">
                  <img src="../assets/images/services/services-img-2.jpg" alt="services" class="img-fluid">
                </div>

                <div class="tab-pane" id="three" role="tabpanel" aria-labelledby="three-tab" tabindex="0">
                  <img src="../assets/images/services/services-img-3.jpg" alt="services" class="img-fluid">
                </div>

                <div class="tab-pane" id="four" role="tabpanel" aria-labelledby="four-tab" tabindex="0">
                  <img src="../assets/images/services/services-img-4.jpg" alt="services" class="img-fluid">
                </div>

              </div>
            </div>

            <div class="col-xl-8">
              <div class="d-flex flex-column gap-5">

                <ul class="nav nav-tabs" id="myTab" role="tablist" data-aos="fade-up" data-aos-delay="200"
                  data-aos-duration="1000">

                  <li
                    class="nav-item py-4 py-lg-8 border-top border-white border-opacity-10 d-flex align-items-center w-100"
                    role="presentation">
                    <div class="row w-100 align-items-center gx-3">
                      <div class="col-lg-6 col-xxl-5">
                        <button class="nav-link fs-10 fw-bold py-1 px-0 border-0 rounded-0 flex-shrink-0 active"
                          id="one-tab" data-bs-toggle="tab" data-bs-target="#one" type="button" role="tab"
                          aria-controls="one" aria-selected="true">Book Lending Services</button>
                      </div>
                      <div class="col-lg-6 col-xxl-7">
                        <p class="text-white text-opacity-70 mb-0">
                          Borrow physical books and return them within the allowed lending period using our library
                          system.
                        </p>
                      </div>
                    </div>
                  </li>

                  <li
                    class="nav-item py-4 py-lg-8 border-top border-white border-opacity-10 d-flex align-items-center w-100"
                    role="presentation">
                    <div class="row w-100 align-items-center gx-3">
                      <div class="col-lg-6 col-xxl-5">
                        <button class="nav-link fs-10 fw-bold py-1 px-0 border-0 rounded-0 flex-shrink-0" id="two-tab"
                          data-bs-toggle="tab" data-bs-target="#two" type="button" role="tab" aria-controls="two"
                          aria-selected="false">Digital Library Access</button>
                      </div>
                      <div class="col-lg-6 col-xxl-7">
                        <p class="text-white text-opacity-70 mb-0">
                          Access e-books, journals, and online research materials anytime and anywhere.
                        </p>
                      </div>
                    </div>
                  </li>

                  <li
                    class="nav-item py-4 py-lg-8 border-top border-white border-opacity-10 d-flex align-items-center w-100"
                    role="presentation">
                    <div class="row w-100 align-items-center gx-3">
                      <div class="col-lg-6 col-xxl-5">
                        <button class="nav-link fs-10 fw-bold py-1 px-0 border-0 rounded-0 flex-shrink-0" id="three-tab"
                          data-bs-toggle="tab" data-bs-target="#three" type="button" role="tab" aria-controls="three"
                          aria-selected="false">Research Assistance</button>
                      </div>
                      <div class="col-lg-6 col-xxl-7">
                        <p class="text-white text-opacity-70 mb-0">
                          Get help finding academic sources, references, and reliable research materials.
                        </p>
                      </div>
                    </div>
                  </li>

                  <li
                    class="nav-item py-4 py-lg-8 border-top border-white border-opacity-10 d-flex align-items-center w-100"
                    role="presentation">
                    <div class="row w-100 align-items-center gx-3">
                      <div class="col-lg-6 col-xxl-5">
                        <button class="nav-link fs-10 fw-bold py-1 px-0 border-0 rounded-0 flex-shrink-0" id="four-tab"
                          data-bs-toggle="tab" data-bs-target="#four" type="button" role="tab" aria-controls="four"
                          aria-selected="false">Study & Reading Spaces</button>
                      </div>
                      <div class="col-lg-6 col-xxl-7">
                        <p class="text-white text-opacity-70 mb-0">
                          Comfortable areas for individual study, group discussions, and quiet reading.
                        </p>
                      </div>
                    </div>
                  </li>

                </ul>

                <a href="projects.html" class="btn border border-white border-opacity-25" data-aos="fade-up"
                  data-aos-delay="300" data-aos-duration="1000">
                  <span class="btn-text">Explore Library</span>
                  <iconify-icon icon="lucide:arrow-up-right"
                    class="btn-icon bg-white text-dark round-52 rounded-circle hstack justify-content-center fs-7 shadow-sm"></iconify-icon>
                </a>

              </div>
            </div>

          </div>

        </div>

      </div>
    </div>
  </section>

  <!--  Why choose us Section -->
  <section class="why-choose-us py-5 py-lg-11 py-xl-12">
    <div class="container">
      <div class="row justify-content-between gap-5 gap-xl-0">

        <div class="col-xl-3 col-xxl-3">
          <div class="d-flex flex-column gap-7">

            <div class="d-flex align-items-center gap-7 py-2" data-aos="fade-right" data-aos-delay="100"
              data-aos-duration="1000">
              <span
                class="round-36 flex-shrink-0 text-dark rounded-circle bg-primary hstack justify-content-center fw-medium">04</span>
              <hr class="border-line">
              <span class="badge text-bg-dark">Library</span>
            </div>

            <h2 class="mb-0" data-aos="fade-right" data-aos-delay="200" data-aos-duration="1000">
              Why choose our library
            </h2>

            <p class="mb-0 fs-5" data-aos="fade-right" data-aos-delay="300" data-aos-duration="1000">
              We provide access to reliable books, digital resources, and research support designed to help students,
              educators, and readers learn and grow.
            </p>

          </div>
        </div>

        <div class="col-xl-9 col-xxl-8">
          <div class="row">

            <!-- Card 1 -->
            <div class="col-lg-4 mb-7 mb-lg-0">
              <div class="card position-relative overflow-hidden bg-primary h-100" data-aos="fade-up"
                data-aos-delay="100" data-aos-duration="1000">

                <div class="card-body d-flex flex-column justify-content-between">

                  <div class="d-flex flex-column gap-3 position-relative z-1">
                    <ul class="list-unstyled mb-0 hstack gap-1">
                      <li><iconify-icon icon="solar:star-bold" class="fs-6 text-dark"></iconify-icon></li>
                      <li><iconify-icon icon="solar:star-bold" class="fs-6 text-dark"></iconify-icon></li>
                      <li><iconify-icon icon="solar:star-bold" class="fs-6 text-dark"></iconify-icon></li>
                      <li><iconify-icon icon="solar:star-bold" class="fs-6 text-dark"></iconify-icon></li>
                      <li><iconify-icon icon="solar:star-line-duotone" class="fs-6 text-dark"></iconify-icon></li>
                    </ul>

                    <p class="mb-0 fs-6 text-dark">
                      The library system provides easy access to thousands of books and digital materials.
                    </p>
                  </div>

                  <div class="position-relative z-1">
                    <div class="pb-6 border-bottom">
                      <h2 class="mb-0">98.6%</h2>
                      <p class="mb-0">User satisfaction rate</p>
                    </div>

                    <div class="hstack gap-6 pt-6">
                      <img src="../assets/images/profile/avatar-1.png"
                        class="img-fluid rounded-circle overflow-hidden flex-shrink-0" width="64" height="64" alt="">
                      <div>
                        <h5 class="mb-0">Library Member</h5>
                        <p class="mb-0">Student Feedback</p>
                      </div>
                    </div>
                  </div>

                </div>

              </div>
            </div>

            <!-- Card 2 -->
            <div class="col-lg-4 mb-7 mb-lg-0">
              <div class="d-flex flex-column gap-7" data-aos="fade-up" data-aos-delay="200" data-aos-duration="1000">

                <div class="position-relative">
                  <img src="../assets/images/services/services-img-2.jpg" alt="" class="img-fluid w-100">
                </div>

                <div class="card bg-dark">
                  <div class="card-body d-flex flex-column gap-7">

                    <div>
                      <h2 class="mb-0 text-white">500+</h2>
                      <p class="mb-0 text-white text-opacity-70">Library resources accessed daily</p>
                    </div>

                    <ul class="d-flex align-items-center mb-0">
                      <li><img src="../assets/images/profile/user-1.jpg" width="44" height="44"
                          class="rounded-circle border border-2 border-dark" alt=""></li>
                      <li class="ms-n2"><img src="../assets/images/profile/user-2.jpg" width="44" height="44"
                          class="rounded-circle border border-2 border-dark" alt=""></li>
                      <li class="ms-n2"><img src="../assets/images/profile/user-3.jpg" width="44" height="44"
                          class="rounded-circle border border-2 border-dark" alt=""></li>
                      <li class="ms-n2"><img src="../assets/images/profile/user-4.jpg" width="44" height="44"
                          class="rounded-circle border border-2 border-dark" alt=""></li>
                    </ul>

                  </div>
                </div>

              </div>
            </div>

            <!-- Card 3 -->
            <div class="col-lg-4 mb-7 mb-lg-0">
              <div class="card border h-100 position-relative overflow-hidden" data-aos="fade-up" data-aos-delay="300"
                data-aos-duration="1000">

                <span
                  class="border rounded-circle round-490 d-block position-absolute top-0 start-50 translate-middle"></span>

                <div class="card-body d-flex flex-column justify-content-between">

                  <div>
                    <h2 class="mb-0">10,000+</h2>
                    <p class="mb-0 text-dark">Books and digital records available</p>
                  </div>

                  <div class="d-flex flex-column gap-3">
                    <h5 class="mb-0">Digital Library System</h5>
                    <p class="mb-0 fs-5 text-dark">
                      Our library provides modern access to learning materials anytime, anywhere.
                    </p>
                  </div>

                </div>

                <span
                  class="border rounded-circle round-490 d-block position-absolute top-100 start-50 translate-middle"></span>

              </div>
            </div>

          </div>
        </div>

      </div>
    </div>
  </section>

  <!--  Testimonial Section -->
  <section class="testimonial py-5 py-lg-11 py-xl-12 bg-light-gray">
    <div class="container">

      <div class="d-flex flex-column gap-5 gap-xl-11">

        <div class="row gap-7 gap-xl-0">

          <div class="col-xl-4 col-xxl-4">
            <div class="d-flex align-items-center gap-7 py-2" data-aos="fade-right" data-aos-delay="100"
              data-aos-duration="1000">
              <span
                class="round-36 flex-shrink-0 text-dark rounded-circle bg-primary hstack justify-content-center fw-medium">05</span>
              <hr class="border-line bg-white">
              <span class="badge text-bg-dark">Library Testimonials</span>
            </div>
          </div>

          <div class="col-xl-8 col-xxl-7">
            <div class="row">
              <div class="col-xxl-8">
                <div class="d-flex flex-column gap-6" data-aos="fade-up" data-aos-delay="100" data-aos-duration="1000">
                  <h2 class="mb-0">What readers say</h2>
                  <p class="fs-5 mb-0 text-opacity-70">
                    Feedback from students and users who benefited from library services and resources.
                  </p>
                </div>
              </div>
            </div>
          </div>

        </div>

        <div class="row gap-7 gap-lg-0">

          <!-- Testimonial 1 -->
          <div class="col-lg-4 col-xl-3 d-flex align-items-stretch">
            <div class="card bg-primary w-100" data-aos="fade-up" data-aos-delay="100" data-aos-duration="1000">
              <div class="card-body d-flex flex-column gap-5 justify-content-between">

                <div>
                  <p class="mb-0">Library Experience</p>
                  <h4 class="mb-0">The library helped me find all my research materials easily.</h4>
                </div>

                <div class="hstack gap-3">
                  <img src="../assets/images/testimonial/testimonial-1.jpg"
                    class="img-fluid rounded-circle overflow-hidden flex-shrink-0" width="60" height="60" alt="">
                  <div>
                    <h5 class="mb-1 fw-normal">Student User</h5>
                    <p class="mb-0">Research Student</p>
                  </div>
                </div>

              </div>
            </div>
          </div>

          <!-- Testimonial 2 -->
          <div class="col-lg-4 col-xl-6 d-flex align-items-stretch">
            <div class="card bg-dark w-100" data-aos="fade-up" data-aos-delay="200" data-aos-duration="1000">

              <div class="card-body d-flex flex-column gap-5 justify-content-between">

                <div>
                  <p class="mb-0 text-white text-opacity-70">Library Experience</p>
                  <h4 class="mb-0 text-white">
                    Access to digital books and journals made studying much easier.
                  </h4>
                </div>

                <div class="hstack gap-2">
                  <h6 class="mb-0 text-white fw-medium">4.8 Rating</h6>
                </div>

                <div class="hstack gap-3">
                  <img src="../assets/images/testimonial/testimonial-2.jpg"
                    class="img-fluid rounded-circle overflow-hidden flex-shrink-0" width="60" height="60" alt="">
                  <div>
                    <h5 class="mb-1 fw-normal text-white">Library User</h5>
                    <p class="mb-0 text-white text-opacity-70">E-Learning Student</p>
                  </div>
                </div>

              </div>

            </div>
          </div>

          <!-- Testimonial 3 -->
          <div class="col-lg-4 col-xl-3 d-flex align-items-stretch">
            <div class="card w-100" data-aos="fade-up" data-aos-delay="300" data-aos-duration="1000">

              <div class="card-body d-flex flex-column gap-5 justify-content-between">

                <div>
                  <p class="mb-0">Library Experience</p>
                  <h4 class="mb-0">Great place for studying and accessing resources.</h4>
                </div>

                <div class="hstack gap-3">
                  <img src="../assets/images/testimonial/testimonial-3.jpg"
                    class="img-fluid rounded-circle overflow-hidden flex-shrink-0" width="60" height="60" alt="">
                  <div>
                    <h5 class="mb-1 fw-normal">Reader</h5>
                    <p class="mb-0">Library Member</p>
                  </div>
                </div>

              </div>

            </div>
          </div>

        </div>

      </div>

    </div>
  </section>

  <!--  Meet our Library Team Section -->
  <section class="meet-our-team py-5 py-lg-11 py-xl-12">
    <div class="container">

      <div class="d-flex flex-column gap-5 gap-xl-11">

        <div class="row gap-7 gap-xl-0">

          <div class="col-xl-4 col-xxl-4">
            <div class="d-flex align-items-center gap-7 py-2" data-aos="fade-right" data-aos-delay="100"
              data-aos-duration="1000">
              <span
                class="round-36 flex-shrink-0 text-dark rounded-circle bg-primary hstack justify-content-center fw-medium">06</span>
              <hr class="border-line bg-white">
              <span class="badge text-bg-dark">Library Team</span>
            </div>

            <h2 class="mb-0">Meet our librarians</h2>

            <p class="fs-5 mb-0 text-opacity-70">
              Our dedicated library staff ensures smooth access to books, digital resources, and academic support for
              all users.
            </p>
          </div>

          <div class="col-xl-8 col-xxl-7">
            <div class="row">

              <!-- Member 1 -->
              <div class="col-md-6 col-xl-3 mb-7 mb-xl-0">
                <div class="meet-team d-flex flex-column gap-4" data-aos="fade-up" data-aos-delay="100"
                  data-aos-duration="1000">

                  <div class="meet-team-img position-relative overflow-hidden">
                    <img src="../assets/images/team/team-img-1.jpg" alt="" class="img-fluid w-100">
                  </div>

                  <div class="meet-team-details">
                    <h4 class="mb-0">Library Director</h4>
                    <p class="mb-0">Administration</p>
                  </div>

                </div>
              </div>

              <!-- Member 2 -->
              <div class="col-md-6 col-xl-3 mb-7 mb-xl-0">
                <div class="meet-team d-flex flex-column gap-4" data-aos="fade-up" data-aos-delay="200"
                  data-aos-duration="1000">

                  <div class="meet-team-img position-relative overflow-hidden">
                    <img src="../assets/images/team/team-img-2.jpg" alt="" class="img-fluid w-100">
                  </div>

                  <div class="meet-team-details">
                    <h4 class="mb-0">Research Librarian</h4>
                    <p class="mb-0">Academic Support</p>
                  </div>

                </div>
              </div>

              <!-- Member 3 -->
              <div class="col-md-6 col-xl-3 mb-7 mb-xl-0">
                <div class="meet-team d-flex flex-column gap-4" data-aos="fade-up" data-aos-delay="300"
                  data-aos-duration="1000">

                  <div class="meet-team-img position-relative overflow-hidden">
                    <img src="../assets/images/team/team-img-3.jpg" alt="" class="img-fluid w-100">
                  </div>

                  <div class="meet-team-details">
                    <h4 class="mb-0">Digital Resources Officer</h4>
                    <p class="mb-0">E-Library</p>
                  </div>

                </div>
              </div>

              <!-- Member 4 -->
              <div class="col-md-6 col-xl-3 mb-7 mb-xl-0">
                <div class="meet-team d-flex flex-column gap-4" data-aos="fade-up" data-aos-delay="400"
                  data-aos-duration="1000">

                  <div class="meet-team-img position-relative overflow-hidden">
                    <img src="../assets/images/team/team-img-4.jpg" alt="" class="img-fluid w-100">
                  </div>

                  <div class="meet-team-details">
                    <h4 class="mb-0">Student Assistant</h4>
                    <p class="mb-0">Front Desk</p>
                  </div>

                </div>
              </div>

            </div>
          </div>

        </div>

      </div>

    </div>
  </section>

  <!--  Library Pricing Section -->
  <section class="pricing-section py-5 py-lg-11 py-xl-12 bg-light-gray">
    <div class="container">

      <div class="d-flex flex-column gap-5 gap-xl-10">

        <div class="d-flex flex-column gap-5 gap-xl-11">

          <div class="row gap-7 gap-xl-0">

            <div class="col-xl-4 col-xxl-4">
              <div class="d-flex align-items-center gap-7 py-2" data-aos="fade-right" data-aos-delay="100"
                data-aos-duration="1000">
                <span
                  class="round-36 flex-shrink-0 text-dark rounded-circle bg-primary hstack justify-content-center fw-medium">07</span>
                <hr class="border-line bg-white">
                <span class="badge text-bg-dark">Library Access Plans</span>
              </div>
            </div>

            <div class="col-xl-8 col-xxl-7">
              <div class="row">
                <div class="col-xxl-8">
                  <div class="d-flex flex-column gap-6" data-aos="fade-up" data-aos-delay="100"
                    data-aos-duration="1000">
                    <h2 class="mb-0">Library membership plans</h2>
                    <p class="fs-5 mb-0 text-opacity-70">
                      Choose the right plan to access books, digital archives, and research materials.
                    </p>
                  </div>
                </div>
              </div>
            </div>

          </div>

          <div class="row">

            <!-- Plan 1 -->
            <div class="col-lg-6 col-xl-4 mb-7 mb-xl-0 d-flex align-items-stretch">
              <div class="card w-100" data-aos="fade-up" data-aos-delay="100">

                <div class="card-body p-7 d-flex flex-column gap-8">

                  <div>
                    <h5 class="mb-0">Basic Access</h5>
                    <h3 class="mb-0">$5</h3>
                    <p class="mb-0">Ideal for casual readers and students.</p>
                  </div>

                  <div class="pt-8 border-top">
                    <h6 class="mb-3">Includes:</h6>
                    <ul class="list-unstyled d-flex flex-column gap-3 mb-0">
                      <li>✔ Borrow physical books</li>
                      <li>✔ Library reading room access</li>
                      <li>✔ Limited digital access</li>
                    </ul>
                  </div>

                  <a href="" class="btn w-100 justify-content-center">Join now</a>

                </div>

              </div>
            </div>

            <!-- Plan 2 -->
            <div class="col-lg-6 col-xl-4 mb-7 mb-xl-0 d-flex align-items-stretch">
              <div class="card w-100" data-aos="fade-up" data-aos-delay="200">

                <div class="card-body p-7 d-flex flex-column gap-8">

                  <div>
                    <h5 class="mb-0">Premium Access</h5>
                    <h3 class="mb-0">$10</h3>
                    <p class="mb-0">Best for students and researchers.</p>
                  </div>

                  <div class="pt-8 border-top">
                    <h6 class="mb-3">Includes:</h6>
                    <ul class="list-unstyled d-flex flex-column gap-3 mb-0">
                      <li>✔ All Basic features</li>
                      <li>✔ Full e-library access</li>
                      <li>✔ Research assistance</li>
                    </ul>
                  </div>

                  <a href="" class="btn w-100 justify-content-center">Join now</a>

                </div>

              </div>
            </div>

            <!-- Plan 3 -->
            <div class="col-lg-6 col-xl-4 mb-7 mb-xl-0 d-flex align-items-stretch">
              <div class="card w-100" data-aos="fade-up" data-aos-delay="300">

                <div class="card-body p-7 d-flex flex-column gap-8">

                  <div>
                    <h5 class="mb-0">Institution Plan</h5>
                    <h3 class="mb-0">Custom</h3>
                    <p class="mb-0">For schools and organizations.</p>
                  </div>

                  <div class="pt-8 border-top">
                    <h6 class="mb-3">Includes:</h6>
                    <ul class="list-unstyled d-flex flex-column gap-3 mb-0">
                      <li>✔ Multi-user access</li>
                      <li>✔ Full archive access</li>
                      <li>✔ Priority support</li>
                    </ul>
                  </div>

                  <a href="" class="btn w-100 justify-content-center">Contact us</a>

                </div>

              </div>
            </div>

          </div>

        </div>

      </div>

    </div>
  </section>

  <!--  FAQ Section -->
  <section class="faq py-5 py-lg-11 py-xl-12">
    <div class="container">
      <div class="d-flex flex-column gap-5 gap-xl-11">
        <div class="row gap-7 gap-xl-0">
          <div class="col-xl-4 col-xxl-4">
            <div class="d-flex align-items-center gap-7 py-2" data-aos="fade-right" data-aos-delay="100"
              data-aos-duration="1000">
              <span
                class="round-36 flex-shrink-0 text-dark rounded-circle bg-primary hstack justify-content-center fw-medium">08</span>
              <hr class="border-line bg-white">
              <span class="badge text-bg-dark">Library</span>
            </div>
          </div>
          <div class="col-xl-8 col-xxl-7">
            <div class="row">
              <div class="col-xxl-9">
                <div class="d-flex flex-column gap-6" data-aos="fade-up" data-aos-delay="100" data-aos-duration="1000">
                  <h2 class="mb-0">Frequently asked questions</h2>
                  <p class="fs-5 mb-0 text-opacity-70">Discover how we organize our library services to meet unique
                    needs,
                    delivering accessible resources, personalized assistance, and excellent user experiences.</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="row justify-content-end">
          <div class="col-xl-8">
            <div class="accordion accordion-flush" id="accordionFlushExample" data-aos="fade-up" data-aos-delay="200"
              data-aos-duration="1000">

              <div class="accordion-item">
                <h2 class="accordion-header">
                  <button class="accordion-button collapsed fs-8 fw-bold" type="button" data-bs-toggle="collapse"
                    data-bs-target="#flush-collapseOne" aria-expanded="false" aria-controls="flush-collapseOne">
                    What services does the library offer?
                  </button>
                </h2>
                <div id="flush-collapseOne" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample">
                  <div class="accordion-body pt-0 fs-5 text-dark">
                    Yes, we provide library support after registration and offer assistance for research, reading
                    materials,
                    and digital catalog access.
                  </div>
                </div>
              </div>

              <div class="accordion-item">
                <h2 class="accordion-header">
                  <button class="accordion-button collapsed fs-8 fw-bold" type="button" data-bs-toggle="collapse"
                    data-bs-target="#flush-collapseTwo" aria-expanded="false" aria-controls="flush-collapseTwo">
                    How long can I borrow books or resources?
                  </button>
                </h2>
                <div id="flush-collapseTwo" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample">
                  <div class="accordion-body pt-0 fs-5 text-dark">
                    Loan duration depends on the type of material, and we also provide renewal options for members.
                  </div>
                </div>
              </div>

              <div class="accordion-item">
                <div id="flush-collapseThree" class="accordion-collapse collapse"
                  data-bs-parent="#accordionFlushExample">
                  <div class="accordion-body pt-0 fs-5 text-dark">
                    Library access includes both physical and digital collections depending on membership type.
                  </div>
                </div>
              </div>

              <div class="accordion-item">
                <h2 class="accordion-header">
                  <button class="accordion-button collapsed fs-8 fw-bold" type="button" data-bs-toggle="collapse"
                    data-bs-target="#flush-collapseFour" aria-expanded="false" aria-controls="flush-collapseFour">
                    Is there any membership fee for library access?
                  </button>
                </h2>
                <div id="flush-collapseFour" class="accordion-collapse collapse"
                  data-bs-parent="#accordionFlushExample">
                  <div class="accordion-body pt-0 fs-5 text-dark">
                    Membership plans vary depending on access level, including student and community options.
                  </div>
                </div>
              </div>

              <div class="accordion-item border-bottom">
                <h2 class="accordion-header">
                  <button class="accordion-button collapsed fs-8 fw-bold" type="button" data-bs-toggle="collapse"
                    data-bs-target="#flush-collapseFive" aria-expanded="false" aria-controls="flush-collapseFive">
                    Do you provide support for research and study?
                  </button>
                </h2>
                <div id="flush-collapseFive" class="accordion-collapse collapse"
                  data-bs-parent="#accordionFlushExample">
                  <div class="accordion-body pt-0 fs-5 text-dark">
                    Yes, we assist users with research guidance, study materials, and library database navigation.
                  </div>
                </div>
              </div>

            </div>
          </div>
        </div>

      </div>
    </div>
  </section>

  <!--  Recent news Section -->
  <section class="Recent-news bg-light-gray py-5 py-lg-11 py-xl-12">
    <div class="container">
      <div class="d-flex flex-column gap-5 gap-xl-11">
        <div class="row gap-7 gap-xl-0">
          <div class="col-xl-4 col-xxl-4">
            <div class="d-flex align-items-center gap-7 py-2" data-aos="fade-right" data-aos-delay="100"
              data-aos-duration="1000">
              <span
                class="round-36 flex-shrink-0 text-dark rounded-circle bg-primary hstack justify-content-center fw-medium">09</span>
              <hr class="border-line bg-white">
              <span class="badge text-bg-dark">Library Updates</span>
            </div>
          </div>

          <div class="col-xl-8 col-xxl-7">
            <div class="row">
              <div class="col-xxl-8">
                <div class="d-flex flex-column gap-6" data-aos="fade-up" data-aos-delay="100" data-aos-duration="1000">
                  <h2 class="mb-0">Library news</h2>
                  <p class="fs-5 mb-0 text-opacity-70">
                    Explore the latest updates, reading programs, and library initiatives that enhance learning,
                    knowledge sharing, and community engagement.
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-xl-6 mb-7 mb-xl-0">
            <div class="resources d-flex flex-column gap-6" data-aos="fade-up" data-aos-delay="100"
              data-aos-duration="1000">
              <a href="blog-detail.html"
                class="resources-img resources-img-first position-relative overflow-hidden d-block">
                <img src="../assets/images/resources/resources-1.jpg" alt="resources" class="img-fluid">
              </a>
              <div class="resources-details">
                <p class="mb-0">Dec 24, 2025</p>
                <h4 class="mb-0">A community reading initiative connects</h4>
              </div>
            </div>
          </div>

          <div class="col-md-6 col-xl-3 mb-7 mb-xl-0">
            <div class="resources d-flex flex-column gap-6" data-aos="fade-up" data-aos-delay="200"
              data-aos-duration="1000">
              <a href="blog-detail.html" class="resources-img position-relative overflow-hidden d-block">
                <img src="../assets/images/resources/resources-2.jpg" alt="resources" class="img-fluid">
              </a>
              <div class="resources-details">
                <p class="mb-0">Dec 24, 2025</p>
                <h4 class="mb-0">Breaking boundaries in our latest library system redesign</h4>
              </div>
            </div>
          </div>

          <div class="col-md-6 col-xl-3 mb-7 mb-xl-0">
            <div class="resources d-flex flex-column gap-6" data-aos="fade-up" data-aos-delay="300"
              data-aos-duration="1000">
              <a href="blog-detail.html" class="resources-img position-relative overflow-hidden d-block">
                <img src="../assets/images/resources/resources-3.jpg" alt="resources" class="img-fluid">
              </a>
              <div class="resources-details">
                <p class="mb-0">Dec 24, 2025</p>
                <h4 class="mb-0">Recognized for library innovation</h4>
              </div>
            </div>
          </div>

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
                class="round-36 flex-shrink-0 text-dark rounded-circle bg-primary hstack justify-content-center fw-medium">10</span>
              <hr class="border-line bg-white">
              <span class="badge text-bg-dark">Contact us</span>
            </div>
          </div>
          <div class="col-xl-8 col-xxl-7">
            <div class="row">
              <div class="col-xxl-8">
                <div class="d-flex flex-column gap-6" data-aos="fade-up" data-aos-delay="100" data-aos-duration="1000">
                  <h2 class="mb-0">Get in touch</h2>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="row justify-content-between gap-7 gap-xl-0">
          <div class="col-xl-3">
            <p class="mb-0 fs-5" data-aos="fade-right" data-aos-delay="100" data-aos-duration="1000">
              Let’s collaborate and build better library experiences together. Tell us your inquiry—we’re here to help.
            </p>
          </div>

          <div class="col-xl-8">
            <form class="d-flex flex-column gap-7" data-aos="fade-up" data-aos-delay="200" data-aos-duration="1000">
              <div>
                <input type="text" class="form-control border-bottom border-dark" placeholder="Name">
              </div>
              <div>
                <input type="email" class="form-control border-bottom border-dark" placeholder="Email">
              </div>
              <div>
                <textarea class="form-control border-bottom border-dark" placeholder="Tell us your inquiry"
                  rows="3"></textarea>
              </div>
              <button type="submit" class="btn w-100 justify-content-center">
                <span class="btn-text">Submit message</span>
                <iconify-icon icon="lucide:arrow-up-right"
                  class="btn-icon bg-white text-dark round-52 rounded-circle hstack justify-content-center fs-7 shadow-sm"></iconify-icon>
              </button>
            </form>
          </div>
        </div>

      </div>
    </div>
  </section>

  <?php
  include('./includes/footer.php');
  ?>  