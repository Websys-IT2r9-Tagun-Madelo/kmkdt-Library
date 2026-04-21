<?php
include('../../app/middleware/admin.php');
include('./includes/header.php');
include('./includes/topbar.php');
include('./includes/sidebar.php');
?>
    <div class="pagetitle">
      <h1>Accounts</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item active">Accounts</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

<!-- Table with stripped rows -->
<table class="table datatable">
  <thead>
    <tr>
      <th><b>U</b>ser</th>
      <th>Email</th>
      <th>Location</th>
      <th data-type="date" data-format="YYYY/MM/DD">Joined</th>
      <th>Status</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>Carlos Sainz</td>
      <td>carlos@gmail.com</td>
      <td>Spain</td>
      <td>2021/09/20</td>
      <td>Verified</td>
    </tr>
    <tr>
      <td>Lewis Hamilton</td>
      <td>lewis@gmail.com</td>
      <td>United Kingdom</td>
      <td>2020/03/15</td>
      <td>Verified</td>
    </tr>
    <tr>
      <td>Max Verstappen</td>
      <td>max@gmail.com</td>
      <td>Netherlands</td>
      <td>2021/06/10</td>
      <td>Active</td>
    </tr>
    <tr>
      <td>Charles Leclerc</td>
      <td>charles@gmail.com</td>
      <td>Monaco</td>
      <td>2022/01/22</td>
      <td>Active</td>
    </tr>
    <tr>
      <td>Lando Norris</td>
      <td>lando@gmail.com</td>
      <td>United Kingdom</td>
      <td>2023/02/11</td>
      <td>Pending</td>
    </tr>
    <tr>
      <td>Fernando Alonso</td>
      <td>fernando@gmail.com</td>
      <td>Spain</td>
      <td>2019/08/30</td>
      <td>Verified</td>
    </tr>


    <tr>
      <td>Monkey D. Luffy</td>
      <td>luffy@pirate.com</td>
      <td>Grand Line</td>
      <td>2024/01/01</td>
      <td>Active</td>
    </tr>
    <tr>
      <td>Roronoa Zoro</td>
      <td>zoro@pirate.com</td>
      <td>East Blue</td>
      <td>2023/07/19</td>
      <td>Verified</td>
    </tr>
    <tr>
      <td>Nami</td>
      <td>nami@pirate.com</td>
      <td>Cocoyasi Village</td>
      <td>2023/09/25</td>
      <td>Active</td>
    </tr>
    <tr>
      <td>Sanji</td>
      <td>sanji@pirate.com</td>
      <td>Baratie</td>
      <td>2022/12/05</td>
      <td>Active</td>
    </tr>
    <tr>
      <td>Tony Tony Chopper</td>
      <td>chopper@pirate.com</td>
      <td>Drum Island</td>
      <td>2023/04/18</td>
      <td>Pending</td>
    </tr>
  </tbody>
</table>
<!-- End Table -->
<?php
include('./includes/footer.php');
?>