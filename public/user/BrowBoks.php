<?php
session_start();

require_once dirname(__DIR__, 2) . '/app/controller/userController.php';

$search = $_GET['search'] ?? '';
$booksResult = getAllBooks($conn, $search);

include_once 'includes/header.php';
include_once 'includes/tsbar.php';
?>

<div class="page-wrapper">
    <!--Banner -->
    <header class="hero-banner text-center text-white">
        <div class="container">
            <!-- Leaf and Title Wrapper -->
            <div class="d-flex align-items-center justify-content-center gap-3 mb-3">
                <div class="primary-spinning-leaf"></div>
                <h1 class="mb-0 fs-14 text-white lh-1">Browse Books</h1>
            </div>
            
            <p class="lead opacity-75 mx-auto" style="max-width: 600px;">
                Access thousands of books from our digital and physical library.
            </p>
        </div>
    </header>

    <!-- Search & Filter Area -->
    <div class="container search-wrapper">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-8">
                <div class="search-glass p-3">
                    <form action="" method="GET">
                        <div class="input-group">
                            <span class="input-group-text bg-transparent border-0 ps-3">
                                <iconify-icon icon="lucide:search" class="fs-4 text-muted"></iconify-icon>
                            </span>
                            <input type="text" name="search" class="form-control border-0 bg-transparent py-3 fs-5" 
                                   placeholder="Title, author, or genre..." value="<?= htmlspecialchars($search); ?>">
                            <button type="submit" class="btn btn-success rounded-pill px-4 ms-2 fw-bold">Find Book</button>
                        </div>
                    </form>
                </div>
                
                <!-- Quick Categories -->
                <div class="d-flex flex-wrap justify-content-center gap-2 mt-4">
                    <?php 
                    $filters = ['' => 'All', 'Fiction' => 'Fiction', 'Non-Fiction' => 'Non-Fiction', 'Research' => 'Research', 'Online' => 'Online'];
                    foreach($filters as $key => $label): 
                        $active = ($search == $key) ? 'active' : '';
                        $link = ($key == '') ? 'BrowBoks' : "?search=$key";
                    ?>
                        <a href="<?= $link ?>" class="filter-pill text-decoration-none <?= $active ?>"><?= $label ?></a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Results Grid -->
    <section class="py-5 mt-5">
        <div class="container">
            <div class="row g-4">
                <?php if ($booksResult && $booksResult->num_rows > 0): ?>
                    <?php while($row = $booksResult->fetch_assoc()): 
                        $genre = $row['genre'] ?? 'General';
                        $loanPeriod = (stripos($genre, 'Fiction') !== false || stripos($genre, 'Manga') !== false) ? "30 Days" : "14 Days";
                        if (stripos($genre, 'Reserve') !== false) $loanPeriod = "3 Days";
                    ?>
                        <div class="col-sm-6 col-lg-4 col-xl-3">
                            <div class="modern-book-card">
                                <div class="img-container">
                                    <img src="assets/images/books/<?= htmlspecialchars($row['cover_image'] ?? 'default-cover.jpg'); ?>" alt="Cover">
                                </div>
                                
                                <div class="card-body-custom flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <span class="badge-category"><?= htmlspecialchars($row['category'] ?? 'Book'); ?></span>
                                        <div class="loan-tag">
                                            <iconify-icon icon="lucide:clock"></iconify-icon> <?= $loanPeriod ?>
                                        </div>
                                    </div>
                                    
                                    <h3 class="book-title"><?= htmlspecialchars($row['title']); ?></h3>
                                    <p class="text-muted small mb-0">by <?= htmlspecialchars($row['author']); ?></p>
                                </div>

                                <div class="card-footer-custom">
                                    <?php if (is_null($row['user_id'])): ?>
                                        <a href="process/borrow_process?id=<?= $row['id']; ?>" 
                                           class="btn btn-borrow text-decoration-none d-block text-center"
                                           onclick="return confirm('Borrow for <?= $loanPeriod ?>?')">Borrow Book</a>
                                    <?php elseif ($row['user_id'] == $currentUserId): ?>
                                        <button class="btn btn-success w-100 rounded-pill disabled mt-3 py-2">Owned</button>
                                    <?php else: ?>
                                        <button class="btn btn-light w-100 rounded-pill text-muted disabled mt-3 py-2">Unavailable</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-12 text-center py-5">
                        <iconify-icon icon="lucide:search-x" class="display-1 text-muted opacity-25"></iconify-icon>
                        <h3 class="text-muted mt-3">No matches found for "<?= htmlspecialchars($search) ?>"</h3>
                        <a href="BrowBoks" class="btn btn-outline-success py-3 fw-bold my-4 fs-4 px-5 d-inline-flex align-items-center justify-content-center">Clear filters</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
</div>

<?php
include('./includes/footer.php');
?>