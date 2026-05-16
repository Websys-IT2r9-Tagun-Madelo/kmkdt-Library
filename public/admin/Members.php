<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include('../../app/middleware/admin.php');

// 1. Fetch the explicit configuration path setup
$configPath = $_SERVER['DOCUMENT_ROOT'] . '/kmkdt-Library/app/config/config.php';
if (!file_exists($configPath)) {
    die("Config file not found at: " . $configPath);
}
include_once($configPath);

// 2. Query the entire table directly to avoid function definition limits
$query = "SELECT * FROM user";
$result = $conn->query($query);

$members = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        
        // 3. CRITICAL FALLBACK LOGIC
        // If 'username' key doesn't exist, check common naming variations
        if (!isset($row['username'])) {
            if (isset($row['userName']))   $row['username'] = $row['userName'];
            if (isset($row['user_name']))  $row['username'] = $row['user_name'];
        }

        // If it is still missing or completely empty/blank in the database, 
        // split their emailAddress (e.g., 'luffy@pirate.com' becomes 'luffy')
        if (empty($row['username']) && !empty($row['emailAddress'])) {
            $emailParts = explode('@', $row['emailAddress']);
            $row['username'] = $emailParts[0]; 
        }
        
        // Final fallback safeguard string
        if (empty($row['username'])) {
            $row['username'] = 'member';
        }

        $members[] = $row;
    }
}

include('./includes/header.php');
include('./includes/topbar.php');
include('./includes/sidebar.php');
?>
<div class="pagetitle">
  <h1>Members</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="Index">Home</a></li>
      <li class="breadcrumb-item active">Members</li>
    </ol>
  </nav>
</div><!-- End Page Title -->

<div class="card rounded-0"> 
  <div class="card-body">
    <h5 class="card-title">Library Members</h5>
    
    <table class="table table-hover align-middle">
      <thead class="table-light">
        <tr>
          <th scope="col">#</th>
          <th scope="col">Name</th>
          <th scope="col">Username</th>
          <th scope="col">Role</th>
          <th scope="col">Date Joined</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($members)): ?>
          <?php foreach ($members as $index => $row): ?>
            <tr>
              <th scope="row"><?php echo $index + 1; ?></th>
              <td><?php echo htmlspecialchars($row['fullName']); ?></td>
              <td>
                <span class="text-muted">@</span><?php echo htmlspecialchars($row['username']); ?>
              </td>
              <td>
                <span class="badge rounded-0"
                  style="background-color: <?php echo ($row['role'] == 'admin') ? '#32cd32' : '#6c757d'; ?>;">
                  <?php echo ucfirst($row['role']); ?>
                </span>
              </td>
              <td><?php echo date('M d, Y', strtotime($row['dateCreated'])); ?></td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="5" class="text-center">No members found.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include('./includes/footer.php'); ?>