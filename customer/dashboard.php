<?php
session_start();
require_once '../db_connect.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Customer') {
    header('Location: ../public/login.php');
    exit();
}
$user_id = $_SESSION['user_id'];
$total_orders = $conn->query("SELECT COUNT(*) FROM orders WHERE user_id = $user_id")->fetchColumn();
$pending_orders = $conn->query("SELECT COUNT(*) FROM orders WHERE user_id = $user_id AND status = 'pending'")->fetchColumn();
$completed_orders = $conn->query("SELECT COUNT(*) FROM orders WHERE user_id = $user_id AND status = 'completed'")->fetchColumn();
$recent_orders = $conn->query("SELECT * FROM orders WHERE user_id = $user_id ORDER BY created_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
ob_start();
?>
<div class="flex-1 p-6 md:ml-64">
    <h1 class="text-2xl font-bold text-[#006837] mb-2">Welcome, Customer!</h1>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        <div class="bg-white rounded-xl shadow p-4 text-center">
            <div class="text-gray-500">Orders Placed</div>
            <div class="text-2xl font-bold text-[#009245]"><?= $total_orders ?></div>
        </div>
        <div class="bg-white rounded-xl shadow p-4 text-center">
            <div class="text-gray-500">Pending</div>
            <div class="text-2xl font-bold text-yellow-600"><?= $pending_orders ?></div>
        </div>
        <div class="bg-white rounded-xl shadow p-4 text-center">
            <div class="text-gray-500">Completed</div>
            <div class="text-2xl font-bold text-green-600"><?= $completed_orders ?></div>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow p-6 mb-6">
        <h2 class="text-lg font-semibold text-[#006837] mb-2">Recent Orders</h2>
        <ul class="divide-y divide-gray-200">
            <?php foreach ($recent_orders as $order): ?>
            <li class="py-2 flex justify-between">
                <span>Order #<?= htmlspecialchars($order['id']) ?></span>
                <span class="<?= $order['status'] === 'completed' ? 'text-green-600' : ($order['status'] === 'pending' ? 'text-yellow-600' : 'text-gray-600') ?>">
                    <?= ucfirst($order['status']) ?>
                </span>
            </li>
            <?php endforeach; ?>
            <?php if (empty($recent_orders)): ?>
            <li class="py-2 text-gray-400">No recent orders.</li>
            <?php endif; ?>
        </ul>
    </div>
</div>
<?php
$content = ob_get_clean();
include 'layout_customer.php';
?>
