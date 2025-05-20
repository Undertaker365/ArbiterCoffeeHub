<?php
require_once '../db_connect.php';
ob_start();
?>
<h1 class="text-2xl font-bold text-[#006837] mb-6">Order #<?= htmlspecialchars($order['id']) ?></h1>
<div class="bg-white p-6 rounded shadow max-w-2xl">
    <h2 class="text-lg font-semibold mb-2">Customer Info</h2>
    <p><strong>Name:</strong> <?= htmlspecialchars($order['first_name'] . ' ' . $order['last_name']) ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($order['email']) ?></p>
    <hr class="my-4">
    <h2 class="text-lg font-semibold mb-2">Order Details</h2>
    <p><strong>Total Price:</strong> ₱<?= number_format($order['total_price'], 2) ?></p>
    <p><strong>Status:</strong>
        <select id="orderStatus" class="border rounded px-2 py-1 <?= $order['status'] === 'completed' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' ?>">
            <option value="pending" <?= $order['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
            <option value="preparing" <?= $order['status'] === 'preparing' ? 'selected' : '' ?>>Preparing</option>
            <option value="completed" <?= $order['status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
        </select>
    </p>
    <p><strong>Date:</strong> <?= date('M d, Y H:i', strtotime($order['created_at'])) ?></p>
    <!-- Order items placeholder: implement if you have an order_items table -->
    <div class="mt-4 text-gray-500">Order items details not implemented (add if you have order_items table).</div>
    <a href="orders.php" class="mt-4 inline-block text-blue-600 hover:underline">Back to Orders</a>
</div>
</div>
<script>
document.getElementById('orderStatus').addEventListener('change', function() {
    fetch('update_order_status.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `id=<?= $order['id'] ?>&status=${this.value}`
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            showToast('Order status updated!', 'success');
        } else {
            showToast(data.message || 'Update failed.', 'error');
        }
    });
});
</script>
<?php
$content = ob_get_clean();
include 'layout_admin.php';
?>
