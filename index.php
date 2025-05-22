<?php
require_once 'db_connect.php';

$stmt = $conn->prepare("SELECT * FROM products WHERE featured = 1 LIMIT 4");
$stmt->execute();
$featuredProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Arbiter Coffee Hub</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body { font-family: 'Montserrat', sans-serif; }
  </style>
</head>
<body class="bg-white text-gray-900">

  <?php include('includes/header.php'); ?>

  <!-- Hero Section -->
  <section class="relative w-full bg-cover bg-center" style="background-image: url('../uploads/background.jpg');">
    <div class="absolute inset-0 bg-[#009245] opacity-50"></div>
    <div class="max-w-5xl mx-auto text-center text-white relative z-10 pt-32 pb-16">
      <h1 class="text-4xl md:text-5xl font-bold mb-4">Welcome to Arbiter Coffee Hub</h1>
      <p class="text-lg md:text-2xl mb-6">Quality coffee shall prevail.</p>
      <a href="../public/menu.php" class="bg-[#1A1A1A] text-white py-2 px-6 rounded-full text-lg hover:bg-black inline-flex items-center gap-2">
        <i class="fas fa-mug-hot"> </i> Browse Menu</a>
    </div>
  </section>

  <!-- Featured Products Section -->
  <section class="py-16 bg-white">
    <div class="max-w-5xl mx-auto text-center">
      <h2 class="text-3xl font-semibold text-[#006837] mb-8">Featured Products</h2>
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-8">

        <?php if (count($featuredProducts) > 0): ?>
          
          <?php foreach ($featuredProducts as $product): ?>
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
              <img src="../uploads/<?= htmlspecialchars($product['image']) ?>" class="w-full object-cover" alt="<?= htmlspecialchars($product['name']) ?>">
              <div class="p-4">
                <h3 class="text-lg font-semibold text-[#009245]"><?= htmlspecialchars($product['name']) ?></h3>
                <p class="text-gray-700 text-sm"><?= htmlspecialchars($product['description']) ?></p>
                <p class="text-gray-800 font-semibold mt-2"><?= number_format($product['price'], 2) ?> PHP</p>
              </div>
            </div>
          <?php endforeach; ?>

        <?php else: ?>
          <p>No featured products available at the moment.</p>
        <?php endif; ?>

      </div>
    </div>
  </section>

  <section class="py-12 bg-white">
    <div class="max-w-5xl mx-auto text-center mb-10">
      <h2 class="text-2xl font-bold text-[#006837] mb-6">Latest Announcements</h2>
      <a href="../public/announcements.php" class="text-[#009245] hover:underline">View all announcements →</a>
    </div>
  </section>

  <hr class="border-t border-gray-200 mt-16">

  <?php include('includes/footer.php'); ?>

</body>
</html>
