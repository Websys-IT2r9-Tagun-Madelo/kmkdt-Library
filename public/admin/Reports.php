<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include('../../app/middleware/admin.php');

// 1. Get database configuration
$configPath = $_SERVER['DOCUMENT_ROOT'] . '/kmkdt-Library/app/config/config.php';
if (!file_exists($configPath)) {
    die("Config file not found at: " . $configPath);
}
include_once($configPath);

// 2. Count rows in the database for each report category
$borrowedQuery = $conn->query("SELECT COUNT(*) as total FROM borrowing_history WHERE status = 'borrowed'");
$borrowedCount = $borrowedQuery ? $borrowedQuery->fetch_assoc()['total'] : 0;

$returnedQuery = $conn->query("SELECT COUNT(*) as total FROM borrowing_history WHERE status = 'returned'");
$returnedCount = $returnedQuery ? $returnedQuery->fetch_assoc()['total'] : 0;

$overdueQuery = $conn->query("SELECT COUNT(*) as total FROM borrowing_history WHERE status = 'overdue'");
$overdueCount = $overdueQuery ? $overdueQuery->fetch_assoc()['total'] : 0;

$catalogQuery = $conn->query("SELECT COUNT(*) as total FROM books");
$catalogCount = $catalogQuery ? $catalogQuery->fetch_assoc()['total'] : 0;

include('./includes/header.php');
include('./includes/topbar.php');
include('./includes/sidebar.php');
?>

<div class="pagetitle">
  <h1>Reports</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="index">Home</a></li>
      <li class="breadcrumb-item active">Reports</li>
    </ol>
  </nav>
</div>

<section class="section">
  <div class="row">
    <div class="col-lg-12">

      <div class="card rounded-0">
        <div class="card-body">
          <h5 class="card-title">Detailed Reports</h5>

          <!-- 1. Borrowed Books -->
          <div class="alert alert-primary alert-dismissible fade show rounded-0" role="alert">
            <h4 class="alert-heading d-flex justify-content-between align-middle">
              <span>Borrowed Books Report</span>
              <span class="badge bg-light text-primary fs-5 px-3"><?php echo $borrowedCount; ?> Borrowed</span>
            </h4>
            <p>List of all books currently out on loan, showing borrower names and due dates.</p>
            <hr>
            <p class="mb-0">Please monitor these to make sure books are returned on time.</p>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>

          <!-- 2. Returned Books -->
          <div class="alert alert-success alert-dismissible fade show rounded-0" role="alert">
            <h4 class="alert-heading d-flex justify-content-between align-middle">
              <span>Returned Books Report</span>
              <span class="badge bg-light text-success fs-5 px-3"><?php echo $returnedCount; ?> Returned</span>
            </h4>
            <p>List of all books that have been successfully brought back to the library.</p>
            <hr>
            <p class="mb-0">These records are updated automatically in real-time.</p>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>

          <!-- 3. Overdue Books -->
          <div class="alert alert-danger alert-dismissible fade show rounded-0" role="alert">
            <h4 class="alert-heading d-flex justify-content-between align-middle">
              <span>Overdue Books Report</span>
              <span class="badge bg-light text-danger fs-5 px-3"><?php echo $overdueCount; ?> Overdue</span>
            </h4>
            <p>List of books that were not returned by their original due date.</p>
            <hr>
            <p class="mb-0">Immediate action is needed to collect these items and issue fines.</p>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>

          <!-- 4. Total Books -->
          <div class="alert alert-info alert-dismissible fade show rounded-0" role="alert">
            <h4 class="alert-heading d-flex justify-content-between align-middle">
              <span>Total Books in Library</span>
              <span class="badge bg-light text-info fs-5 px-3"><?php echo $catalogCount; ?> Total</span>
            </h4>
            <p>Overall count of all books and newly added titles stored in the system catalog.</p>
            <hr>
            <p class="mb-0">Shows the total size of your current library collection.</p>
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