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
                    
                    <!-- Secure Viewer Embed Section -->
                    <div class="alert alert-light border p-0 overflow-hidden">
                        <div class="d-flex align-items-center justify-content-center secure-viewer-viewport" style="min-height: 600px; background: #525659;">
                            <?php if (!empty($book['ebook_file'])): ?>
                                <?php 
                                    // Set up the dynamic relative path pointing to your uploads folder
                                    $filePath = '/kmkdt-Library/app/uploads/ebooks/' . $book['ebook_file']; 
                                ?>
                                <!-- Embedded Secure PDF Viewer (Disables toolbars and sidebars to mitigate easy copies) -->
                                <iframe src="<?= htmlspecialchars($filePath); ?>#toolbar=0&navpanes=0" width="100%" height="650px" style="border: none;"></iframe>
                            <?php else: ?>
                                <!-- Fallback display state if an admin has not uploaded a digital copy yet -->
                                <div class="text-white p-5 text-center">
                                    <iconify-icon icon="lucide:alert-triangle" class="display-4 text-warning mb-3"></iconify-icon>
                                    <h4 class="fw-bold">No Digital File Available</h4>
                                    <p class="small text-light opacity-75">This item is currently only available as a physical copy in the library catalog.</p>
                                </div>
                            <?php endif; ?>
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

<?php include_once 'includes/footer.php'; ?>