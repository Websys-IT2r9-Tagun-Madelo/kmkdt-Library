<?php
if (!defined('APP_PATH')) {
    define('APP_PATH', dirname(__DIR__, 2) . '/app');
}

require_once APP_PATH . '/config/config.php';
require_once APP_PATH . '/controller/adminController.php';

handleAdminLogoutRequest();

$successMessage = $_SESSION['success'] ?? null;
$errorMessage   = $_SESSION['error'] ?? null;
unset($_SESSION['success'], $_SESSION['error']);

// Fetch data using clean controller functions
$books           = getCatalog($conn);
$pendingRequests = getPendingReturnRequests($conn);

include('./includes/header.php');
include('./includes/topbar.php');
include('./includes/sidebar.php');
?>

<div class="pagetitle d-flex justify-content-between align-items-center mb-4">
  <div>
    <h1>Catalog Management</h1>
    <nav>
      <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="index">Home</a></li>
        <li class="breadcrumb-item">Library Management</li>
        <li class="breadcrumb-item active">Catalog</li>
      </ol>
    </nav>
  </div>
  <button type="button" class="btn btn-primary rounded-0 d-flex align-items-center gap-2 px-3 py-2" data-bs-toggle="modal" data-bs-target="#addBookModal">
    <i class="bi bi-plus-lg"></i> Add New Book
  </button>
</div>

<?php if ($successMessage): ?>
  <div class="alert alert-success alert-dismissible fade show rounded-0 mb-3" role="alert">
    <i class="bi bi-check-circle me-2"></i> <?= htmlspecialchars($successMessage) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
<?php endif; ?>

<?php if ($errorMessage): ?>
  <div class="alert alert-danger alert-dismissible fade show rounded-0 mb-3" role="alert">
    <i class="bi bi-exclamation-triangle me-2"></i> <?= htmlspecialchars($errorMessage) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
<?php endif; ?>

<div class="col-lg-12 mb-4">
  <div class="card rounded-0 border-0 shadow-sm">
    <div class="card-body pt-4">
      
      <div class="row g-3 mb-4">
        <div class="col-md-8">
          <div class="input-group">
            <span class="input-group-text bg-light border-end-0 rounded-0 text-muted"><i class="bi bi-search"></i></span>
            <input type="text" id="searchInput" class="form-control border-start-0 rounded-0" placeholder="Search by title, author, genre, or category...">
          </div>
        </div>
        <div class="col-md-4">
          <select id="categoryFilter" class="form-select rounded-0">
            <option value="">All Collections</option>
            <optgroup label="General Reference">
              <option value="fiction">Fiction</option>
              <option value="non-fiction">Non-Fiction</option>
            </optgroup>
            <optgroup label="Academic Journals">
              <option value="research">Research</option>
              <option value="case studies">Case studies</option>
            </optgroup>
            <optgroup label="Special Designations">
              <option value="reserve">Reserve</option>
              <option value="online">Online</option>
            </optgroup>
          </select>
        </div>
      </div>

       <div class="table-responsive">
         <table class="table table-hover align-middle" id="catalogTable">
          <thead class="table-light text-secondary text-uppercase fs-7 small">
            <tr>
              <th class="ps-3" style="width: 60px;">#</th>
              <th style="width: 80px;">Cover</th>
              <th>Title</th>
              <th>Author / Genre</th>
              <th>Category</th>
              <th>Status</th>
              <th class="text-end pe-3" style="width: 140px;">Actions</th>
            </tr>
          </thead>
  
          <tbody>
            <?php if (!empty($books)): ?>
              <?php foreach ($books as $index => $book): ?>
                <tr class="book-row border-bottom align-middle">
                  <td class="ps-3 text-muted fw-semibold"><?= $index + 1 ?></td>
                  <td>
                    <?php if (!empty($book['cover_image'])): ?>
                      <img src="/kmkdt-Library/app/uploads/covers/<?= htmlspecialchars($book['cover_image']) ?>" alt="Cover" class="img-thumbnail rounded-0 cover-img-thumbnail">
                    <?php else: ?>
                      <div class="bg-light text-muted border d-flex align-items-center justify-content-center fw-bold small text-uppercase cover-placeholder">No Cover</div>
                    <?php endif; ?>
                  </td>
                  <td>
                    <div class="fw-bold text-dark book-title">
                      <?= htmlspecialchars($book['title'] ?? '') ?>
                      <?php if (!empty($book['ebook_file'])): ?>
                        <span class="badge bg-info text-white ms-1 rounded-pill" style="font-size: 0.65rem;" title="E-Book File Attached">
                          <i class="bi bi-file-earmark-pdf-fill"></i> PDF
                        </span>
                      <?php endif; ?>
                    </div>
                    <small class="text-muted d-block text-truncate book-title-description"><?= htmlspecialchars($book['description'] ?? 'No description provided.') ?></small>
                  </td>
                  <td class="text-secondary">
                     <span class="d-block text-dark book-author"><i class="bi bi-person me-1 text-muted"></i><?= htmlspecialchars($book['author'] ?? '') ?></span>
                     <small class="text-muted ms-4 book-genre">Genre: <?= htmlspecialchars($book['genre'] ?? 'N/A') ?></small>
                  </td>
                  <td class="book-category align-middle">
                    <span class="d-none"><?= htmlspecialchars($book['category'] ?? '') ?></span>
                    <div class="d-flex flex-wrap gap-1">
                      <?php 
                      if (!empty($book['category'])) {
                          $tags = explode(',', $book['category']);
                          foreach ($tags as $tag) {
                              $trimmedTag = strtolower(trim($tag));
                              if (empty($trimmedTag)) continue;

                              if ($trimmedTag === 'non-ficion' || $trimmedTag === 'non fiction') {
                                  $trimmedTag = 'non-fiction';
                              }

                              if ($trimmedTag === 'reserve') {
                                  $badgeClass = 'bg-reserve text-white';
                              } elseif ($trimmedTag === 'online') {
                                  $badgeClass = 'bg-online text-dark';
                              } else {
                                  $badgeClass = 'bg-light text-dark border';
                              }
                              echo '<span class="badge ' . $badgeClass . ' text-capitalize px-2 py-1">' . htmlspecialchars($trimmedTag) . '</span>';
                          }
                      }
                      ?>
                    </div>
                  </td>
                  <td>
                    <?php
                      $rawStatus = strtolower($book['status'] ?? 'available');
                      if ($rawStatus === 'borrowed') {
                          $badgeClass = 'bg-borrowed-light text-borrowed-dark border border-borrowed-subtle';
                          $displayStatus = 'Borrowed';
                      } elseif ($rawStatus === 'online') {
                          $badgeClass = 'bg-info-light text-info-dark border border-info-subtle'; 
                          $displayStatus = 'Online';
                      } else {
                          $badgeClass = 'bg-success-light text-success-dark border border-success-subtle';  
                          $displayStatus = 'Available';
                      }
                    ?>
                    <span class="badge <?= $badgeClass ?> px-2.5 py-1.5 fs-7 rounded-1">
                      <?= $displayStatus ?>
                    </span>
                  </td>
                  <td class="text-end pe-3">
                    <div class="btn-group gap-1">
                      <button type="button" 
                              class="btn btn-sm btn-outline-secondary edit-book-btn rounded-0"
                              data-bs-toggle="modal" 
                              data-bs-target="#editBookModal"
                              data-id="<?= htmlspecialchars($book['id'] ?? '') ?>"
                              data-title="<?= htmlspecialchars($book['title'] ?? '') ?>"
                              data-author="<?= htmlspecialchars($book['author'] ?? '') ?>"
                              data-category="<?= htmlspecialchars($book['category'] ?? '') ?>"
                              data-genre="<?= htmlspecialchars($book['genre'] ?? '') ?>"
                              data-description="<?= htmlspecialchars($book['description'] ?? '') ?>"
                              data-status="<?= htmlspecialchars($book['status'] ?? '') ?>"
                              data-has-ebook="<?= !empty($book['ebook_file']) ? 'true' : 'false' ?>">
                        <i class="bi bi-pencil"></i>
                      </button>
                      <button type="button" 
                              class="btn btn-sm btn-outline-danger delete-book-btn rounded-0"
                              data-bs-toggle="modal" 
                              data-bs-target="#deleteBookModal"
                              data-id="<?= htmlspecialchars($book['id'] ?? '') ?>"
                              data-title="<?= htmlspecialchars($book['title'] ?? '') ?>">
                        <i class="bi bi-trash"></i>
                      </button>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
  
            <tr id="noResultsRow" style="<?= empty($books) ? '' : 'display:none;' ?>">
              <td colspan="7" class="text-center py-5 text-muted">
                <div class="mb-2">
                  <i class="bi bi-search" style="font-size: 2rem; opacity: 0.3;"></i>
                </div>
                No books matching your criteria were found.
              </td>
            </tr>
          </tbody>
         </table>
       </div>

    </div>
  </div>
</div>

<div class="col-lg-12 mb-4">
  <div class="card rounded-0 border-0 shadow-sm">
    <div class="card-body pt-4">
      <h5 class="card-title p-0 mb-4 fw-bold">Pending Return Requests</h5>
      <div class="requests-list">
        <?php if (!empty($pendingRequests)): ?>
          <?php foreach ($pendingRequests as $act): 
              $timestamp = !empty($act['borrowed_at']) ? $act['borrowed_at'] : 'now'; 
              $bookTitle = !empty($act['title']) ? '"' . $act['title'] . '"' : 'Unknown Book';
              $requestId = $act['request_id'] ?? 0;
            ?>
            <div class="request-item d-flex align-items-start justify-content-between p-3 mb-3 border rounded">
              <div class="d-flex align-items-start">
                <div class="icon-box me-3 p-2 bg-light rounded border">
                  <i class="bi bi-box-arrow-in-left fs-5 text-primary"></i>
                </div>
                
                <div>
                  <div class="mb-1">
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-0.5" style="font-size: 0.7rem; font-weight: 600;">
                      RETURN REQUEST
                    </span>
                    <small class="text-muted ms-2"><?= date('M d, g:i A', strtotime($timestamp)) ?></small>
                  </div>
                  
                  <div class="text-dark">
                    <strong><?= htmlspecialchars($act['borrower_name'] ?? 'System User') ?></strong> 
                    <span class="text-muted">requested to return</span> 
                    <strong class="text-dark"><?= htmlspecialchars($bookTitle) ?></strong>
                  </div>
                </div>
              </div>

              <div class="request-actions d-flex gap-2 align-self-center">
                <form action="/kmkdt-Library/app/controller/process/adminApprovalsProcess.php" method="POST" class="d-inline" onsubmit="return confirm('Approve this return request? The book will be marked as Available.');">
                  <input type="hidden" name="request_id" value="<?= intval($requestId) ?>">
                  <input type="hidden" name="action" value="approve">
                  <button type="submit" class="btn btn-sm btn-success rounded-1 px-3 d-flex align-items-center gap-1">
                    <i class="bi bi-check-lg"></i> Approve
                  </button>
                </form>
                
                <form action="/kmkdt-Library/app/controller/process/adminApprovalsProcess.php" method="POST" class="d-inline" onsubmit="return confirm('Reject this return request? The book will remain in the user\'s borrowed list.');">
                  <input type="hidden" name="request_id" value="<?= intval($requestId) ?>">
                  <input type="hidden" name="action" value="reject">
                  <button type="submit" class="btn btn-sm btn-outline-danger rounded-1 px-3 d-flex align-items-center gap-1">
                    <i class="bi bi-x-lg"></i> Reject
                  </button>
                </form>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="text-center py-4">
            <i class="bi bi-check2-circle text-success fs-1"></i>
            <p class="text-muted mt-2 mb-0">No pending return requests found.</p>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="addBookModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content rounded-0 border-0 shadow">
      <form action="/kmkdt-Library/app/controller/process/bookProcess.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="action" value="create">
        <div class="modal-header border-bottom-0 bg-light">
          <h5 class="modal-title fw-bold">Add New Catalog Item</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body py-3">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label text-secondary small fw-bold">Book Title</label>
              <input type="text" name="title" class="form-control rounded-0" required placeholder="e.g. The Great Gatsby">
            </div>
            <div class="col-md-6">
              <label class="form-label text-secondary small fw-bold">Author</label>
              <input type="text" name="author" class="form-control rounded-0" required placeholder="e.g. F. Scott Fitzgerald">
            </div>
            <div class="col-md-6">
              <label class="form-label text-secondary small fw-bold">Genre</label>
              <input type="text" name="genre" class="form-control rounded-0" required placeholder="e.g. Tragedy, Realism">
            </div>
            
            <div class="col-md-6">
              <div class="mb-1">
                <label class="form-label text-secondary small fw-bold mb-0">Collection Category <span class="text-danger">*</span></label>
              </div>

              <div class="p-2.5 border bg-white rounded-0 d-flex flex-column gap-1 overflow-auto category-selection-box" id="add_category_list_box">
                <div class="d-flex flex-wrap gap-x-3 gap-y-1" id="add_base_categories_group">
                  <div class="form-check me-3">
                    <input class="form-check-input add-cat-check" type="checkbox" name="category[]" value="Fiction" id="add_cat_fiction">
                    <label class="form-check-label small" for="add_cat_fiction">Fiction</label>
                  </div>
                  <div class="form-check me-3">
                    <input class="form-check-input add-cat-check" type="checkbox" name="category[]" value="Non-Fiction" id="add_cat_nonfiction">
                    <label class="form-check-label small" for="add_cat_nonfiction">Non-Fiction</label>
                  </div>
                  <div class="form-check me-3">
                    <input class="form-check-input add-cat-check" type="checkbox" name="category[]" value="Research" id="add_cat_research">
                    <label class="form-check-label small" for="add_cat_research">Research</label>
                  </div>
                  <div class="form-check me-3">
                    <input class="form-check-input add-cat-check" type="checkbox" name="category[]" value="Case Studies" id="add_cat_casestudies">
                    <label class="form-check-label small" for="add_cat_casestudies">Case Studies</label>
                  </div>
                </div>

                <div class="w-100 border-top my-1 designation-divider"></div>
                
                <div class="d-flex gap-3 designation-item">
                  <div class="form-check">
                    <input class="form-check-input add-cat-check" type="checkbox" name="category[]" value="Reserve" id="add_cat_reserve">
                    <label class="form-check-label small" for="add_cat_reserve">Reserve</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input add-cat-check" type="checkbox" name="category[]" value="Online" id="add_cat_online">
                    <label class="form-check-label small" for="add_cat_online">Online</label>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-md-6">
              <label class="form-label text-secondary small fw-bold">Status</label>
              <select name="status" class="form-select rounded-0" required>
                <option value="available" selected>Available</option>
                <option value="borrowed">Borrowed</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label text-secondary small fw-bold">Cover Image File <span class="text-danger">*</span></label>
              <input type="file" name="cover_image" class="form-control rounded-0" accept="image/*" required>
            </div>

            <div class="col-md-12">
              <label class="form-label text-secondary small fw-bold">
                <i class="bi bi-file-earmark-pdf text-danger me-1"></i> Upload Digital E-Book File <span class="text-muted fw-normal">(Optional)</span>
              </label>
              <input type="file" name="ebook_file" class="form-control rounded-0" accept=".pdf">
              <div class="form-text mt-1 text-muted small" style="font-size: 11px;">Supported format: <strong>PDF only</strong>. Leave empty if this is exclusively a physical catalog record.</div>
            </div>

            <div class="col-12">
              <label class="form-label text-secondary small fw-bold">Description / Summary</label>
              <textarea name="description" class="form-control rounded-0" rows="3" placeholder="Enter book synopsis..."></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer border-top-0 bg-light rounded-0">
          <button type="button" class="btn btn-outline-secondary rounded-0" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary rounded-0 px-4">Save Book</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="editBookModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content rounded-0 border-0 shadow">
      <form action="/kmkdt-Library/app/controller/process/bookProcess.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="action" value="update">
        <input type="hidden" name="book_id" id="edit_id">
        <div class="modal-header border-bottom-0 bg-light">
          <h5 class="modal-title fw-bold">Modify Book Properties</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body py-3">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label text-secondary small fw-bold">Book Title</label>
              <input type="text" name="title" id="edit_title" class="form-control rounded-0" required>
            </div>
            <div class="col-md-6">
              <label class="form-label text-secondary small fw-bold">Author</label>
              <input type="text" name="author" id="edit_author" class="form-control rounded-0" required>
            </div>
            <div class="col-md-6">
              <label class="form-label text-secondary small fw-bold">Genre</label>
              <input type="text" name="genre" id="edit_genre" class="form-control rounded-0" required>
            </div>
            
            <div class="col-md-6">
              <div class="mb-1">
                <label class="form-label text-secondary small fw-bold mb-0">Collection Category <span class="text-danger">*</span></label>
              </div>

              <div class="p-2.5 border bg-white rounded-0 d-flex flex-column gap-1 overflow-auto category-selection-box" id="edit_category_list_box">
                <div class="d-flex flex-wrap gap-x-3 gap-y-1" id="edit_base_categories_group">
                  <div class="form-check me-3">
                    <input class="form-check-input edit-cat-check" type="checkbox" name="category[]" value="Fiction" id="edit_cat_fiction">
                    <label class="form-check-label small" for="edit_cat_fiction">Fiction</label>
                  </div>
                  <div class="form-check me-3">
                    <input class="form-check-input edit-cat-check" type="checkbox" name="category[]" value="Non-Fiction" id="edit_cat_nonfiction">
                    <label class="form-check-label small" for="edit_cat_nonfiction">Non-Fiction</label>
                  </div>
                  <div class="form-check me-3">
                    <input class="form-check-input edit-cat-check" type="checkbox" name="category[]" value="Research" id="edit_cat_research">
                    <label class="form-check-label small" for="edit_cat_research">Research</label>
                  </div>
                  <div class="form-check me-3">
                    <input class="form-check-input edit-cat-check" type="checkbox" name="category[]" value="Case Studies" id="edit_cat_case">
                    <label class="form-check-label small" for="edit_cat_case">Case Studies</label>
                  </div>
                </div>

                <div class="w-100 border-top my-1 designation-divider"></div>
                
                <div class="d-flex gap-3 designation-item">
                  <div class="form-check">
                    <input class="form-check-input edit-cat-check" type="checkbox" name="category[]" value="Reserve" id="edit_cat_reserve">
                    <label class="form-check-label small" for="edit_cat_reserve">Reserve</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input edit-cat-check" type="checkbox" name="category[]" value="Online" id="edit_cat_online">
                    <label class="form-check-label small" for="edit_cat_online">Online</label>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-md-6">
              <label class="form-label text-secondary small fw-bold">Availability Status</label>
              <select name="status" id="edit_status" class="form-select rounded-0" required>
                <option value="available">Available</option>
                <option value="borrowed">Borrowed</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label text-secondary small fw-bold">Change Cover Image <span class="text-muted small fw-normal">(Optional)</span></label>
              <input type="file" name="cover_image" class="form-control rounded-0" accept="image/*">
            </div>

            <div class="col-md-12">
              <label class="form-label text-secondary small fw-bold">
                <i class="bi bi-file-earmark-pdf text-danger me-1"></i> Replace/Update Digital E-Book File <span class="text-muted fw-normal">(Optional)</span>
              </label>
              <input type="file" name="ebook_file" class="form-control rounded-0" accept=".pdf">
              <div class="form-text mt-1 text-muted small" style="font-size: 11px;" id="edit_ebook_hint">
                Supported format: <strong>PDF only</strong>. Leave empty to preserve the current digital asset file configuration.
              </div>
            </div>

            <div class="col-12">
              <label class="form-label text-secondary small fw-bold">Description / Summary</label>
              <textarea name="description" id="edit_description" class="form-control rounded-0" rows="3" required></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer border-top-0 bg-light rounded-0">
          <button type="button" class="btn btn-outline-secondary rounded-0" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success rounded-0 px-4">Update Properties</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="deleteBookModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-0 border-0 shadow">
      <form action="/kmkdt-Library/app/controller/process/bookProcess.php" method="POST">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="book_id" id="delete_id">
        <div class="modal-header border-bottom-0 bg-light">
          <h5 class="modal-title fw-bold text-danger">Remove Catalog Item</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body py-3">
          <p class="mb-0 text-secondary">
            Are you completely sure you want to delete <strong class="text-dark id-title-placeholder">this book</strong>? This process clears out linked cover images from storage and cannot be undone.
          </p>
        </div>
        <div class="modal-footer border-top-0 bg-light rounded-0">
          <button type="button" class="btn btn-outline-secondary rounded-0" data-bs-dismiss="modal">Cancel Removal</button>
          <button type="submit" class="btn btn-danger rounded-0 px-4">Confirm Delete</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include('./includes/footer.php'); ?>