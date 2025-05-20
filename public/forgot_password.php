<?php
session_start();
$success = $_SESSION['reset_success'] ?? null;
$error = $_SESSION['reset_error'] ?? null;

// Clear session messages
unset($_SESSION['reset_success']);
unset($_SESSION['reset_error']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Forgot Password - Arbiter Coffee Hub</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
  <style> body { font-family: 'Montserrat', sans-serif; } </style>
</head>
<body class="bg-[#f4f4f4] min-h-screen flex items-center justify-center px-4">

  <div class="bg-white shadow-xl rounded-2xl max-w-md w-full p-8 space-y-6 border border-gray-200">
    <div class="text-center">
      <img src="../uploads/logo.png" alt="Arbiter Coffee Hub" class="mx-auto mb-4 h-12">
      <h2 class="text-2xl font-bold text-[#006837]">Forgot Your Password?</h2>
      <p class="text-sm text-gray-600">Enter your registered email address to reset your password.</p>
    </div>

    <?php if ($success): ?>
      <div class="bg-green-100 border border-green-400 text-green-700 p-3 rounded-md">
        <?= htmlspecialchars($success) ?>
      </div>
    <?php elseif ($error): ?>
      <div class="bg-red-100 border border-red-400 text-red-700 p-3 rounded-md">
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <form action="send_reset_email.php" method="POST" class="space-y-4">
      <div class="relative">
        <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
        <div class="absolute left-3 top-9 text-gray-500">
          <i class="fas fa-envelope"></i>
        </div>
        <input type="email" name="email" id="email" required
          class="mt-1 w-full pl-10 pr-4 py-2 border rounded-md focus:ring-[#009245] focus:border-[#009245]"/>
      </div>

      <button type="submit"
        class="w-full bg-[#009245] text-white py-2 rounded-md font-semibold hover:bg-[#006837] transition">
        <i class="fas fa-paper-plane mr-2"></i>Send Reset Email
      </button>
    </form>

    <div class="text-center text-sm text-gray-600">
      <p>Remember your password? <a href="login.php" class="text-[#009245] hover:underline">Login here</a>.</p>
    </div>
  </div>
</body>
</html>
