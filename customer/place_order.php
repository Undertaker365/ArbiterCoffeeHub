<?php
session_start();
require_once '../includes/db_util.php';
require_once '../includes/csrf.php';
csrf_validate();
ob_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Customer') {
    header('Location: ../public/login.php');
    exit();
}
// Handle add to cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $pid = (int)$_POST['product_id'];
    $_SESSION['cart'][$pid] = ($_SESSION['cart'][$pid] ?? 0) + 1;
}
// Handle submit order
if (isset($_POST['submit_order']) && !empty($_SESSION['cart'])) {
    $user_id = $_SESSION['user_id'];
    $total = 0;
    $items = [];
    foreach ($_SESSION['cart'] as $pid => $qty) {
        $prod = db_fetch_one("SELECT * FROM products WHERE id = ?", [$pid]);
        if ($prod) {
            $total += $prod['price'] * $qty;
            $items[] = ['id' => $pid, 'qty' => $qty, 'price' => $prod['price']];
        }
    }
    db_execute("INSERT INTO orders (user_id, total_price, status, created_at) VALUES (?, ?, 'pending', NOW())", [$user_id, $total]);
    $order_id = db_last_insert_id();
    foreach ($items as $item) {
        db_execute("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)", [$order_id, $item['id'], $item['qty'], $item['price']]);
    }
    unset($_SESSION['cart']);
    header('Location: order_history.php?success=1');
    exit();
}
$products = db_fetch_all("SELECT * FROM products ORDER BY category, name");
$cart = $_SESSION['cart'] ?? [];
?>
<div class="flex-1 p-6 md:ml-64">
    <h1 class="text-2xl font-bold text-[#006837] mb-4">Place an Order</h1>
    <div class="bg-white rounded-xl shadow p-6 mb-6">
        <form method="post" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 w-full px-2 sm:px-0">
            <?= csrf_input() ?>
            <?php foreach ($products as $prod): ?>
            <div class="bg-gray-50 rounded-lg p-4 flex flex-col items-center">
                <?php if ($prod['image_filename']): ?>
                    <img src="../uploads/<?= htmlspecialchars($prod['image_filename']) ?>" class="h-20 w-20 object-cover rounded mb-2" alt="<?= htmlspecialchars($prod['name']) ?>">
                <?php endif; ?>
                <span class="font-semibold text-gray-800"><?= htmlspecialchars($prod['name']) ?></span>
                <span class="text-gray-500 text-sm mb-2">₱<?= number_format($prod['price'], 2) ?></span>
                <button name="product_id" value="<?= $prod['id'] ?>" class="bg-[#009245] text-white px-4 py-1 rounded hover:bg-green-800" type="submit">Add to Cart</button>
            </div>
            <?php endforeach; ?>
        </form>
    </div>
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-lg font-semibold text-[#006837] mb-2">Your Cart</h2>
        <ul class="divide-y divide-gray-200 mb-4">
            <?php $total = 0; foreach ($cart as $pid => $qty):
                $prod = $conn->query("SELECT * FROM products WHERE id = $pid")->fetch(PDO::FETCH_ASSOC);
                if ($prod): $total += $prod['price'] * $qty; ?>
                <li class="py-2 flex justify-between"><span><?= htmlspecialchars($prod['name']) ?> x<?= $qty ?></span><span>₱<?= number_format($prod['price'] * $qty, 2) ?></span></li>
            <?php endif; endforeach; ?>
            <?php if (empty($cart)): ?>
                <li class="py-2 text-gray-400">Cart is empty.</li>
            <?php endif; ?>
        </ul>
        <?php if (!empty($cart)): ?>
        <div class="font-bold mb-2">Total: ₱<?= number_format($total, 2) ?></div>
        <form method="post">
            <?= csrf_input() ?>
            <button name="submit_order" class="bg-[#006837] text-white px-6 py-2 rounded hover:bg-green-900">Submit Order</button>
        </form>
        <?php endif; ?>
    </div>
</div>
<?php
$content = ob_get_clean();
include 'layout_customer.php';
?>
