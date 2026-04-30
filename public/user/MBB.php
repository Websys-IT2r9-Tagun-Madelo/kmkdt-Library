<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once dirname(__DIR__, 2) . '/app/controller/userController.php';

$authUser = $_SESSION['authUser'] ?? null;
if (!$authUser) {
    header("Location: /kmkdt-Library/public/login");
    exit();
}

$currentUserId = $authUser['user_id'];
$myBooks = getMyBooks($conn, $currentUserId);

include('./includes/header.php');
include('./includes/tsbar.php');
?>

<div class="page-wrapper overflow-hidden">
    <!-- Banner Section -->
    <section class="banner-section banner-inner-section position-relative overflow-hidden d-flex align-items-end"
        style="background-image: url('assets/images/backgrounds/MMB.jpg');">
            <div class="container">
      <div class="d-flex flex-column gap-4 pb-8 position-relative z-1">
        <div class="row align-items-center">
          <div class="col-xl-4">
            <div class="d-flex align-items-center gap-4" data-aos="fade-up" data-aos-delay="100"
              data-aos-duration="1000">
              <img src="../assets/images/svgs/primary-leaf.svg" alt="" class="img-fluid animate-spin">
            </div>
          </div>
        </div>
            <div class="d-flex flex-column gap-4 pb-5 pb-xl-10 position-relative z-1">
                <div class="d-flex align-items-end gap-3" data-aos="fade-up" data-aos-delay="200">
                    <h1 class="mb-0 fs-16 text-white lh-1">My Borrowed Books</h1>
                </div>
            </div>
        </div>
    </section>

    <!-- Project Section -->
    <section class="project py-5 py-lg-11">
        <div class="container">
            <div class="row">
                <?php if ($myBooks && $myBooks->num_rows > 0): ?>
                    <?php while($book = $myBooks->fetch_assoc()): ?>
                        <div class="col-lg-6 mb-7">
                            <div class="portfolio d-flex flex-column gap-4 shadow-sm p-3 rounded-4 bg-white" data-aos="fade-up">
                                
                                
                                <div class="portfolio-img position-relative overflow-hidden rounded-4" style="background-color: #f8f9fa;">
                                    <img src="assets/images/books/<?php echo htmlspecialchars($book['cover_image'] ?? 'one-piece-vol-1.jpg'); ?>" 
                                         alt="Book Cover" 
                                         class="img-fluid w-100" 
                                         style="height: 500px; object-fit: contain; object-position: center;">
                                    
                                    <div class="portfolio-overlay">
                                        <a href="../../app/controller/userController.php?action=renew&book_id=<?php echo $book['id'] ?? 0; ?>" 
                                           class="position-absolute top-50 start-50 translate-middle bg-primary round-64 rounded-circle hstack justify-content-center">
                                            <iconify-icon icon="lucide:refresh-cw" class="fs-8 text-dark"></iconify-icon>
                                        </a>
                                    </div>
                                </div>
                                
                                <div class="portfolio-details">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h3 class="mb-2 fw-bold"><?php echo htmlspecialchars($book['title']); ?></h3>
                                        </div>

                                      <div class="d-flex gap-2">
                                          <!-- Renew Button: links to renew_process -->
                                          <a href="process/renew_process?id=<?php echo $book['id']; ?>" 
                                            class="btn rounded-pill px-4 fw-bold"
                                            style="background-color: #CCFF66; color: #000; border: none;">
                                            Renew
                                          </a>

                                          <!-- Return Button: links to return_process -->
                                          <a href="process/return_process?id=<?php echo $book['id']; ?>" 
                                            class="btn btn-outline-danger rounded-pill px-4 fw-bold"
                                            onclick="return confirm('Are you sure you want to return this book?');">
                                            Return
                                          </a>
                                      </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-12 text-center py-5">
                        <h3 class="text-muted">No books currently borrowed.</h3>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
</div>

<?php include('./includes/footer.php'); ?>