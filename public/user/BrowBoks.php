<?php

$currentUserId = $_SESSION['user_id'] ?? null;


require_once dirname(__DIR__, 2) . '/app/controller/userController.php';


$search = isset($_GET['search']) ? $_GET['search'] : '';
$booksResult = getAllBooks($conn, $search);

$root = dirname(__DIR__, 2);
$searchPaths = [
    $root . '/public/user/includes/', 
    $root . '/public/includes/',      
    $root . '/public/user/', 
];

function loadInclude($file, $paths) {
    foreach ($paths as $path) {
        if (file_exists($path . $file)) {
            include_once $path . $file;
            return true;
        }
    }
    return false;
}

loadInclude('header.php', $searchPaths);
loadInclude('tsbar.php', $searchPaths);
?>

<div class="page-wrapper overflow-hidden">
    <!-- Banner Section -->
    <section class="banner-section banner-inner-section position-relative overflow-hidden d-flex align-items-end"
        style="background-image: url('assets/images/backgrounds/BrowBoks.jpg'); min-height: 400px; background-size: cover;">
        <div class="container">
            <div class="d-flex flex-column gap-4 pb-5 pb-xl-10 position-relative z-1">
                <div class="row align-items-center">
                    <div class="col-xl-4">
                        <div class="d-flex align-items-center gap-4" data-aos="fade-up" data-aos-delay="100" data-aos-duration="1000">
                            <img src="../../assets/images/svgs/primary-leaf.svg" alt="" class="img-fluid animate-spin">
                            <p class="mb-0 text-white fs-5 text-opacity-70">
                                Explore our <span class="text-primary">library collection</span> and discover your next favorite book.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="d-flex align-items-end gap-3" data-aos="fade-up" data-aos-delay="200" data-aos-duration="1000">
                    <h1 class="mb-0 fs-16 text-white lh-1">Browse Books</h1>
                </div>
            </div>
        </div>
    </section>

    <section class="filter-section pt-5">
        <div class="container">

            <!-- Search Bar -->
            <div class="row g-4 align-items-center mb-5">
                <div class="col-md-5">
                    <form action="" method="GET" class="position-relative">
                        <input type="text" name="search" class="form-control rounded-pill py-3 ps-5" 
                               placeholder="Search..." value="<?php echo htmlspecialchars($search); ?>" 
                               style="border: 1px solid #32cd32;">
                        <button type="submit" class="btn position-absolute end-0 top-50 translate-middle-y me-2 bg-primary rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <iconify-icon icon="lucide:search" class="text-white"></iconify-icon>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Dynamic Results -->
            <div class="row">
                <?php if ($booksResult && $booksResult->num_rows > 0): ?>
                    <?php while($row = $booksResult->fetch_assoc()): ?>
                        <div class="col-12 mb-4">
                            <div class="p-4 rounded-4 shadow-sm d-flex justify-content-between align-items-center border-start border-5" 
                                 style="border-color: #32cd32 !important; background-color: #fff;">
                                <div>
                                    <div class="d-flex gap-2 mb-2">
                                        <small class="text-uppercase fw-bold text-muted"><?php echo htmlspecialchars($row['category'] ?? 'Book'); ?></small>
                                        <span class="badge rounded-pill px-3" style="background-color: #f0fdf4; color: #32cd32; border: 1px solid #32cd32;">
                                            <?php echo htmlspecialchars($row['genre'] ?? 'General'); ?>
                                        </span>
                                    </div>
                                    <h3 class="mb-1 h4"><?php echo htmlspecialchars($row['title']); ?></h3>
                                    <p class="mb-0 text-secondary">By <span class="fw-semibold"><?php echo htmlspecialchars($row['author']); ?></span></p>
                                </div>
                                <div class="text-end">
                                    <?php if (is_null($row['user_id'])): ?>
                                        <!-- CASE 1: Book is available -->
                                        <a href="./borrow_process?id=<?php echo $row['id']; ?>" 
                                        class="btn btn-dark rounded-pill px-4">Borrow Book</a>

                                    <?php elseif ($row['user_id'] == $currentUserId): ?>
                                        <!-- CASE 2: Borrowed by the current logged-in account (e.g., Nico Robin) -->
                                        <span class="badge bg-success rounded-pill px-4 py-2">Your Borrowed Book</span>

                                    <?php else: ?>
                                        <!-- CASE 3: Borrowed by a different account -->
                                        <button class="btn btn-secondary rounded-pill px-4" disabled>Currently Unavailable</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-12 text-center py-5">
                        <p class="text-muted">No books found matching your search.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
</div>

<?php
// FIX: Use loadInclude for footer too
loadInclude('footer.php', $searchPaths);
?>