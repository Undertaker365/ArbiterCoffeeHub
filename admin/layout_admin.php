<?php
// layout_admin.php - Admin layout and navigation
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header('Location: ../public/login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel - Arbiter Coffee Hub</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/admin.css" rel="stylesheet">
    <script src="../assets/js/admin.js" defer></script>
</head>
<body class="flex flex-col max-h-screen bg-[#FFFFFF] font-[Montserrat] text-[#1A1A1A]">
<!-- Toast Notification Container -->
<div id="toast-container" class="fixed top-4 right-4 space-y-2 toast"></div>
<!-- Loading Indicator -->
<div id="loading-indicator" class="fixed inset-0 bg-[#1A1A1A] bg-opacity-30 flex items-center justify-center z-50 hidden">
  <div class="bg-white px-6 py-4 rounded shadow text-lg flex items-center gap-3">
    <i class="fas fa-spinner fa-spin text-[#009245] text-2xl"></i> Loading...
  </div>
</div>
<div class="flex flex-1 w-full px-2 sm:px-0">
<!-- Sidebar -->
<div id="adminSidebar" class="w-64 bg-[#009245] text-white flex flex-col min-h-screen px-4 py-6 md:block fixed md:static top-0 left-0 z-40 h-full transition-transform duration-300 md:translate-x-0 -translate-x-full md:shadow-none shadow-2xl md:rounded-none rounded-r-2xl">
    <div class="text-center mb-8">
        <img src="../uploads/logo.png" alt="Logo" class="bg-white rounded-full h-24 px-3 py-3 mx-auto mb-2">
        <h2 class="text-xl font-bold">Admin Panel</h2>
    </div>
    <nav class="flex-1 space-y-2">
        <a href="dashboard.php" class="flex items-center p-2 hover:bg-[#006837] rounded"><i class="fas fa-chart-line w-6"></i> <span class="ml-2">Dashboard</span></a>
        <a href="products.php" class="flex items-center p-2 hover:bg-[#006837] rounded"><i class="fas fa-coffee w-6"></i> <span class="ml-2">Product Management</span></a>
        <a href="users.php" class="flex items-center p-2 hover:bg-[#006837] rounded"><i class="fas fa-users w-6"></i> <span class="ml-2">User Management</span></a>
        <a href="orders.php" class="flex items-center p-2 hover:bg-[#006837] rounded"><i class="fas fa-receipt w-6"></i> <span class="ml-2">Order Management</span></a>
        <a href="reports.php" class="flex items-center p-2 hover:bg-[#006837] rounded"><i class="fas fa-file-alt w-6"></i> <span class="ml-2">Reports</span></a>
        <a href="reviews.php" class="flex items-center p-2 hover:bg-[#006837] rounded"><i class="fas fa-star w-6"></i> <span class="ml-2">Reviews</span></a>
        <a href="logout.php" class="flex items-center p-2 hover:bg-[#1A1A1A] rounded mt-4"><i class="fas fa-sign-out-alt w-6"></i> <span class="ml-2">Logout</span></a>
    </nav>
    <!-- Close button for mobile -->
    <button id="sidebarClose" class="md:hidden absolute top-4 right-4 bg-white text-[#009245] p-2 rounded-full shadow focus:outline-none">
        <i class="fas fa-times"></i>
    </button>
</div>
<!-- Overlay for mobile sidebar -->
<div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-40 z-30 hidden md:hidden transition-opacity duration-300"></div>
<!-- Sidebar Toggle for Mobile -->
<button id="sidebarToggle" class="md:hidden fixed top-4 left-4 z-50 bg-[#009245] text-white p-2 rounded-full shadow-lg focus:outline-none">
    <i class="fas fa-bars"></i>
</button>
<!-- Main Content -->
<div class="flex-1 p-6">
    <!-- Breadcrumbs placeholder -->
    <nav aria-label="Breadcrumb" class="mb-4 hidden md:block">
        <ol class="flex text-[#006837] space-x-2 text-sm" id="breadcrumb">
            <!-- JS can inject breadcrumbs here -->
        </ol>
    </nav>
    <!-- Page content will be injected here -->
    <?php if (isset($content)) echo $content; ?>
</div>
</div>
<!-- Floating Date & Time Widget -->
<div id="dateTimeWidget" class="fixed top-6 right-8 z-50 bg-white shadow-lg rounded-xl px-6 py-3 flex items-center gap-3 border border-[#1A1A1A] transition-all duration-300 opacity-100 pointer-events-auto">
    <i class="fas fa-clock text-[#009245] text-xl"></i>
    <div>
        <div id="dateWidget" class="font-semibold text-[#006837] text-sm"></div>
        <div id="timeWidget" class="font-mono text-lg text-[#009245]"></div>
    </div>
</div>
<!-- Footer -->
<footer class="bg-[#1A1A1A] text-white text-center py-3 mt-auto w-full border-t-2 border-[#009245]">
    &copy; <?= date('Y') ?> Arbiter Coffee Hub. All rights reserved.
</footer>
</body>
</html>
