<?php
session_start();
require_once '../db_connect.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Customer') {
    header('Location: ../public/login.php');
    exit();
}
$user_id = $_SESSION['user_id'];
$orders = $conn->query("SELECT * FROM orders WHERE user_id = $user_id ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
ob_start();
?>
<div class="flex-1 p-6 md:ml-64">
    <h1 class="text-2xl font-bold text-[#006837] mb-2">Order History</h1>
    <div class="bg-white rounded-xl shadow p-6">
        <ul class="divide-y divide-gray-200">
            <?php foreach ($orders as $order): ?>
            <li class="py-2 flex justify-between">
                <span>Order #<?= htmlspecialchars($order['id']) ?></span>
                <span class="<?= $order['status'] === 'completed' ? 'text-green-600' : ($order['status'] === 'pending' ? 'text-yellow-600' : 'text-gray-600') ?>">
                    <?= ucfirst($order['status']) ?>
                </span>
            </li>
            <?php endforeach; ?>
            <?php if (empty($orders)): ?>
            <li class="py-2 text-gray-400">No orders found.</li>
            <?php endif; ?>
        </ul>
    </div>
</div>
<?php
$content = ob_get_clean();
include 'layout_customer.php';
?>
