<?php
require_once '../db_connect.php';

// Fetch announcement ID from the URL
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$page_title = 'Announcement - Arbiter Coffee Hub';

// Get the announcement details from the database
$stmt = $conn->prepare("SELECT * FROM announcements WHERE id = ?");
$stmt->execute([$id]);
$announcement = $stmt->fetch(PDO::FETCH_ASSOC);

ob_start();
?>
<section class="py-16 bg-white">
  <div class="max-w-2xl mx-auto px-4">
    <?php if ($announcement): ?>
      <h2 class="text-3xl font-bold text-[#006837] mb-4"><?= htmlspecialchars($announcement['title']) ?></h2>
      <div class="text-gray-700 mb-6"><?= nl2br(htmlspecialchars($announcement['content'])) ?></div>
      <div class="text-xs text-gray-500 mb-2">Posted on <?= date('F j, Y', strtotime($announcement['created_at'])) ?></div>
      <a href="announcements.php" class="text-[#009245] hover:underline">&larr; Back to Announcements</a>
    <?php else: ?>
      <div class="text-gray-400 text-center">Announcement not found.</div>
    <?php endif; ?>

    <?php
    // Social share buttons
    $url = urlencode((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    $title = isset($announcement['title']) ? htmlspecialchars($announcement['title'], ENT_QUOTES, 'UTF-8') : '';
    ?>
    <div class="flex gap-4 justify-center mt-6">
      <a href="https://www.facebook.com/sharer/sharer.php?u=<?= $url ?>" target="_blank" class="text-blue-600 hover:underline flex items-center"><i class="fab fa-facebook mr-1"></i>Share</a>
      <a href="https://twitter.com/intent/tweet?url=<?= $url ?>&text=<?= $title ?>" target="_blank" class="text-blue-400 hover:underline flex items-center"><i class="fab fa-twitter mr-1"></i>Tweet</a>
      <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?= $url ?>&title=<?= $title ?>" target="_blank" class="text-blue-700 hover:underline flex items-center"><i class="fab fa-linkedin mr-1"></i>LinkedIn</a>
    </div>
  </div>
</section>
<?php
$content = ob_get_clean();
include 'layout_public.php';
?>