<?php
include('../../app/middleware/admin.php');

// Set root path constants
define('ROOT_PATH', dirname(__DIR__, 2));
define('APP_PATH', ROOT_PATH . '/app');

// Include core files
require_once APP_PATH . '/config/config.php';
require_once APP_PATH . '/controller/adminController.php';

// Fetch data
$stats = getCirculationStats($conn); 
$records = getCirculationRecords($conn);

// Render layouts
include('./includes/header.php');
include('./includes/topbar.php');
include('./includes/sidebar.php');
?>

<div class="pagetitle">
  <h1>Circulation</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="index.php">Home</a></li>
      <li class="breadcrumb-item">Library Management</li>
      <li class="breadcrumb-item active">Circulation</li>
    </ol>
  </nav>
</div><!-- End Page Title -->

<section class="section">
  <div class="row">

    <!-- Live Circulation Updates Section -->
    <div class="col-lg-12">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Live Circulation Updates</h5>

          <!-- Returned - Success (Lime Green) -->
          <div class="progress mt-3" style="height: 25px;">
            <div class="progress-bar progress-bar-striped bg-success progress-bar-animated" 
                 style="width: <?= $stats['returned_pct'] ?>%">
                 Returned (<?= round($stats['returned_pct']) ?>%)
            </div>
          </div>

          <!-- Borrowed - Custom Orange/Yellow -->
          <div class="progress mt-3" style="height: 25px;">
            <div class="progress-bar progress-bar-striped bg-borrowed progress-bar-animated" 
                 style="width: <?= $stats['borrowed_pct'] ?>%">
                 Borrowed (<?= round($stats['borrowed_pct']) ?>%)
            </div>
          </div>

          <!-- Due Soon - Warning Yellow -->
          <div class="progress mt-3" style="height: 25px;">
            <div class="progress-bar progress-bar-striped bg-warning text-dark progress-bar-animated" 
                 style="width: <?= $stats['due_soon_pct'] ?>%">
                 Due Soon (<?= round($stats['due_soon_pct']) ?>%)
            </div>
          </div>

          <!-- Overdue - Danger Red -->
          <div class="progress mt-3" style="height: 25px;">
            <div class="progress-bar progress-bar-striped bg-danger progress-bar-animated" 
                 style="width: <?= $stats['overdue_pct'] ?>%">
                 Overdue (<?= round($stats['overdue_pct']) ?>%)
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Circulation Records Table -->
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
                <th class="text-center">Renewals</th>
              </tr>
            </thead>

            <tbody>
              <?php if (!empty($records)): ?>
                <?php foreach ($records as $row): ?>
                  <?php
                    $status = strtolower($row['status'] ?? 'unknown');
                    $badgeClass = 'bg-secondary';

                    if ($status === 'borrowed') $badgeClass = 'bg-borrowed';
                    elseif ($status === 'due soon') $badgeClass = 'bg-warning text-dark';
                    elseif ($status === 'overdue') $badgeClass = 'bg-danger';
                    elseif ($status === 'returned') $badgeClass = 'bg-success';
                  ?>
                  <tr>
                    <td>#<?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['fullName'] ?? 'Unknown Member') ?></td>
                    <td><span class="fw-semibold text-dark"><?= htmlspecialchars($row['title'] ?? 'Untitled') ?></span></td>
                    <td><?= !empty($row['borrowed_at']) ? date('M d, Y', strtotime($row['borrowed_at'])) : '-' ?></td>
                    
                    
                    <td><?= !empty($row['due_date']) ? date('M d, Y', strtotime($row['due_date'])) : '-' ?></td>
                    
                    <td><span class="badge <?= $badgeClass ?> px-3 py-2"><?= ucfirst($status) ?></span></td>
                    
                    <!-- Renewal Tracking (Max 2 rule) -->
                    <td class="text-center">
                       <span class="small fw-bold <?= ($row['renewal_count'] >= 2) ? 'text-danger' : 'text-muted' ?>">
                          <?= $row['renewal_count'] ?> / 2
                       </span>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="7" class="text-center py-4 text-muted">
                    No circulation records found.
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

<?php
include('./includes/footer.php');
?>