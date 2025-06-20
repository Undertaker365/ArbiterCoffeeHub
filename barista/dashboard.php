<?php
session_start();
require_once '../db_connect.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Barista') {
    header('Location: ../public/login.php');
    exit();
}
// Today range
$today = date('Y-m-d');
$pending = $conn->query("SELECT COUNT(*) FROM orders WHERE status = 'pending' AND DATE(created_at) = '$today'")->fetchColumn();
$inprogress = $conn->query("SELECT COUNT(*) FROM orders WHERE status = 'in progress' AND DATE(created_at) = '$today'")->fetchColumn();
$completed = $conn->query("SELECT COUNT(*) FROM orders WHERE status = 'completed' AND DATE(created_at) = '$today'")->fetchColumn();
ob_start();
?>
<div class="flex-1 p-6 md:ml-64 flex flex-col items-center justify-start min-w-screen">
    <h1 class="text-2xl font-bold text-[#006837] mb-6 text-center">Welcome, Barista!</h1>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8 w-full max-w-2xl mx-auto justify-items-center px-2 sm:px-0">
        <div class="bg-white rounded-xl shadow p-4 text-center w-48">
            <div class="text-gray-500">Pending</div>
            <div class="text-2xl font-bold text-yellow-600"><?= $pending ?></div>
        </div>
        <div class="bg-white rounded-xl shadow p-4 text-center w-48">
            <div class="text-gray-500">In Progress</div>
            <div class="text-2xl font-bold text-blue-600"><?= $inprogress ?></div>
        </div>
        <div class="bg-white rounded-xl shadow p-4 text-center w-48">
            <div class="text-gray-500">Completed</div>
            <div class="text-2xl font-bold text-green-600"><?= $completed ?></div>
        </div>
    </div>
    <div class="mb-6 bg-white rounded-xl shadow p-6 w-full max-w-md mx-auto">
        <p class="mb-4 text-center">Manage and prepare today's orders efficiently.</p>
        <div class="flex gap-4 flex-wrap justify-center">
            <a href="orders.php" class="bg-[#009245] text-white px-4 py-2 rounded shadow hover:bg-green-800 flex items-center"><i class="fas fa-list mr-2"></i>View Orders</a>
            <a href="profile.php" class="bg-gray-700 text-white px-4 py-2 rounded shadow hover:bg-black flex items-center"><i class="fas fa-user mr-2"></i>Profile</a>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
include 'layout_barista.php';
?>
<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
$notification = $_SESSION['notification'] ?? '';
unset($_SESSION['notification']);
?>
<?php if ($notification): ?>
  <div class="bg-green-100 border border-green-400 text-green-700 p-4 rounded mb-6 text-center max-w-xl mx-auto">
    <?= htmlspecialchars($notification) ?>
  </div>
<?php endif; ?>
