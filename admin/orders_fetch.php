<?php
require_once '../includes/db_util.php';
header('Content-Type: text/html; charset=UTF-8');

// Fetch all orders with customer names from the database
$orders = db_fetch_all("SELECT o.*, u.first_name, u.last_name FROM orders o LEFT JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC");

foreach ($orders as $order): ?>
<tr class="border-b">
    <td class="px-4 py-2"><?= htmlspecialchars($order['id']) ?></td>
    <td class="px-4 py-2"><?= htmlspecialchars($order['first_name'] . ' ' . $order['last_name']) ?></td>
    <td class="px-4 py-2">₱<?= number_format($order['total_price'], 2) ?></td>
    <td class="px-4 py-2">
        <select onchange="updateOrderStatus(<?= $order['id'] ?>, this.value)" class="border rounded px-2 py-1 <?= $order['status'] === 'completed' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' ?>">
            <option value="pending" <?= $order['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
            <option value="preparing" <?= $order['status'] === 'preparing' ? 'selected' : '' ?>>Preparing</option>
            <option value="completed" <?= $order['status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
        </select>
    </td>
    <td class="px-4 py-2"><?= date('M d, Y H:i', strtotime($order['created_at'])) ?></td>
    <td class="px-4 py-2 space-x-2">
        <a href="view_order.php?id=<?= $order['id'] ?>" class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded"><i class="fas fa-eye"></i> View</a>
    </td>
</tr>
<?php endforeach; ?>
