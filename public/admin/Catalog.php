<?php
include('../../app/middleware/admin.php');

// Fetch dynamic data - Ensure getCatalog() uses the subquery fix discussed
$books = getCatalog($conn);
$activities = getRecentActivity($conn);

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

<!-- Main Catalog -->
<div class="col-lg-12">
  <div class="card rounded-0">
    <div class="card-body">

      <!-- Search -->
      <input type="text" id="searchInput" class="form-control mb-3 rounded-0" placeholder="Search by title or author...">

      <!-- Category Filter -->
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
                    // Syncing logic: Check the active_status from our controller query
                    $rawStatus = strtolower($book['active_status'] ?? 'available');
                    
                    if ($rawStatus !== 'available' && $rawStatus !== '') {
                        $badgeClass = 'bg-borrowed'; // Custom yellow with black text
                        $displayStatus = 'Borrowed';
                    } else {
                        $badgeClass = 'bg-success';  // Lime green theme
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

<!-- Recent Activity -->
<div class="col-lg-12">
  <div class="card rounded-0">
    <div class="card-body">
      <h5 class="card-title">Recent Activity</h5>
      <div class="activity">
        <?php if (!empty($activities)): ?>
          <?php foreach ($activities as $act): ?>
            <?php
              $actStatus = strtolower($act['status'] ?? 'borrowed');
              $isReturn = ($actStatus === 'returned');
            ?>
            <div class="activity-item d-flex align-items-start mb-3">
              <div class="activity-icon <?= $isReturn ? 'bg-primary' : 'bg-borrowed' ?> text-white rounded-circle me-3" 
                   style="width:38px;height:38px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="bi <?= $isReturn ? 'bi-arrow-return-left' : 'bi-book' ?>"></i>
              </div>
              <div>
                <div class="small text-muted">
                  <?= date('g:i A', strtotime($act['borrowed_at'] ?? 'now')) ?>
                </div>
                <div>
                  <strong><?= htmlspecialchars($act['fullName'] ?? '') ?></strong> 
                  <?= htmlspecialchars($actStatus) ?> 
                  <strong><?= htmlspecialchars($act['title'] ?? '') ?></strong>
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