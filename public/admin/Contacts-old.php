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
          <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item active">Contacts</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->
 <div class="pagetitle">

<section class="section contact">
  <div class="row">

    <!-- LEFT SIDE -->
    <div class="col-lg-5">
      <div class="row g-3">

        <div class="col-12">
          <div class="info-box card p-3 d-flex flex-row align-items-center">
            <i class="bi bi-geo-alt fs-3 me-3 text-primary"></i>
            <div>
              <h6 class="mb-1">Library Address</h6>
              <p class="mb-0 small">University Library Building,<br>Campus Main Road</p>
            </div>
          </div>
        </div>

        <div class="col-12">
          <div class="info-box card p-3 d-flex flex-row align-items-center">
            <i class="bi bi-telephone fs-3 me-3 text-success"></i>
            <div>
              <h6 class="mb-1">Contact Number</h6>
              <p class="mb-0 small">+63 912 345 6789<br>+63 998 765 4321</p>
            </div>
          </div>
        </div>

        <div class="col-12">
          <div class="info-box card p-3 d-flex flex-row align-items-center">
            <i class="bi bi-envelope fs-3 me-3 text-danger"></i>
            <div>
              <h6 class="mb-1">Library Email</h6>
              <p class="mb-0 small">library@school.edu<br>support@library.edu</p>
            </div>
          </div>
        </div>

        <div class="col-12">
          <div class="info-box card p-3 d-flex flex-row align-items-center">
            <i class="bi bi-clock fs-3 me-3 text-warning"></i>
            <div>
              <h6 class="mb-1">Library Hours</h6>
              <p class="mb-0 small">Monday - Saturday<br>8:00AM - 6:00PM</p>
            </div>
          </div>
        </div>

      </div>
    </div>

    <!-- RIGHT SIDE -->
    <div class="col-lg-7">
      <div class="card p-4 h-100">
        <h5 class="card-title">User Inquiries</h5>

        <div class="overflow-auto" style="max-height: 500px;">

          <div class="border rounded p-3 mb-3">
            <h6 class="mb-1"><strong>Lewis Hamilton</strong></h6>
            <small class="text-muted">lewis@gmail.com</small>
            <p class="mt-2 mb-1"><strong>Subject:</strong> Borrowing Books</p>
            <p class="mb-1 small">Hi, I would like to ask how many books I can borrow at once?</p>
            <span class="badge bg-warning text-dark">New</span>
          </div>

          <div class="border rounded p-3 mb-3">
            <h6 class="mb-1"><strong>Roronoa Zoro</strong></h6>
            <small class="text-muted">zoro@gmail.com</small>
            <p class="mt-2 mb-1"><strong>Subject:</strong> Overdue Fine</p>
            <p class="mb-1 small">I returned my book late. How can I pay the fine?</p>
            <span class="badge bg-primary">Read</span>
          </div>

          <div class="border rounded p-3 mb-3">
            <h6 class="mb-1"><strong>Carlos Sainz</strong></h6>
            <small class="text-muted">carlos@gmail.com</small>
            <p class="mt-2 mb-1"><strong>Subject:</strong> Lost Book</p>
            <p class="mb-1 small">I lost a borrowed book. What should I do?</p>
            <span class="badge bg-success">Replied</span>
          </div>

        </div>
      </div>
    </div>

  </div>
</section>

<?php
include('./includes/footer.php');
?>