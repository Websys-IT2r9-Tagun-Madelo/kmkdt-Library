<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Adjusted path to reach app/controller/userController.php
require_once dirname(__DIR__, 2) . '/app/controller/userController.php'; 

// Catch the ID from the URL
$bookId = $_GET['id'] ?? null;

// Attempt to fetch book details
$book = $bookId ? getBookForReader($conn, $bookId) : null; 

include('./includes/header.php');
include('./includes/tsbar.php');
?>

<div class="page-wrapper p-5">
    <div class="container text-center bg-white p-5 shadow" style="border-radius: 20px; border-top: 5px solid #57cb57;">
        
        <?php if ($book): ?>
            <iconify-icon icon="lucide:book-open" class="display-1 mb-4" style="color: #57cb57;"></iconify-icon>
            <h1 class="fw-bold"><?= htmlspecialchars($book['title']); ?></h1>
            <p class="lead text-muted">By <?= htmlspecialchars($book['author']); ?></p>
            <hr class="my-4">
            <div class="alert alert-light border">
                <p class="mb-0">Digital Content Loading for Book ID: <?= htmlspecialchars($bookId); ?></p>
                <div class="mt-3 p-5 bg-light border-dashed" style="border: 2px dashed #ddd; border-radius: 10px;">
                    <p>PDF/EPUB Viewer Integration Point</p>
                </div>
            </div>
            <a href="BrowBoks" class="btn btn-success rounded-pill px-5 mt-3" style="background-color: #57cb57; border: none;">
                Back to Library
            </a>

        <?php else: ?>
            
            <iconify-icon icon="hugeicons:book-open-01" class="display-1 mb-4" style="color: #ccc;"></iconify-icon>
            <h1 class="fw-bold text-secondary">No E-book Selected</h1>
            <p class="lead text-muted">It looks like you haven't selected a book to read yet or the link is invalid.</p>
            
            
            <div class="my-5 p-4 bg-dark text-white" style="border-radius: 15px;">
                <h5 class="fw-bold text-white">How to start reading:</h5>
                <ul class="list-unstyled mt-3">
                    <li class="mb-2">
                        <i class="fas fa-check-circle me-2" style="color: #57cb57;"></i> 
                        Go to the <strong>Browse Books</strong> section.
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check-circle me-2" style="color: #57cb57;"></i> 
                        Look for books categorized as <strong>Online</strong>.
                    </li>
                    <li>
                        <i class="fas fa-check-circle me-2" style="color: #57cb57;"></i> 
                        Click the <strong>Read Now</strong> button!
                    </li>
                </ul>
            </div>

            <a href="BrowBoks" class="btn btn-success rounded-pill px-5" style="background-color: #57cb57; border: none;">
                Go to Browse Books
            </a>
        <?php endif; ?>

    </div>
</div>

<?php include_once 'includes/footer.php'; ?>