<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include('../../app/middleware/admin.php');

if (!defined('APP_PATH')) {
    define('APP_PATH', dirname(__DIR__, 2) . '/app');
}

require_once APP_PATH . '/config/config.php';
require_once APP_PATH . '/controller/adminController.php'; 

$payments = getPenaltyPaymentsHistory($conn);

include('./includes/header.php');
include('./includes/topbar.php');
include('./includes/sidebar.php');
?>

<div class="pagetitle">
  <h1>Penalty Payments History</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="index">Home</a></li>
      <li class="breadcrumb-item">Analytics & Reporting</li>
      <li class="breadcrumb-item active">Payment History</li>
    </ol>
  </nav>
</div><section class="section">
  <div class="row">
    <div class="col-lg-12">

      <div class="card rounded-0">
        <div class="card-body">
          <p class="text-muted small">A complete audit trail of all overdue book fines collected from library members.</p>

          <table class="table datatable table-hover align-middle">
            <thead class="table-light">
              <tr>
                <th scope="col" style="width: 70px;">Receipt #</th>
                <th scope="col">Member Name</th>
                <th scope="col">Book Reference</th>
                <th scope="col">Amount Paid</th>
                <th scope="col">Date & Time Settled</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($payments)): ?>
                <?php foreach ($payments as $row): ?>
                  <tr>
                    <th scope="row" class="text-muted">#<?= $row['id']; ?></th>
                    <td>
                      <div class="fw-bold text-dark"><?= htmlspecialchars($row['fullName']); ?></div>
                      <small class="text-muted">@<?= htmlspecialchars($row['username'] ?? 'member'); ?></small>
                    </td>
                    <td>
                      <i class="bi bi-book me-1 text-secondary"></i>
                      <?= htmlspecialchars($row['book_title']); ?>
                    </td>
                    <td>
                      <span class="fw-bold text-success">
                        Php <?= number_format($row['amount_paid'], 2); ?>
                      </span>
                    </td>
                    <td>
                      <i class="bi bi-clock me-1 text-muted"></i>
                      <?= date('M d, Y | g:i A', strtotime($row['paid_at'])); ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="5" class="text-center py-4 text-muted">No penalty payments have been recorded yet.</td>
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