<?php
// app/controllers/userController.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. PATH CONFIGURATION 
$configPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php';
if (file_exists($configPath)) {
    include_once($configPath);
} else {
    die("Config file not found at: " . $configPath);
}

// 2. CENTRAL ROUTING ACTIONS
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Logout Action Route
    if (isset($_POST['logoutButton'])) {
        unset($_SESSION['authUser']);
        unset($_SESSION['user_id']);
        unset($_SESSION['userRole']);
        session_destroy();
        header("Location: /kmkdt-Library/public/login");
        exit();
    }
}

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