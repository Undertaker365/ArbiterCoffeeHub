<?php
require_once '../includes/db_util.php';
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Fetch announcement ID from the URL
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$page_title = 'Announcement - Arbiter Coffee Hub';

// Get the announcement details from the database
$announcement = db_fetch_one("SELECT * FROM announcements WHERE id = ?", [$id]);

// --- Open Graph meta tags: set $og_meta for layout_public.php ---
$og_meta = '';
if ($announcement) {
  $og_title = htmlspecialchars($announcement['title']);
  $og_desc = mb_strimwidth(strip_tags($announcement['content']), 0, 120, '...');
  $og_img = !empty($announcement['image']) ? ("https://" . $_SERVER['HTTP_HOST'] . "/uploads/" . rawurlencode($announcement['image'])) : '';
  $og_url = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
  $og_meta = "<meta property=\"og:title\" content=\"$og_title\" />\n";
  $og_meta .= "<meta property=\"og:description\" content=\"$og_desc\" />\n";
  if ($og_img) $og_meta .= "<meta property=\"og:image\" content=\"$og_img\" />\n";
  $og_meta .= "<meta property=\"og:url\" content=\"$og_url\" />\n";
  $og_meta .= "<meta property=\"og:type\" content=\"article\" />\n";
}
// --- End Open Graph meta tags ---

ob_start();
?>
<body class="bg-white min-h-screen font-[Montserrat] flex flex-col">
  <div class="flex-1 w-full">
    <section class="py-16 bg-white">
      <div class="max-w-2xl mx-auto px-4">
        <?php if ($announcement): ?>
          <h2 class="text-3xl font-bold text-[#006837] mb-4"><?= htmlspecialchars($announcement['title']) ?></h2>
          <?php if (!empty($announcement['image'])): ?>
            <div class="w-full rounded-2xl overflow-hidden mb-6 bg-gray-100 flex items-center justify-center relative aspect-[3/2] shadow-lg">
              <img src="../uploads/<?= htmlspecialchars($announcement['image']) ?>" alt="" aria-hidden="true" class="absolute inset-0 w-full h-full object-cover filter blur-md scale-110 z-0 transition-transform duration-500" style="pointer-events:none;" />
              <img src="../uploads/<?= htmlspecialchars($announcement['image']) ?>" alt="<?= htmlspecialchars($announcement['title']) ?>" class="max-w-full max-h-full object-contain bg-transparent relative z-10 m-auto block rounded-2xl shadow-md transition-transform duration-500 group-hover:scale-105" style="object-fit:contain;" loading="lazy">
            </div>
          <?php endif; ?>
          <div class="text-gray-700 mb-6 animate-fade-in-up text-lg leading-relaxed"><?= nl2br(htmlspecialchars($announcement['content'])) ?></div>
          <div class="text-xs text-gray-500 mb-2">
            <?php
              $created = strtotime($announcement['created_at']);
              $now = time();
              $diff = $now - $created;
              if ($diff < 60*60*24) {
                echo 'Posted Today';
              } elseif ($diff < 60*60*48) {
                echo 'Posted Yesterday';
              } elseif ($diff < 60*60*24*7) {
                echo 'Posted ' . floor($diff/(60*60*24)) . ' days ago';
              } else {
                echo 'Posted on ' . date('F j, Y', $created);
              }
            ?>
          </div>
          <?php if (!empty($announcement['author'])): ?>
            <div class="text-xs text-[#006837] mb-4">By <span class="font-semibold"><?= htmlspecialchars($announcement['author']) ?></span></div>
          <?php endif; ?>
          <a href="announcements.php" class="inline-block mt-2 px-6 py-2 rounded-full bg-[#009245] text-white font-semibold shadow hover:bg-[#1A1A1A] hover:text-[#FFFFFF] transition focus:outline-none focus:ring-2 focus:ring-[#009245] text-base">&larr; Back to Announcements</a>
        <?php else: ?>
          <div class="text-gray-400 text-center flex flex-col items-center gap-4 py-12">
            <svg width="64" height="64" fill="none" viewBox="0 0 64 64" aria-hidden="true"><circle cx="32" cy="32" r="30" fill="#F3F4F6"/><path d="M20 40l12-12 12 12" stroke="#006837" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/><path d="M32 28v12" stroke="#006837" stroke-width="3" stroke-linecap="round"/></svg>
            <span class="block text-lg">Announcement not found.</span>
            <a href='announcements.php' class='inline-block px-4 py-2 rounded-full bg-[#FFFFFF] text-[#006837] font-semibold shadow hover:bg-[#1A1A1A] hover:text-[#FFFFFF] border border-[#006837] transition focus:outline-none focus:ring-2 focus:ring-[#009245]'>Go back to Announcements</a>
          </div>
        <?php endif; ?>

        <!-- Social share buttons: TikTok, Facebook, Instagram only, all green, icon-only with tooltips -->
        <div class="flex gap-4 justify-center mt-12">
          <a href="https://www.facebook.com/sharer/sharer.php?u=<?= $url ?>" target="_blank" class="text-[#006837] hover:bg-[#1A1A1A] hover:text-[#FFFFFF] border border-[#006837] rounded-full w-12 h-12 flex items-center justify-center text-2xl transition focus:outline-none focus:ring-2 focus:ring-[#009245] shadow" title="Share on Facebook" aria-label="Share on Facebook"><i class="fab fa-facebook"></i></a>
          <a href="https://www.instagram.com/?url=<?= $url ?>" target="_blank" class="text-[#006837] hover:bg-[#1A1A1A] hover:text-[#FFFFFF] border border-[#006837] rounded-full w-12 h-12 flex items-center justify-center text-2xl transition focus:outline-none focus:ring-2 focus:ring-[#009245] shadow" title="Share on Instagram" aria-label="Share on Instagram"><i class="fab fa-instagram"></i></a>
          <a href="https://www.tiktok.com/share?url=<?= $url ?>" target="_blank" class="text-[#006837] hover:bg-[#1A1A1A] hover:text-[#FFFFFF] border border-[#006837] rounded-full w-12 h-12 flex items-center justify-center text-2xl transition focus:outline-none focus:ring-2 focus:ring-[#009245] shadow" title="Share on TikTok" aria-label="Share on TikTok"><i class="fab fa-tiktok"></i></a>
          <button data-copy-link class="text-[#006837] hover:bg-[#1A1A1A] hover:text-[#FFFFFF] border border-[#006837] rounded-full w-12 h-12 flex items-center justify-center text-2xl transition focus:outline-none focus:ring-2 focus:ring-[#009245] shadow" title="Copy link" aria-label="Copy link"><i class="fas fa-link"></i></button>
          <span id="copy-annc-feedback" class="ml-2 text-xs text-[#009245] font-semibold hidden" aria-live="polite">Copied!</span>
        </div>

        <?php if ($announcement): ?>
        <?php
          // Fetch 3 recent announcements excluding the current one, with author info
          $rel_stmt = db_fetch_all("SELECT a.id, a.title, a.image, a.created_at, u.first_name, u.last_name FROM announcements a JOIN users u ON a.user_id = u.id WHERE a.id != ? ORDER BY a.created_at DESC LIMIT 3", [$announcement['id']]);
          $related = $rel_stmt;
        ?>
        <?php if ($related && count($related) > 0): ?>
        <div class="mt-16 animate-fade-in-up">
          <h3 class="text-2xl font-bold text-[#006837] mb-8 text-center tracking-tight">Related Announcements</h3>
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-8">
            <?php foreach ($related as $rel): ?>
              <a href="announcement-single.php?id=<?= $rel['id'] ?>" class="group block bg-[#FFFFFF] rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden focus:outline-none focus:ring-2 focus:ring-[#009245] relative flex flex-col h-full">
                <div class="relative aspect-[3/2] flex items-center justify-center bg-[#F3F4F6]" style="background:#1A1A1A;">
                  <?php if (!empty($rel['image'])): ?>
                    <img src="../uploads/<?= htmlspecialchars($rel['image']) ?>" alt="" aria-hidden="true" class="absolute inset-0 w-full h-full object-cover filter blur-md scale-110 z-0 transition-transform duration-500" style="pointer-events:none;opacity:0.7;" />
                    <img src="../uploads/<?= htmlspecialchars($rel['image']) ?>" alt="<?= htmlspecialchars($rel['title']) ?>" class="max-w-full max-h-full object-contain bg-transparent relative z-10 m-auto block rounded-2xl shadow-md transition-transform duration-500 group-hover:scale-105" loading="lazy">
                  <?php else: ?>
                    <div class="flex items-center justify-center w-full h-full text-[#009245] text-4xl bg-transparent">📢</div>
                  <?php endif; ?>
                  <div class="absolute top-2 left-2 bg-[#006837] text-white text-xs font-bold px-3 py-1 rounded-full shadow" style="letter-spacing:0.03em;">Announcement</div>
                </div>
                <div class="p-5 flex flex-col h-full">
                  <div class="font-semibold text-[#1A1A1A] group-hover:text-[#006837] transition mb-1 truncate text-base" title="<?= htmlspecialchars($rel['title']) ?>"><?= htmlspecialchars($rel['title']) ?></div>
                  <div class="flex items-center gap-2 mb-2">
                    <span class="text-xs bg-[#009245] text-white px-2 py-0.5 rounded-full" title="Announcement date">
                      <?php
                        $rel_created = strtotime($rel['created_at']);
                        $rel_diff = $now - $rel_created;
                        if ($rel_diff < 60*60*24) {
                          echo 'Today';
                        } elseif ($rel_diff < 60*60*48) {
                          echo 'Yesterday';
                        } elseif ($rel_diff < 60*60*24*7) {
                          echo floor($rel_diff/(60*60*24)) . ' days ago';
                        } else {
                          echo date('F j, Y', $rel_created);
                        }
                      ?>
                    </span>
                    <?php if (!empty($rel['first_name']) || !empty($rel['last_name'])): ?>
                      <span class="text-xs text-[#006837] flex items-center gap-1" title="Announcement author">
                        <i class="fas fa-user"></i> <span class="font-semibold"><?= htmlspecialchars(trim($rel['first_name'] . ' ' . $rel['last_name'])) ?></span>
                      </span>
                    <?php endif; ?>
                  </div>
                  <div class="text-[#1A1A1A] text-sm line-clamp-3 mb-3 flex-1">
                    <?= nl2br(htmlspecialchars(mb_strimwidth($rel['content'] ?? '', 0, 90, '...'))) ?>
                  </div>
                  <a href="announcement-single.php?id=<?= $rel['id'] ?>" class="block mt-auto text-xs text-[#009245] font-semibold hover:underline focus:outline-none focus:ring-2 focus:ring-[#009245] transition" tabindex="0" aria-label="Read more about <?= htmlspecialchars($rel['title']) ?>">Read more &rarr;</a>
                </div>
              </a>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>
        <?php endif; ?>
      </div>
    </section>
  </div>
  <?php include '../includes/footer.php'; ?>
</body>
<?php
$content = ob_get_clean();
include 'layout_public.php';
?>