<?php
require_once '../db_connect.php';

$stmt = $conn->prepare("SELECT * FROM products");
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Menu - Arbiter Coffee Hub</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body { font-family: 'Montserrat', sans-serif; }
  </style>
</head>
<body class="bg-[#f9f9f9] text-gray-900">

  <?php include('../includes/header.php'); ?>

  <section class="py-16 bg-white">
    <div class="max-w-6xl mx-auto px-4 text-center">
      <h2 class="text-4xl font-bold text-[#006837] mb-10">Our Coffee Menu</h2>

      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
        <?php foreach ($products as $product): ?>
          <div class="bg-white rounded-2xl shadow-md hover:shadow-xl transition duration-300 overflow-hidden">
            <img src="../uploads/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="w-full h-48 object-cover">
            <div class="p-5 text-left">
              <h3 class="text-lg font-semibold text-[#009245]"><?= htmlspecialchars($product['name']) ?></h3>
              <p class="text-gray-600 text-sm"><?= htmlspecialchars($product['description']) ?></p>
              <p class="text-gray-800 font-bold mt-2"><?= number_format($product['price'], 2) ?> PHP</p>
              <a href="login.php" class="inline-block mt-3 bg-[#1A1A1A] text-white px-4 py-2 rounded-full text-sm hover:bg-black">
                Order Now
              </a>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <?php if (count($products) === 0): ?>
        <p class="text-gray-600 mt-10">No products found.</p>
      <?php endif; ?>
    </div>
  </section>

  <hr class="border-t border-gray-200 mt-16">
  <?php include('../includes/footer.php'); ?>
</body>
</html>
