<?php
$page_title = 'Contact - Arbiter Coffee Hub';
ob_start();
?>
<!-- Contact Section -->
<section class="py-16 bg-white">
  <div class="max-w-2xl mx-auto px-4">
    <h2 class="text-3xl font-bold text-[#006837] mb-8 text-center">Contact Us</h2>
    <form action="" method="post" class="space-y-6 bg-gray-50 p-8 rounded-2xl shadow">
      <div>
        <label class="block text-gray-700 font-semibold mb-2">Name</label>
        <input type="text" name="name" class="w-full border border-gray-300 rounded px-4 py-2 focus:ring-[#009245] focus:border-[#009245]" required>
      </div>
      <div>
        <label class="block text-gray-700 font-semibold mb-2">Email</label>
        <input type="email" name="email" class="w-full border border-gray-300 rounded px-4 py-2 focus:ring-[#009245] focus:border-[#009245]" required>
      </div>
      <div>
        <label class="block text-gray-700 font-semibold mb-2">Message</label>
        <textarea name="message" rows="4" class="w-full border border-gray-300 rounded px-4 py-2 focus:ring-[#009245] focus:border-[#009245]" required></textarea>
      </div>
      <button type="submit" class="w-full bg-[#009245] text-white py-2 rounded font-semibold hover:bg-[#006837] transition">Send Message</button>
    </form>
  </div>
</section>
<?php
$content = ob_get_clean();
include 'layout_public.php';
?>
