<?php
require_once '../db_connect.php';
ob_start();
?>
<h1 class="text-2xl font-bold text-[#006837] mb-6">Order Management</h1>
<div class="mb-4 flex flex-wrap gap-2 justify-between items-center">
    <form id="orderSearchForm" class="flex items-center space-x-2" autocomplete="off">
        <input type="text" id="orderSearchInput" name="search" placeholder="Search orders..." class="border px-3 py-2 rounded" />
        <select id="statusFilter" class="border px-2 py-2 rounded">
            <option value="">All Status</option>
            <option value="pending">Pending</option>
            <option value="preparing">Preparing</option>
            <option value="completed">Completed</option>
        </select>
        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700"><i class="fas fa-search"></i></button>
    </form>
</div>
<div class="overflow-x-auto bg-white rounded shadow">
    <table class="w-full table-auto border-collapse">
        <thead class="bg-green-100 text-green-900">
            <tr>
                <th class="px-4 py-2">Order ID</th>
                <th class="px-4 py-2">Customer</th>
                <th class="px-4 py-2">Total Price</th>
                <th class="px-4 py-2">Status</th>
                <th class="px-4 py-2">Date</th>
                <th class="px-4 py-2">Actions</th>
            </tr>
        </thead>
        <tbody id="ordersBody" class="bg-white text-gray-700">
            <?php foreach ($orders as $order): ?>
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
        </tbody>
    </table>
    <div id="orderPagination" class="flex justify-center mt-4"></div>
</div>
<script>
function updateOrderStatus(id, status) {
    fetch('update_order_status.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `id=${id}&status=${status}`
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            showToast('Order status updated!', 'success');
        } else {
            showToast(data.message || 'Update failed.', 'error');
        }
    });
}
document.getElementById('orderSearchForm').addEventListener('submit', function(e) {
    e.preventDefault();
    showToast('Search/filter not yet implemented (demo only)', 'error');
});
document.getElementById('statusFilter').addEventListener('change', function() {
    showToast('Search/filter not yet implemented (demo only)', 'error');
});
</script>
<!-- Pagination placeholder: implement server-side pagination as needed -->
<?php
$content = ob_get_clean();
include 'layout_admin.php';
?>
