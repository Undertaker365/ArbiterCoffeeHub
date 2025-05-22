<?php
// session_start() should be called in the layout, not in the header include.
?>
<!-- Header Section -->
<header class="bg-[#1A1A1A] text-white py-4 shadow">
  <div class="max-w-6xl mx-auto px-4 flex justify-between items-center">
    <a href="../index.php" class="flex items-center space-x-2">
      <span class="text-xl font-bold">Arbiter Coffee Hub</span>
    </a>
    <nav class="hidden md:flex">
      <ul class="flex space-x-6 text-sm font-medium">
        <li><a href="../public/menu.php" class="hover:text-[#009245] flex items-center"><i class="fas fa-mug-hot mr-1"></i> Menu</a></li>
        <li><a href="../public/about.php" class="hover:text-[#009245] flex items-center"><i class="fas fa-info-circle mr-1"></i> About Us</a></li>
        <li><a href="../public/contact.php" class="hover:text-[#009245] flex items-center"><i class="fas fa-envelope mr-1"></i> Contact</a></li>
        <?php if (isset($_SESSION['role'])): ?>
          <?php if ($_SESSION['role'] === 'Customer'): ?>
            <li><a href="../customer/dashboard.php" class="hover:text-[#009245] flex items-center"><i class="fas fa-user mr-1"></i> Dashboard</a></li>
            <li><a href="../public/logout.php" class="hover:text-[#009245] flex items-center"><i class="fas fa-sign-out-alt mr-1"></i> Logout</a></li>
          <?php elseif ($_SESSION['role'] === 'Barista'): ?>
            <li><a href="../barista/dashboard.php" class="hover:text-[#009245] flex items-center"><i class="fas fa-user mr-1"></i> Dashboard</a></li>
            <li><a href="../public/logout.php" class="hover:text-[#009245] flex items-center"><i class="fas fa-sign-out-alt mr-1"></i> Logout</a></li>
          <?php elseif ($_SESSION['role'] === 'Admin'): ?>
            <li><a href="../admin/dashboard.php" class="hover:text-[#009245] flex items-center"><i class="fas fa-user-shield mr-1"></i> Admin</a></li>
            <li><a href="../admin/logout.php" class="hover:text-[#009245] flex items-center"><i class="fas fa-sign-out-alt mr-1"></i> Logout</a></li>
          <?php endif; ?>
        <?php else: ?>
          <li><a href="../public/login.php" class="hover:text-[#009245] flex items-center"><i class="fas fa-sign-in-alt mr-1"></i> Login</a></li>
          <li><a href="../public/register.php" class="hover:text-[#009245] flex items-center"><i class="fas fa-user-plus mr-1"></i> Register</a></li>
        <?php endif; ?>
      </ul>
    </nav>
  </div>
</header>