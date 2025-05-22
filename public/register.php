<?php
require_once '../db_connect.php';

$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
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
            
            header("Location: ../public/login.php");
            exit();
        }
    }
}

$page_title = 'Register - Arbiter Coffee Hub';
ob_start();
?>


<div class="flex min-h-screen items-center justify-center bg-gray-100 px-4">
  <div class="w-full max-w-md bg-white p-8 rounded-2xl shadow-lg border border-gray-200">
    <div class="text-center mb-4">
      <img src="../uploads/logo.png" alt="Arbiter Coffee Hub" class="h-12 mx-auto mb-2">
      <h2 class="text-2xl font-bold text-[#006837]">Create an Account</h2>
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

      <button type="submit" class="w-full bg-[#009245] text-white py-2 rounded-md font-semibold hover:bg-[#006837] transition">
        Register
      </button>
    </form>

    <div class="mt-4 text-center text-sm text-gray-600">
      <p>Already have an account? <a href="login.php" class="text-[#009245] font-medium hover:underline">Login here</a></p>
    </div>
  </div>
</div>

<?php
$content = ob_get_clean();
include 'layout_public.php';
?>
