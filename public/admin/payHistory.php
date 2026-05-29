<?php
include('../../app/middleware/admin.php');

if (!defined('APP_PATH')) {
    define('APP_PATH', dirname(__DIR__, 2) . '/app');
}

require_once APP_PATH . '/config/config.php';
require_once APP_PATH . '/controller/adminController.php'; 

// --- CONTROLLER ROUTER DISPATCHER ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $loanId = isset($_POST['loan_id']) ? intval($_POST['loan_id']) : 0;
    
    if ($_POST['action'] === 'approve_received_payment') {
        // Call the approval controller process
        $result = approvePenaltyPayment($conn, $loanId);
        $_SESSION['message'] = $result['message'];
        $_SESSION['code'] = $result['code'];
    } 
    elseif ($_POST['action'] === 'reject_received_payment') {
        // Call the new rejection controller process
        $result = rejectPenaltyPayment($conn, $loanId);
        $_SESSION['message'] = $result['message'];
        $_SESSION['code'] = $result['code'];
    }
    
    // Explicit relative destination paths prevent folder duplication errors
    header("Location: /kmkdt-Library/public/admin/payHistory");
    exit();
}

// Fetch historical dataset snapshots via core controllers
$payments = getPenaltyPaymentsHistory($conn);
$pendingFines = getPendingPenaltyPayments($conn);

// Compute real-time dashboard metrics summaries
$totalCollected = 0.00;
$receiptCount = count($payments);
foreach ($payments as $pay) {
    $totalCollected += floatval($pay['amount_paid']);
}
$averageFine = $receiptCount > 0 ? ($totalCollected / $receiptCount) : 0.00;

include('./includes/header.php');
include('./includes/topbar.php');
include('./includes/sidebar.php');
?>

<div class="pagetitle">
  <h1>Penalty Payments History</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="index">Home</a></li>
      <li class="breadcrumb-item">Analytics &amp; Reporting</li>
      <li class="breadcrumb-item active">Payment History</li>
    </ol>
  </nav>
</div>

<section class="section">
  <div class="row mb-3">
    <div class="col-md-4">
      <div class="card rounded-0 border-start border-success border-3 shadow-sm mb-3">
        <div class="card-body py-3">
          <div class="d-flex align-items-center justify-content-between">
            <div>
              <h6 class="text-muted small text-uppercase fw-bold mb-1">Total Revenue Collected</h6>
              <h3 class="fw-bold text-success mb-0">Php <?= number_format($totalCollected, 2); ?></h3>
            </div>
            <div class="bg-light p-3 rounded text-success">
              <i class="bi bi-cash-stack fs-4"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <div class="col-md-4">
      <div class="card rounded-0 border-start border-primary border-3 shadow-sm mb-3">
        <div class="card-body py-3">
          <div class="d-flex align-items-center justify-content-between">
            <div>
              <h6 class="text-muted small text-uppercase fw-bold mb-1">Total Fines Processed</h6>
              <h3 class="fw-bold text-primary mb-0"><?= $receiptCount; ?> Receipts</h3>
            </div>
            <div class="bg-light p-3 rounded text-primary">
              <i class="bi bi-receipt fs-4"></i>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card rounded-0 border-start border-warning border-3 shadow-sm mb-3">
        <div class="card-body py-3">
          <div class="d-flex align-items-center justify-content-between">
            <div>
              <h6 class="text-muted small text-uppercase fw-bold mb-1">Average Penalty Collection</h6>
              <h3 class="fw-bold text-warning mb-0">Php <?= number_format($averageFine, 2); ?></h3>
            </div>
            <div class="bg-light p-3 rounded text-warning">
              <i class="bi bi-calculator fs-4"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-lg-12">

      <?php if (isset($_SESSION['message']) && isset($_SESSION['code'])): ?>
        <div class="alert alert-<?= ($_SESSION['code'] == 'success') ? 'success' : 'danger'; ?> alert-dismissible fade show rounded-0 small mb-4" role="alert">
          <i class="bi <?= ($_SESSION['code'] == 'success') ? 'bi-check-circle' : 'bi-exclamation-triangle'; ?> me-2"></i>
          <?= $_SESSION['message']; ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['message'], $_SESSION['code']); ?>
      <?php endif; ?>

      <div class="card rounded-0 border-0 shadow-sm mb-4">
        <div class="card-body pt-4">
          <h5 class="card-title p-0 mb-4 fw-bold text-dark">Pending Fine Approvals</h5>
          <div class="requests-list">
            <?php if (!empty($pendingFines)): ?>
              <?php foreach ($pendingFines as $fine): ?>
                <div class="request-item d-flex align-items-start justify-content-between p-3 mb-3 border rounded bg-white">
                  <div class="d-flex align-items-start">
                    <div class="icon-box me-3 p-2 bg-light rounded border">
                      <i class="bi bi-cash-coin fs-5 text-warning"></i>
                    </div>
                    <div>
                      <div class="mb-1">
                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-2 py-0.5" style="font-size: 0.7rem; font-weight: 600;">
                          UNSETTLED PENALTY
                        </span>
                        <small class="text-muted ms-2">Overdue Since: <?= date('M d, Y', strtotime($fine['due_date'])) ?></small>
                      </div>
                      <div class="text-dark">
                        <strong><?= htmlspecialchars($fine['fullName']) ?></strong> 
                        <span class="text-muted">owes an active penalty of</span> 
                        <strong class="text-danger">Php <?= number_format($fine['penalty'], 2) ?></strong>
                        <span class="text-muted">for</span> 
                        <strong class="text-dark">"<?= htmlspecialchars($fine['title']) ?>"</strong>
                      </div>
                    </div>
                  </div>

                  <div class="request-actions d-flex gap-2 align-self-center">
                    <form action="/kmkdt-Library/public/admin/payHistory" method="POST" class="d-inline" onsubmit="return confirm('Confirm receipt of Php <?= number_format($fine['penalty'], 2) ?>? The user\'s penalty will be cleared and book timeline extended.');">
                      <input type="hidden" name="loan_id" value="<?= intval($fine['loan_id']) ?>">
                      <input type="hidden" name="action" value="approve_received_payment">
                      <button type="submit" class="btn btn-sm btn-success rounded-1 px-3 d-flex align-items-center gap-1 fw-semibold">
                        <i class="bi bi-check-lg"></i> Approve
                      </button>
                    </form>

                    <form action="/kmkdt-Library/public/admin/payHistory" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to reject this payment verification request? This will return the user loan state back to unpaid status.');">
                      <input type="hidden" name="loan_id" value="<?= intval($fine['loan_id']) ?>">
                      <input type="hidden" name="action" value="reject_received_payment">
                      <button type="submit" class="btn btn-sm btn-outline-danger rounded-1 px-3 d-flex align-items-center gap-1 fw-semibold">
                        <i class="bi bi-x-lg"></i> Reject
                      </button>
                    </form>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <div class="text-center py-4 bg-light border border-dashed">
                <i class="bi bi-check2-circle text-success fs-1"></i>
                <p class="text-muted mt-2 mb-0">No outstanding member fine penalties awaiting approval context.</p>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <div class="card rounded-0 shadow-sm">
        <div class="card-body pt-3">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <h5 class="fw-bold text-dark mb-0 fs-6">Settled Payment Archive</h5>
            <button class="btn btn-sm btn-outline-secondary rounded-0 small" onclick="window.print()">
              <i class="bi bi-printer me-1"></i> Export Log
            </button>
          </div>
          <hr class="mt-1 mb-3 text-muted opacity-25">

          <table class="table datatable table-hover align-middle">
            <thead class="table-light">
              <tr>
                <th scope="col" style="width: 100px;">Receipt #</th>
                <th scope="col">Member Name</th>
                <th scope="col">Book Reference Title</th>
                <th scope="col" class="text-end" style="width: 140px;">Amount Settled</th>
                <th scope="col" class="text-center" style="width: 180px;">Status</th>
                <th scope="col" class="text-center" style="width: 200px;">Date &amp; Time Settled</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($payments)): ?>
                <?php foreach ($payments as $row): ?>
                  <tr>
                    <td class="text-muted fw-mono">
                      <span class="badge bg-light text-dark border rounded-0 px-2 py-1">#<?= str_pad($row['id'], 5, '0', STR_PAD_LEFT); ?></span>
                    </td>
                    <td>
                      <div class="fw-bold text-dark"><?= htmlspecialchars($row['fullName']); ?></div>
                      <small class="text-secondary">@<?= htmlspecialchars($row['username'] ?? 'member'); ?></small>
                    </td>
                    <td>
                      <div class="text-truncate" style="max-width: 280px;" title="<?= htmlspecialchars($row['book_title']); ?>">
                        <i class="bi bi-book me-2 text-secondary"></i><?= htmlspecialchars($row['book_title']); ?>
                      </div>
                    </td>
                    <td class="text-end">
                      <span class="fw-bold text-success">
                        Php <?= number_format($row['amount_paid'], 2); ?>
                      </span>
                    </td>
                    <td class="text-center">
                      <span class="badge bg-success-light text-success border border-success rounded-0 px-2 py-1 small">
                        <i class="bi bi-check2-all me-1"></i> Received &amp; Verified
                      </span>
                    </td>
                    <td class="text-center text-secondary small">
                      <i class="bi bi-clock me-1 text-muted"></i>
                      <?= date('M d, Y | g:i A', strtotime($row['paid_at'])); ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="6" class="text-center py-5 text-muted">
                    <i class="bi bi-folder-x fs-2 d-block text-secondary opacity-50 mb-2"></i>
                    No penalty ledger transactions have been recorded yet.
                  </td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </div>
</section>

<?php include('./includes/footer.php'); ?>