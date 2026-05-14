<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once dirname(__DIR__, 2) . '/app/controller/userController.php'; 

// Handle the "Close" action to clear the sticky session
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
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" 
             style="background-color: #d4edda; color: #155724; border-radius: 15px;" role="alert">
            <iconify-icon icon="lucide:check-circle" class="me-2"></iconify-icon>
            <strong>Success!</strong> This book is now active in your E-book reader.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row justify-content-center">
        <!-- Main Reader Column -->
        <div class="col-lg-10 col-12">
            <div class="container text-center bg-white p-5 shadow" style="border-radius: 20px; border-top: 5px solid #32cd32;">
                <?php if ($book): ?>
                    <iconify-icon icon="lucide:book-open" class="display-4 mb-3" style="color: #32cd32;"></iconify-icon>
                    <h2 class="fw-bold"><?= htmlspecialchars($book['title']); ?></h2>
                    <p class="text-muted small">Author: <?= htmlspecialchars($book['author']); ?></p>
                    <hr class="my-4">
                    
                    <!-- Secure Viewer Placeholder -->
                    <div class="alert alert-light border">
                        <div class="mt-2 p-4 bg-light d-flex align-items-center justify-content-center" style="border: 2px dashed #ddd; border-radius: 10px; min-height: 550px;">
                             <div class="text-center">
                                <iconify-icon icon="line-md:loading-twotone-loop" style="font-size: 50px; color: #32cd32;"></iconify-icon>
                                <p class="mt-2 text-white-50">Loading Secure Digital Reader...</p>
                             </div>
                        </div>
                    </div>

                    <!-- Navigation Buttons -->
                    <div class="d-flex justify-content-center gap-3 mt-4">
                        <a href="eBook?action=close" class="btn btn-outline-secondary rounded-pill px-4">
                            Close Reader
                        </a>
                        <a href="browseBooks" class="btn btn-success rounded-pill px-5" style="background-color: #32cd32; border: none;">
                            Back to Library
                        </a>
                    </div>

                <?php else: ?>
                    <!-- Empty State -->
                    <iconify-icon icon="hugeicons:book-open-01" class="display-1 mb-4" style="color: #ccc;"></iconify-icon>
                    <h1 class="fw-bold text-secondary">No E-book Selected</h1>
                    <p class="text-muted">Choose a title from the library to begin reading.</p>
                    <a href="browseBooks" class="btn btn-success rounded-pill px-5 mt-4" style="background-color: #32cd32; border: none;">Browse Library</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>