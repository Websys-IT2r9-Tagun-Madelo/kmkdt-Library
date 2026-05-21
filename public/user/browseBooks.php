<?php
session_start();

require_once dirname(__DIR__, 2) . '/app/controller/userController.php';

$currentUserId = $_SESSION['user_id'] ?? null;

$search = $_GET['search'] ?? '';
$booksResult = getAllBooks($conn, $search);

include_once 'includes/header.php';
include_once 'includes/tsbar.php';
?>

<div class="page-wrapper">

    <!-- Banner -->
    <header class="hero-banner text-center text-white">
        <div class="container">

            <div class="d-flex align-items-center justify-content-center gap-3 mb-3">
                <div class="primary-spinning-leaf"></div>
                <h1 class="mb-0 fs-14 text-white lh-1">Browse Books</h1>
            </div>

            <p class="lead opacity-75 mx-auto">
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

                            <input 
                                type="text" 
                                name="search" 
                                class="form-control border-0 bg-transparent py-3 fs-5"
                                placeholder="Title, author, or genre..."
                                value="<?= htmlspecialchars($search); ?>"
                            >

                            <button type="submit" class="btn btn-success rounded-pill px-4 ms-2 fw-bold">
                                Find Book
                            </button>
                        </div>

                    </form>
                </div>

                <!-- Quick Categories -->
                <div class="d-flex flex-wrap justify-content-center gap-2 mt-4">

                    <?php
                    $filters = [
                        '' => 'All',
                        'Fiction' => 'Fiction',
                        'Non-Fiction' => 'Non-Fiction',
                        'Research' => 'Research',
                        'Reserve' => 'Reserve',
                        'Online' => 'Online'
                    ];

                    foreach ($filters as $key => $label):
                        $active = ($search == $key) ? 'active' : '';
                        $link = ($key == '') ? '/kmkdt-Library/public/user/browseBooks' : "?search=$key";
                    ?>

                        <a href="<?= $link; ?>" class="filter-pill text-decoration-none <?= $active; ?>">
                            <?= $label; ?>
                        </a>

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
                    <?php while ($row = $booksResult->fetch_assoc()): 
                        $category = $row['category'] ?? 'General';
                        $genre = $row['genre'] ?? '';
                        
                        $isOnline = (stripos($category, 'Online') !== false);
                        $status = is_null($row['user_id']) ? 'available' : ($row['user_id'] == $currentUserId ? 'owned' : 'taken');
                        
                        if ($isOnline) { $loanPeriod = "Unlimited"; } 
                        elseif (stripos($category, 'Reserve') !== false) { $loanPeriod = "3 Days"; }
                        elseif (stripos($category, 'Non-Fiction') !== false) { $loanPeriod = "14 Days"; }  
                        elseif (stripos($category, 'Research') !== false) { $loanPeriod = "7 Days"; }
                        else { $loanPeriod = "18 Days"; }
                    ?>

                    <div class="col-sm-6 col-lg-4 col-xl-3">
                        <div class="modern-book-card h-100 d-flex flex-column"
                            data-bs-toggle="modal"
                            data-bs-target="#bookModal"
                            data-title="<?= htmlspecialchars($row['title']); ?>"
                            data-author="<?= htmlspecialchars($row['author']); ?>"
                            data-category="<?= htmlspecialchars($category); ?>"
                            data-genre="<?= htmlspecialchars($row['genre']); ?>"
                            data-desc="<?= htmlspecialchars($row['description'] ?? 'No description available.'); ?>"
                            data-img="assets/images/books/<?= htmlspecialchars($row['cover_image'] ?? 'default-cover.jpg'); ?>"
                            data-id="<?= $row['id']; ?>"
                            data-status="<?= $status; ?>"
                            data-online="<?= $isOnline ? 'true' : 'false'; ?>"
                            data-loan-period="<?= $loanPeriod; ?>">

                            <div class="img-container">
                                <img src="assets/images/books/<?= htmlspecialchars($row['cover_image'] ?? 'default-cover.jpg'); ?>" alt="Cover">
                            </div>

                            <div class="card-body-custom flex-grow-1 p-3">
                                <div class="d-flex justify-content-between align-items-center mb-2 w-100">
                                    
                                    <div class="d-flex align-items-center gap-1 text-truncate visual-badge-cap">
                                        <span class="badge-category"><?= htmlspecialchars($category); ?></span>
                                        
                                        <?php if (!empty($genre) && strtolower($genre) !== strtolower($category)): ?>
                                            <span class="badge-category"><?= htmlspecialchars($genre); ?></span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="loan-tag text-nowrap">
                                        <iconify-icon icon="lucide:clock"></iconify-icon> <?= $loanPeriod; ?>
                                    </div>
                                </div>
                                <h3 class="book-title h5 mb-1"><?= htmlspecialchars($row['title']); ?></h3>
                                <p class="text-muted small mb-2">by <?= htmlspecialchars($row['author']); ?></p>
                            </div>

                            <div class="card-footer-custom p-3 mt-auto">
                                <?php if ($isOnline): ?>
                                    <a href="/kmkdt-Library/public/user/eBook?id=<?= $row['id']; ?>" 
                                    class="btn rounded-pill w-100 mt-2 btn-read-online" 
                                    onclick="return confirm('Warning: Opening this may remove your previous e-book.');">
                                    Read Online</a>
                                <?php elseif ($status === 'available'): ?>
                                    <a href="/kmkdt-Library/app/controller/process/borrowProcess.php?id=<?= $row['id']; ?>" 
                                    class="btn btn-success w-100 rounded-pill fw-bold"
                                    onclick="return confirm('Are you sure you want to borrow this book for <?= $loanPeriod; ?>?');">
                                    Borrow Book
                                    </a>
                                <?php elseif ($status === 'owned'): ?>  
                                    <button class="btn btn-secondary w-100 rounded-pill disabled">In Your Shelf</button>
                                <?php else: ?>
                                    <button class="btn btn-light w-100 rounded-pill text-muted disabled">Unavailable</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-12 text-center py-5">
                        <iconify-icon icon="lucide:search-x" class="display-1 text-muted opacity-25"></iconify-icon>
                        <h3 class="text-muted mt-3">No matches found for "<?= htmlspecialchars($search) ?>"</h3>
                        <a href="/kmkdt-Library/public/user/browseBooks" class="btn btn-success rounded-pill px-4 py-2 fw-bold text-dark text-decoration-none shadow-sm">Clear filters</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

</div> 

<!-- Book Details Modal -->
<div class="modal fade" id="bookModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">

            <div class="modal-header border-0 bg-light">
                <h5 class="modal-title fw-bold" id="modalTitle">Book Overview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-4">
                <div class="row">

                    <div class="col-md-5 mb-3 text-center">
                        <img id="modalImg" src="" class="img-fluid rounded shadow" alt="Book Cover View">
                    </div>
                    
                    <div class="col-md-7">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <div id="modalCategory" class="d-flex align-items-center gap-2 mb-3"></div>
                            <div id="modalGenre" class="badge rounded-pill px-3 py-2 text-white"></div>
                        </div>
                        
                        <h2 id="modalBookTitle" class="fw-bold mb-1"></h2>
                        <p id="modalAuthor" class="text-muted fs-5 mb-3"></p>
                        <hr>
                        <h6 class="fw-bold">About this book</h6>
                        <p id="modalDesc" class="text-secondary"></p>
                    </div>

                </div>
            </div>

            <div class="modal-footer border-0 bg-light p-3">
                <div id="modalActionContainer" class="w-100"></div>
            </div>

        </div>
    </div>
</div>

<?php include('./includes/footer.php'); ?>