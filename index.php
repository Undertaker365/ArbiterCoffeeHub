<?php
// Only start output buffering ONCE, and only include the header/layout ONCE at the end
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
require_once 'includes/db_util.php';

// Fetch testimonials from DB (show 6 random for carousel)
$testimonials = db_fetch_all("SELECT * FROM testimonials ORDER BY RAND() LIMIT 6");

// Fetch featured products (no badges column)
$featuredProducts = db_fetch_all("SELECT * FROM products WHERE featured = 1 LIMIT 5");

// Fetch latest 3 announcements (show featured first if any, then most recent)
$categoryFilter = isset($_GET['category']) && $_GET['category'] !== '' ? $_GET['category'] : null;
$announcementsQuery = "SELECT id, title, content, image, created_at, category, featured FROM announcements ";
$params = [];
if ($categoryFilter) {
  $announcementsQuery .= "WHERE category = :cat ";
  $params['cat'] = $categoryFilter;
}
$announcementsQuery .= "ORDER BY featured DESC, created_at DESC LIMIT 3";
$latestAnnouncements = db_fetch_all($announcementsQuery, $params);
$featuredAnnouncement = null;
foreach ($latestAnnouncements as $a) { if (!empty($a['featured'])) { $featuredAnnouncement = $a; break; } }

$page_title = 'Welcome to Arbiter Coffee Hub';
$welcome_user = isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : '';
ob_start();
?>
<!-- The welcome bar is now handled by main.js using a data attribute on <body> -->
<main id="main-content" role="main" aria-label="Arbiter Coffee Hub Homepage">
  <!-- Hero Section -->
  <section class="relative w-full bg-cover bg-center animate-fade-in" style="background-image: url('../uploads/background.jpg');">
    <div class="absolute inset-0 bg-gradient-to-b from-gray-900 via-green-700 to-green-800 opacity-80"></div>
    <div class="max-w-5xl mx-auto text-center text-white relative z-10 pt-32 pb-16">
      <h1 class="text-4xl md:text-5xl font-bold mb-4 drop-shadow-lg">Welcome to Arbiter Coffee Hub</h1>
      <p class="text-lg md:text-2xl mb-6 drop-shadow">Quality coffee shall prevail.</p>
      <div class="max-w-5xl mx-auto flex flex-col sm:flex-row gap-4 justify-center items-center mt-6 px-2">
        <!-- Hero section buttons -->
        <a href="../public/menu.php" class="bg-[#1A1A1A] text-white py-2 px-6 rounded-full text-lg hover:bg-[#009245] hover:text-white inline-flex items-center gap-2 shadow-lg transition-transform hover:scale-105">
          <i class="fas fa-mug-hot"></i> Browse Menu
        </a>
        <a href="../public/announcements.php" class="bg-white text-[#009245] py-2 px-6 rounded-full text-lg hover:bg-[#009245] hover:text-white inline-flex items-center gap-2 shadow-lg border border-[#009245] transition-transform hover:scale-105">
          <i class="fas fa-bullhorn"></i> See Announcements
        </a>
      </div>
      <div class="mt-10 animate-bounce text-3xl opacity-80">
        <i class="fas fa-chevron-down"></i>
      </div>
    </div>
  </section>

  <!-- Featured Products Section (unified, no duplicate) -->
  <section class="py-16 bg-white animate-fade-in-up" aria-labelledby="featured-products-heading">
    <div class="max-w-5xl mx-auto text-center">
      <h2 id="featured-products-heading" class="text-3xl font-semibold text-green-800 mb-8">Featured Products</h2>
      <?php if (count($featuredProducts) > 0): ?>
        <div class="w-full overflow-x-auto scrollbar-hide">
          <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-4 gap-4 sm:gap-8" id="featured-carousel" tabindex="0" aria-label="Featured products carousel" aria-live="polite" aria-describedby="featured-carousel-desc">
            <span id="featured-carousel-desc" class="sr-only">Use left and right arrow keys to navigate featured products. Carousel supports mouse drag and auto-scroll.</span>
            <?php foreach ($featuredProducts as $product): ?>
              <?php
                $imgPath = 'uploads/' . ($product['image_filename'] ?? '');
                $badge = '';
                $created = isset($product['created_at']) ? strtotime($product['created_at']) : false;
                if ($created && $created >= strtotime('-30 days')) {
                  $badge = 'New';
                } elseif (!empty($product['name']) && stripos($product['name'], 'best') !== false) {
                  $badge = 'Best Seller';
                }
                $rating = isset($product['rating']) ? (int)$product['rating'] : rand(4,5);
              ?>
              <div class="bg-white rounded-xl shadow-lg overflow-hidden w-full min-w-0 max-w-full transition-transform hover:scale-105 relative group h-full flex flex-col" aria-label="Featured product: <?= htmlspecialchars($product['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                <img src="<?= $imgPath ?>" class="w-full h-48 object-cover object-top" alt="<?= htmlspecialchars($product['name'] ?? '', ENT_QUOTES, 'UTF-8') ?> product image">
                <?php if ($badge): ?>
                  <span class="absolute top-3 left-3 bg-green-700 text-white text-xs font-bold px-3 py-1 rounded-full shadow z-10 animate-bounce-in" aria-label="<?= $badge ?> badge"> <?= $badge ?> </span>
                <?php endif; ?>
                <div class="p-4 flex flex-col flex-1 items-center w-full">
                  <h3 class="text-lg font-semibold text-green-700 mb-1 truncate w-full text-center" title="<?= htmlspecialchars($product['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    <?= htmlspecialchars($product['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                  </h3>
                  <div class="flex items-center mb-1 w-full justify-center" aria-label="Product rating: <?= $rating ?> out of 5 stars">
                    <?php for ($i=0; $i<$rating; $i++) echo '<span class=\'text-yellow-500 text-lg\'>★</span>'; for ($i=$rating; $i<5; $i++) echo '<span class=\'text-gray-300 text-lg\'>☆</span>'; ?>
                  </div>
                  <p class="text-gray-700 text-sm mb-2 line-clamp-2 w-full text-center min-h-[2.5em]"> <?= htmlspecialchars($product['description'] ?? '', ENT_QUOTES, 'UTF-8') ?> </p>
                  <p class="text-gray-800 font-semibold text-base w-full mb-4 text-center">₱<?= number_format($product['price'], 2) ?></p>
                  <div class="flex gap-2 w-full justify-center mt-auto">
                    <a href="public/menu.php" class="flex items-center justify-center bg-green-700 text-white px-4 py-1 rounded-full text-xs hover:bg-green-800 transition whitespace-nowrap min-w-[90px] w-full sm:w-auto">Order Now</a>
                    <a href="#" class="flex items-center justify-center text-[#009245] text-xs hover:text-[#006837] transition quick-view-btn whitespace-nowrap min-w-[90px] w-full sm:w-auto no-underline hover:underline focus:underline" data-product='<?= json_encode(["name"=>$product['name'],"description"=>$product['description'],"price"=>$product['price'],"image"=>$imgPath,"rating"=>$rating], JSON_HEX_APOS|JSON_HEX_QUOT) ?>'>Quick View</a>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      <?php else: ?>
        <p>No featured products available at the moment.</p>
      <?php endif; ?>
    </div>
  </section>

  <!-- Quick View Modal (hidden by default) -->
  <div id="quick-view-modal" class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center hidden" aria-modal="true" role="dialog" aria-labelledby="quick-view-modal-title" aria-describedby="quick-view-modal-desc">
    <div class="bg-white rounded-xl shadow-lg max-w-md w-full p-6 relative animate-fade-in-up">
      <button id="close-quick-view" class="absolute top-3 right-3 text-gray-400 hover:text-green-700 text-2xl" aria-label="Close quick view">&times;</button>
      <img id="qv-image" src="" alt="Product image" class="w-full h-48 object-cover object-top rounded mb-4">
      <h3 id="qv-name" class="text-xl font-bold text-green-700 mb-2"></h3>
      <div id="qv-rating" class="flex items-center mb-2"></div>
      <p id="qv-description" class="text-gray-700 mb-3"></p>
      <p id="qv-price" class="text-gray-900 font-semibold text-lg mb-4"></p>
      <a href="public/menu.php" class="bg-green-700 text-white px-6 py-2 rounded-full text-base hover:bg-green-800 transition">Order Now</a>
      <span id="quick-view-modal-desc" class="sr-only">Product quick view modal. Press Escape to close.</span>
    </div>
  </div>
  <!-- Announcements Section with Category Filter and Featured -->
  <section class="py-12 bg-white animate-fade-in-up" aria-labelledby="announcements-heading">
    <div class="max-w-5xl mx-auto text-center px-2 mb-10">
      <h2 id="announcements-heading" class="text-2xl font-bold text-green-800 mb-6">Latest Announcements</h2>
      <form method="get" class="mb-6 flex flex-wrap gap-2 justify-center" aria-label="Filter announcements by category">
        <select name="category" class="border border-gray-300 rounded px-3 py-1 text-sm" onchange="this.form.submit()" aria-label="Select announcement category">
          <option value="" aria-current="<?= !isset($_GET['category']) || $_GET['category'] === '' ? 'true' : 'false' ?>">All Categories</option>
          <?php
          $catRes = $conn->query("SELECT DISTINCT category FROM announcements WHERE category IS NOT NULL AND category != ''");
          foreach ($catRes as $row) {
            $cat = htmlspecialchars($row['category']);
            $sel = (isset($_GET['category']) && $_GET['category'] === $row['category']) ? 'selected' : '';
            $ariaCurrent = (isset($_GET['category']) && $_GET['category'] === $row['category']) ? 'aria-current="true"' : '';
            echo "<option value=\"$cat\" $sel $ariaCurrent>$cat</option>";
          }
          ?>
        </select>
      </form>
      <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 w-full">
        <?php if (count($latestAnnouncements) > 0) {
          foreach ($latestAnnouncements as $a) {
        ?>
          <!-- Announcement Card -->
          <a href="public/announcement-single.php?id=<?= $a['id'] ?>" class="group block bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow overflow-hidden relative">
            <div class="h-48 w-full overflow-hidden flex items-center justify-center bg-[#FFFFFF] relative">
              <img src="uploads/<?= htmlspecialchars($a['image']) ?>" alt="<?= htmlspecialchars($a['title']) ?>" class="absolute inset-0 w-full h-full object-cover filter blur-md scale-110 z-0" aria-hidden="true" />
              <img src="uploads/<?= htmlspecialchars($a['image']) ?>" alt="<?= htmlspecialchars($a['title']) ?>" class="relative z-10 max-h-48 w-auto max-w-full object-contain group-hover:scale-105 transition-transform duration-300 shadow-md bg-white rounded-lg" loading="lazy">
              <?php if (!empty($a['featured'])) { ?>
                <span class="absolute top-2 left-2 bg-green-700 text-white text-xs font-semibold px-2 py-1 rounded shadow z-20">Featured</span>
              <?php } ?>
            </div>
            <div class="p-4">
              <div class="flex items-center gap-2 mb-2">
                <!-- Announcement card category badge -->
                <span class="text-xs bg-[#009245] text-white px-2 py-0.5 rounded">#<?= htmlspecialchars($a['category']) ?></span>
                <span class="text-xs text-gray-500 italic flex items-center gap-1"><i class="fas fa-user"></i> Admin</span>
              </div>
              <h3 class="text-lg font-semibold mb-1 line-clamp-1"><?= htmlspecialchars($a['title']) ?></h3>
              <div class="text-gray-700 text-base line-clamp-3 mb-3 leading-relaxed"><?= nl2br(htmlspecialchars(mb_strimwidth($a['content'], 0, 140, '...'))) ?></div>
              <div class="text-xs text-gray-400 flex items-center gap-1"><i class="far fa-clock"></i> <?= date('M j, Y', strtotime($a['created_at'])) ?></div>
            </div>
          </a>
        <?php 
          }
        } else { ?>
          <div class="col-span-2 text-gray-400">No announcements<?= $categoryFilter ? ' in this category' : '' ?> yet.</div>
        <?php } ?>
      </div>
      <a href="public/announcements.php" class="text-green-700 hover:underline font-semibold inline-flex items-center gap-1">View all announcements <i class="fas fa-arrow-right"></i></a>
    </div>
  </section>

  <!-- Testimonials Section -->
  <section class="py-16 bg-gradient-to-b from-gray-50 to-white animate-fade-in-up">
    <div class="max-w-5xl mx-auto text-center mb-12 px-2">
      <h2 class="text-3xl font-bold text-green-800 mb-4 tracking-tight">What Our Customers Say</h2>
      <p class="text-gray-500 mb-10 text-lg">Real feedback from our valued guests</p>
      <div class="relative pt-8">
        <!-- Navigation buttons OUTSIDE the scrollable container -->
        <button id="testimonial-prev" class="absolute left-0 top-1/2 -translate-y-1/2 bg-white text-[#009245] rounded-full w-10 h-10 flex items-center justify-center shadow hover:bg-[#009245] hover:text-white z-10 transition hidden md:flex" aria-label="Previous testimonial" style="left: 0; margin-left: 0.5rem;"><i class="fas fa-chevron-left"></i></button>
        <div id="testimonial-carousel" class="flex gap-8 overflow-x-auto scrollbar-hide snap-x px-2 md:px-12 group" style="scroll-behavior:smooth; scroll-padding-left: 0;" tabindex="0" aria-label="Testimonials carousel" aria-live="polite" aria-describedby="testimonial-carousel-desc">
          <span id="testimonial-carousel-desc" class="sr-only">Use left and right arrow keys to navigate testimonials. Carousel auto-scrolls on hover.</span>
          <?php foreach ($testimonials as $t): ?>
          <div class="bg-white rounded-2xl shadow-lg p-8 flex flex-col items-center min-w-[320px] max-w-xs w-full snap-center transition-transform duration-300 hover:scale-105 hover:shadow-2xl relative group-hover:shadow-2xl" style="margin-bottom: 2rem;">
            <?php if ($t['photo']): ?>
              <img src="uploads/<?= htmlspecialchars($t['photo']) ?>" alt="Customer photo" class="w-20 h-20 rounded-full mb-4 border-4 border-gray-200 object-cover shadow-lg">
            <?php else: ?>
              <!-- Testimonial avatar fallback -->
              <div class="w-20 h-20 rounded-full mb-4 bg-[#009245] flex items-center justify-center text-3xl text-white font-bold shadow-lg">
                <i class="fas fa-user"></i>
              </div>
            <?php endif; ?>
            <div class="flex items-center mb-2" aria-label="Testimonial rating: <?= (int)$t['rating'] ?> out of 5 stars">
              <?php for ($i=0; $i<$t['rating']; $i++) echo '<span class=\'text-yellow-500 text-xl\'>★</span>'; for ($i=$t['rating']; $i<5; $i++) echo '<span class=\'text-gray-300 text-xl\'>☆</span>'; ?>
            </div>
            <blockquote class="text-gray-700 italic mb-3 text-base line-clamp-4 relative">
              <i class="fas fa-quote-left text-gray-200 text-2xl absolute -left-6 top-0"></i>
              “<?= htmlspecialchars($t['content']) ?>”
            </blockquote>
            <span class="font-semibold text-green-800 text-lg mt-2"><?= htmlspecialchars($t['name']) ?></span>
            <span class="text-xs text-gray-400 mb-1"><?= htmlspecialchars($t['role']) ?></span>
          </div>
          <?php endforeach; ?>
        </div>
        <!-- Testimonial navigation buttons (remove border) -->
        <button id="testimonial-next" class="absolute right-0 top-1/2 -translate-y-1/2 bg-white text-[#009245] rounded-full w-10 h-10 flex items-center justify-center shadow hover:bg-[#009245] hover:text-white z-10 transition hidden md:flex" aria-label="Next testimonial" style="right: 0; margin-right: 0.5rem; border-radius: 100%;"><i class="fas fa-chevron-right"></i></button>
      </div>
    </div>
  </section>
  <!-- Feedback Form & Social Links -->
  <section class="py-10 bg-gray-50 animate-fade-in-up" aria-labelledby="feedback-heading">
    <div class="max-w-2xl mx-auto text-center px-2">
      <h2 id="feedback-heading" class="text-2xl font-bold text-green-800 mb-4">We Value Your Feedback</h2>
      <form action="public/contact.php" method="get" class="mb-6">
        <!-- Feedback form textarea -->
        <textarea name="message" rows="3" class="w-full rounded p-3 mb-3 bg-white text-[#1A1A1A]" placeholder="Share your thoughts or suggestions..." required aria-label="Feedback" id="feedback-message"></textarea>
        <button type="submit" class="bg-green-700 text-white px-6 py-2 rounded-full hover:bg-green-800 transition">Send Feedback</button>
      </form>
    </div>
  </section>
  <!-- Floating Social Media Button -->
  <div id="floating-social" class="fixed bottom-6 right-6 z-50">
    <div id="social-links" class="flex flex-col items-center space-y-4 mb-4 transition-all duration-300 opacity-0 pointer-events-none translate-y-4">
      <a href="https://facebook.com/arbitercoffeehub" target="_blank" rel="noopener" aria-label="Facebook" class="bg-white shadow-lg rounded-full w-10 h-10 text-[#009245] hover:text-white hover:bg-[#009245] transition flex items-center justify-center"><i class="fab fa-facebook-f text-xl"></i></a>
      <a href="https://instagram.com/arbitercoffeehub" target="_blank" rel="noopener" aria-label="Instagram" class="bg-white shadow-lg rounded-full w-10 h-10 text-[#009245] hover:text-white hover:bg-[#009245] transition flex items-center justify-center"><i class="fab fa-instagram text-xl"></i></a>
      <a href="https://tiktok.com/@arbitercoffeehub" target="_blank" rel="noopener" aria-label="TikTok" class="bg-white shadow-lg rounded-full w-10 h-10 text-[#009245] hover:text-white hover:bg-[#009245] transition flex items-center justify-center"><i class="fab fa-tiktok text-xl"></i></a>
    </div>
    <button id="social-toggle" class="bg-[#009245] text-white rounded-full w-12 h-12 flex items-center justify-center shadow-lg hover:bg-[#006837] transition text-2xl focus:outline-none" aria-label="Open social media links" aria-expanded="false">
      <i class="fas fa-share-alt"></i>
    </button>
  </div>
</main>

<!-- 2. Add skip to content link for keyboard users -->
<a href="#main-content" class="sr-only focus:not-sr-only absolute top-2 left-2 bg-green-700 text-white px-4 py-2 rounded z-50">Skip to main content</a>

<!-- 3. Ensure all images have descriptive alt text (already present, but double-check) -->
<!-- 4. Add lang attribute to html tag (if not already in layout_public.php) -->

<?php
// --- Helper Functions ---
function renderStars($rating) {
    $stars = '';
    for ($i = 0; $i < $rating; $i++) $stars .= '<span class="text-yellow-500 text-lg" aria-hidden="true">★</span>';
    for ($i = $rating; $i < 5; $i++) $stars .= '<span class="text-gray-300 text-lg" aria-hidden="true">☆</span>';
    return $stars;
}
function renderTestimonialStars($rating) {
    $stars = '';
    for ($i = 0; $i < $rating; $i++) $stars .= '<span class="text-yellow-500 text-xl" aria-hidden="true">★</span>';
    for ($i = $rating; $i < 5; $i++) $stars .= '<span class="text-gray-300 text-xl" aria-hidden="true">☆</span>';
    return $stars;
}

$content = ob_get_clean();
include 'public/layout_public.php';
?>
