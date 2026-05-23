<?php

require_once dirname(__DIR__, 2) . '/app/controller/userController.php'; 

if (isset($_GET['action']) && $_GET['action'] === 'close') {
    unset($_SESSION['current_reading_id']); 
    header("Location: browseBooks"); 
    exit();
}

$bookId = $_GET['id'] ?? null;

if ($bookId) {
    $_SESSION['current_reading_id'] = $bookId;
}

$book = $bookId ? getBookForReader($conn, $bookId) : null; 

include('./includes/header.php');
include('./includes/tsbar.php');
?>

<div class="page-wrapper p-5">
    <!-- Success Alert -->
    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'added'): ?>
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4 reader-success-alert" role="alert">
            <iconify-icon icon="lucide:check-circle" class="me-2"></iconify-icon>
            <strong>Success!</strong> This book is now active in your E-book reader.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row justify-content-center">
        <!-- Main Reader Column -->
        <div class="col-lg-10 col-12">
            <div class="container text-center p-5 shadow reader-main-container">
                <?php if ($book): ?>
                    <iconify-icon icon="lucide:book-open" class="display-4 mb-3 reader-icon"></iconify-icon>
                    <h2 class="fw-bold"><?= htmlspecialchars($book['title']); ?></h2>
                    <p class="text-muted small">Author: <?= htmlspecialchars($book['author']); ?></p>
                    <hr class="my-4">
                    
                    <!-- Secure Viewer Placeholder -->
                    <div class="alert alert-light border">
                        <div class="mt-2 p-4 d-flex align-items-center justify-content-center secure-viewer-viewport">
                             <div class="text-center">
                                <iconify-icon icon="line-md:loading-twotone-loop" class="loading-spinner"></iconify-icon>
                                <p class="mt-2 text-dark-50">Loading Secure Digital Reader...</p>
                             </div>
                        </div>
                    </div>

                    <!-- Navigation Buttons -->
                    <div class="d-flex justify-content-center gap-3 mt-4">
                        <a href="eBook?action=close" class="btn btn-outline-secondary rounded-pill px-4">
                            Close Reader
                        </a>
                        <a href="browseBooks" class="btn btn-success rounded-pill px-5 btn-action-library">
                            Back to Library
                        </a>
                    </div>

                <?php else: ?>
                    <!-- Empty State -->
                    <div class="reader-empty-state">
                        <iconify-icon icon="hugeicons:book-open-01" class="display-1 mb-4 empty-icon"></iconify-icon>
                        <h1 class="fw-bold text-secondary">No E-book Selected</h1>
                        <p class="text-muted">Choose a title from the library to begin reading.</p>
                        <a href="browseBooks" class="btn btn-success rounded-pill px-5 mt-4 btn-browse-trigger">Browse Library</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
</div>

<?php include_once 'includes/footer.php'; ?>