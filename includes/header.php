<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// Determine if this is a public page (in /public/ or index.php)
$isPublicPage = false;
$publicRoots = ['/index.php', '/ArbiterCoffeeHub/index.php'];
$currentScript = $_SERVER['SCRIPT_NAME'] ?? '';
if (strpos($currentScript, '/public/') !== false || in_array($currentScript, $publicRoots)) {
  $isPublicPage = true;
}
?>
<!-- Header Section -->
<header class="w-full bg-black shadow<?= $isPublicPage ? ' sticky top-0 z-40' : '' ?>">
  <?php if (isset($_SESSION['user_name'])): ?>
    <div class="w-full bg-black text-white py-3 text-center font-semibold text-lg animate-fade-in-down" aria-live="polite">
      Welcome back, <?= htmlspecialchars($_SESSION['user_name']) ?>!
    </div>
  <?php endif; ?>
  <div class="max-w-5xl mx-auto flex items-center justify-between px-4 py-3">
    <a href="/index.php" class="text-xl font-bold text-white flex items-center gap-2">
      <img src="/uploads/logo.png" alt="Arbiter Coffee Hub Logo" class="h-10 w-10 rounded-full bg-white mr-2" style="display:inline-block;vertical-align:middle;"> Arbiter Coffee Hub
    </a>
    <nav class="flex gap-6 items-center">
      <a href="/public/menu.php" class="text-gray-200 hover:text-green-500 flex items-center gap-1">
        <i class="fas fa-mug-hot"></i>
        <span class="hidden sm:inline">Menu</span>
      </a>
      <a href="/public/announcements.php" class="text-gray-200 hover:text-green-500 flex items-center gap-1">
        <i class="fas fa-bullhorn"></i>
        <span class="hidden sm:inline">Announcements</span>
      </a>
      <a href="/public/about.php" class="text-gray-200 hover:text-green-500 flex items-center gap-1">
        <i class="fas fa-info-circle"></i>
        <span class="hidden sm:inline">About</span>
      </a>
      <a href="/public/contact.php" class="text-gray-200 hover:text-green-500 flex items-center gap-1">
        <i class="fas fa-envelope"></i>
        <span class="hidden sm:inline">Contact</span>
      </a>
      <?php if (!isset($_SESSION['user_id'])): ?>
      <a href="javascript:void(0);" id="header-login-btn" class="bg-green-700 text-white px-4 py-1 rounded hover:bg-green-800 ml-2 flex items-center gap-1" aria-haspopup="dialog" aria-controls="login-modal">
        <i class="fas fa-sign-in-alt"></i>
        <span class="hidden sm:inline">Login</span>
      </a>
      <?php endif; ?>
    </nav>
  </div>
</header>