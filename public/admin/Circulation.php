<?php
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params(0, '/'); 
    session_start();
}

include('../../app/middleware/admin.php');

define('ROOT_PATH', dirname(__DIR__, 2));
define('APP_PATH', ROOT_PATH . '/app');

require_once APP_PATH . '/config/config.php';
require_once APP_PATH . '/controller/adminController.php';

$stats = getCirculationStats($conn);
$records = getCirculationRecords($conn);

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
</div>

<section class="circulation-management py-3">
  <div class="row g-4">

    <!-- Live Circulation Updates Section -->
    <div class="col-lg-12">
      <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-4">
          <h5 class="card-title fw-bold text-dark mb-3 p-0">Live Circulation Updates</h5>

          <!-- Returned -->
          <div class="mb-3">
            <div class="progress progress-circulation-track rounded-pill">
              <div class="progress-bar progress-bar-striped bg-success progress-bar-animated" 
                   role="progressbar"
                   style="width: <?php echo $stats['returned_pct']; ?>%"
                   aria-valuenow="<?php echo $stats['returned_pct']; ?>" 
                   aria-valuemin="0" 
                   aria-valuemax="100">
                   Returned (<?php echo round($stats['returned_pct']); ?>%)
              </div>
            </div>
          </div>

          <!-- Borrowed -->
          <div class="mb-3">
            <div class="progress progress-circulation-track rounded-pill">
              <div class="progress-bar progress-bar-striped bg-borrowed progress-bar-animated text-white" 
                   role="progressbar"
                   style="width: <?php echo $stats['borrowed_pct']; ?>%"
                   aria-valuenow="<?php echo $stats['borrowed_pct']; ?>" 
                   aria-valuemin="0" 
                   aria-valuemax="100">
                   Borrowed (<?php echo round($stats['borrowed_pct']); ?>%)
              </div>
            </div>
          </div>

          <!-- Due Soon -->
          <div class="mb-3">
            <div class="progress progress-circulation-track rounded-pill">
              <div class="progress-bar progress-bar-striped bg-warning text-dark progress-bar-animated" 
                   role="progressbar"
                   style="width: <?php echo $stats['due_soon_pct']; ?>%"
                   aria-valuenow="<?php echo $stats['due_soon_pct']; ?>" 
                   aria-valuemin="0" 
                   aria-valuemax="100">
                   Due Soon (<?php echo round($stats['due_soon_pct']); ?>%)
              </div>
            </div>
          </div>

          <!-- Overdue -->
          <div class="mb-2">
            <div class="progress progress-circulation-track rounded-pill">
              <div class="progress-bar progress-bar-striped bg-danger progress-bar-animated" 
                   role="progressbar"
                   style="width: <?php echo $stats['overdue_pct']; ?>%"
                   aria-valuenow="<?php echo $stats['overdue_pct']; ?>" 
                   aria-valuemin="0" 
                   aria-valuemax="100">
                   Overdue (<?php echo round($stats['overdue_pct']); ?>%)
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>

    <!-- Circulation Records Table -->
    <div class="col-12">
      <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        <div class="card-body p-4">
          <h5 class="card-title fw-bold text-dark mb-3 p-0">Circulation Records</h5>

          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th scope="col" class="py-3">#</th>
                  <th scope="col" class="py-3">Member</th>
                  <th scope="col" class="py-3">Book Title</th>
                  <th scope="col" class="py-3">Borrowed Date</th>
                  <th scope="col" class="py-3">Due Date</th> 
                  <th scope="col" class="py-3">Status</th>
                  <th scope="col" class="py-3 text-center">Renewals</th>
                </tr>
              </thead>

              <tbody>
                <?php if (!empty($records)): ?>
                  <?php foreach ($records as $row): ?>
                    <?php
                      $status = strtolower($row['status'] ?? 'unknown');
                      $badgeClass = 'bg-secondary text-white';

                      if ($status === 'borrowed') $badgeClass = 'bg-borrowed text-white';
                      elseif ($status === 'due soon') $badgeClass = 'bg-warning text-dark';
                      elseif ($status === 'overdue') $badgeClass = 'bg-danger text-white';
                      elseif ($status === 'returned') $badgeClass = 'bg-success text-white';
                    ?>
                    <tr>
                      <td class="fw-medium text-secondary">#<?php echo $row['id']; ?></td>
                      <td class="text-dark font-medium"><?php echo htmlspecialchars($row['fullName'] ?? 'Unknown Member'); ?></td>
                      <td>
                        <span class="fw-semibold text-dark text-wrap-title">
                          <?php echo htmlspecialchars($row['title'] ?? 'Untitled'); ?>
                        </span>
                      </td>
                      <td class="text-secondary small">
                        <?php echo !empty($row['borrowed_at']) ? date('M d, Y', strtotime($row['borrowed_at'])) : '-'; ?>
                      </td>
                      <td class="text-secondary small fw-medium">
                        <?php echo !empty($row['due_date']) ? date('M d, Y', strtotime($row['due_date'])) : '-'; ?>
                      </td>
                      <td>
                        <span class="badge <?php echo $badgeClass; ?> px-3 py-2 text-uppercase font-tracking-wide">
                          <?php echo ucfirst($status); ?>
                        </span>
                      </td>
                      <td class="text-center">
                         <span class="small fw-bold <?php echo ($row['renewal_count'] >= 1) ? 'text-danger' : 'text-muted'; ?>">
                            <?php echo $row['renewal_count']; ?> / 1
                         </span>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="7" class="text-center py-5 text-muted bg-light border-0 rounded-bottom">
                      <div class="d-flex flex-column align-items-center justify-content-center my-2">
                        <span class="fw-medium">No system metrics or tracking logs match this query footprint.</span>
                      </div>
                    </td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>

        </div>
      </div>
    </div>

  </div>
</section>

<?php
include('./includes/footer.php');
?>