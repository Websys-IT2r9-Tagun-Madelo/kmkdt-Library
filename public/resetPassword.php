<?php
if (session_status() === PHP_SESSION_NONE) {
    if (isset($_COOKIE['KMKDT_ADMIN_SESSION'])) {
        session_name('KMKDT_ADMIN_SESSION');
    } elseif (isset($_COOKIE['KMKDT_USER_SESSION'])) {
        session_name('KMKDT_USER_SESSION');
    }

    session_set_cookie_params(0, '/');
    session_start();
}

$isValid = false;

$configPath = $_SERVER['DOCUMENT_ROOT'] . '/kmkdt-Library/app/config/config.php';

if (file_exists($configPath)) {
    include_once($configPath);
} else {
    die("Still not found. Looking at: " . $configPath);
}

// Logic to verify the token
$token = $_GET['token'] ?? null;

if ($token && isset($conn)) {
    $query = "SELECT id FROM user WHERE reset_token = ? AND token_expire > NOW() LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $isValid = true; // Token is confirmed
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/svg+xml"
        href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%2397ee5b'><path d='M1 2.828c.885-.37 2.154-.769 3.388-.893 1.33-.134 2.458.063 3.112.752v9.746c-.935-.53-2.12-.603-3.213-.493-1.18.12-2.37.461-3.287.811V2.828zm7.5-.141c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492V2.687zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783z'/></svg>">

    <title>Reset Password | kmkdt Library</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/kmkdt-Library/public/assets/css/styles.css" />
</head>

<body>

    <div class="page-wrapper overflow-hidden">
        <section class="bg-light-gray border-top border-4 d-flex align-items-center justify-content-center min-vh-100"
            style="border-top-color: #57cb57 !important;">

            <div class="container d-flex justify-content-center">
                <div class="card reset-card shadow-lg">
                    <div class="card-body">
                        <h3 class="text-center fw-bold">New Password</h3>

                        <?php if ($isValid): ?>
                            <p class="text-muted text-center description-text">Please enter a strong password to secure your
                                account.</p>

                            <form id="passwordResetForm" action="/kmkdt-Library/app/controller/loginController.php"
                                method="POST">
                                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">

                                <div class="mb-3">
                                    <input type="password" id="resetPassword" name="newPassword" class="form-control"
                                        placeholder="New Password" required minlength="6">
                                </div>

                                <div class="mb-4">
                                    <input type="password" id="resetConfirmPassword" name="confirmPassword"
                                        class="form-control" placeholder="Confirm New Password" required minlength="6">
                                    <div id="pwMismatch" class="invalid-feedback text-start">
                                        Passwords do not match.
                                    </div>
                                </div>

                                <button type="submit" name="updatePasswordButton"
                                    class="btn btn-dark w-100 action-btn mb-2">
                                    Update Password
                                </button>

                                <a href="/kmkdt-Library/public/login"
                                    class="btn btn-outline-secondary w-100 action-btn secondary-btn">
                                    Back to Login
                                </a>
                            </form>

                        <?php else: ?>
                            <div class="alert alert-danger text-center">
                                This reset link is invalid or has expired.
                            </div>
                            <a href="/kmkdt-Library/public/login" class="btn btn-lime w-100 action-btn mt-3">Back to
                                Login</a>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </section>

        <script src="/kmkdt-Library/public/assets/libs/jquery/dist/jquery.min.js"></script>
        <script src="/kmkdt-Library/public/assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>

        <script>
            $(document).ready(function () {
                $('#passwordResetForm').on('submit', function (e) {
                    const password = $('#resetPassword').val();
                    const confirmPassword = $('#resetConfirmPassword').val();

                    if (password !== confirmPassword) {
                        e.preventDefault();
                        $('#resetConfirmPassword').addClass('is-invalid');
                        $('#pwMismatch').show();
                    } else {
                        $('#resetConfirmPassword').removeClass('is-invalid');
                        $('#pwMismatch').hide();
                    }
                });

                $('#resetConfirmPassword, #resetPassword').on('input', function () {
                    $('#resetConfirmPassword').removeClass('is-invalid');
                    $('#pwMismatch').hide();
                });
            });
        </script>

        <?php
        if (isset($_SESSION['message']) && !empty($_SESSION['code'])) {
            ?>
            <script>
                const Toast = Swal.mixin({
                    toast: true,
                    position: "top-end",
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.onmouseenter = Swal.stopTimer;
                        toast.onmouseleave = Swal.resumeTimer;
                    }
                });

                Toast.fire({
                    icon: "<?php echo $_SESSION['code']; ?>",
                    title: "<?php echo $_SESSION['message']; ?>"
                });
            </script>
            <?php
            unset($_SESSION['message']);
            unset($_SESSION['code']);
        }
        ?>

</body>

</html>