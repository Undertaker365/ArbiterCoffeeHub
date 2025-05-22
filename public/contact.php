<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $name = htmlspecialchars($_POST['name']);
  $email = htmlspecialchars($_POST['email']);
  $message = htmlspecialchars($_POST['message']);

  // You can store this to DB or send via email
  echo "<script>alert('Thank you, $name! Your message has been received.'); window.location.href='contact.php';</script>";
  exit;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contact Us - Arbiter Coffee Hub</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body { font-family: 'Montserrat', sans-serif; }
  </style>
</head>
<body class="bg-[#f9f9f9] text-gray-900">

  <?php include('../includes/header.php'); ?>

  <!-- Hero Banner -->
  <section class="relative bg-cover bg-center h-64 flex items-center justify-center" style="background-image: url('../uploads/contact-bg.jpg');">
    <div class="absolute inset-0 bg-[#006837] opacity-60"></div>
    <h1 class="text-white text-4xl font-bold relative z-10">Contact Us</h1>
  </section>

  <!-- Contact Info + Form -->
  <section class="py-16 bg-white">
    <div class="max-w-6xl mx-auto px-4 grid md:grid-cols-2 gap-12">
      
      <!-- Info Section -->
      <div>
        <h2 class="text-3xl font-semibold text-[#009245] mb-4">Get in Touch</h2>
        <p class="text-gray-700 mb-6">Have questions, feedback, or just want to say hello? We'd love to hear from you!</p>

        <ul class="space-y-4 text-gray-700">
          <li><i class="fas fa-map-marker-alt text-[#009245] mr-2"></i>123 Bean Street, Brewtown, PH</li>
          <li><i class="fas fa-phone-alt text-[#009245] mr-2"></i>(+63) 912 345 6789</li>
          <li><i class="fas fa-envelope text-[#009245] mr-2"></i>contact@arbitercoffee.com</li>
          <li><i class="fab fa-facebook text-[#009245] mr-2"></i><a href="#" class="hover:underline">facebook.com/arbitercoffee</a></li>
        </ul>
      </div>

      <!-- Contact Form -->
      <div>
        <h2 class="text-3xl font-semibold text-[#009245] mb-4">Send Us a Message</h2>
        <form action="contact_submit.php" method="POST" class="space-y-4">
          <input type="text" name="name" required placeholder="Your Name" class="w-full border border-gray-300 rounded-lg px-4 py-2">
          <input type="email" name="email" required placeholder="Your Email" class="w-full border border-gray-300 rounded-lg px-4 py-2">
          <textarea name="message" required placeholder="Your Message" rows="5" class="w-full border border-gray-300 rounded-lg px-4 py-2"></textarea>
          <button type="submit" class="bg-[#006837] text-white py-2 px-6 rounded-full hover:bg-[#009245] transition">Send Message</button>
        </form>
      </div>
    </div>
  </section>

  <hr class="border-t border-gray-200 mt-16">
  <?php include('../includes/footer.php'); ?>

</body>
</html>
