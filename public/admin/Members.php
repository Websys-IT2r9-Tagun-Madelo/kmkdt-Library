<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include('../../app/middleware/admin.php');
include('./includes/header.php');
include('./includes/topbar.php');
include('./includes/sidebar.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/kmkdt-Library/app/config/config.php');

$standardUsers = [];
$admins = [];
$result = $conn->query("SELECT * FROM user ORDER BY dateCreated ASC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        if (strtolower($row['role']) === 'admin') $admins[] = $row;
        else $standardUsers[] = $row;
    }
}

$message = $_SESSION['message'] ?? '';
$messageType = $_SESSION['code'] ?? '';
unset($_SESSION['message'], $_SESSION['code']);
?>

<div class="pagetitle d-flex justify-content-between align-items-center mb-4">
  <div>
    <h1>Members & Authority Profiles</h1>
    <nav>
      <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
        <li class="breadcrumb-item">Member Management</li>
        <li class="breadcrumb-item active">Members</li>
      </ol>
    </nav>
  </div>
  <button class="btn btn-primary d-flex align-items-center gap-2 px-3 py-2 shadow-sm fs-6 fw-semibold rounded-2" data-bs-toggle="modal" data-bs-target="#createMemberModal">
    <i class="bi bi-person-plus-fill fs-5"></i> Add New User
  </button>
</div>

<?php if (!empty($message)): ?>
    <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
        <div class="d-flex align-items-center gap-2">
            <i class="bi <?php echo ($messageType === 'success') ? 'bi-check-circle-fill' : (($messageType === 'warning') ? 'bi-exclamation-triangle-fill' : 'bi-x-circle-fill'); ?> fs-5"></i>
            <div><?php echo $message; ?></div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="row g-3 mb-4">
  <div class="col-md-4">
    <div class="card border-0 border-start border-primary border-4 shadow-sm rounded-2">
      <div class="card-body py-3">
        <div class="d-flex align-items-center justify-content-between">
          <div>
            <h6 class="text-muted small text-uppercase mb-1 fw-bold">Library Members</h6>
            <h3 class="m-0 fw-bold text-dark"><?php echo count($standardUsers); ?></h3>
          </div>
          <div class="p-3 bg-light rounded-circle text-primary"><i class="bi bi-people-fill fs-3"></i></div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card border-0 border-start border-dark border-4 shadow-sm rounded-2">
      <div class="card-body py-3">
        <div class="d-flex align-items-center justify-content-between">
          <div>
            <h6 class="text-muted small text-uppercase mb-1 fw-bold">Admin Staff</h6>
            <h3 class="m-0 fw-bold text-dark"><?php echo count($admins); ?></h3>
          </div>
          <div class="p-3 bg-light rounded-circle text-dark"><i class="bi bi-shield-lock-fill fs-3"></i></div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card border-0 border-start border-success border-4 shadow-sm rounded-2">
      <div class="card-body py-3">
        <div class="d-flex align-items-center justify-content-between">
          <div>
            <h6 class="text-muted small text-uppercase mb-1 fw-bold">Total Accounts</h6>
            <h3 class="m-0 fw-bold text-dark"><?php echo (count($standardUsers) + count($admins)); ?></h3>
          </div>
          <div class="p-3 bg-light rounded-circle text-success"><i class="bi bi-database-fill-check fs-3"></i></div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Library Members Table Section -->
<div class="card border-0 shadow-sm rounded-3 mb-4">
  <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
    <div class="d-flex align-items-center gap-2">
      <div class="bg-primary-subtle text-primary px-2 py-1 rounded"><i class="bi bi-journal-bookmark-fill fs-5"></i></div>
      <h5 class="m-0 fw-bold text-dark" style="font-size: 1.1rem;">Library Members / Borrowers</h5>
    </div>
  </div>
  <div class="card-body pt-3">
    <div class="table-responsive">
      <table class="table table-hover align-middle datatable mb-0">
        <thead class="table-light text-secondary small text-uppercase">
          <tr>
            <th style="width: 50px;">#</th>
            <th>Name & Email</th>
            <th>Username</th>
            <th>Address</th>
            <th>Creation Date</th>
            <th class="text-end" style="width: 140px;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($standardUsers)): 
              $index = 1;
              foreach ($standardUsers as $row): 
                  $locStr = trim(($row['street'] ? $row['street'] . ', ' : '') . $row['barangay'] . ' ' . $row['city']);
          ?>
              <tr>
                <td class="text-muted small font-monospace"><?php echo $index++; ?></td>
                <td>
                  <div class="fw-bold text-dark fs-6"><?php echo htmlspecialchars($row['fullName']); ?></div>
                  <div class="text-muted small"><i class="bi bi-envelope-fill me-1"></i><?php echo htmlspecialchars($row['emailAddress']); ?></div>
                </td>
                <td>
                  <div class="badge bg-light text-dark border border-secondary-subtle px-2 py-1 mb-1">
                    <i class="bi bi-person-badge text-primary me-1"></i>@<?php echo htmlspecialchars($row['username']); ?>
                  </div>
                  <div><span class="badge bg-success-subtle text-success border border-success-subtle px-2">Member</span></div>
                </td>
                <td class="small text-wrap" style="max-width: 220px;">
                  <?php echo htmlspecialchars($locStr ?: 'No Address Added'); ?>
                </td>
                <td class="small text-secondary"><?php echo date('M d, Y', strtotime($row['dateCreated'])); ?></td>
                <td class="text-end">
                  <div class="d-flex justify-content-end gap-1">
                    <button class="btn btn-sm btn-outline-primary border-0 rounded-2 py-1 px-2" title="Edit Details"
                            data-bs-toggle="modal" data-bs-target="#editMemberModal"
                            data-fullname="<?php echo htmlspecialchars($row['fullName']); ?>"
                            data-username="<?php echo htmlspecialchars($row['username']); ?>"
                            data-email="<?php echo htmlspecialchars($row['emailAddress']); ?>"
                            data-role="<?php echo htmlspecialchars($row['role']); ?>"
                            data-street="<?php echo htmlspecialchars($row['street']); ?>"
                            data-barangay="<?php echo htmlspecialchars($row['barangay']); ?>"
                            data-city="<?php echo htmlspecialchars($row['city']); ?>">
                      <i class="bi bi-pencil-square fs-6"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger border-0 rounded-2 py-1 px-2 btn-delete-trigger" title="Delete User"
                            data-bs-toggle="modal" data-bs-target="#deleteMemberModal"
                            data-username="<?php echo htmlspecialchars($row['username']); ?>"
                            data-fullname="<?php echo htmlspecialchars($row['fullName']); ?>">
                      <i class="bi bi-trash3-fill fs-6"></i>
                    </button>
                  </div>
                </td>
              </tr>
          <?php endforeach; 
          else: ?>
              <tr><td colspan="6" class="text-center py-5 text-muted"><i class="bi bi-people fs-2 d-block mb-2 text-secondary"></i>No members found.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Library Admins Table Section -->
<div class="card border-0 shadow-sm rounded-3 mb-4">
  <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
    <div class="d-flex align-items-center gap-2">
      <div class="bg-dark-subtle text-dark px-2 py-1 rounded"><i class="bi bi-shield-lock-fill fs-5"></i></div>
      <h5 class="m-0 fw-bold text-dark" style="font-size: 1.1rem;">Library Admins & Staff</h5>
    </div>
  </div>
  <div class="card-body pt-3">
    <div class="table-responsive">
      <table class="table table-hover align-middle datatable mb-0">
        <thead class="table-light text-secondary small text-uppercase">
          <tr>
            <th style="width: 50px;">#</th>
            <th>Name & Email</th>
            <th>Username</th>
            <th>Address</th>
            <th>Creation Date</th>
            <th class="text-end" style="width: 140px;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($admins)): 
              $index = 1;
              foreach ($admins as $row): 
                  $locStr = trim(($row['street'] ? $row['street'] . ', ' : '') . $row['barangay'] . ' ' . $row['city']);
          ?>
              <tr>
                <td class="text-muted small font-monospace"><?php echo $index++; ?></td>
                <td>
                  <div class="fw-bold text-dark fs-6"><?php echo htmlspecialchars($row['fullName']); ?></div>
                  <div class="text-muted small"><i class="bi bi-envelope-fill me-1"></i><?php echo htmlspecialchars($row['emailAddress']); ?></div>
                </td>
                <td>
                  <div class="badge bg-light text-dark border border-secondary-subtle px-2 py-1 mb-1">
                    <i class="bi bi-person-workspace text-danger me-1"></i>@<?php echo htmlspecialchars($row['username']); ?>
                  </div>
                  <div><span class="badge bg-danger text-white px-2">Admin</span></div>
                </td>
                <td class="small text-wrap" style="max-width: 220px;">
                  <?php echo htmlspecialchars($locStr ?: 'Main Office'); ?>
                </td>
                <td class="small text-secondary"><?php echo date('M d, Y', strtotime($row['dateCreated'])); ?></td>
                <td class="text-end">
                  <div class="d-flex justify-content-end gap-1">
                    <button class="btn btn-sm btn-outline-primary border-0 rounded-2 py-1 px-2" title="Edit Details"
                            data-bs-toggle="modal" data-bs-target="#editMemberModal"
                            data-fullname="<?php echo htmlspecialchars($row['fullName']); ?>"
                            data-username="<?php echo htmlspecialchars($row['username']); ?>"
                            data-email="<?php echo htmlspecialchars($row['emailAddress']); ?>"
                            data-role="<?php echo htmlspecialchars($row['role']); ?>"
                            data-street="<?php echo htmlspecialchars($row['street']); ?>"
                            data-barangay="<?php echo htmlspecialchars($row['barangay']); ?>"
                            data-city="<?php echo htmlspecialchars($row['city']); ?>">
                      <i class="bi bi-pencil-square fs-6"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger border-0 rounded-2 py-1 px-2 btn-delete-trigger" title="Delete Admin"
                            data-bs-toggle="modal" data-bs-target="#deleteMemberModal"
                            data-username="<?php echo htmlspecialchars($row['username']); ?>"
                            data-fullname="<?php echo htmlspecialchars($row['fullName']); ?>">
                      <i class="bi bi-trash3-fill fs-6"></i>
                    </button>
                  </div>
                </td>
              </tr>
          <?php endforeach; 
          else: ?>
              <tr><td colspan="6" class="text-center py-5 text-muted"><i class="bi bi-shield-slash fs-2 d-block mb-2 text-secondary"></i>No admin accounts found.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- MODAL: Create New Member -->
<div class="modal fade" id="createMemberModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <form action="/kmkdt-Library/app/controller/adminController.php" method="POST" class="modal-content border-0 shadow rounded-3">
      <input type="hidden" name="action" value="create">
      
      <div class="modal-header text-white border-0 py-3" style="background-color: #5cb85c;">
        <h5 class="modal-title fw-bold text-white d-flex align-items-center gap-2">
          <i class="bi bi-person-plus-fill fs-5"></i> Create New Registry Entry
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      
      <div class="modal-body p-4">
        <div class="row g-3 mb-3">
          <div class="col-md-6">
            <label class="form-label small fw-bold text-secondary text-uppercase">Full Legal Name</label>
            <input type="text" name="fullName" class="form-control rounded-2 border-secondary-subtle py-2" placeholder="e.g. John Doe" autocomplete="off" required>
          </div>
          <div class="col-md-6">
            <label class="form-label small fw-bold text-secondary text-uppercase">Unique System Username</label>
            <div class="input-group">
              <span class="input-group-text bg-white text-muted border-secondary-subtle">@</span>
              <input type="text" name="username" class="form-control rounded-2 border-secondary-subtle py-2" placeholder="admin1" autocomplete="off" required>
            </div>
          </div>
        </div>
        
        <div class="row g-3 mb-4">
          <div class="col-md-5">
            <label class="form-label small fw-bold text-secondary text-uppercase">Active Email Address</label>
            <input type="email" name="emailAddress" class="form-control rounded-2 border-secondary-subtle py-2" placeholder="john@example.com" autocomplete="off" required>
          </div>
          <div class="col-md-4">
            <label class="form-label small fw-bold text-secondary text-uppercase">Account Password</label>
            <input type="password" name="password" class="form-control rounded-2 border-secondary-subtle py-2" placeholder="••••••••" autocomplete="new-password">
          </div>
          <div class="col-md-3">
            <label class="form-label small fw-bold text-secondary text-uppercase">System Access Level</label>
            <select name="role" class="form-select rounded-2 border-secondary-subtle py-2">
              <option value="user" selected> Borrowers</option>
              <option value="admin"> Admin / Staff</option>
            </select>
          </div>
        </div>
        
        <hr class="text-secondary opacity-25 my-4">
        
        <div class="d-flex align-items-center gap-2 mb-3">
          <i class="bi bi-geo-alt-fill text-danger fs-5"></i>
          <h6 class="fw-bold m-0 text-dark" style="font-size: 0.95rem;">Residential/Location Profile</h6>
        </div>
        
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label small fw-bold text-secondary text-uppercase">Street / House No.</label>
            <input type="text" name="street" class="form-control rounded-2 border-secondary-subtle py-2" placeholder="123 Main St.">
          </div>
          <div class="col-md-4">
            <label class="form-label small fw-bold text-secondary text-uppercase">Barangay</label>
            <input type="text" name="barangay" class="form-control rounded-2 border-secondary-subtle py-2" placeholder="Barangay 12">
          </div>
          <div class="col-md-4">
            <label class="form-label small fw-bold text-secondary text-uppercase">City / Municipality</label>
            <input type="text" name="city" class="form-control rounded-2 border-secondary-subtle py-2" placeholder="Cagayan de Oro">
          </div>
        </div>
      </div>
      
      <div class="modal-footer bg-white border-0 pt-0 pb-4 pe-4">
        <button type="button" class="btn btn-outline-secondary px-4 py-2 fw-semibold rounded-2" data-bs-dismiss="modal" style="border-color: #ccc; color: #666;">Cancel</button>
        <button type="submit" class="btn text-white px-4 py-2 fw-semibold rounded-2" style="background-color: #5cb85c; border-color: #4cae4c;">Save Entry</button>
      </div>
    </form>
  </div>
</div>

<!-- MODAL: Edit Member Details -->
<div class="modal fade" id="editMemberModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <form action="/kmkdt-Library/app/controller/adminController.php" method="POST" class="modal-content border-0 shadow rounded-3">
      <input type="hidden" name="action" value="update">
      <input type="hidden" name="username" id="edit_username_hidden">
      
      <div class="modal-header bg-dark text-white border-0 py-3">
        <h5 class="modal-title fw-bold text-white d-flex align-items-center gap-2">
          <i class="bi bi-pencil-square fs-5"></i> Update Registry Entry
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      
      <div class="modal-body p-4">
        <div class="row g-3 mb-3">
          <div class="col-md-6">
            <label class="form-label small fw-bold text-secondary text-uppercase">Full Legal Name</label>
            <input type="text" name="fullName" id="edit_fullName" class="form-control rounded-2 border-secondary-subtle py-2" required>
          </div>
          <div class="col-md-6">
            <label class="form-label small fw-bold text-secondary text-uppercase">Unique System Username (Locked)</label>
            <div class="input-group">
              <span class="input-group-text bg-light text-muted border-secondary-subtle">@</span>
              <input type="text" id="edit_username_display" class="form-control rounded-2 bg-light text-muted py-2" disabled>
            </div>
          </div>
        </div>
        
        <div class="row g-3 mb-4">
          <div class="col-md-8">
            <label class="form-label small fw-bold text-secondary text-uppercase">Active Email Address</label>
            <input type="email" name="emailAddress" id="edit_emailAddress" class="form-control rounded-2 border-secondary-subtle py-2" required>
          </div>
          <div class="col-md-4">
            <label class="form-label small fw-bold text-secondary text-uppercase">System Access Level</label>
            <select name="role" id="edit_role" class="form-select rounded-2 border-secondary-subtle py-2">
              <option value="user">Standard Patron</option>
              <option value="admin">Library Admin / Staff</option>
            </select>
          </div>
        </div>
        
        <hr class="text-secondary opacity-25 my-4">
        
        <div class="d-flex align-items-center gap-2 mb-3">
          <i class="bi bi-geo-alt-fill text-danger fs-5"></i>
          <h6 class="fw-bold m-0 text-dark" style="font-size: 0.95rem;">Residential/Location Profile</h6>
        </div>
        
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label small fw-bold text-secondary text-uppercase">Street / House No.</label>
            <input type="text" name="street" id="edit_street" class="form-control rounded-2 border-secondary-subtle py-2">
          </div>
          <div class="col-md-4">
            <label class="form-label small fw-bold text-secondary text-uppercase">Barangay</label>
            <input type="text" name="barangay" id="edit_barangay" class="form-control rounded-2 border-secondary-subtle py-2">
          </div>
          <div class="col-md-4">
            <label class="form-label small fw-bold text-secondary text-uppercase">City / Municipality</label>
            <input type="text" name="city" id="edit_city" class="form-control rounded-2 border-secondary-subtle py-2">
          </div>
        </div>
      </div>
      
      <div class="modal-footer bg-white border-0 pt-0 pb-4 pe-4">
        <button type="button" class="btn btn-outline-secondary px-4 py-2 fw-semibold rounded-2" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-dark px-4 py-2 fw-semibold rounded-2">Save Changes</button>
      </div>
    </form>
  </div>
</div> 

<!-- MODAL: Confirm Deletion (Moved outside of edit modal container) -->
<div class="modal fade" id="deleteMemberModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form action="/kmkdt-Library/app/controller/adminController.php" method="POST" class="modal-content border-0 shadow rounded-3">
      <input type="hidden" name="action" value="delete">
      <input type="hidden" name="username" id="delete_username">
      
      <div class="modal-header bg-danger text-white border-0 py-3">
        <h5 class="modal-title fw-bold text-white d-flex align-items-center gap-2">
          <i class="bi bi-trash3-fill fs-5"></i> Confirm Deletion
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      
      <div class="modal-body p-4 text-center">
        <i class="bi bi-exclamation-triangle text-warning fs-1"></i>
        <p class="mt-3 mb-1">Are you sure you want to delete this user?</p>
        <h5 class="fw-bold text-dark" id="delete_fullname_display"></h5>
        <div class="text-muted fw-semibold" id="delete_username_display"></div>
        <p class="text-danger small mt-3">This action cannot be undone.</p>
      </div>
      
      <div class="modal-footer bg-light border-0 py-3">
        <button type="button" class="btn btn-outline-secondary px-4 py-2" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-danger px-4 py-2 fw-bold">Delete Permanently</button>
      </div>
    </form>
  </div>
</div>

<?php include('./includes/footer.php'); ?>