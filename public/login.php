<?php
// All PHP logic and ob_start() must be at the very top, before any HTML or whitespace.

if (ob_get_level() === 0) ob_start();

require_once __DIR__ . '/../db_connect.php';
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$errors = [];
$ip_address = $_SERVER['REMOTE_ADDR'];
$lockout_minutes = 15;
$max_attempts = 5;

// Only process POST and output errors if accessed directly
if (basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME'])) {
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        try {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                $errors[] = "Invalid CSRF token.";
            } else {
                $email = trim($_POST['email']);
                $password = $_POST['password'];

                if (empty($email) || empty($password)) {
                    $errors[] = "Email and password are required.";
                } else {
                    // Check for lockout
                    $stmt = $conn->prepare("SELECT attempts, last_attempt, locked_until FROM login_attempts WHERE email = ? AND ip_address = ?");
                    $stmt->execute([$email, $ip_address]);
                    $attempt = $stmt->fetch(PDO::FETCH_ASSOC);
                    $now = date('Y-m-d H:i:s');
                    if ($attempt && $attempt['locked_until'] && strtotime($attempt['locked_until']) > time()) {
                        $remaining = ceil((strtotime($attempt['locked_until']) - time()) / 60);
                        $errors[] = "Account locked due to too many failed attempts. Try again in $remaining minute(s).";
                    } else {
                        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
                        $stmt->execute([$email]);
                        $user = $stmt->fetch(PDO::FETCH_ASSOC);

                        if ($user && password_verify($password, $user['password'])) {
                            // Reset login attempts on success
                            $conn->prepare("DELETE FROM login_attempts WHERE email = ? AND ip_address = ?")->execute([$email, $ip_address]);
                            // Regenerate session ID to prevent session fixation
                            session_regenerate_id(true);
                            // Set session variables
                            $_SESSION['user_id'] = $user['id'];
                            $_SESSION['full_name'] = $user['first_name'] . ' ' . $user['last_name'];
                            $_SESSION['role'] = $user['role'];

                            // TODO: Implement "Remember me" backend logic if checked

                            // Redirect based on role
                            if ($user['role'] === 'Admin') {
                                header("Location: ../admin/dashboard.php");
                            } elseif ($user['role'] === 'Barista') {
                                header("Location: ../barista/dashboard.php");
                            } else {
                                header("Location: ../customer/dashboard.php");
                            }
                            exit();
                        } else {
                            // Failed login: update/increment attempts
                            if ($attempt) {
                                $attempts = ($attempt['last_attempt'] && strtotime($attempt['last_attempt']) > strtotime('-15 minutes')) ? $attempt['attempts'] + 1 : 1;
                                $locked_until = null;
                                if ($attempts >= $max_attempts) {
                                    $locked_until = date('Y-m-d H:i:s', strtotime("+$lockout_minutes minutes"));
                                    $errors[] = "Account locked due to too many failed attempts. Try again in $lockout_minutes minutes.";
                                } else {
                                    $errors[] = "Invalid email or password.";
                                }
                                $conn->prepare("UPDATE login_attempts SET attempts = ?, last_attempt = ?, locked_until = ? WHERE email = ? AND ip_address = ?")
                                    ->execute([$attempts, $now, $locked_until, $email, $ip_address]);
                            } else {
                                $conn->prepare("INSERT INTO login_attempts (email, ip_address, attempts, last_attempt) VALUES (?, ?, 1, ?)")
                                    ->execute([$email, $ip_address, $now]);
                                $errors[] = "Invalid email or password.";
                            }
                        }
                    }
                }
            }
        } catch (PDOException $e) {
            // Log error to file for debugging
            error_log('['.date('Y-m-d H:i:s').'] Login error: '.$e->getMessage().PHP_EOL, 3, __DIR__.'/../logs/error.log');
            $errors[] = "A system error occurred. Please try again later.";
        }
    }
    // Show success message if redirected from registration or password reset
    if (isset($_SESSION['register_success'])) {
        echo '<div class="bg-green-100 border border-green-400 text-green-700 p-3 rounded mb-4">'.htmlspecialchars($_SESSION['register_success']).'</div>';
        unset($_SESSION['register_success']);
    }
    if (isset($_SESSION['reset_success'])) {
        echo '<div class="bg-green-100 border border-green-400 text-green-700 p-3 rounded mb-4">'.htmlspecialchars($_SESSION['reset_success']).'</div>';
        unset($_SESSION['reset_success']);
    }
    // --- Floating modal output for standalone page ---
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login - Arbiter Coffee Hub</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
        <link rel="stylesheet" href="/assets/css/main.css">
        <style>body { font-family: 'Montserrat', sans-serif; }</style>
    </head>
    <body class="flex flex-col min-h-screen bg-[#FFFFFF] text-[#1A1A1A] font-[Montserrat]">
    <div id="login-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" aria-modal="true" role="dialog" aria-labelledby="login-modal-title" aria-describedby="login-modal-desc">
      <div class="bg-white w-full max-w-md p-8 rounded-2xl shadow-lg border border-gray-200 relative animate-fade-in-up mt-10 mb-10">
        <div class="text-center mb-4">
          <img src="../uploads/logo.png" alt="Arbiter Coffee Hub" class="h-12 mx-auto mb-2">
          <h2 id="login-modal-title" class="text-2xl font-bold text-[#006837]">Sign In</h2>
          <p id="login-modal-desc" class="sr-only">Sign in to your Arbiter Coffee Hub account.</p>
        </div>
        <?php if (!empty($errors)): ?>
          <div class="bg-red-100 border border-red-400 text-red-700 p-3 rounded mb-4" role="alert">
            <ul class="list-disc pl-5">
              <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>
        <form method="POST" action="login.php" class="space-y-4">
          <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
          <div class="relative">
            <label class="block text-sm font-medium text-gray-700" for="login-email">Email</label>
            <div class="absolute left-3 top-9 text-gray-400"><i class="fas fa-envelope"></i></div>
            <input id="login-email" type="email" name="email" required autocomplete="username" class="w-full mt-1 px-10 py-2 border rounded-md focus:ring-[#009245] focus:border-[#009245]" placeholder="you@email.com" />
          </div>
          <div class="relative">
            <label class="block text-sm font-medium text-gray-700" for="login-password">Password</label>
            <div class="absolute left-3 top-9 text-gray-400"><i class="fas fa-lock"></i></div>
            <input id="login-password" type="password" name="password" required autocomplete="current-password" class="w-full mt-1 px-10 py-2 border rounded-md focus:ring-[#009245] focus:border-[#009245]" placeholder="Your password" />
          </div>
          <div class="flex items-center justify-between">
            <div class="flex items-center">
              <input id="remember-me" name="remember" type="checkbox" class="h-4 w-4 text-[#009245] focus:ring-[#009245] border-gray-300 rounded">
              <label for="remember-me" class="ml-2 block text-sm text-gray-700">Remember me</label>
            </div>
            <a href="forgot_password.php" class="text-sm text-[#009245] hover:underline">Forgot password?</a>
          </div>
          <button type="submit" class="w-full bg-[#009245] text-white py-2 rounded-md font-semibold hover:bg-[#006837] transition">Sign In</button>
          <div class="text-center mt-2">
            <span class="text-sm">Don't have an account? <a href="register.php" class="text-[#009245] font-semibold hover:underline">Register</a></span>
          </div>
        </form>
      </div>
    </div>
    </body>
    </html>
    <?php
    if (ob_get_level() > 0) ob_end_flush();
    return;
}
// Only output modal markup, never a standalone page
?>
<div id="login-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm transition-opacity duration-200 hidden" aria-modal="true" role="dialog" aria-labelledby="login-modal-title" aria-describedby="login-modal-desc" style="pointer-events: auto;">
  <div class="bg-white w-full max-w-md w-[95vw] p-8 rounded-2xl shadow-2xl border border-gray-200 relative animate-fade-in-up mt-10 mb-10 focus:outline-none" tabindex="-1" style="z-index:1001;">
    <button type="button" id="login-modal-close" aria-label="Close login modal" class="absolute top-3 right-3 text-gray-400 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#009245] rounded-full p-2 transition">
      <span aria-hidden="true">&times;</span>
    </button>
    <div class="text-center mb-4">
      <img src="../uploads/logo.png" alt="Arbiter Coffee Hub" class="h-12 mx-auto mb-2">
      <h2 id="login-modal-title" class="text-2xl font-bold text-[#006837]">Sign In</h2>
      <p id="login-modal-desc" class="sr-only">Sign in to your Arbiter Coffee Hub account.</p>
    </div>
    <div id="login-error-container" class="mb-4">
      <?php if (!empty($errors)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 p-3 rounded mb-4" role="alert">
          <ul class="list-disc pl-5">
            <?php foreach ($errors as $error): ?>
              <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>
    </div>
    <form method="POST" action="login.php" class="space-y-4" autocomplete="on" id="login-form">
      <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>" id="csrf-token">
      <div class="relative">
        <label class="block text-sm font-medium text-gray-700" for="login-email">Email</label>
        <div class="absolute left-3 top-9 text-gray-400"><i class="fas fa-envelope"></i></div>
        <input id="login-email" type="email" name="email" required autocomplete="username" class="w-full mt-1 px-10 py-2 border rounded-md focus:ring-[#009245] focus:border-[#009245]" placeholder="you@email.com" />
      </div>
      <div class="relative">
        <label class="block text-sm font-medium text-gray-700" for="login-password">Password</label>
        <div class="absolute left-3 top-9 text-gray-400"><i class="fas fa-lock"></i></div>
        <input id="login-password" type="password" name="password" required autocomplete="current-password" class="w-full mt-1 px-10 py-2 border rounded-md focus:ring-[#009245] focus:border-[#009245]" placeholder="Your password" />
      </div>
      <div class="flex items-center justify-between">
        <div class="flex items-center">
          <input id="remember-me" name="remember" type="checkbox" class="h-4 w-4 text-[#009245] focus:ring-[#009245] border-gray-300 rounded">
          <label for="remember-me" class="ml-2 block text-sm text-gray-700">Remember me</label>
        </div>
        <a href="forgot_password.php" class="text-sm text-[#009245] hover:underline">Forgot password?</a>
      </div>
      <button type="submit" class="w-full bg-[#009245] text-white py-2 rounded-md font-semibold hover:bg-[#006837] transition">Sign In</button>
      <div class="text-center mt-2">
        <span class="text-sm">Don't have an account? <a href="register.php" class="text-[#009245] font-semibold hover:underline">Register</a></span>
      </div>
    </form>
  </div>
</div>
<?php
if (basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME'])) {
    ?>
    </main>
    </body>
    </html>
    <?php
    if (ob_get_level() > 0) ob_end_flush();
} else {
    if (ob_get_level() > 0) ob_get_clean(); // do not set or echo $content
}
