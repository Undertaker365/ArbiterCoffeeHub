<?php
require_once '../db_connect.php';
ob_start();
$start = $_GET['start'] ?? date('Y-m-01');
$end = $_GET['end'] ?? date('Y-m-d');
$stmt = $conn->prepare("SELECT o.id, u.first_name, u.last_name, o.total_price, o.status, o.created_at FROM orders o JOIN users u ON o.user_id = u.id WHERE DATE(o.created_at) BETWEEN ? AND ? ORDER BY o.created_at DESC");
$stmt->execute([$start, $end]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalSales = array_sum(array_column($orders, 'total_price'));
$totalOrders = count($orders);
?>
<h1 class="text-2xl font-bold text-[#006837] mb-6">Sales Reports</h1>
<form method="get" class="mb-4 flex items-center space-x-2">
    <label>From: <input type="date" name="start" value="<?= htmlspecialchars($start) ?>" class="border px-2 py-1 rounded" /></label>
    <label>To: <input type="date" name="end" value="<?= htmlspecialchars($end) ?>" class="border px-2 py-1 rounded" /></label>
    <button class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700"><i class="fas fa-search"></i> Filter</button>
    <a href="export_report.php?start=<?= $start ?>&end=<?= $end ?>" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"><i class="fas fa-file-export mr-1"></i> Export CSV</a>
</form>
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
    <div class="bg-white p-6 rounded-xl shadow flex items-center space-x-4">
        <div class="bg-[#009245] text-white p-4 rounded-full"><i class="fas fa-coins text-2xl"></i></div>
        <div>
            <p class="text-gray-500">Total Sales</p>
            <h2 class="text-xl font-bold">₱<?= number_format($totalSales, 2) ?></h2>
        </div>
    </div>
    <div class="bg-white p-6 rounded-xl shadow flex items-center space-x-4">
        <div class="bg-[#009245] text-white p-4 rounded-full"><i class="fas fa-receipt text-2xl"></i></div>
        <div>
            <p class="text-gray-500">Total Orders</p>
            <h2 class="text-xl font-bold"><?= $totalOrders ?></h2>
        </div>
    </div>
</div>
<div class="bg-white p-6 rounded shadow mb-4">
    <h2 class="text-lg font-semibold mb-2">Order Breakdown</h2>
    <table class="w-full table-auto border-collapse mt-2">
        <thead class="bg-green-100 text-green-900">
            <tr>
                <th class="px-4 py-2">Order ID</th>
                <th class="px-4 py-2">Customer</th>
                <th class="px-4 py-2">Total Price</th>
                <th class="px-4 py-2">Status</th>
                <th class="px-4 py-2">Date</th>
            </tr>
        </thead>
        <tbody class="bg-white text-gray-700">
            <?php foreach ($orders as $order): ?>
            <tr class="border-b">
                <td class="px-4 py-2"><?= htmlspecialchars($order['id']) ?></td>
                <td class="px-4 py-2"><?= htmlspecialchars($order['first_name'] . ' ' . $order['last_name']) ?></td>
                <td class="px-4 py-2">₱<?= number_format($order['total_price'], 2) ?></td>
                <td class="px-4 py-2">
                    <span class="inline-block px-2 py-1 text-sm rounded <?= $order['status'] === 'completed' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' ?>">
                        <?= ucfirst($order['status']) ?>
                    </span>
                </td>
                <td class="px-4 py-2"><?= date('M d, Y H:i', strtotime($order['created_at'])) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<script>
// Example: show a toast on export click
const exportBtn = document.querySelector('a[href^="export_report.php"]');
if (exportBtn) {
    exportBtn.addEventListener('click', function(e) {
        showToast('Exporting CSV... (implement server-side export)', 'success');
    });
}
</script>
<?php
$content = ob_get_clean();
include 'layout_admin.php';
