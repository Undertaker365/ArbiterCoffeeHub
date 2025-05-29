<?php
require_once '../includes/db_util.php';
require_once '../includes/csrf.php';
csrf_validate();
ob_start();
?>
<h1 class="text-2xl font-bold text-[#006837] mb-6">Product Management</h1>
<form method="GET" class="mb-4 flex flex-wrap gap-2 items-center">
    <?= csrf_input() ?>
    <input type="text" name="search" placeholder="Search products..." class="border px-3 py-2 rounded" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" />
    <input type="number" name="min_price" step="0.01" placeholder="Min Price" class="border px-3 py-2 rounded w-28" value="<?= htmlspecialchars($_GET['min_price'] ?? '') ?>" />
    <input type="number" name="max_price" step="0.01" placeholder="Max Price" class="border px-3 py-2 rounded w-28" value="<?= htmlspecialchars($_GET['max_price'] ?? '') ?>" />
    <input type="date" name="start_date" class="border px-3 py-2 rounded" value="<?= htmlspecialchars($_GET['start_date'] ?? '') ?>" />
    <input type="date" name="end_date" class="border px-3 py-2 rounded" value="<?= htmlspecialchars($_GET['end_date'] ?? '') ?>" />
    <button type="submit" class="bg-[#009245] text-white px-4 py-2 rounded">Filter</button>
</form>
<div class="mb-4 flex flex-wrap gap-2 justify-between items-center">
    <a href="add_product.php" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded"><i class="fas fa-plus mr-1"></i> Add Product</a>
</div>
<div class="overflow-x-auto bg-white rounded shadow px-2 sm:px-4">
    <table class="w-full table-auto border-collapse">
        <thead class="bg-green-100 text-green-900">
            <tr>
                <th class="px-4 py-2">Image</th>
                <th class="px-4 py-2">Name</th>
                <th class="px-4 py-2">Category</th>
                <th class="px-4 py-2">Price</th>
                <th class="px-4 py-2">Featured</th>
                <th class="px-4 py-2">New?</th>
                <th class="px-4 py-2">Actions</th>
            </tr>
        </thead>
        <tbody id="productsBody" class="bg-white text-gray-700">
            <!-- AJAX content here -->
        </tbody>
    </table>
    <div id="pagination" class="flex justify-center mt-4"></div>
</div>
<div id="loading-indicator" class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50" style="display:none;">
    <div class="bg-white px-6 py-4 rounded shadow text-lg flex items-center gap-3">
        <i class="fas fa-spinner fa-spin text-green-700 text-2xl"></i> Loading...
    </div>
</div>
<!-- Pagination placeholder: implement server-side pagination as needed -->
<?php
if (isset($_POST['toggle_new'])) {
  $pid = (int)$_POST['product_id'];
  $cur = db_fetch_one("SELECT is_new FROM products WHERE id=?", [$pid]);
  $new = $cur && $cur['is_new'] ? 0 : 1;
  db_execute("UPDATE products SET is_new=? WHERE id=?", [$new, $pid]);
  header('Location: products.php');
  exit();
}

$where = [];
$params = [];
if (!empty($_GET['search'])) {
    $where[] = '(name LIKE ? OR description LIKE ?)';
    $params[] = '%' . $_GET['search'] . '%';
    $params[] = '%' . $_GET['search'] . '%';
}
if (!empty($_GET['min_price'])) {
    $where[] = 'price >= ?';
    $params[] = $_GET['min_price'];
}
if (!empty($_GET['max_price'])) {
    $where[] = 'price <= ?';
    $params[] = $_GET['max_price'];
}
if (!empty($_GET['start_date'])) {
    $where[] = 'created_at >= ?';
    $params[] = $_GET['start_date'] . ' 00:00:00';
}
if (!empty($_GET['end_date'])) {
    $where[] = 'created_at <= ?';
    $params[] = $_GET['end_date'] . ' 23:59:59';
}
$where_sql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';
$products = db_fetch_all("SELECT * FROM products $where_sql ORDER BY created_at DESC", $params);

$content = ob_get_clean();
include 'layout_admin.php';
?>
