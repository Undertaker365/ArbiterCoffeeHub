<?php
session_start();
require_once '../includes/db_util.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Customer') {
    http_response_code(403);
    exit();
}
$user_id = $_SESSION['user_id'];
$orders = db_fetch_all("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC", [$user_id]);
foreach ($orders as $order): ?>
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
