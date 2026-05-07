<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$currentUserId = $_SESSION['user_id'] ?? null;
require_once dirname(__DIR__, 2) . '/app/controller/userController.php';

$search = $_GET['search'] ?? '';
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

<style>
    :root {
        --primary: #32cd32;
        --primary-dark: #28a428;
        --bg-soft: #f4f7f6;
        --text-main: #2d3436;
        --text-muted: #636e72;
    }

    body { background-color: var(--bg-soft); color: var(--text-main); font-family: 'Inter', sans-serif; }

    /* Modern Hero Section */
    .hero-banner {
        background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('assets/images/backgrounds/BrowBoks.jpg');
        background-size: cover;
        background-position: center;
        padding: 120px 0 150px 0;
        clip-path: ellipse(150% 100% at 50% 0%);
    }

    /* Floating Search Bar */
    .search-wrapper {
        margin-top: -80px;
        position: relative;
        z-index: 5;
    }

    .search-glass {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(15px);
        border: 1px solid rgba(255, 255, 255, 0.4);
        border-radius: 30px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    }

    .form-control:focus { box-shadow: none; border-color: var(--primary); }

    /* Book Card Evolution */
    .modern-book-card {
        background: #fff;
        border: none;
        border-radius: 24px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        height: 100%;
        display: flex;
        flex-direction: column;
        padding: 20px;
    }

    .modern-book-card:hover {
        transform: translateY(-12px);
        box-shadow: 0 30px 60px rgba(0,0,0,0.12);
    }

    .img-container {
        position: relative;
        border-radius: 18px;
        overflow: hidden;
        aspect-ratio: 2/3;
        margin-bottom: 20px;
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }

    .img-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .modern-book-card:hover .img-container img { transform: scale(1.1); }

    /* Small components */
    .badge-category {
        background: #e8f5e9;
        color: var(--primary-dark);
        font-weight: 600;
        font-size: 0.75rem;
        padding: 6px 12px;
        border-radius: 10px;
    }

    .loan-tag {
        font-size: 0.8rem;
        color: var(--text-muted);
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .book-title {
        font-size: 1.1rem;
        font-weight: 700;
        line-height: 1.3;
        margin: 10px 0 5px 0;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        min-height: 2.6rem;
    }

    .btn-borrow {
        background: var(--text-main);
        color: white;
        border-radius: 12px;
        padding: 10px;
        font-weight: 600;
        width: 100%;
        margin-top: 15px;
        transition: 0.3s;
    }

    .btn-borrow:hover { background: var(--primary); color: white; border-color: var(--primary); }

    /* Filter Pill */
    .filter-pill {
        border: 1.5px solid #eee;
        background: white;
        color: var(--text-muted);
        padding: 8px 20px;
        font-size: 0.9rem;
        border-radius: 50px;
        transition: 0.3s;
    }

    .filter-pill.active, .filter-pill:hover {
        background: var(--primary);
        border-color: var(--primary);
        color: white;
    }
</style>

<div class="page-wrapper">
    <!-- Hero Header -->
    <header class="hero-banner text-center text-white">
        <div class="container">
            <!-- Leaf and Title Wrapper -->
            <div class="d-flex align-items-center justify-content-center gap-3 mb-3">
                <img src="../assets/images/svgs/primary-leaf.svg" alt="Leaf Icon" 
                    class="img-fluid animate-spin" 
                    style="width: 45px; filter: brightness(0) invert(1);"> 
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
                    $filters = ['' => 'All', 'Fiction' => 'Fiction', 'Non-Fiction' => 'Non-Fiction', 'Manga' => 'Manga', 'Technology' => 'Tech'];
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
                        <a href="BrowBoks" class="btn btn-link text-success">Clear filters</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
</div>

<?php loadInclude('footer.php', $searchPaths); ?>