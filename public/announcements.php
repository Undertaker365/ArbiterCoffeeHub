<?php
include('../includes/header.php');
require_once '../db_connect.php';
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
  
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Announcements - Arbiter Coffee Hub</title>
  <link rel="stylesheet" href="../public/css/tailwind-output.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Montserrat', sans-serif; }
  </style>
</head>

<body class="bg-[#f9f9f9] text-gray-900">

  <div class="max-w-5xl mx-auto px-4 py-10">
    <h1 class="text-4xl font-bold text-center text-[#006837] mb-10">
      <i class="fas fa-bullhorn mr-2"></i>Latest Announcements
    </h1>

    <div class="grid grid-cols-1 gap-6">
      <!-- Dropdown Filter UI -->
      <form method="GET" class="mb-6 text-center">
        <label class="block text-sm font-semibold mb-1 text-[#006837]">Filter by Category</label>

        <select name="category" onchange="this.form.submit()" class="border px-4 py-2 rounded">
          <option value="all" <?= $filter === 'all' ? 'selected' : '' ?>>All Categories</option>
          <option value="Featured" <?= $filter === 'Featured' ? 'selected' : '' ?>>Featured</option>
          <option value="Update" <?= $filter === 'Update' ? 'selected' : '' ?>>Update</option>
          <option value="Announcement" <?= $filter === 'Announcement' ? 'selected' : '' ?>>Announcement</option>
        </select>
      </form> 

      <?php if (count($announcements) > 0): ?>
        <?php foreach ($announcements as $post): ?>
          <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition">
            <?php if ($post['image']): ?>
              <img src="../uploads/<?= htmlspecialchars($post['image']) ?>" class="w-full object-cover" alt="Announcement Image">
            <?php endif; ?>

            <div class="p-6">
              <h2 class="text-2xl font-semibold text-[#009245] mb-2">
                <a href="announcement-single.php?id=<?= $post['id'] ?>" class="hover:underline">
                  <?= htmlspecialchars($post['title']) ?>
                </a>
              </h2>

              <p class="text-gray-700 mb-4"><?= nl2br(htmlspecialchars($post['content'])) ?></p>
              <p class="text-sm text-gray-500 mb-4"><i class="far fa-clock mr-1"></i><?= date('F j, Y h:i A', strtotime($post['created_at'])) ?></p>

              <!-- Share buttons -->
              <div class="flex gap-3">
                <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode('http://localhost/arbiter-coffee-hub/public/announcements.php') ?>" target="_blank" class="text-blue-600 hover:text-blue-800">
                  <i class="fab fa-facebook fa-lg"></i>
                </a>
                <a href="https://twitter.com/intent/tweet?url=<?= urlencode('http://localhost/arbiter-coffee-hub/public/announcements.php') ?>&text=<?= urlencode($post['title']) ?>" target="_blank" class="text-blue-400 hover:text-blue-600">
                  <i class="fab fa-twitter fa-lg"></i>
                </a>
                <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?= urlencode('http://localhost/arbiter-coffee-hub/public/announcements.php') ?>" target="_blank" class="text-blue-700 hover:text-blue-900">
                  <i class="fab fa-linkedin fa-lg"></i>
                </a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>    
      <?php else: ?>
        <p class="text-center text-gray-600">No announcements yet.</p>
      <?php endif; ?>

      <div class="flex justify-center gap-2 mt-8">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
          <a href="?category=<?= urlencode($filter) ?>&page=<?= $i ?>"
            class="px-4 py-2 border rounded transition hover:bg-[#006837] hover:text-white">
            <?= $i ?>
          </a>
        <?php endfor; ?>
      </div>

    </div>
  </div>

  <?php include('../includes/footer.php'); ?>

</body>
</html>
