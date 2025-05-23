<?php
require_once '../db_connect.php';

$stmt = $conn->prepare("SELECT * FROM products");
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
$page_title = 'Menu - Arbiter Coffee Hub';
ob_start();
?>
<form method="GET" class="mb-8 flex justify-center">
  <input type="text" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="Search menu..." class="border rounded-l px-4 py-2 w-64 focus:outline-none focus:ring-2 focus:ring-[#009245]">
  <button type="submit" class="bg-[#009245] text-white px-4 py-2 rounded-r font-semibold hover:bg-[#006837]">Search</button>
</form>
<?php
$search = trim($_GET['search'] ?? '');
$filteredProducts = $products;
if ($search !== '') {
  $filteredProducts = array_filter($products, function($p) use ($search) {
    return stripos($p['name'], $search) !== false || stripos($p['description'], $search) !== false;
  });
}
?>
<section class="py-16 bg-white">
  <div class="max-w-6xl mx-auto px-4 text-center">
    <h2 class="text-4xl font-bold text-[#006837] mb-10 text-center">Our Coffee Menu</h2>
    <div class="flex justify-center">
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
        <?php foreach ($filteredProducts as $product): ?>
          <div class="bg-white rounded-2xl shadow-md hover:shadow-xl transition duration-300 overflow-hidden">
            <img src="../uploads/<?= htmlspecialchars($product['image_filename'] ?? '', ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($product['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>" class="w-full h-48 object-cover">
            <?php if (!empty($product['is_new'])): ?><span class="absolute top-2 left-2 bg-[#009245] text-white text-xs px-2 py-1 rounded">NEW</span><?php endif; ?>
            <div class="p-5 text-left">
              <h3 class="text-lg font-semibold text-[#009245] text-center"><?= htmlspecialchars($product['name']) ?></h3>
              <p class="text-gray-600 text-sm text-center"><?= htmlspecialchars($product['description']) ?></p>
              <p class="text-gray-800 font-bold mt-2 text-center"><?= number_format($product['price'], 2) ?> PHP</p>
              <div class="flex justify-center">
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'Customer'): ?>
                  <form method="post" action="../customer/cart.php">
                    <input type="hidden" name="add_to_cart" value="1">
                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                    <input type="number" name="quantity" value="1" min="1" class="w-16 border rounded px-2 py-1 mr-2">
                    <button type="submit" class="bg-[#009245] text-white px-4 py-2 rounded-full text-sm hover:bg-green-800">Add to Cart</button>
                  </form>
                <?php else: ?>
                  <a href="login.php" class="inline-block mt-3 bg-[#1A1A1A] text-white px-4 py-2 rounded-full text-sm hover:bg-black">
                    Order Now
                  </a>
                <?php endif; ?>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php if (count($filteredProducts) === 0): ?>
      <p class="text-gray-600 mt-10 text-center">No products found.</p>
    <?php endif; ?>
  </div>
</section>
<?php
$content = ob_get_clean();
include 'layout_public.php';
?>
