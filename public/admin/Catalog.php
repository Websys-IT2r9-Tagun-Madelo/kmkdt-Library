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

// 2. Fetch Catalog Items with Availability Status
$booksQuery = "SELECT b.*, 
               (SELECT bh.status FROM borrowing_history bh 
                WHERE bh.book_id = b.id AND bh.status IN ('borrowed', 'overdue') 
                ORDER BY bh.borrowed_at DESC LIMIT 1) AS active_status 
               FROM books b";
$booksResult = $conn->query($booksQuery);
$books = [];
if ($booksResult && $booksResult->num_rows > 0) {
    while ($row = $booksResult->fetch_assoc()) {
        $books[] = $row;
    }
}

// 3. Fetch Recent Activity combining Borrowing and Penalty Payments
$activities = [];

// A. Pull loans and returns
$loanActivityQuery = "SELECT bh.status, bh.borrowed_at AS activity_time, u.fullName, b.title 
                      FROM borrowing_history bh
                      JOIN user u ON bh.user_id = u.id
                      JOIN books b ON bh.book_id = b.id
                      ORDER BY bh.borrowed_at DESC LIMIT 5";
$loanResult = $conn->query($loanActivityQuery);
if ($loanResult && $loanResult->num_rows > 0) {
    while ($row = $loanResult->fetch_assoc()) {
        $activities[] = [
            'type' => 'history',
            'status' => $row['status'],
            'time' => $row['activity_time'],
            'fullName' => $row['fullName'],
            'details' => $row['title']
        ];
    }
}

// B. Pull penalty payments
$paymentActivityQuery = "SELECT pp.amount_paid, pp.paid_at AS activity_time, u.fullName 
                         FROM penalty_payments pp
                         JOIN user u ON pp.user_id = u.id
                         ORDER BY pp.paid_at DESC LIMIT 5";
$paymentResult = $conn->query($paymentActivityQuery);
if ($paymentResult && $paymentResult->num_rows > 0) {
    while ($row = $paymentResult->fetch_assoc()) {
        $activities[] = [
            'type' => 'payment',
            'status' => 'Paid Fine',
            'time' => $row['activity_time'],
            'fullName' => $row['fullName'],
            'details' => 'Php ' . number_format($row['amount_paid'], 2)
        ];
    }
}

// Sort the combined list by time descending
usort($activities, function($a, $b) {
    return strcmp($b['time'], $a['time']);
});
// Trim to latest 5 items total
$activities = array_slice($activities, 0, 5);


include('./includes/header.php');
include('./includes/topbar.php');
include('./includes/sidebar.php');
?>

<div class="pagetitle">
  <h1>Catalog</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="index">Home</a></li>
      <li class="breadcrumb-item active">Catalog</li>
    </ol>
  </nav>
</div>

<div class="col-lg-12">
  <div class="card rounded-0">
    <div class="card-body">

      <input type="text" id="searchInput" class="form-control mb-3 rounded-0" placeholder="Search by title or author...">

      <div class="row mb-3">
        <div class="col-md-12">
          <select id="categoryFilter" class="form-select rounded-0">
            <option value="">All Collections</option>
            <optgroup label="General Reference">
              <option value="fiction">Fiction</option>
              <option value="non-fiction">Non-Fiction</option>
            </optgroup>
            <optgroup label="Academic Journals">
              <option value="research">Research</option>
              <option value="science">Science</option>
            </optgroup>
            <optgroup label="Digital eBooks">
              <option value="e-book">E-Book</option>
              <option value="online">Online Resources</option>
            </optgroup>
          </select>
        </div>
      </div>

       <table class="table table-hover align-middle">
        <thead class="table-light">
          <tr>
            <th>#</th>
            <th>Title</th>
            <th>Author</th>
            <th>Category</th>
            <th>Status</th>
          </tr>
        </thead>

        <tbody>
          <?php if (!empty($books)): ?>
            <?php foreach ($books as $index => $book): ?>
              <tr class="shadow-sm">
                <td class="py-3 ps-3 text-muted" style="width: 50px;"><?= $index + 1 ?></td>
                <td class="py-3">
                  <div class="fw-bold text-dark"><?= htmlspecialchars($book['title'] ?? '') ?></div>
                </td>
                <td class="py-3 text-secondary">
                   <i class="bi bi-person me-1"></i><?= htmlspecialchars($book['author'] ?? '') ?>
                </td>
                <td class="py-3">
                  <span class="text-uppercase small fw-semibold text-muted bg-light px-2 py-1 rounded">
                    <?= htmlspecialchars($book['category'] ?? '') ?>
                  </span>
                </td>
                <td class="py-3 pe-3">
                  <?php
                    $rawStatus = strtolower($book['active_status'] ?? '');
                    
                    if ($rawStatus === 'borrowed' || $rawStatus === 'overdue') {
                        $badgeClass = 'bg-warning text-dark'; 
                        $displayStatus = ucfirst($rawStatus);
                    } else {
                        $badgeClass = 'bg-success text-white';  
                        $displayStatus = 'Available';
                    }
                  ?>
                  <span class="badge <?= $badgeClass ?>" style="padding: 0.6em 1em;">
                    <?= $displayStatus ?>
                  </span>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>

          <tr id="noResultsRow" style="<?= empty($books) ? '' : 'display:none;' ?>">
            <td colspan="5" class="text-center py-5 text-muted">
              <div class="mb-2">
                <i class="bi bi-search" style="font-size: 2rem; opacity: 0.5;"></i>
              </div>
              No books matching your criteria were found.
            </td>
          </tr>
        </tbody>
      </table>

    </div>
  </div>
</div>

<div class="col-lg-12">
  <div class="card rounded-0">
    <div class="card-body">
      <h5 class="card-title">Recent Activity</h5>
      <div class="activity">
        <?php if (!empty($activities)): ?>
          <?php foreach ($activities as $act): ?>
            <?php
              // Dynamic Icon picker depending on the action type
              if ($act['type'] === 'payment') {
                  $iconClass = 'bg-success';
                  $icon = 'bi-cash-coin';
                  $actionLabel = 'settled fine of';
              } else {
                  $isReturn = (strtolower($act['status']) === 'returned');
                  $iconClass = $isReturn ? 'bg-primary' : 'bg-warning text-dark';
                  $icon = $isReturn ? 'bi-arrow-return-left' : 'bi-book';
                  $actionLabel = strtolower($act['status']);
              }
            ?>
            <div class="activity-item d-flex align-items-start mb-3">
              <div class="activity-icon <?= $iconClass ?> text-white rounded-circle me-3" 
                   style="width:38px;height:38px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="bi <?= $icon ?>"></i>
              </div>
              <div>
                <div class="small text-muted">
                  <?= date('g:i A', strtotime($act['time'])) ?>
                </div>
                <div>
                  <strong><?= htmlspecialchars($act['fullName']) ?></strong> 
                  <?= htmlspecialchars($actionLabel) ?> 
                  <strong><?= htmlspecialchars($act['details']) ?></strong>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p class="text-muted ps-2">No recent activity.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<script src="../assets/js/catalog.js"></script>

<?php include('./includes/footer.php'); ?>