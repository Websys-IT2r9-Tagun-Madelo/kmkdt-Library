<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include('../../app/middleware/admin.php');

$configPath = $_SERVER['DOCUMENT_ROOT'] . '/kmkdt-Library/app/config/config.php';
if (!file_exists($configPath)) {
    die("Config file not found at: " . $configPath);
}
include_once($configPath);

// Simplified structured lookup array query
$query = "SELECT fullName, username, role, dateCreated FROM user ORDER BY fullName ASC";
$result = $conn->query($query);

include('./includes/header.php');
include('./includes/topbar.php');
include('./includes/sidebar.php');
?>
<div class="pagetitle">
  <h1>Members</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="index">Home</a></li>
      <li class="breadcrumb-item">Library Management</li>
      <li class="breadcrumb-item active">Members</li>
    </ol>
  </nav>
</div>

<div class="card rounded-0 shadow-sm border-0"> 
  <div class="card-body">
    <h5 class="card-title">Library Members</h5>
    
    <table class="table table-hover align-middle">
      <thead class="table-light">
        <tr>
          <th scope="col" style="width: 60px;">#</th>
          <th scope="col">Name</th>
          <th scope="col">Username</th>
          <th scope="col">Role</th>
          <th scope="col">Date Joined</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($result && $result->num_rows > 0): 
            $index = 1;
            while ($row = $result->fetch_assoc()): 
        ?>
            <tr>
              <th scope="row" class="text-muted"><?php echo $index++; ?></th>
              <td class="fw-semibold text-dark"><?php echo htmlspecialchars($row['fullName']); ?></td>
              <td>
                <span class="text-muted small">@</span><?php echo htmlspecialchars($row['username']); ?>
              </td>
              <td>
                <span class="badge rounded-0 px-3 py-1"
                  style="background-color: <?php echo ($row['role'] === 'admin') ? '#198754' : '#6c757d'; ?>;">
                  <?php echo ucfirst($row['role']); ?>
                </span>
              </td>
              <td class="small text-secondary"><?php echo date('M d, Y', strtotime($row['dateCreated'])); ?></td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr>
            <td colspan="5" class="text-center py-4 text-muted">No library registry profiles found.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include('./includes/footer.php'); ?>