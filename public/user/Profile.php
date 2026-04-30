<?php
// Start session safely
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Load the controller and connection
require_once dirname(__DIR__, 2) . '/app/controller/userController.php';

// 2. Fetch Active User Data from the 'authUser' session key
$authUser = $_SESSION['authUser'] ?? null;

// Redirect to login if the session is missing
if (!$authUser) {
    header("Location: /kmkdt-Library/public/login");
    exit();
}

// Map session data to local variables
$currentUserId = $authUser['user_id'];
$fullName      = $authUser['fullName'];

// 3. Get Database Records
$stats = getUserStats($conn, $currentUserId);
$myBooks = getMyBooks($conn, $currentUserId);

include('./includes/header.php');
include('./includes/tsbar.php');
?>

<div class="page-wrapper overflow-hidden">

    <!-- Profile Header Section -->
    <section class="banner-section banner-inner-section position-relative overflow-hidden d-flex align-items-end"
        style="background-image: url('assets/images/backgrounds/ProfileBg.jpg'); min-height: 350px; background-size: cover;">
        <div class="container">
            <div class="d-flex flex-column gap-4 pb-5 pb-xl-10 position-relative z-1">
                <div class="row align-items-center">
                    <div class="col-md-8">  
                        <div class="d-flex align-items-center gap-4" data-aos="fade-up">
                            
                            <!-- Avatar using initials API -->
                            <div class="bg-primary d-flex align-items-center justify-content-center overflow-hidden border border-4 border-white shadow-sm" 
                                style="width: 150px; height: 150px; flex-shrink: 0; border-radius: 15px;">
                                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($fullName); ?>&background=32cd32&color=fff&size=150" 
                                     alt="Profile" class="w-100 h-100 object-fit-cover">
                            </div>

                            <!-- Dynamic Name and ID -->
                            <div>
                                <h1 class="mb-1 text-white fw-bold" style="font-size: 2.5rem;">
                                    <?php echo htmlspecialchars($fullName); ?>
                                </h1>
                                <p class="mb-0 text-white text-opacity-75 fs-4">Member ID: #<?php echo $currentUserId; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="profile-content py-5">
        <div class="container">
            <div class="row g-5">
                
                <!-- Left Sidebar: Library Stats -->
                <div class="col-lg-4">
                    <div class="p-4 rounded-4 border bg-white shadow-sm">
                        <h4 class="border-bottom pb-3 mb-4">Library Overview</h4>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Total Borrowed</span>
                            <span class="fw-bold text-primary"><?php echo $stats['total_borrowed']; ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Currently Holding</span>
                            <span class="fw-bold text-primary"><?php echo $stats['current_holdings']; ?></span>
                        </div>
                        <hr>
                        <button class="btn btn-primary rounded-pill w-100">Update Profile</button>
                    </div>
                </div>

                <!-- Right Content: Borrowed Books List -->
                <div class="col-lg-8">
                    <h2 class="mb-4">Current Borrowed Books</h2>
                    
                    <?php if ($myBooks && $myBooks->num_rows > 0): ?>
                        <?php while($book = $myBooks->fetch_assoc()): ?>
                            <div class="p-4 rounded-4 border-start border-5 mb-3 d-flex justify-content-between align-items-center shadow-sm bg-white" 
                                 style="border-color: #32cd32 !important;">
                                <div>
                                    <h4 class="mb-1"><?php echo htmlspecialchars($book['title']); ?></h4>
                                    <p class="mb-0 text-muted small">Category: <?php echo htmlspecialchars($book['genre']); ?></p>
                                </div>
                                <a href="../../app/controller/userController.php?action=renew&book_id=<?php echo $book['id']; ?>" 
                                  class="btn btn-outline-dark rounded-pill">
                                  Renew
                                </a>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="p-5 text-center border rounded-4 bg-light">
                            <p class="text-muted mb-0">You don't have any books checked out right now.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include('./includes/footer.php'); ?>