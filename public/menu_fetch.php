<?php
require_once '../includes/db_util.php';
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
header('Content-Type: text/html; charset=UTF-8');
$products = db_fetch_all("SELECT * FROM products");
$search = trim($_GET['search'] ?? '');
$category = $_GET['category'] ?? '';
$sort = $_GET['sort'] ?? '';
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage = 8;
$filteredProducts = $products;
if ($search !== '') {
    $filteredProducts = array_filter($filteredProducts, function($p) use ($search) {
        return stripos($p['name'], $search) !== false || stripos($p['description'], $search) !== false;
    });
}
if (!empty($category)) {
    $filteredProducts = array_filter($filteredProducts, function($p) use ($category) {
        return $p['category'] === $category;
    });
}
if (!empty($sort)) {
    usort($filteredProducts, function($a, $b) use ($sort) {
        if ($sort === 'price_asc') return $a['price'] <=> $b['price'];
        if ($sort === 'price_desc') return $b['price'] <=> $a['price'];
        if ($sort === 'name_asc') return strcmp($a['name'], $b['name']);
        if ($sort === 'name_desc') return strcmp($b['name'], $a['name']);
        return 0;
    });
}
$totalProducts = count($filteredProducts);
$totalPages = max(1, ceil($totalProducts / $perPage));
$start = ($page - 1) * $perPage;
$productsToShow = array_slice($filteredProducts, $start, $perPage);
?>
<div id="product-grid" class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-10 w-full overflow-x-auto scrollbar-hide min-h-[300px]" aria-live="polite" role="region" aria-labelledby="menu-section-title">
<?php foreach ($productsToShow as $product): ?>
  <div class="bg-white rounded-3xl shadow-lg hover:shadow-2xl hover:scale-[1.04] hover:-translate-y-2 transition-transform duration-300 overflow-hidden flex flex-col h-full group animate-fade-in-up relative product-card" tabindex="0" aria-label="<?= htmlspecialchars($product['name']) ?> card">
    <div class="relative">
      <img src="../uploads/<?= htmlspecialchars($product['image_filename'] ?? '', ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($product['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>" class="w-full h-52 object-cover transition-transform duration-300 group-hover:scale-110 group-hover:brightness-95 rounded-t-3xl" loading="lazy">
      <?php if (!empty($product['is_new'])): ?><span class="absolute top-3 left-3 bg-[#009245] text-white text-xs px-3 py-1 rounded-full shadow">NEW</span><?php endif; ?>
      <?php if (!empty($product['is_best_seller'])): ?><span class="absolute top-3 right-3 bg-[#006837] text-white text-xs px-3 py-1 rounded-full shadow">Best Seller</span><?php endif; ?>
    </div>
    <div class="p-6 text-left flex flex-col flex-1">
      <h3 class="text-xl font-bold text-[#009245] text-center truncate mb-1" title="<?= htmlspecialchars($product['name']) ?>" tabindex="0" aria-label="<?= htmlspecialchars($product['name']) ?>"><?= htmlspecialchars($product['name']) ?></h3>
      <p class="text-[#1A1A1A] text-sm text-center flex-1 mb-2 line-clamp-2" title="<?= htmlspecialchars($product['description']) ?>"><?= htmlspecialchars($product['description']) ?></p>
      <p class="text-[#1A1A1A] font-extrabold mt-2 text-center text-lg tracking-wide"><?= number_format($product['price'], 2) ?> <span class="text-xs font-semibold">PHP</span></p>
      <div class="flex justify-center mt-4">
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'Customer'): ?>
          <form method="post" action="../customer/cart.php" class="flex items-center gap-2">
            <input type="hidden" name="add_to_cart" value="1">
            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
            <input type="number" name="quantity" value="1" min="1" class="w-16 border rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-[#009245]">
            <button type="submit" class="bg-[#009245] text-white px-4 py-2 rounded-full text-sm font-semibold hover:bg-[#006837] focus:outline-none focus:ring-2 focus:ring-[#009245] transition">Add to Cart</button>
          </form>
        <?php else: ?>
          <a href="#" class="order-now-btn inline-block bg-[#1A1A1A] text-white px-4 py-2 rounded-full text-sm font-semibold hover:bg-black focus:outline-none focus:ring-2 focus:ring-[#009245] transition" aria-label="Order <?= htmlspecialchars($product['name']) ?>" role="button" tabindex="0">
            Order Now
          </a>
        <?php endif; ?>
      </div>
    </div>
  </div>
<?php endforeach; ?>
<?php if (count($productsToShow) === 0): ?>
  <div class="flex flex-col items-center justify-center min-h-[60vh] w-full text-[#1A1A1A] text-center gap-6 animate-fade-in-up" style="position:absolute;left:0;right:0;top:0;bottom:0;">
    <svg width="80" height="80" fill="none" viewBox="0 0 80 80" aria-hidden="true" focusable="false" class="mb-2">
      <circle cx="40" cy="40" r="38" fill="#F3F4F6"/>
      <path d="M25 50l15-15 15 15" stroke="#006837" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
      <path d="M40 35v15" stroke="#006837" stroke-width="4" stroke-linecap="round"/>
    </svg>
    <span class="block text-xl font-semibold">No products found.</span>
    <span class="block text-base">Try a different search, adjust your filters, or <a href='contact.php' class='text-[#009245] underline font-semibold hover:text-[#006837] transition'>contact us</a> for help!</span>
    <button onclick="window.location.href='menu.php'" class="mt-2 px-5 py-2 bg-[#009245] text-white rounded-full font-semibold shadow hover:bg-[#006837] focus:outline-none focus:ring-2 focus:ring-[#009245] transition">Reset Filters</button>
  </div>
<?php endif; ?>
</div>
<div id="menu-pagination" class="flex justify-center mt-8">
  <?php if ($totalPages > 1): ?>
    <nav aria-label="Pagination">
      <ul class="inline-flex items-center gap-1">
        <?php if ($page > 1): ?>
          <li>
            <a href="#" data-page="<?= $page-1 ?>" class="px-3 py-2 rounded-full font-semibold text-sm transition focus:outline-none focus:ring-2 focus:ring-[#009245] bg-[#FFFFFF] text-[#006837] hover:bg-[#009245] hover:text-white" aria-label="Previous Page">&laquo;</a>
          </li>
        <?php endif; ?>
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
          <li>
            <a href="#" data-page="<?= $i ?>" class="px-4 py-2 rounded-full font-semibold text-sm transition focus:outline-none focus:ring-2 focus:ring-[#009245] <?php if ($i == $page) echo 'bg-[#009245] text-white'; else echo 'bg-[#FFFFFF] text-[#006837] hover:bg-[#009245] hover:text-white'; ?>" aria-current="<?= $i == $page ? 'page' : false ?>"> <?= $i ?> </a>
          </li>
        <?php endfor; ?>
        <?php if ($page < $totalPages): ?>
          <li>
            <a href="#" data-page="<?= $page+1 ?>" class="px-3 py-2 rounded-full font-semibold text-sm transition focus:outline-none focus:ring-2 focus:ring-[#009245] bg-[#FFFFFF] text-[#006837] hover:bg-[#009245] hover:text-white" aria-label="Next Page">&raquo;</a>
          </li>
        <?php endif; ?>
      </ul>
    </nav>
  <?php endif; ?>
</div>
