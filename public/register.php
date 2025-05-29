<?php
require_once __DIR__ . '/../db_connect.php';
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$errors = [];

$csrf_token = bin2hex(random_bytes(32));
$_SESSION['csrf_token'] = $csrf_token;

// Only process POST and output errors if accessed directly
if (basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME'])) {
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
            $errors[] = "Invalid CSRF token.";
        } else {
            $first_name = trim($_POST['first_name'] ?? '');
            $last_name = trim($_POST['last_name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $phone_number = trim($_POST['phone'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';

            if (empty($first_name) || empty($last_name) || empty($email) || empty($phone_number) || empty($password) || empty($confirm_password)) {
                $errors[] = "All fields are required.";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Invalid email format.";
            } elseif (!preg_match('/^[0-9]{11}$/', $phone_number)) {
                $errors[] = "Phone number must be 11 digits.";
            } elseif ($password !== $confirm_password) {
                $errors[] = "Passwords do not match.";
            } else {
                try {
                    // Check if the email already exists
                    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
                    $stmt->execute([$email]);
                    if ($stmt->fetch()) {
                        $errors[] = "Email is already registered.";
                    } else {
                        // Default role is Customer
                        $role = 'Customer';
                        // Hash password
                        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                        // Insert new user with default role
                        $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, phone_number, password, role) VALUES (?, ?, ?, ?, ?, ?)");
                        $stmt->execute([$first_name, $last_name, $email, $phone_number, $hashed_password, $role]);
                        // Set session variables for the user
                        $_SESSION['user_id'] = $conn->lastInsertId();
                        $_SESSION['full_name'] = $first_name . ' ' . $last_name;
                        $_SESSION['role'] = $role;
                        // Show success message and redirect
                        $_SESSION['register_success'] = 'Registration successful! Please log in.';
                        header("Location: login.php");
                        exit();
                    }
                } catch (PDOException $e) {
                    $errors[] = "A system error occurred. Please try again later.";
                }
            }
        }
    }
    if (isset($_SESSION['register_success'])): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 p-3 rounded mb-4">
        <?= htmlspecialchars($_SESSION['register_success']) ?>
    </div>
    <?php unset($_SESSION['register_success']); endif;
}

$page_title = 'Register - Arbiter Coffee Hub';
ob_start();
?>
<div id="register-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 hidden" aria-modal="true" role="dialog" aria-labelledby="register-modal-title" aria-describedby="register-modal-desc">
  <div class="bg-white w-full max-w-md p-8 rounded-2xl shadow-lg border border-gray-200 relative">
    <button class="absolute top-3 right-3 text-gray-400 hover:text-green-700 text-2xl" onclick="closeRegisterModal()" aria-label="Close register">&times;</button>
    <div class="text-center mb-4">
      <img src="../uploads/logo.png" alt="Arbiter Coffee Hub" class="h-12 mx-auto mb-2">
      <h2 id="register-modal-title" class="text-2xl font-bold text-[#006837]">Create an Account</h2>
      <p id="register-modal-desc" class="sr-only">Register for an Arbiter Coffee Hub account.</p>
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
    <form method="POST" action="register.php" class="space-y-4">
      <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
      <div class="relative">
        <label class="block text-sm font-medium text-gray-700">First Name</label>
        <div class="absolute left-3 top-9 text-gray-400"><i class="fas fa-user"></i></div>
        <input type="text" name="first_name" required class="w-full mt-1 px-10 py-2 border rounded-md focus:ring-[#009245] focus:border-[#009245]" />
      </div>
      <div class="relative">
        <label class="block text-sm font-medium text-gray-700">Last Name</label>
        <div class="absolute left-3 top-9 text-gray-400"><i class="fas fa-user"></i></div>
        <input type="text" name="last_name" required class="w-full mt-1 px-10 py-2 border rounded-md focus:ring-[#009245] focus:border-[#009245]" />
      </div>
      <div class="relative">
        <label class="block text-sm font-medium text-gray-700">Email</label>
        <div class="absolute left-3 top-9 text-gray-400"><i class="fas fa-envelope"></i></div>
        <input type="email" name="email" required class="w-full mt-1 px-10 py-2 border rounded-md focus:ring-[#009245] focus:border-[#009245]" />
      </div>
      <div class="relative">
        <label class="block text-sm font-medium text-gray-700">Phone Number</label>
        <div class="absolute left-3 top-9 text-gray-400"><i class="fas fa-phone"></i></div>
        <input type="text" name="phone" required class="w-full mt-1 px-10 py-2 border rounded-md focus:ring-[#009245] focus:border-[#009245]" />
      </div>
      <div class="relative">
        <label class="block text-sm font-medium text-gray-700">Password</label>
        <div class="absolute left-3 top-9 text-gray-400"><i class="fas fa-lock"></i></div>
        <input type="password" name="password" required class="w-full mt-1 px-10 py-2 border rounded-md focus:ring-[#009245] focus:border-[#009245]" />
      </div>
      <div class="relative">
        <label class="block text-sm font-medium text-gray-700">Confirm Password</label>
        <div class="absolute left-3 top-9 text-gray-400"><i class="fas fa-lock"></i></div>
        <input type="password" name="confirm_password" required class="w-full mt-1 px-10 py-2 border rounded-md focus:ring-[#009245] focus:border-[#009245]" />
      </div>
      <button type="submit" class="w-full bg-[#009245] text-white py-2 rounded-md font-semibold hover:bg-[#006837] transition">Register</button>
      <div class="text-center mt-2">
        <span class="text-sm">Already have an account? <a href="#" class="text-[#009245] font-semibold hover:underline" onclick="openLoginModal();closeRegisterModal();return false;">Sign In</a></span>
      </div>
    </form>
  </div>
</div>
<?php
// Only output modal HTML, never include layout_public.php
if (basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME'])) {
    ob_end_flush();
} else {
    ob_get_clean(); // do not set or echo $content
}
