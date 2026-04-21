<?php
include('../../app/middleware/admin.php');
include('./includes/header.php');
include('./includes/topbar.php');
include('./includes/sidebar.php');
?>
<div class="pagetitle">
  <h1>Catalog</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="index.php">Home</a></li>
      <li class="breadcrumb-item active">Catalog</li>
    </ol>
  </nav>
</div><!-- End Page Title -->

<!-- Main Catalog -->
<div class="col-lg-12">
  <div class="card">
    <div class="card-body">

      <h5 class="card-title">Library Catalog</h5>

      <!-- Search -->
      <input type="text" id="searchInput" class="form-control mb-3" placeholder="Search books...">

      <!-- Category Filter -->
      <select id="categoryFilter" class="form-select mb-3">
        <option value="">All Categories</option>
        <option value="Technology">Technology</option>
        <option value="History">History</option>
        <option value="Sports">Sports</option>
        <option value="Manga">Manga</option>
      </select>

      <!-- Catalog Table -->
      <table class="table table-borderless" id="catalogTable">
        <thead>
          <tr>
            <th>#</th>
            <th>Title</th>
            <th>Author</th>
            <th>Category</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>

          <tr>
            <td>1</td>
            <td>Introduction to Programming</td>
            <td>John Smith</td>
            <td>Technology</td>
            <td><span class="badge bg-success">Available</span></td>
          </tr>

          <tr>
            <td>2</td>
            <td>World History</td>
            <td>Maria Lopez</td>
            <td>History</td>
            <td><span class="badge bg-warning">Borrowed</span></td>
          </tr>

          <tr>
            <td>3</td>
            <td>Formula 1 Racing Guide</td>
            <td>Various Authors</td>
            <td>Sports</td>
            <td><span class="badge bg-success">Available</span></td>
          </tr>

          <tr>
            <td>4</td>
            <td>One Piece Vol. 1</td>
            <td>Eiichiro Oda</td>
            <td>Manga</td>
            <td><span class="badge bg-warning">Borrowed</span></td>
          </tr>

        </tbody>
      </table>

    </div>
  </div>
</div>

<!-- Recent Activity -->
<div class="col-lg-12">
  <div class="card">
    <div class="card-body">

      <h5 class="card-title">Recent Activity</h5>

      <div class="activity">

        <!-- Item -->
        <div class="activity-item d-flex align-items-start mb-3">
          <div class="activity-icon bg-success text-white rounded-circle me-3">
            <i class="bi bi-book"></i>
          </div>
          <div>
            <div class="small text-muted">2 mins ago</div>
            <div>
              Zoro borrowed <strong>One Piece Vol. 1</strong>
            </div>
          </div>
        </div>

        <!-- Item -->
        <div class="activity-item d-flex align-items-start mb-3">
          <div class="activity-icon bg-primary text-white rounded-circle me-3">
            <i class="bi bi-arrow-return-left"></i>
          </div>
          <div>
            <div class="small text-muted">10 mins ago</div>
            <div>
              Carlos returned <strong>World History</strong>
            </div>
          </div>
        </div>

        <!-- Item -->
        <div class="activity-item d-flex align-items-start">
          <div class="activity-icon bg-warning text-white rounded-circle me-3">
            <i class="bi bi-plus-circle"></i>
          </div>
          <div>
            <div class="small text-muted">1 hour ago</div>
            <div>
              New book added: <strong>Formula 1 Racing Guide</strong>
            </div>
          </div>
        </div>

      </div>

    </div>
  </div>
</div>

</div>
</section>

<?php
include('./includes/footer.php');
?>