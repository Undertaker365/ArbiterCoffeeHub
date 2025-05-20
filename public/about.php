<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>About Us - Arbiter Coffee Hub</title>
  <link rel="stylesheet" href="../public/css/tailwind-output.css">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body { font-family: 'Montserrat', sans-serif; }
  </style>
</head>
<body class="bg-[#f9f9f9] text-gray-900">

  <?php include('../includes/header.php'); ?>

  <!-- Hero Banner -->
  <section class="relative bg-cover bg-center h-64 flex items-center justify-center" style="background-image: url('../uploads/hero.jpg');">
    <div class="absolute inset-0 bg-[#006837] opacity-60"></div>
    <h1 class="text-white text-4xl font-bold relative z-10">About Us</h1>
  </section>

  <!-- Content Section -->
  <section class="py-16 bg-white">
    <div class="max-w-4xl mx-auto px-4 text-center">
      <h2 class="text-3xl font-semibold text-[#009245] mb-6">Our Story</h2>
      <p class="text-gray-700 text-lg leading-relaxed">
        Arbiter Coffee Hub was born from a simple passion: to bring quality, ethically sourced coffee to our community. Founded in 2024, we’ve grown into a local favorite known for our artisanal brews, cozy ambiance, and warm customer service.
      </p>

      <h2 class="text-3xl font-semibold text-[#009245] mt-12 mb-6">Our Mission</h2>
      <p class="text-gray-700 text-lg leading-relaxed">
        We aim to create a welcoming space where coffee lovers can gather, connect, and savor the taste of freshly brewed excellence. Every cup tells a story — from bean to brew.
      </p>

      <h2 class="text-3xl font-semibold text-[#009245] mt-12 mb-6">Why Choose Us?</h2>
      <ul class="text-gray-700 text-lg list-disc list-inside text-left max-w-2xl mx-auto">
        <li>100% Arabica, single-origin beans</li>
        <li>Handcrafted beverages by expert baristas</li>
        <li>Comfortable environment with free Wi-Fi</li>
        <li>Community-driven initiatives and events</li>
      </ul>
    </div>
  </section>

  <hr class="border-t border-gray-200 mt-16">
  <?php include('../includes/footer.php'); ?>

</body>
</html>
