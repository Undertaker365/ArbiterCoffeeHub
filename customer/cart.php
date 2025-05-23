<?php
require_once '../db_connect.php';
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Customer') {
    header('Location: ../public/login.php');
    exit();
}

// Initialize cart if not set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle add, update, remove actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_to_cart'])) {
        $pid = (int)$_POST['product_id'];
        $qty = max(1, (int)$_POST['quantity']);
        if (isset($_SESSION['cart'][$pid])) {
            $_SESSION['cart'][$pid] += $qty;
        } else {
            $_SESSION['cart'][$pid] = $qty;
        }
        header('Location: cart.php');
        exit();
    }
    if (isset($_POST['update_cart'])) {
        foreach ($_POST['quantities'] as $pid => $qty) {
            $pid = (int)$pid;
            $qty = max(1, (int)$qty);
            $_SESSION['cart'][$pid] = $qty;
        }
        header('Location: cart.php');
        exit();
    }
    if (isset($_POST['remove_item'])) {
        $pid = (int)$_POST['product_id'];
        unset($_SESSION['cart'][$pid]);
        header('Location: cart.php');
        exit();
    }
}

// Fetch products in cart
$cartProducts = [];
$total = 0;
if ($_SESSION['cart']) {
    $ids = implode(',', array_map('intval', array_keys($_SESSION['cart'])));
    $stmt = $conn->query("SELECT * FROM products WHERE id IN ($ids)");
    $cartProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($cartProducts as &$p) {
        $p['quantity'] = $_SESSION['cart'][$p['id']];
        $p['subtotal'] = $p['quantity'] * $p['price'];
        $total += $p['subtotal'];
    }
}
$page_title = 'My Cart - Arbiter Coffee Hub';
ob_start();
?>
<section class="py-16 bg-white">
  <div class="max-w-3xl mx-auto px-4">
    <h2 class="text-3xl font-bold text-[#006837] mb-8 text-center">My Cart</h2>
    <?php if ($cartProducts): ?>
    <form method="post">
      <table class="w-full mb-6 text-left">
        <thead>
          <tr class="border-b">
            <th>Product</th>
            <th>Price</th>
            <th>Qty</th>
            <th>Subtotal</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($cartProducts as $p): ?>
          <tr class="border-b">
            <td class="py-2 flex items-center gap-2">
              <img src="../uploads/<?= htmlspecialchars($p['image_filename'] ?? '', ENT_QUOTES, 'UTF-8') ?>" class="h-12 w-12 object-cover rounded" alt="<?= htmlspecialchars($p['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
              <?= htmlspecialchars($p['name']) ?>
            </td>
            <td><?= number_format($p['price'], 2) ?> PHP</td>
            <td><input type="number" name="quantities[<?= $p['id'] ?>]" value="<?= $p['quantity'] ?>" min="1" class="w-16 border rounded px-2 py-1"></td>
            <td><?= number_format($p['subtotal'], 2) ?> PHP</td>
            <td>
              <button name="remove_item" value="1" type="submit" class="text-red-600 hover:underline" onclick="this.form.product_id.value=<?= $p['id'] ?>;">Remove</button>
              <input type="hidden" name="product_id" value="">
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <div class="flex justify-between items-center mb-6">
        <div class="text-xl font-bold">Total: <?= number_format($total, 2) ?> PHP</div>
        <button name="update_cart" value="1" type="submit" class="bg-[#009245] text-white px-4 py-2 rounded hover:bg-[#006837]">Update Cart</button>
      </div>
      <div class="flex justify-end">
        <a href="checkout.php" class="bg-[#1A1A1A] text-white px-6 py-2 rounded-full font-semibold hover:bg-black">Proceed to Checkout</a>
      </div>
    </form>
    <?php else: ?>
      <p class="text-gray-600 text-center">Your cart is empty.</p>
    <?php endif; ?>
  </div>
</section>
<?php
$content = ob_get_clean();
include 'layout_customer.php';
