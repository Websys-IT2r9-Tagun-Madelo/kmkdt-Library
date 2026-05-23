<?php
include('../../app/middleware/admin.php');

if (!defined('APP_PATH')) {
    define('APP_PATH', dirname(__DIR__, 2) . '/app');
}

require_once APP_PATH . '/config/config.php';
require_once APP_PATH . '/controller/adminController.php';

$metrics         = getLibraryMetrics($conn);
$borrowedRecords = getActiveBorrows($conn);
$returnedRecords = getHistoricalReturns($conn);
$overdueRecords  = getOverdueViolations($conn);

include('./includes/header.php');
include('./includes/topbar.php');
include('./includes/sidebar.php');
?>

<div class="pagetitle">
  <h1>Reports Engine</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="index">Home</a></li>
      <li class="breadcrumb-item">Analytics & Reporting</li>
      <li class="breadcrumb-item active">Reports</li>
    </ol>
  </nav>
</div>

<section class="section dashboard">
  <div class="row">
    
    <!-- Active Borrows Metric -->
    <div class="col-xxl-3 col-md-6">
      <div class="card info-card sales-card rounded-0 shadow-sm border-start border-primary border-4">
        <div class="card-body">
          <h5 class="card-title text-muted small text-uppercase">On Borrow</h5>
          <div class="d-flex align-items-center">
            <div class="card-icon rounded-circle bg-light-primary text-primary d-flex align-items-center justify-content-center p-3 fs-3 me-3 metric-icon-box">
              <i class="bi bi-journal-arrow-up"></i>
            </div>
            <div>
              <h6 class="fs-3 fw-bold mb-0"><?= $metrics['borrowedCount'] ?></h6>
              <span class="text-muted small">Active Borrow</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Total Returned Metric -->
    <div class="col-xxl-3 col-md-6">
      <div class="card info-card revenue-card rounded-0 shadow-sm border-start border-success border-4">
        <div class="card-body">
          <h5 class="card-title text-muted small text-uppercase">Total Returned</h5>
          <div class="d-flex align-items-center">
            <div class="card-icon rounded-circle bg-light-success text-success d-flex align-items-center justify-content-center p-3 fs-3 me-3 metric-icon-box">
              <i class="bi bi-journal-check"></i>
            </div>
            <div>
              <h6 class="fs-3 fw-bold mb-0"><?= $metrics['returnedCount'] ?></h6>
              <span class="text-muted small">Completed cycles</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Overdue Limit Metric -->
    <div class="col-xxl-3 col-md-6">
      <div class="card info-card customers-card rounded-0 shadow-sm border-start border-danger border-4">
        <div class="card-body">
          <h5 class="card-title text-muted small text-uppercase">Overdue Limit</h5>
          <div class="d-flex align-items-center">
            <div class="card-icon rounded-circle bg-light-danger text-danger d-flex align-items-center justify-content-center p-3 fs-3 me-3 metric-icon-box">
              <i class="bi bi-exclamation-triangle"></i>
            </div>
            <div>
              <h6 class="fs-3 fw-bold mb-0"><?= $metrics['overdueCount'] ?></h6>
              <span class="text-danger small fw-bold">Action required</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Catalog Strength Metric -->
    <div class="col-xxl-3 col-md-6">
      <div class="card info-card rounded-0 shadow-sm border-start border-info border-4">
        <div class="card-body">
          <h5 class="card-title text-muted small text-uppercase">Catalog Strength</h5>
          <div class="d-flex align-items-center">
            <div class="card-icon rounded-circle bg-light-info text-info d-flex align-items-center justify-content-center p-3 fs-3 me-3 metric-icon-box">
              <i class="bi bi-book"></i>
            </div>
            <div>
              <h6 class="fs-3 fw-bold mb-0"><?= $metrics['catalogCount'] ?></h6>
              <span class="text-muted small">Titles registered</span>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>

  <div class="row mt-4">
    <div class="col-12">
      <div class="card rounded-0 shadow-sm border-0">
        <div class="card-body pt-3">
          
          <!-- Tab Navigation Headers -->
          <ul class="nav nav-tabs nav-tabs-buffered d-flex mb-3" id="reportTabs" role="tablist">
            <li class="nav-item flex-fill" role="presentation">
              <button class="nav-link w-100 active rounded-0 text-center fw-bold" id="borrowed-tab" data-bs-toggle="tab" data-bs-target="#tab-borrowed" type="button" role="tab">
                <i class="bi bi-journal-arrow-up me-2"></i>Active Borrows (<?= $metrics['borrowedCount'] ?>)
              </button>
            </li>
            <li class="nav-item flex-fill" role="presentation">
              <button class="nav-link w-100 rounded-0 text-center fw-bold text-success" id="returned-tab" data-bs-toggle="tab" data-bs-target="#tab-returned" type="button" role="tab">
                <i class="bi bi-journal-check me-2"></i>Returned Logs (<?= $metrics['returnedCount'] ?>)
              </button>
            </li>
            <li class="nav-item flex-fill" role="presentation">
              <button class="nav-link w-100 rounded-0 text-center fw-bold text-danger" id="overdue-tab" data-bs-toggle="tab" data-bs-target="#tab-overdue" type="button" role="tab">
                <i class="bi bi-exclamation-triangle me-2"></i>Overdue Violations (<?= $metrics['overdueCount'] ?>)
              </button>
            </li>
          </ul>

          <div class="tab-content pt-2" id="reportTabsContent">
            
            <!-- Tab Pane: Active Borrows -->
            <div class="tab-pane fade show active" id="tab-borrowed" role="tabpanel">
              <h5 class="card-title px-1">Current Active Book Borrows</h5>
              <div class="table-responsive">
                <table class="table table-hover align-middle custom-report-table">
                  <thead class="table-light">
                    <tr>
                      <th>Borrower Name</th>
                      <th>Book Title</th>
                      <th>Issue Date</th>
                      <th>Expected Due Date</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (!empty($borrowedRecords)): ?>
                      <?php foreach ($borrowedRecords as $row): ?>
                        <tr>
                          <td class="fw-bold text-secondary"><?= htmlspecialchars($row['fullName']) ?></td>
                          <td><?= htmlspecialchars($row['title']) ?></td>
                          <td><span class="badge bg-light text-dark border"><?= date('M d, Y', strtotime($row['borrowed_at'])) ?></span></td>
                          <td><span class="badge bg-primary text-white"><?= date('M d, Y', strtotime($row['due_date'])) ?></span></td>
                        </tr>
                      <?php endforeach; ?>
                    <?php else: ?>
                      <tr><td colspan="4" class="text-center py-4 text-muted">No books are currently checked out.</td></tr>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>
            </div>

            <!-- Tab Pane: Returned Logs -->
            <div class="tab-pane fade" id="tab-returned" role="tabpanel">
              <h5 class="card-title text-success px-1">Historical Return Inventory Logs</h5>
              <div class="table-responsive">
                <table class="table table-hover align-middle custom-report-table">
                  <thead class="table-light">
                    <tr>
                      <th>Borrower Name</th>
                      <th>Book Title</th>
                      <th>Issue Date</th>
                      <th>Returned Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (!empty($returnedRecords)): ?>
                      <?php foreach ($returnedRecords as $row): ?>
                        <tr>
                          <td class="fw-bold text-secondary"><?= htmlspecialchars($row['fullName']) ?></td>
                          <td><?= htmlspecialchars($row['title']) ?></td>
                          <td><?= date('M d, Y', strtotime($row['borrowed_at'])) ?></td>
                          <td><span class="badge bg-success-lime text-white"><i class="bi bi-check-circle me-1"></i> Returned Safely</span></td>
                        </tr>
                      <?php endforeach; ?>
                    <?php else: ?>
                      <tr><td colspan="4" class="text-center py-4 text-muted">No return logs discovered in system database.</td></tr>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>
            </div>

            <!-- Tab Pane: Overdue Violations -->
            <div class="tab-pane fade" id="tab-overdue" role="tabpanel">
              <h5 class="card-title text-danger px-1">Critical Overdue Violations Exception Report</h5>
              <div class="table-responsive">
                <table class="table table-hover align-middle custom-report-table">
                  <thead class="table-light">
                    <tr>
                      <th>Borrower Name</th>
                      <th>Book Title</th>
                      <th>Due Date Boundary</th>
                      <th>Delay Period</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (!empty($overdueRecords)): ?>
                      <?php foreach ($overdueRecords as $row): ?>
                        <tr>
                          <td class="fw-bold text-danger"><?= htmlspecialchars($row['fullName']) ?></td>
                          <td><?= htmlspecialchars($row['title']) ?></td>
                          <td><span class="text-danger fw-bold"><?= date('M d, Y', strtotime($row['due_date'])) ?></span></td>
                          <td><span class="badge bg-danger px-2 py-1"><?= $row['days_overdue'] ?> Days Late</span></td>
                        </tr>
                      <?php endforeach; ?>
                    <?php else: ?>
                      <tr><td colspan="4" class="text-center py-4 text-success fw-bold"><i class="bi bi-emoji-smile me-2"></i>Excellent! Zero overdue book items detected.</td></tr>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>
            </div>

          </div>

        </div>
      </div>
    </div>
  </div>
</section>

<?php include('./includes/footer.php'); ?>