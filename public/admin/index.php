<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ACCESS CONTROL
include('../../app/middleware/admin.php');

// DATABASE CONNECTION
$configPath = $_SERVER['DOCUMENT_ROOT'] . '/kmkdt-Library/app/config/config.php';
if (!file_exists($configPath)) {
    die("Config file not found at: " . $configPath);
}
include_once($configPath);

$currentDate = date('Y-m-d');

// INITIALIZE COUNTERS
$borrowedCount = 0;
$overdueCount  = 0;
$catalogCount   = 0;
$totalRevenue = 0.00;
$totalMembers = 0;

// GET LOAN CODES AND OVERDUE CODES
$countsQuery = $conn->query("
    SELECT 
        SUM(CASE WHEN status = 'borrowed' AND due_date >= '$currentDate' THEN 1 ELSE 0 END) as borrowed,
        SUM(CASE WHEN status = 'overdue' OR (status = 'borrowed' AND due_date < '$currentDate') THEN 1 ELSE 0 END) as overdue
    FROM borrowing_history
");
if ($countsQuery) {
    $row = $countsQuery->fetch_assoc();
    $borrowedCount = (int)($row['borrowed'] ?? 0);
    $overdueCount  = (int)($row['overdue'] ?? 0);
}

// GET TOTAL BOOKS IN LIBRARY
$catalogQuery = $conn->query("SELECT COUNT(*) as total FROM books");
if ($catalogQuery) {
    $catalogCount = (int)$catalogQuery->fetch_assoc()['total'];
}

// GET TOTAL FINES COLLECTED
$revenueQuery = $conn->query("SELECT SUM(amount_paid) as total_fines FROM penalty_payments");
if ($revenueQuery) {
    $totalRevenue = (float)($revenueQuery->fetch_assoc()['total_fines'] ?? 0.00);
}

// GET TOTAL MEMBERS REGISTERED 
$memberQuery = $conn->query("SELECT COUNT(*) as total FROM user");
if ($memberQuery) {
    $totalMembers = (int)$memberQuery->fetch_assoc()['total'];
}

// LOCAL FUNCTION TO GET RECENT ACTIVITY LOGS
function getDashboardRecentActivity($conn) {
    $sql = "SELECT h.*, u.fullName, b.title 
            FROM borrowing_history h
            JOIN user u ON h.user_id = u.id
            JOIN books b ON h.book_id = b.id
            ORDER BY h.id DESC LIMIT 5";
    $result = mysqli_query($conn, $sql);
    return ($result) ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
}

// RUN THE LOCAL ACTIVITY FUNCTION
$recentActivities = getDashboardRecentActivity($conn);

// INCLUDE PAGE LAYOUTS
include('./includes/header.php');
include('./includes/topbar.php');
include('./includes/sidebar.php');
?>

<div class="pagetitle">
  <h1>Dashboard Overview</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="index.php">Home</a></li>
      <li class="breadcrumb-item active">Dashboard</li>
    </ol>
  </nav>
</div>

<section class="section dashboard">
  
  <div class="row">
    
    <div class="col-xxl-3 col-md-6">
      <div class="card info-card sales-card rounded-0 shadow-sm border-start border-primary border-4">
        <div class="card-body">
          <h5 class="card-title text-muted small text-uppercase">Books Borrowed</h5>
          <div class="d-flex align-items-center">
            <div class="card-icon rounded-circle bg-light-primary text-primary d-flex align-items-center justify-content-center p-3 fs-3 me-3" style="width:50px; height:50px;">
              <i class="bi bi-journal-arrow-up"></i>
            </div>
            <div>
              <h6 class="fs-3 fw-bold mb-0"><?= $borrowedCount; ?></h6>
              <span class="text-muted small">Circulating volumes</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-xxl-3 col-md-6">
      <div class="card info-card customers-card rounded-0 shadow-sm border-start border-danger border-4">
        <div class="card-body">
          <h5 class="card-title text-muted small text-uppercase">Overdue Breaches</h5>
          <div class="d-flex align-items-center">
            <div class="card-icon rounded-circle bg-light-danger text-danger d-flex align-items-center justify-content-center p-3 fs-3 me-3" style="width:50px; height:50px;">
              <i class="bi bi-exclamation-triangle"></i>
            </div>
            <div>
              <h6 class="fs-3 fw-bold mb-0"><?= $overdueCount; ?></h6>
              <span class="text-danger small fw-bold">Requires attention</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-xxl-3 col-md-6">
      <div class="card info-card revenue-card rounded-0 shadow-sm border-start border-success border-4">
        <div class="card-body">
          <h5 class="card-title text-muted small text-uppercase">Fines Collected</h5>
          <div class="d-flex align-items-center">
            <div class="card-icon rounded-circle bg-light-success text-success d-flex align-items-center justify-content-center p-3 fs-3 me-3" style="width:50px; height:50px;">
              <i class="bi bi-cash-coin"></i>
            </div>
            <div>
              <h6 class="fs-4 fw-bold mb-0">Php <?= number_format($totalRevenue, 2); ?></h6>
              <span class="text-muted small">Total system ledger</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-xxl-3 col-md-6">
      <div class="card info-card rounded-0 shadow-sm border-start border-info border-4">
        <div class="card-body">
          <h5 class="card-title text-muted small text-uppercase">Library Patrons</h5>
          <div class="d-flex align-items-center">
            <div class="card-icon rounded-circle bg-light-info text-info d-flex align-items-center justify-content-center p-3 fs-3 me-3" style="width:50px; height:50px;">
              <i class="bi bi-people"></i>
            </div>
            <div>
              <h6 class="fs-3 fw-bold mb-0"><?= $totalMembers; ?></h6>
              <span class="text-muted small">Registered accounts</span>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>

  <div class="row mt-3">
    
    <div class="col-lg-7">
      <div class="card rounded-0 shadow-sm border-0">
        <div class="card-body pt-3">
          <h5 class="card-title">Management Engine</h5>
          <p class="text-muted small">Quick navigation shortcuts directly connecting your dashboard modules.</p>
          
          <div class="row g-3">
            <div class="col-sm-6">
              <a href="reports" class="btn btn-outline-primary d-flex align-items-center p-3 rounded-0 text-start w-100 h-100">
                <i class="bi bi-graph-up fs-3 me-3"></i>
                <div>
                  <div class="fw-bold">View Reports</div>
                  <small class="text-muted">Analyze Borrows and violations</small>
                </div>
              </a>
            </div>
            <div class="col-sm-6">
              <a href="circulation" class="btn btn-outline-warning d-flex align-items-center p-3 rounded-0 text-start w-100 h-100 text-dark">
                <i class="bi bi-arrow-repeat fs-3 me-3 text-warning"></i>
                <div>
                  <div class="fw-bold text-dark">Circulation Monitor</div>
                  <small class="text-muted">Live update trackers & ratios</small>
                </div>
              </a>
            </div>
            <div class="col-sm-6">
              <a href="penalty_payments" class="btn btn-outline-success d-flex align-items-center p-3 rounded-0 text-start w-100 h-100">
                <i class="bi bi-receipt fs-3 me-3"></i>
                <div>
                  <div class="fw-bold">Audit Penalty Logs</div>
                  <small class="text-muted">Track library invoice collections</small>
                </div>
              </a>
            </div>
            <div class="col-sm-6">
              <a href="members" class="btn btn-outline-secondary d-flex align-items-center p-3 rounded-0 text-start w-100 h-100">
                <i class="bi bi-person-lines-fill fs-3 me-3"></i>
                <div>
                  <div class="fw-bold">Manage Members</div>
                  <small class="text-muted">Review library cards registry</small>
                </div>
              </a>
            </div>
          </div>

          <div class="bg-light p-3 mt-4 border border-start border-3 border-info">
             <small class="text-muted d-block"><i class="bi bi-info-circle me-1 text-info"></i> <strong>Catalog Total:</strong> Total registered titles currently in inventory storage: <strong><?= $catalogCount; ?></strong></small>
          </div>

        </div>
      </div>
    </div>

    <div class="col-lg-5">
      <div class="card rounded-0 shadow-sm border-0 h-100">
        <div class="card-body pt-3">
          <h5 class="card-title">Live Transactions</h5>
          <p class="text-muted small">Latest user activities updated in real-time from system logs.</p>
          
          <div class="activity mt-3">
            <?php if (!empty($recentActivities)): ?>
              <?php foreach ($recentActivities as $act): 
                  $status = strtolower($act['status']);
                  $badgeColor = ($status === 'returned') ? 'bg-success' : (($status === 'overdue') ? 'bg-danger' : 'bg-warning text-dark');
              ?>
                <div class="activity-item d-flex align-items-start mb-3 border-bottom pb-2">
                  <span class="badge <?= $badgeColor; ?> me-3 px-2 py-1 rounded-0 text-uppercase" style="font-size:0.65rem; width: 75px; text-align: center;">
                    <?= $status; ?>
                  </span>
                  <div class="w-100">
                    <div class="small fw-semibold text-dark"><?= htmlspecialchars($act['fullName']); ?></div>
                    <div class="text-muted small text-truncate" style="max-width: 200px;">
                      Book: "<?= htmlspecialchars($act['title']); ?>"
                    </div>
                    <small class="text-muted d-block" style="font-size:0.75rem;">
                      <i class="bi bi-clock me-1"></i><?= date('M d | g:i A', strtotime($act['borrowed_at'])); ?>
                    </small>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <div class="text-center py-5 text-muted">
                <i class="bi bi-cloud-slash d-block fs-2 opacity-50 mb-2"></i>
                No historical checkout transactions processed yet.
              </div>
            <?php endif; ?>
          </div>

        </div>
      </div>
    </div>

  </div>

</section>

<?php
// FOOTER LAYOUT
include('./includes/footer.php');
?>