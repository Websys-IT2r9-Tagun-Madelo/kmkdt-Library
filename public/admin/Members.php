<?php
include('../../app/middleware/admin.php');
$members = getAllMembers($conn);

include('./includes/header.php');
include('./includes/topbar.php');
include('./includes/sidebar.php');
?>
<div class="pagetitle">
  <h1>Members</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="index">Home</a></li>
      <li class="breadcrumb-item active">Members</li>
    </ol>
  </nav>
</div><!-- End Page Title -->

<div class="card rounded-0"> <!-- Maintained the requested non-rounded look -->
  <div class="card-body">
    <h5 class="card-title">Library Members</h5>
    
      <table class="table table-hover align-middle">
      <thead class="table-light">
        <tr>
          <th scope="col">#</th>
          <th scope="col">Name</th>
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
            <td colspan="4" class="text-center">No members found.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include('./includes/footer.php'); ?>