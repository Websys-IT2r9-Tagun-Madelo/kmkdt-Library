<?php
include('../../app/middleware/admin.php');

// ESTABLISH CORE APP PATH CONSTANTS
if (!defined('APP_PATH')) {
    define('APP_PATH', dirname(__DIR__, 2) . '/app');
}
require_once APP_PATH . '/config/config.php';
require_once APP_PATH . '/controller/adminController.php';

// FETCH COMPACTED STATISTICS 
$dashboardData = getDashboardAnalytics($conn);

// DESTRUCTURE INTO LOCAL VARIABLES 
$borrowedCount    = $dashboardData['borrowedCount'];
$overdueCount     = $dashboardData['overdueCount'];
$catalogCount     = $dashboardData['catalogCount'];
$totalRevenue     = $dashboardData['totalRevenue'];
$totalMembers     = $dashboardData['totalMembers'];
$recentActivities = $dashboardData['recentActivities'];

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
              <h6 class="fs-3 fw-bold mb-0"><?php echo $borrowedCount; ?></h6>
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
              <h6 class="fs-3 fw-bold mb-0"><?php echo $overdueCount; ?></h6>
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
              <h6 class="fs-4 fw-bold mb-0">Php <?php echo number_format($totalRevenue, 2); ?></h6>
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
              <h6 class="fs-3 fw-bold mb-0"><?php echo $totalMembers; ?></h6>
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
                <i class="bi bi-arrow-repeat fs-3 me-3 text-black"></i>
                <div>
                  <div class="fw-bold text-dark">Circulation Monitor</div>
                  <small class="text-muted">Live update trackers & ratios</small>
                </div>
              </a>
            </div>
            <div class="col-sm-6">
              <a href="payHistory" class="btn btn-outline-dangerz d-flex align-items-center p-3 rounded-0 text-start w-100 h-100">
                <i class="bi bi-receipt fs-3 me-3 text-black"></i>
                <div>
                  <div class="fw-bold text-dark">Penalty Logs</div>
                  <small class="text-muted">Track library invoice collections</small>
                </div>
              </a>
            </div>
            <div class="col-sm-6">
              <a href="members" class="btn btn-outline-success d-flex align-items-center p-3 rounded-0 text-start w-100 h-100">
                <i class="bi bi-person-lines-fill fs-3 me-3"></i>
                <div>
                  <div class="fw-bold">Manage Members</div>
                  <small class="text-muted">Review library cards registry</small>
                </div>
              </a>
            </div>
          </div>

          <div class="bg-light p-3 mt-4 border border-start border-3 border-info">
              <small class="text-muted d-block"><i class="bi bi-info-circle me-1 text-info"></i> <strong>Catalog Total:</strong> Total registered titles currently in inventory storage: <strong><?php echo $catalogCount; ?></strong></small>
          </div>

        </div>
      </div>
    </div>

<div class="col-lg-5 col-md-12 mb-4">
  <div class="card rounded-3 shadow-sm border-0 h-100">
    <div class="card-body p-4">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
          <h5 class="card-title mb-1 fw-bold text-dark">Live Transactions</h5>
          <p class="text-muted small mb-0">Latest user activities updated in real-time from system logs.</p>
        </div>
        <!-- Pulse effect for live feed feel -->
        <span class="spinner-grow spinner-grow-sm text-success" role="status" aria-hidden="true"></span>
      </div>
      
      <div class="activity mt-4" style="max-height: 450px; overflow-y: auto;">
        <?php if (!empty($recentActivities)): ?>
          <?php foreach ($recentActivities as $act): 
              $status = strtolower($act['status'] ?? '');
              $isOverdue = (!empty($act['is_overdue']) && $act['is_overdue'] == 1);
              
              // Safe date calculation using absolute day bounds
              if ($status === 'borrowed' && !$isOverdue && !empty($act['due_date'])) {
                  $dueDate = strtotime(date('Y-m-d', strtotime($act['due_date'])));
                  $today = strtotime(date('Y-m-d'));
                  $daysRemaining = round(($dueDate - $today) / 86400); 

                  if ($daysRemaining >= 0 && $daysRemaining <= 3) {
                      $status = 'due soon';
                  }
              }

              // Dynamic badge mapping
              switch(true) {
                  case ($isOverdue || $status === 'overdue'):
                      $badgeColor = 'bg-danger text-white';
                      $statusLabel = 'overdue';
                      break;
                  case ($status === 'due soon' || $status === 'due_soon'):
                      $badgeColor = 'bg-warning text-dark';
                      $statusLabel = 'due soon';
                      break;
                  case ($status === 'returned'):
                      $badgeColor = 'bg-success text-white';
                      $statusLabel = 'returned';
                      break;
                  case ($status === 'pending_return'):
                      $badgeColor = 'bg-pending text-white'; 
                      $statusLabel = 'pending';
                      break;
                  default:
                      // Ensure this class matches your CSS file
                      $badgeColor = 'bg-borrowed text-white'; 
                      $statusLabel = $status ?: 'borrowed';
                      break;
              }
          ?>
            <div class="activity-item d-flex align-items-start py-3 border-bottom">
              <!-- Uniform width & centered text alignment badge -->
              <div class="me-3">
                <span class="badge <?php echo $badgeColor; ?> rounded-1 text-uppercase text-center d-inline-block" 
                      style="font-size: 0.65rem; width: 78px; letter-spacing: 0.5px; padding: 5px 0;">
                  <?php echo htmlspecialchars($statusLabel); ?>
                </span>
              </div>
              
              <div class="flex-grow-1 min-w-0">
                <div class="d-flex justify-content-between align-items-baseline">
                  <h6 class="small fw-bold text-dark mb-0 text-truncate me-2">
                    <?php echo htmlspecialchars($act['fullName'] ?? 'System User'); ?>
                  </h6>
                  <small class="text-muted text-nowrap" style="font-size:0.7rem;">
                    <i class="bi bi-clock me-1"></i><?php echo !empty($act['borrowed_at']) ? date('M d, g:i A', strtotime($act['borrowed_at'])) : 'N/A'; ?>
                  </small>
                </div>
                
                <div class="text-muted small text-truncate mt-1">
                  Book: <span class="text-secondary fw-medium">"<?php echo htmlspecialchars($act['title'] ?? 'Unknown Title'); ?>"</span>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <!-- Polished Empty State Display -->
          <div class="text-center py-5 my-3 text-muted">
            <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
              <i class="bi bi-folder2-open fs-3 text-secondary opacity-75"></i>
            </div>
            <p class="mb-1 fw-semibold text-dark">No transactions found</p>
            <p class="small text-muted mb-0">Historical activities will propagate here automatically.</p>
          </div>
        <?php endif; ?>
      </div>

    </div>
  </div>
</div>

<?php
include('./includes/footer.php');
?>