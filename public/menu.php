<?php
require_once '../includes/db_util.php';
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
$categories = [
  'Worldwide Specialty Coffee',
  'Global Signature Beverages',
  'Japanese Snacks',
  'Ricebowls & Noodles',
  'Dessert'
];
$page_title = 'Menu - Arbiter Coffee Hub';
ob_start();
?>
<div class="mt-10"></div>
<form id="menu-search-form" class="mb-4 flex flex-wrap sm:flex-row flex-row justify-center items-center gap-2 w-full max-w-2xl mx-auto" autocomplete="off">
  <div class="relative flex-1 min-w-0">
    <input type="text" name="search" id="search-input" placeholder="Search menu (e.g. espresso, matcha, snack)..." class="border border-[#006837] rounded-l px-4 py-2 w-full focus:outline-none focus:ring-2 focus:ring-[#009245] pr-10" aria-label="Search menu" autocomplete="off">
    <div id="search-suggestions" class="absolute left-0 right-0 top-full z-10 bg-white border border-[#006837] rounded-b shadow-lg text-left hidden" role="listbox" aria-label="Menu search suggestions" aria-live="polite"></div>
    <button type="button" id="clear-search" class="absolute right-2 top-1/2 -translate-y-1/2 text-[#006837] hover:text-[#009245] focus:outline-none hidden" aria-label="Clear search">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>
  </div>
  <select name="sort" id="sort-select" class="border border-[#006837] px-3 py-2 rounded focus:outline-none focus:ring-2 focus:ring-[#009245] flex-shrink-0 w-auto" aria-label="Sort menu">
    <option value="">Sort By</option>
    <option value="price_asc">Price: Low to High</option>
    <option value="price_desc">Price: High to Low</option>
    <option value="name_asc">Name: A-Z</option>
    <option value="name_desc">Name: Z-A</option>
  </select>
  <button type="submit" class="bg-[#009245] text-white px-4 py-2 rounded font-semibold hover:bg-[#006837] flex-shrink-0">Search</button>
  <a href="#" id="clear-filters" class="px-4 py-2 rounded-full bg-[#1A1A1A] text-white font-semibold text-sm border border-[#009245] hover:bg-[#009245] hover:text-white transition focus:outline-none focus:ring-2 focus:ring-[#009245] flex-shrink-0 whitespace-nowrap hidden" aria-label="Clear filters">Clear</a>
</form>
<div class="flex flex-wrap gap-2 mb-8 justify-center" id="category-filters" role="group" aria-label="Menu categories">
  <a href="#" data-category="" class="category-filter px-3 py-1 rounded-full text-xs font-semibold transition focus:outline-none focus:ring-2 focus:ring-[#009245] bg-[#009245] text-white ring-2 ring-[#009245] hover:bg-[#009245] hover:text-white" tabindex="0" aria-pressed="true">All</a>
  <?php foreach ($categories as $cat): ?>
    <a href="#" data-category="<?= htmlspecialchars($cat) ?>" class="category-filter px-3 py-1 rounded-full text-xs font-semibold transition focus:outline-none focus:ring-2 focus:ring-[#009245] bg-[#FFFFFF] text-[#006837] hover:bg-[#009245] hover:text-white" tabindex="0" aria-pressed="false">
      <?= htmlspecialchars($cat) ?>
    </a>
  <?php endforeach; ?>
</div>
<section class="py-16 bg-white min-h-[80vh]">
  <div class="max-w-6xl mx-auto px-2 sm:px-4 text-center">
    <h2 class="text-4xl font-extrabold text-[#006837] mb-10 text-center tracking-tight drop-shadow-sm" id="menu-section-title">Menu</h2>
    <div class="relative">
      <div id="product-grid" class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-10 w-full overflow-x-auto scrollbar-hide min-h-[300px]" aria-live="polite" role="region" aria-labelledby="menu-section-title">
        <noscript>
          <div class="text-[#1A1A1A] text-center py-16 flex flex-col items-center gap-6 animate-fade-in-up">
            <span class="block text-xl font-semibold">JavaScript is required to view the menu.</span>
          </div>
        </noscript>
      </div>
      <div id="menu-loading-spinner" class="absolute inset-0 flex items-center justify-center bg-white bg-opacity-70 z-10 hidden" aria-hidden="true">
        <svg class="animate-spin h-10 w-10 text-[#009245]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path></svg>
      </div>
    </div>
    <div id="menu-pagination" class="flex justify-center mt-8"></div>
  </div>
</section>
<?php
$content = ob_get_clean();
include 'layout_public.php';
?>
