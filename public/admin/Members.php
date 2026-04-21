<?php
include('../../app/middleware/admin.php');
include('./includes/header.php');
include('./includes/topbar.php');
include('./includes/sidebar.php');
?>

    <div class="pagetitle">
      <h1>Members</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item active">Members</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

<div class="card">
  <div class="card-body">
    <h5 class="card-title">Library Members</h5>
    <p>List of administrators and registered users in the library system.</p>

    <!-- Bordered Table -->
    <table class="table table-bordered">
      <thead>
        <tr>
          <th scope="col">#</th>
          <th scope="col">Name</th>
          <th scope="col">Role</th>
          <th scope="col">Status</th>
          <th scope="col">Date Joined</th>
        </tr>
      </thead>
      <tbody>

        <!-- Admin -->
        <tr>
          <th scope="row">1</th>
          <td>admin123</td>
          <td>Administrator</td>
          <td>Active</td>
          <td>2022-01-01</td>
        </tr>

        <!-- F1 Drivers -->
        <tr>
          <th scope="row">2</th>
          <td>Lewis Hamilton</td>
          <td>User</td>
          <td>Verified</td>
          <td>2020-03-15</td>
        </tr>
        <tr>
          <th scope="row">3</th>
          <td>Max Verstappen</td>
          <td>User</td>
          <td>Active</td>
          <td>2021-06-10</td>
        </tr>
        <tr>
          <th scope="row">4</th>
          <td>Charles Leclerc</td>
          <td>User</td>
          <td>Active</td>
          <td>2022-01-22</td>
        </tr>

        <!-- One Piece -->
        <tr>
          <th scope="row">5</th>
          <td>Monkey D. Luffy</td>
          <td>User</td>
          <td>Active</td>
          <td>2024-01-01</td>
        </tr>
        <tr>
          <th scope="row">6</th>
          <td>Roronoa Zoro</td>
          <td>User</td>
          <td>Verified</td>
          <td>2023-07-19</td>
        </tr>
        <tr>
          <th scope="row">7</th>
          <td>Nami</td>
          <td>User</td>
          <td>Active</td>
          <td>2023-09-25</td>
        </tr>
        <tr>
          <th scope="row">8</th>
          <td>Sanji</td>
          <td>User</td>
          <td>Active</td>
          <td>2022-12-05</td>
        </tr>
        <tr>
          <th scope="row">9</th>
          <td>Tony Tony Chopper</td>
          <td>User</td>
          <td>Pending</td>
          <td>2023-04-18</td>
        </tr>

      </tbody>
    </table>
    <!-- End Bordered Table -->

  </div>
</div>

<?php
include('./includes/footer.php');
?>