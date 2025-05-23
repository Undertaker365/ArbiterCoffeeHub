<?php
require_once '../db_connect.php';
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header('Location: ../public/login.php');
    exit();
}
$stmt = $conn->query("SELECT r.*, u.first_name, u.last_name, p.name AS product_name FROM reviews r JOIN users u ON r.user_id = u.id JOIN products p ON r.product_id = p.id ORDER BY r.created_at DESC");
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (isset($_POST['delete_review'])) {
    $rid = (int)$_POST['review_id'];
    $conn->prepare("DELETE FROM reviews WHERE id = ?")->execute([$rid]);
    header('Location: reviews.php');
    exit();
}
$page_title = 'Product Reviews - Arbiter Coffee Hub';
ob_start();
?>
<section class="py-16 bg-white">
  <div class="max-w-5xl mx-auto px-4">
    <h2 class="text-3xl font-bold text-[#006837] mb-8 text-center">Product Reviews</h2>
    <table class="w-full mb-6 text-left">
      <thead>
        <tr class="border-b">
          <th>Product</th>
          <th>User</th>
          <th>Rating</th>
          <th>Review</th>
          <th>Date</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($reviews as $r): ?>
        <tr class="border-b">
          <td><?= htmlspecialchars($r['product_name']) ?></td>
          <td><?= htmlspecialchars($r['first_name'] . ' ' . $r['last_name']) ?></td>
          <td><?= $r['rating'] ?></td>
          <td><?= htmlspecialchars($r['review']) ?></td>
          <td><?= date('Y-m-d', strtotime($r['created_at'])) ?></td>
          <td>
            <form method="post" style="display:inline;">
              <input type="hidden" name="review_id" value="<?= $r['id'] ?>">
              <button name="delete_review" value="1" type="submit" class="text-red-600 hover:underline">Delete</button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php if (count($reviews) === 0): ?>
      <p class="text-gray-600 text-center">No reviews found.</p>
    <?php endif; ?>
  </div>
</section>
<?php
$content = ob_get_clean();
include 'layout_admin.php';
