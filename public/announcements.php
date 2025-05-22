<?php
require_once '../db_connect.php';
$page_title = 'Announcements - Arbiter Coffee Hub';
$filter = $_GET['category'] ?? 'all';
$limit = 5;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

if ($filter === 'all') {
  $stmt = $conn->prepare("SELECT * FROM announcements ORDER BY created_at DESC LIMIT $limit OFFSET $offset");
  $stmt->execute();
} else {
  $stmt = $conn->prepare("SELECT * FROM announcements WHERE category = ? ORDER BY created_at DESC LIMIT $limit OFFSET $offset");
  $stmt->execute([$filter]);
}
$announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get total for pagination
$countStmt = $conn->prepare($filter === 'all' 
  ? "SELECT COUNT(*) FROM announcements"
  : "SELECT COUNT(*) FROM announcements WHERE category = ?");
$filter === 'all' ? $countStmt->execute() : $countStmt->execute([$filter]);
$totalPosts = $countStmt->fetchColumn();
$totalPages = ceil($totalPosts / $limit);
  
ob_start();
?>
<section class="py-16 bg-white">
  <div class="max-w-4xl mx-auto px-4">
    <h2 class="text-3xl font-bold text-[#006837] mb-8 text-center">Announcements</h2>
    <div class="space-y-6">
      <?php foreach ($announcements as $a): ?>
        <div class="bg-gray-50 rounded-xl shadow p-6">
          <h3 class="text-xl font-semibold text-[#009245] mb-2"><?= htmlspecialchars($a['title']) ?></h3>
          <p class="text-gray-700 mb-2"><?= nl2br(htmlspecialchars($a['content'])) ?></p>
          <div class="text-xs text-gray-500">Posted on <?= date('F j, Y', strtotime($a['created_at'])) ?></div>
        </div>
      <?php endforeach; ?>
      <?php if (empty($announcements)): ?>
        <div class="text-gray-400 text-center">No announcements yet.</div>
      <?php endif; ?>
    </div>
  </div>
</section>
<?php
$content = ob_get_clean();
include 'layout_public.php';
?>
