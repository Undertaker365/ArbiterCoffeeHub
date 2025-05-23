<?php
// All PHP logic and ob_start() must be at the very top, before any HTML or whitespace.

require_once '../db_connect.php';
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $errors[] = "Invalid CSRF token.";
    } else {
        $email = trim($_POST['email']);
        $password = $_POST['password'];

        if (empty($email) || empty($password)) {
            $errors[] = "Email and password are required.";
        } else {
            $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['full_name'] = $user['first_name'] . ' ' . $user['last_name'];
                $_SESSION['role'] = $user['role'];  // Store the role

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
                $errors[] = "Invalid email or password.";
            }
        }
    }
}

$page_title = 'Login - Arbiter Coffee Hub';
ob_start();
?>

<div class="flex min-h-screen items-center justify-center bg-gray-100 px-4">
    <div class="w-full max-w-md bg-white p-8 rounded-2xl shadow-lg border border-gray-200">
        <div class="text-center mb-4">
        <img src="../uploads/logo.png" alt="Arbiter Coffee Hub" class="h-12 mx-auto mb-2">
        <h2 class="text-2xl font-bold text-[#006837]">Login to Your Account</h2>
        </div>

        <?php if (!empty($errors)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 p-3 rounded mb-4">
            <ul class="list-disc pl-5">
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <form method="POST" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" class="space-y-4">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

        <div class="relative">
            <label class="block text-sm font-medium text-gray-700">Email</label>
            <div class="absolute left-3 top-9 text-gray-400"><i class="fas fa-envelope"></i></div>
            <input type="email" name="email" required class="w-full mt-1 px-10 py-2 border rounded-md focus:ring-[#009245] focus:border-[#009245]" />
        </div>

        <div class="relative">
            <label class="block text-sm font-medium text-gray-700">Password</label>
            <div class="absolute left-3 top-9 text-gray-400"><i class="fas fa-lock"></i></div>
            <input type="password" name="password" required class="w-full mt-1 px-10 py-2 border rounded-md focus:ring-[#009245] focus:border-[#009245]" />
        </div>

        <button type="submit" class="w-full bg-[#009245] text-white py-2 rounded-md font-semibold hover:bg-[#006837] transition">
            Login
        </button>
        </form>

        <div class="text-center text-sm text-gray-600 space-y-1">
            <p>Don’t have an account?
                <a href="register.php" class="text-[#009245] font-medium hover:underline">Register here</a>.
            </p>
            <p>
                <a href="forgot_password.php" class="text-[#009245] font-medium hover:underline">Forgot password?</a>
            </p>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'layout_public.php';
?>
