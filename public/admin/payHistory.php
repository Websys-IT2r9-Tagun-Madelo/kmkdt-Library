<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include('../../app/middleware/admin.php');

// 1. Get database configuration path
$configPath = $_SERVER['DOCUMENT_ROOT'] . '/kmkdt-Library/app/config/config.php';
if (!file_exists($configPath)) {
    die("Config file not found at: " . $configPath);
}
include_once($configPath);

// 2. Fetch all penalty payments with user names and book titles
$query = "SELECT pp.id, pp.amount_paid, pp.paid_at, u.fullName, u.username, IFNULL(b.title, 'System Fine Adjustment') as book_title
          FROM penalty_payments pp
          INNER JOIN user u ON pp.user_id = u.id
          LEFT JOIN borrowing_history bh ON pp.loan_id = bh.id
          LEFT JOIN books b ON bh.book_id = b.id
          ORDER BY pp.paid_at DESC";

$result = $conn->query($query);
$payments = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $payments[] = $row;
    }
}

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
          <h5 class="card-title">Collected Penalty Fees Log</h5>
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