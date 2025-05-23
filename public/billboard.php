<?php
require_once '../db_connect.php';
$stmt = $conn->prepare("SELECT * FROM products WHERE is_new = 1");
$stmt->execute();
$newProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);
$page_title = 'New Menu Items - Arbiter Coffee Hub';
ob_start();
?>
<section class="py-16 bg-white">
  <div class="max-w-6xl mx-auto px-4 text-center">
    <h2 class="text-4xl font-bold text-[#009245] mb-10">New Menu Items</h2>
    <div class="flex justify-center">
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
        <?php foreach ($newProducts as $product): ?>
          <div class="bg-white rounded-2xl shadow-md hover:shadow-xl transition duration-300 overflow-hidden relative">
            <img src="../uploads/<?= htmlspecialchars($product['image_filename'] ?? '', ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($product['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>" class="w-full h-48 object-cover">
            <span class="absolute top-2 left-2 bg-[#009245] text-white text-xs px-2 py-1 rounded">NEW</span>
            <div class="p-5 text-left">
              <h3 class="text-lg font-semibold text-[#009245] text-center"><?= htmlspecialchars($product['name']) ?></h3>
              <p class="text-gray-600 text-sm text-center"><?= htmlspecialchars($product['description']) ?></p>
              <p class="text-gray-800 font-bold mt-2 text-center"><?= number_format($product['price'], 2) ?> PHP</p>
              <div class="flex justify-center">
                <a href="login.php" class="inline-block mt-3 bg-[#1A1A1A] text-white px-4 py-2 rounded-full text-sm hover:bg-black">
                  Order Now
                </a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php if (count($newProducts) === 0): ?>
      <p class="text-gray-600 mt-10 text-center">No new menu items at the moment.</p>
    <?php endif; ?>
  </div>
</section>
<?php
$content = ob_get_clean();
include 'layout_public.php';
