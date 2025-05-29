<?php
require_once '../db_connect.php';
header('Content-Type: text/html; charset=UTF-8');
$orders = $conn->query("SELECT * FROM orders ORDER BY created_at DESC LIMIT 20")->fetchAll(PDO::FETCH_ASSOC);
function next_status($status) {
    if ($status === 'pending') return 'in progress';
    if ($status === 'in progress') return 'ready';
    if ($status === 'ready') return 'completed';
    return null;
}
foreach ($orders as $order): ?>
<li class="py-2 flex justify-between items-center">
    <span>Order #<?= htmlspecialchars($order['id']) ?></span>
    <span class="<?= $order['status'] === 'completed' ? 'text-green-600' : ($order['status'] === 'pending' ? 'text-yellow-600' : ($order['status'] === 'in progress' ? 'text-blue-600' : 'text-gray-600')) ?>">
        <?= ucfirst($order['status']) ?>
    </span>
    <?php $next = next_status($order['status']); if ($next): ?>
    <form method="post" class="inline">
        <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
        <input type="hidden" name="next_status" value="<?= $next ?>">
        <button class="bg-[#009245] text-white px-3 py-1 rounded hover:bg-green-800 ml-2" type="submit">Mark as <?= ucfirst($next) ?></button>
    </form>
    <?php endif; ?>
</li>
<?php endforeach; ?>
<?php if (empty($orders)): ?>
<li class="py-2 text-gray-400">No orders found.</li>
<?php endif; ?>
