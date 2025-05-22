<?php
session_start();

$success = $_SESSION['reset_success'] ?? null;
$error = $_SESSION['reset_error'] ?? null;

// Clear session messages
unset($_SESSION['reset_success']);
unset($_SESSION['reset_error']);

$page_title = 'Forgot Password - Arbiter Coffee Hub';
ob_start();
?>
<section class="py-16 bg-white">
  <div class="max-w-md mx-auto px-4">
    <h2 class="text-3xl font-bold text-[#006837] mb-8 text-center">Forgot Password</h2>

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

    <div class="text-center text-sm text-gray-600 mt-4">
      <p>Remember your password? <a href="login.php" class="text-[#009245] hover:underline">Login here</a>.</p>
    </div>
  </div>
</section>
<?php
$content = ob_get_clean();
include 'layout_public.php';
?>
