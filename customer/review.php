<?php
require_once '../includes/db_util.php';
require_once '../includes/csrf.php';
csrf_validate();
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Customer') {
    header('Location: ../public/login.php');
    exit();
}
$user_id = $_SESSION['user_id'];
// Fetch products from completed orders
$products = db_fetch_all("SELECT oi.product_id, p.name, p.image_filename FROM order_items oi JOIN orders o ON oi.order_id = o.id JOIN products p ON oi.product_id = p.id WHERE o.user_id = ? AND o.status = 'completed' GROUP BY oi.product_id", [$user_id]);
// Handle review submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'], $_POST['rating'], $_POST['review'])) {
    $pid = (int)$_POST['product_id'];
    $rating = max(1, min(5, (int)$_POST['rating']));
    $review = trim($_POST['review']);
    db_execute("INSERT INTO reviews (user_id, product_id, rating, review, created_at) VALUES (?, ?, ?, ?, NOW())", [$user_id, $pid, $rating, $review]);
    $success = true;
}
$page_title = 'Leave a Review - Arbiter Coffee Hub';
ob_start();
?>
<section class="py-16 bg-white">
  <div class="max-w-3xl mx-auto px-2 sm:px-4">
    <h2 class="text-3xl font-bold text-[#006837] mb-8 text-center">Leave a Product Review</h2>
    <?php if (!empty($success)): ?>
      <div class="bg-green-100 border border-green-400 text-green-700 p-4 rounded mb-6 text-center">Thank you for your review!</div>
    <?php endif; ?>
    <?php if ($products): ?>
      <form method="post" class="space-y-6">
        <?= csrf_input() ?>
        <label for="product_id" class="block font-semibold mb-2">Select Product</label>
        <select name="product_id" id="product_id" class="border rounded px-4 py-2 w-full mb-4">
          <?php foreach ($products as $p): ?>
            <option value="<?= $p['product_id'] ?>"><?= htmlspecialchars($p['name']) ?></option>
          <?php endforeach; ?>
        </select>
        <label class="block font-semibold mb-2">Rating</label>
        <select name="rating" class="border rounded px-4 py-2 w-full mb-4">
          <option value="5">5 - Excellent</option>
          <option value="4">4 - Good</option>
          <option value="3">3 - Average</option>
          <option value="2">2 - Poor</option>
          <option value="1">1 - Terrible</option>
        </select>
        <label class="block font-semibold mb-2">Review</label>
        <textarea name="review" rows="4" class="border rounded px-4 py-2 w-full mb-4" required></textarea>
        <div class="flex justify-end">
          <button type="submit" class="bg-[#009245] text-white px-6 py-2 rounded-full font-semibold hover:bg-green-800">Submit Review</button>
        </div>
      </form>
    <?php else: ?>
      <p class="text-gray-600 text-center">You have no completed orders to review.</p>
    <?php endif; ?>
  </div>
</section>
<?php
$content = ob_get_clean();
include 'layout_customer.php';
