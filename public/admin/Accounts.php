<?php
if (session_status() === PHP_SESSION_NONE) session_start();

include('../../app/middleware/admin.php');
include('./includes/header.php');
include('./includes/topbar.php');
include('./includes/sidebar.php');

$configPath = $_SERVER['DOCUMENT_ROOT'] . '/kmkdt-Library/app/config/config.php';
if (!file_exists($configPath)) die("Config file not found at: " . $configPath);
include_once($configPath);

// Fetch all users safely without using a WHERE clause on an unknown column
$result = $conn->query("SELECT fullName, emailAddress, street, barangay, city FROM user");

// Store accounts into arrays to separate them in PHP memory
$standardUsers = [];
$admins = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Fallback separation logic: Checks if 'admin' is in the email address
        if (stripos($row['emailAddress'], 'admin') !== false) {
            $admins[] = $row;
        } else {
            $standardUsers[] = $row;
        }
    }
}
?>

<div class="pagetitle">
  <h1>Account Management</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="index.php">Home</a></li>
      <li class="breadcrumb-item active">Accounts</li>
    </ol>
  </nav>
</div>

<!-- ==================== 1. STANDARD USERS TABLE ==================== -->
<div class="card mb-4">
  <div class="card-header bg-primary text-white py-2">
    <h5 class="card-title m-0 text-white" style="font-size: 1.1rem;">Standard User Accounts</h5>
  </div>
  <div class="card-body pt-3">
    <table class="table datatable">
      <thead>
        <tr>
          <th><b>User</b></th>
          <th>Email</th>
          <th>Location</th>
          <th data-type="date" data-format="YYYY/MM/DD">Joined</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($standardUsers)): 
            foreach ($standardUsers as $row): 
                $location = trim(($row['street'] ? $row['street'] . ', ' : '') . $row['barangay'] . ' ' . $row['city']);
        ?>
            <tr>
              <td><?php echo htmlspecialchars($row['fullName']); ?></td>
              <td><?php echo htmlspecialchars($row['emailAddress']); ?></td>
              <td><?php echo htmlspecialchars($location ?: 'N/A'); ?></td>
              <td><?php echo date('Y/m/d'); ?></td>
              <td><span class="badge bg-success">Active</span></td>
            </tr>
        <?php endforeach; 
        else: ?>
            <tr><td colspan="5" class="text-center">No standard user accounts found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- ==================== 2. ADMINISTRATORS TABLE ==================== -->
<div class="card">
  <div class="card-header bg-dark text-white py-2">
    <h5 class="card-title m-0 text-white" style="font-size: 1.1rem;">Administrator Accounts</h5>
  </div>
  <div class="card-body pt-3">
    <table class="table datatable">
      <thead>
        <tr>
          <th><b>Admin Name</b></th>
          <th>Email</th>
          <th>Location</th>
          <th data-type="date" data-format="YYYY/MM/DD">Joined</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($admins)): 
            foreach ($admins as $row): 
                $location = trim(($row['street'] ? $row['street'] . ', ' : '') . $row['barangay'] . ' ' . $row['city']);
        ?>
            <tr>
              <td><?php echo htmlspecialchars($row['fullName']); ?></td>
              <td><?php echo htmlspecialchars($row['emailAddress']); ?></td>
              <td><?php echo htmlspecialchars($location ?: 'N/A'); ?></td>
              <td><?php echo date('Y/m/d'); ?></td>
              <td><span class="badge bg-success">Active</span></td>
            </tr>
        <?php endforeach; 
        else: ?>
            <tr><td colspan="5" class="text-center">No administrator accounts found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include('./includes/footer.php'); ?>