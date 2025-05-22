<?php
include('../includes/header.php');
require_once '../db_connect.php';

// Fetch announcement ID from the URL
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    die('Invalid announcement ID.');
}

// Get the announcement details from the database
$stmt = $conn->prepare("SELECT * FROM announcements WHERE id = ?");
$stmt->execute([$id]);
$announcement = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$announcement) {
    die('Announcement not found.');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($announcement['title']) ?> - Arbiter Coffee Hub</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Montserrat', sans-serif; }
  </style>
</head>
<body class="bg-[#f9f9f9] text-gray-900">

  <div class="max-w-5xl mx-auto px-4 py-10">
    <h1 class="text-4xl font-bold text-center text-[#006837] mb-10">
      <i class="fas fa-bullhorn mr-2"></i><?= htmlspecialchars($announcement['title']) ?>
    </h1>

    <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition">
      <?php if ($announcement['image']): ?>
        <img src="../uploads/<?= htmlspecialchars($announcement['image']) ?>" class="w-full object-cover" alt="Announcement Image">
      <?php endif; ?>

      <div class="p-6">
        <p class="text-sm text-gray-500 mb-4"><i class="far fa-clock mr-1"></i><?= date('F j, Y h:i A', strtotime($announcement['created_at'])) ?></p>
        <p class="text-gray-700 mb-4"><?= nl2br(htmlspecialchars($announcement['content'])) ?></p>

        <!-- Share buttons -->
        <div class="flex gap-3">
          <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode('http://localhost/arbiter-coffee-hub/public/announcement-single.php?id=' . $announcement['id']) ?>" target="_blank" class="text-blue-600 hover:text-blue-800">
            <i class="fab fa-facebook fa-lg"></i>
          </a>
          <a href="https://twitter.com/intent/tweet?url=<?= urlencode('http://localhost/arbiter-coffee-hub/public/announcement-single.php?id=' . $announcement['id']) ?>&text=<?= urlencode($announcement['title']) ?>" target="_blank" class="text-blue-400 hover:text-blue-600">
            <i class="fab fa-twitter fa-lg"></i>
          </a>
          <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?= urlencode('http://localhost/arbiter-coffee-hub/public/announcement-single.php?id=' . $announcement['id']) ?>" target="_blank" class="text-blue-700 hover:text-blue-900">
            <i class="fab fa-linkedin fa-lg"></i>
          </a>
        </div>
      </div>
    </div>
    
    <div class="text-center mt-8">
      <a href="announcements.php" class="text-[#006837] hover:underline text-lg">
        <i class="fas fa-arrow-left mr-2"></i>Back to Announcements
      </a>
    </div>
  </div>

</body>
<?php include('../includes/footer.php'); ?>
</html>