    <?php
    require_once dirname(__DIR__, 2) . '/app/middleware/userAuth.php';

    $myBooks = getMyBooks($conn, $currentUserId);

    include_once 'includes/header.php';
    include_once 'includes/tsbar.php';
    ?>

    <div class="page-wrapper overflow-hidden">
        <section class="banner-section banner-inner-section position-relative overflow-hidden d-flex align-items-end"
            style="background-image: url('assets/images/backgrounds/MMB.jpg'); min-height: 400px; background-size: cover;">
            <div class="container text-center">
                <div class="d-flex flex-column align-items-center gap-4 pb-5 pb-xl-10 position-relative z-1">
                    <div class="d-flex align-items-center gap-4" data-aos="fade-up" data-aos-delay="100">
                        <div class="primary-spinning-leaf"></div>
                        <p class="mb-0 text-white fs-5 text-opacity-70">
                            Manage your current loans and <span style="color: #57cb57;"> track due dates.</span>.
                        </p>
                    </div>
                    <h1 class="mb-0 fs-16 text-white lh-1">My Borrowed Books</h1>
                </div>
            </div>
        </section>

        <section class="project py-5 py-lg-8 bg-light">
            <div class="container">
                <?php if (isset($_GET['status'])): ?>
                    <div class="mb-5">
                        <?php if ($_GET['status'] == 'renewed'): ?>
                            <div class="alert alert-success rounded-4 border-0 shadow-sm p-3">Book renewed successfully! Your new due date has been updated.</div>
                        <?php elseif ($_GET['status'] == 'limit_reached'): ?>
                            <div class="alert alert-warning rounded-4 border-0 shadow-sm p-3">Maximum renewal limit reached.</div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if ($myBooks && $myBooks->num_rows > 0): ?>
                    <div class="d-flex justify-content-between align-items-center mb-5 p-4 bg-white rounded-5 shadow-sm border-start border-5" style="border-color: #57cb57 !important;">
                        <div>
                            <h4 class="mb-1 fw-bold text-dark">Active Loans</h4>
                            <p class="mb-0 text-muted small">You currently have <?= $myBooks->num_rows; ?> book(s) borrowed.</p>
                        </div>
                        <a href="/kmkdt-Library/public/user/browseBooks" 
                        class="btn btn-lg rounded-pill px-4 fw-bold d-flex align-items-center gap-2" 
                        style="background-color: #57cb57; color: #000; border: none;">
                        <iconify-icon icon="lucide:plus-circle" style="font-size: 1.5rem;"></iconify-icon>
                        Borrow More Books
                        </a>
                    </div>

                    <div class="row g-5">
                        <?php while ($book = $myBooks->fetch_assoc()): ?>
                            <?php
                            $category = $book['genre'] ?? 'General';
                            
                            // Determine loanDays based on category for the FALLBACK and RENEWAL MESSAGE
                            if (stripos($category, 'Online') !== false) {
                                $loanDays = 0;
                            } elseif (stripos($category, 'Reserve') !== false || stripos($category, 'Technology') !== false) {
                                $loanDays = 3; 
                            } elseif (stripos($category, 'Research') !== false) {
                                $loanDays = 7;
                            } elseif (stripos($category, 'Non-Fiction') !== false) {
                                $loanDays = 14;
                            } elseif (stripos($category, 'Fiction') !== false) {
                                $loanDays = 30; 
                            } else {
                                $loanDays = 18;
                            }

                            // PRIORITY: Use the pre-calculated due_date from the DB
                            if (!empty($book['due_date'])) {
                                $dueDate = strtotime($book['due_date']);
                            } else {
                                // Only calculate manually if the DB column is NULL
                                $borrowedAt = strtotime($book['borrowed_at']);
                                $dueDate = strtotime("+$loanDays days", $borrowedAt);
                            }

                            $today = time();
                            $isOverdue = $today > $dueDate;

                            // Calculation: 5 pesos per day
                            $penalty = $isOverdue ? ceil(($today - $dueDate) / 86400) * 5 : 0;
                            
                            $renewalsUsed = $book['renewal_count'] ?? 0;
                            $renewalsLeft = 2 - $renewalsUsed;
                            ?>

                            <div class="col-12" data-aos="fade-up">
                                <div class="card border-0 shadow-lg rounded-5 overflow-hidden bg-white"
                                    style="border-left: 12px solid <?php echo $isOverdue ? '#ff4d4d' : '#57cb57'; ?> !important;">
                                    <div class="card-body p-0">
                                        <div class="row g-0">
                                            <div class="col-lg-4 col-md-5 bg-white d-flex align-items-center justify-content-center p-5 border-end">
                                                <img src="assets/images/books/<?php echo htmlspecialchars($book['cover_image'] ?? 'default.jpg'); ?>"
                                                    alt="Book" class="img-fluid rounded-4 shadow-lg"
                                                    style="max-height: 350px; width: auto; object-fit: contain;">
                                            </div>

                                            <div class="col-lg-8 col-md-7 p-4 p-lg-5 d-flex flex-column justify-content-center">
                                                <div class="d-flex justify-content-between align-items-start mb-4">
                                                    <div>
                                                        <span class="badge mb-2 px-3 py-2 rounded-pill text-uppercase"
                                                            style="background-color: rgba(50, 205, 50, 0.1); color: #57cb57; font-weight: 700;">
                                                            <?php echo htmlspecialchars($category); ?>
                                                        </span>
                                                        <h2 class="display-5 fw-bold text-dark mt-2" style="font-size: 2.2rem;">
                                                            <?php echo htmlspecialchars($book['title']); ?>
                                                        </h2>
                                                    </div>
                                                    <div class="pt-2">                                          
                                                        <?php if ($isOverdue): ?>
                                                            <span class="badge bg-danger rounded-pill px-4 py-2 fs-6 text-dark shadow-sm">
                                                                DUE: ₱<?php echo number_format($penalty, 2); ?> LATE FEE
                                                            </span>
                                                        <?php else: ?>
                                                            <span class="badge rounded-pill px-4 py-2 fs-6 text-dark shadow-sm"
                                                                style="background-color: #32cd32;">ON TIME</span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>

                                                <div class="row bg-dark rounded-4 p-4 mb-4 g-3 text-white">
                                                    <!-- Due Date Column -->
                                                    <div class="col-sm-4 border-end border-secondary">
                                                        <small class="text-uppercase fw-bold d-block mb-1" style="font-size: 0.7rem; color: #57cb57;">Due Date</small>
                                                        <p class="mb-0 fw-bold h4 text-white">
                                                            <?php echo date('M d, Y', $dueDate); ?>
                                                        </p>
                                                    </div>

                                                    <!-- Renewals Column -->
                                                    <div class="col-sm-4 border-end border-secondary ps-sm-4">
                                                        <small class="text-uppercase fw-bold d-block mb-1" style="font-size: 0.7rem; color: #57cb57;">Renewals</small>
                                                        <p class="mb-0 fw-bold h4 text-white">
                                                            <?php echo $renewalsLeft; ?> Available
                                                        </p>
                                                    </div>

                                                    <!-- Late Fee Column -->
                                                    <div class="col-sm-4 ps-sm-4">
                                                        <small class="text-uppercase fw-bold d-block mb-1" style="font-size: 0.7rem; color: #57cb57;">Late Fee</small>
                                                        <p class="mb-0 fw-bold h4 text-white">
                                                            ₱<?php echo number_format($penalty, 2); ?>
                                                        </p>
                                                    </div>
                                                </div>

                                                <div class="d-flex flex-wrap gap-3">
                                                    <?php if ($renewalsLeft > 0 && !$isOverdue): ?>
                                                        <a href="/kmkdt-Library/app/controller/process/renewProcess.php?id=<?php echo $book['id']; ?>"
                                                            class="btn btn-lg rounded-pill px-5 fw-bold"
                                                            style="background-color: #57cb57; color: #000; border: none;"
                                                            onclick="return confirm('Renew this book for another <?php echo $loanDays; ?> days?');">
                                                            Renew Now
                                                        </a>
                                                    <?php else: ?>
                                                        <button class="btn btn-lg rounded-pill px-5 fw-bold btn-secondary" disabled>
                                                            <?php echo $isOverdue ? 'Clear Fees First' : 'Renew Locked'; ?>
                                                        </button>
                                                    <?php endif; ?>

                                                    <a href="/kmkdt-Library/app/controller/process/returnProcess.php?id=<?php echo $book['id']; ?>"
                                                        class="btn btn-lg btn-outline-dark rounded-pill px-5 fw-bold"
                                                        onclick="return confirm('Ready to return this book?');">
                                                        Return Book
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div class="col-12 text-center py-5">
                        <div class="bg-white p-5 rounded-5 shadow-sm border">
                            <iconify-icon icon="lucide:book-open" style="font-size: 5rem; color: #57cb57;"></iconify-icon>
                            <h2 class="mt-4 fw-bold text-dark">No books currently borrowed.</h2>
                            <a href="/kmkdt-Library/public/user/browseBooks" class="btn btn-primary rounded-pill px-5 py-3 mt-3 fw-bold"
                                style="background-color: #57cb57; color: #000; border: none;">
                                Browse Books</a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </div>

    <?php include('./includes/footer.php'); ?>