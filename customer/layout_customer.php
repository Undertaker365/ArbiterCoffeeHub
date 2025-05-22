<?php
// layout_customer.php - Customer layout and navigation
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Customer') {
    header('Location: ../public/login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Panel - Arbiter Coffee Hub</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>body { font-family: 'Montserrat', sans-serif; }</style>
</head>
<body class="flex flex-col min-h-screen bg-[#f9f9f9] text-gray-900">
<?php include('../includes/header.php'); ?>
<div class="flex flex-1">
    <!-- Sidebar -->
    <div id="customerSidebar" class="w-64 bg-[#009245] text-white flex flex-col min-h-screen px-4 py-6 md:block fixed md:static top-0 left-0 z-40 h-full transition-transform duration-300 md:translate-x-0 -translate-x-full md:shadow-none shadow-2xl md:rounded-none rounded-r-2xl" aria-label="Sidebar">
        <div class="text-center mb-8">
            <img src="../uploads/logo.png" alt="Logo" class="bg-white rounded-full h-20 px-3 py-3 mx-auto mb-2">
            <h2 class="text-xl font-bold">Customer Panel</h2>
        </div>
        <nav class="flex-1 space-y-2">
            <a href="dashboard.php" class="flex items-center p-2 hover:bg-[#006837] rounded"><i class="fas fa-home w-6"></i> <span class="ml-2">Dashboard</span></a>
            <a href="place_order.php" class="flex items-center p-2 hover:bg-[#006837] rounded"><i class="fas fa-mug-hot w-6"></i> <span class="ml-2">Place Order</span></a>
            <a href="order_history.php" class="flex items-center p-2 hover:bg-[#006837] rounded"><i class="fas fa-history w-6"></i> <span class="ml-2">Order History</span></a>
            <a href="profile.php" class="flex items-center p-2 hover:bg-[#006837] rounded"><i class="fas fa-user w-6"></i> <span class="ml-2">Profile</span></a>
            <a href="../public/logout.php" class="flex items-center p-2 hover:bg-red-600 rounded mt-4"><i class="fas fa-sign-out-alt w-6"></i> <span class="ml-2">Logout</span></a>
        </nav>
        <button id="sidebarClose" class="md:hidden absolute top-4 right-4 bg-white text-[#009245] p-2 rounded-full shadow focus:outline-none"><i class="fas fa-times"></i></button>
    </div>
    <div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-40 z-30 hidden md:hidden transition-opacity duration-300"></div>
    <button id="sidebarToggle" class="md:hidden fixed top-4 left-4 z-50 bg-[#009245] text-white p-2 rounded-full shadow-lg focus:outline-none" aria-label="Open sidebar"><i class="fas fa-bars"></i></button>
    <script>
    const sidebar = document.getElementById('customerSidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    const sidebarClose = document.getElementById('sidebarClose');
    function openSidebar() {
        sidebar.classList.remove('-translate-x-full');
        sidebarOverlay.classList.remove('hidden');
    }
    function closeSidebar() {
        sidebar.classList.add('-translate-x-full');
        sidebarOverlay.classList.add('hidden');
    }
    if (sidebar && sidebarToggle && sidebarOverlay && sidebarClose) {
        sidebarToggle.addEventListener('click', openSidebar);
        sidebarClose.addEventListener('click', closeSidebar);
        sidebarOverlay.addEventListener('click', closeSidebar);
        if (window.innerWidth < 768) {
            closeSidebar();
        }
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 768) {
                sidebar.classList.remove('-translate-x-full');
                sidebarOverlay.classList.add('hidden');
            } else {
                closeSidebar();
            }
        });
    }
    </script>
    <main class="flex-1">
        <?php if (isset($content)) echo $content; ?>
    </main>
</div>
<?php include('../includes/footer.php'); ?>
</body>
</html>
