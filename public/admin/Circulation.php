<?php
include('../../app/middleware/admin.php');
include('./includes/header.php');
include('./includes/topbar.php');
include('./includes/sidebar.php');
?>
    <div class="pagetitle">
      <h1>Circulation</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item active">Circulation</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

<section class="section">
  <div class="row">
    <div class="col-lg-12">

      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Live Circulation Updates</h5>

          <!-- Progress Bars with Striped Animated Backgrounds-->
          <div class="progress">
            <div class="progress-bar progress-bar-striped progress-bar-animated" style="width: 10%">Incoming Returns</div>
          </div>
          <div class="progress mt-3">
            <div class="progress-bar progress-bar-striped bg-success progress-bar-animated" style="width: 25%">Books Checked In</div>
          </div>
          <div class="progress mt-3">
            <div class="progress-bar progress-bar-striped bg-info progress-bar-animated" style="width: 50%">Books Borrowed</div>
          </div>
          <div class="progress mt-3">
            <div class="progress-bar progress-bar-striped bg-warning progress-bar-animated" style="width: 75%">Pending Transactions</div>
          </div>
          <div class="progress mt-3">
            <div class="progress-bar progress-bar-striped bg-danger progress-bar-animated" style="width: 100%">Overdue Alerts</div>
          </div><!-- End Progress Bars with Striped Animated Backgrounds -->
        </div>
      </div>
    </div>
  </div>

<div class="col-12">
  <div class="card overflow-auto">

    <div class="card-body">
      <h5 class="card-title">Circulation Records</h5>

      <table class="table table-hover align-middle">
        <thead class="table-light">
          <tr>
            <th>#</th>
            <th>Member</th>
            <th>Book Title</th>
            <th>Borrowed Date</th>
            <th>Due Date</th>
            <th>Status</th>
          </tr>
        </thead>

        <tbody>

          <tr>
            <td>#3001</td>
            <td>Zoro</td>
            <td><span class="fw-semibold">One Piece Vol. 1</span></td>
            <td>Apr 20, 2026</td>
            <td>Apr 27, 2026</td>
            <td><span class="badge bg-success">Borrowed</span></td>
          </tr>

          <tr>
            <td>#3002</td>
            <td>Luffy</td>
            <td><span class="fw-semibold">One Piece Vol. 2</span></td>
            <td>Apr 18, 2026</td>
            <td>Apr 25, 2026</td>
            <td><span class="badge bg-warning text-dark">Due Soon</span></td>
          </tr>

          <tr>
            <td>#3003</td>
            <td>Max</td>
            <td><span class="fw-semibold">Formula 1 Racing Guide</span></td>
            <td>Apr 10, 2026</td>
            <td>Apr 20, 2026</td>
            <td><span class="badge bg-danger">Overdue</span></td>
          </tr>

          <tr>
            <td>#3004</td>
            <td>Lewis</td>
            <td><span class="fw-semibold">F1 Strategies & Techniques</span></td>
            <td>Apr 15, 2026</td>
            <td>Apr 22, 2026</td>
            <td><span class="badge bg-primary">Returned</span></td>
          </tr>

          <tr>
            <td>#3005</td>
            <td>Nami</td>
            <td><span class="fw-semibold">One Piece Vol. 3</span></td>
            <td>Apr 21, 2026</td>
            <td>Apr 28, 2026</td>
            <td><span class="badge bg-success">Borrowed</span></td>
          </tr>

        </tbody>
      </table>

    </div>
  </div>
</div>


</section>

<?php
include('./includes/footer.php');
?>