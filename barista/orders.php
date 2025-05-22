<?php
session_start();
require_once '../db_connect.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Barista') {
    header('Location: ../public/login.php');
    exit();
}
// Handle status update
if (isset($_POST['order_id'], $_POST['next_status'])) {
    $oid = (int)$_POST['order_id'];
    $next = $_POST['next_status'];
    $conn->prepare("UPDATE orders SET status = ?, updated_at = NOW() WHERE id = ?")
        ->execute([$next, $oid]);
}
$orders = $conn->query("SELECT * FROM orders ORDER BY created_at DESC LIMIT 20")->fetchAll(PDO::FETCH_ASSOC);
function next_status($status) {
    if ($status === 'pending') return 'in progress';
    if ($status === 'in progress') return 'ready';
    if ($status === 'ready') return 'completed';
    return null;
}
ob_start();
?>
<div class="flex-1 p-6 md:ml-64">
    <h1 class="text-2xl font-bold text-[#006837] mb-2">Orders</h1>
    <div class="bg-white rounded-xl shadow p-6">
        <ul class="divide-y divide-gray-200">
            <?php foreach ($orders as $order): ?>
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
        </ul>
    </div>
</div>
<?php
$content = ob_get_clean();
include 'layout_barista.php';
?>
