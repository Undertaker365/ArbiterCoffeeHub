<?php
require_once '../includes/db_util.php';
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Customer') {
    header('Location: ../public/login.php');
    exit();
}
if (empty($_SESSION['cart'])) {
    header('Location: cart.php');
    exit();
}
$cart = $_SESSION['cart'];
$ids = implode(',', array_map('intval', array_keys($cart)));
$products = db_fetch_all("SELECT * FROM products WHERE id IN ($ids)");
$total = 0;
foreach ($products as &$p) {
    $p['quantity'] = $cart[$p['id']];
    $p['subtotal'] = $p['quantity'] * $p['price'];
    $total += $p['subtotal'];
}
$orderSuccess = false;
$csrf_token = bin2hex(random_bytes(32));
$_SESSION['csrf_token'] = $csrf_token;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
        die('Invalid CSRF token.');
    }
    $user_id = $_SESSION['user_id'];
    $status = 'pending';
    db_execute("INSERT INTO orders (user_id, total_price, status, created_at) VALUES (?, ?, ?, NOW())", [$user_id, $total, $status]);
    $order_id = db_last_insert_id();
    foreach ($products as $p) {
        db_execute("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)", [$order_id, $p['id'], $p['quantity'], $p['price']]);
    }
    unset($_SESSION['cart']);
    $orderSuccess = true;
    $_SESSION['notification'] = 'Order placed successfully!';
}
$page_title = 'Checkout - Arbiter Coffee Hub';
ob_start();
?>
<section class="py-16 bg-white">
  <div class="max-w-3xl mx-auto px-2 sm:px-4">
    <h2 class="text-3xl font-bold text-[#006837] mb-8 text-center">Checkout</h2>
    <?php if ($orderSuccess): ?>
      <div class="bg-green-100 border border-green-400 text-green-700 p-4 rounded mb-6 text-center">Order placed successfully!</div>
      <div class="flex justify-center"><a href="dashboard.php" class="bg-[#009245] text-white px-6 py-2 rounded-full font-semibold hover:bg-green-800">Go to Dashboard</a></div>
    <?php else: ?>
    <form method="post">
      <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
      <table class="w-full mb-6 text-left">
        <thead>
          <tr class="border-b">
            <th>Product</th>
            <th>Price</th>
            <th>Qty</th>
            <th>Subtotal</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($products as $p): ?>
          <tr class="border-b">
            <td class="py-2 flex items-center gap-2">
              <img src="../uploads/<?= htmlspecialchars($p['image_filename'] ?? '', ENT_QUOTES, 'UTF-8') ?>" class="h-12 w-12 object-cover rounded" alt="<?= htmlspecialchars($p['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
              <?= htmlspecialchars($p['name']) ?>
            </td>
            <td><?= number_format($p['price'], 2) ?> PHP</td>
            <td><?= $p['quantity'] ?></td>
            <td><?= number_format($p['subtotal'], 2) ?> PHP</td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <div class="flex justify-between items-center mb-6">
        <div class="text-xl font-bold">Total: <?= number_format($total, 2) ?> PHP</div>
      </div>
      <div class="flex justify-end">
        <button name="place_order" value="1" type="submit" class="bg-[#009245] text-white px-6 py-2 rounded-full font-semibold hover:bg-green-800">Place Order</button>
      </div>
    </form>
    <?php endif; ?>
  </div>
</section>
<?php
$content = ob_get_clean();
include 'layout_customer.php';
