<?php
include('../../app/middleware/admin.php');
include('./includes/header.php');
include('./includes/topbar.php');
include('./includes/sidebar.php');
?>

    <div class="pagetitle">
      <h1>Reports</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item active">Reports</li>
        </ol>
      </nav>
    </div>

<section class="section">
  <div class="row">
    <div class="col-lg-12">

      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Detailed Reports</h5>

          <div class="alert alert-primary alert-dismissible fade show" role="alert">
            <h4 class="alert-heading">Borrowed Books Report</h4>
            <p>Overview of all borrowed books, including borrower names and due dates.</p>
            <hr>
            <p class="mb-0">Ensure timely returns to avoid penalties.</p>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>

          <div class="alert alert-success alert-dismissible fade show" role="alert">
            <h4 class="alert-heading">Returned Books Report</h4>
            <p>Summary of all books successfully returned to the library.</p>
            <hr>
            <p class="mb-0">Records are updated in real-time.</p>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>

          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h4 class="alert-heading">Overdue Books Report</h4>
            <p>List of books not returned by the expected due date.</p>
            <hr>
            <p class="mb-0">Immediate action is required for these items.</p>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>

          <div class="alert alert-info alert-dismissible fade show" role="alert">
            <h4 class="alert-heading">Library Updates Report</h4>
            <p>Latest updates, announcements, and newly added books.</p>
            <hr>
            <p class="mb-0">Stay informed with recent changes.</p>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>

        </div>
      </div>

    </div>
  </div>
</section>
    
<?php
include('./includes/footer.php');
?>