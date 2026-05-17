<?php
session_start();

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
    <title>Reset Password | kmkdt Library</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .reset-card { max-width: 450px; margin-top: 100px; border: none; border-radius: 15px; }
        .btn-lime { background-color: #97ee5b; color: #2A3547; font-weight: bold; border: none; }
        .btn-lime:hover { background-color: #86d451; }
    </style>
</head>
<body>

<div class="container">
    <div class="card reset-card shadow-lg mx-auto">
        <div class="card-body p-5">
            <h3 class="text-center fw-bold mb-4" style="color: #2A3547;">New Password</h3>
            
            <?php if ($isValid): ?>
                <p class="text-muted text-center mb-4">Please enter a strong password to secure your account.</p>
                
                <form action="/kmkdt-Library/app/controller/loginController.php" method="POST">
                    <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                    
                    <div class="mb-3">
                        <input type="password" name="newPassword" class="form-control border-bottom" 
                               placeholder="New Password" required minlength="6">
                    </div>
                    
                    <div class="mb-4">
                        <input type="password" name="confirmPassword" class="form-control border-bottom" 
                               placeholder="Confirm New Password" required minlength="6">
                    </div>
                    
                    <button type="submit" name="updatePasswordButton" class="btn btn-dark w-100 py-3 shadow-sm">
                        Update Password
                    </button>
                </form>

            <?php else: ?>
                <!-- If the token is fake or expired -->
                <div class="alert alert-danger text-center">
                    This reset link is invalid or has expired.
                </div>
                <a href="login" class="btn btn-lime w-100 py-3 mt-3">Back to Login</a>
            <?php endif; ?>

        </div>
    </div>
</div>

  <script src="/kmkdt-Library/public/assets/libs/jquery/dist/jquery.min.js"></script>
  <script src="/kmkdt-Library/public/assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>

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