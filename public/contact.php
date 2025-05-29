<?php
$page_title = 'Contact - Arbiter Coffee Hub';
ob_start();
require_once '../includes/csrf.php';
csrf_validate();
require_once '../includes/db_util.php';
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
?>

<body class="bg-white min-h-screen font-[Montserrat] flex flex-col">
  <div class="flex-1 w-full">
    <!-- Contact Section -->
    <section class="py-16 bg-white">
      <div class="max-w-4xl mx-auto px-2 sm:px-4 text-center">
        <h2 class="text-3xl font-bold text-[#006837] mb-8">Contact Us</h2>
        <form action="" method="post" class="space-y-6 bg-white rounded-2xl shadow">
          <?= csrf_input() ?>
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
            <textarea name="message" rows="4" class="w-full border border-gray-300 rounded px-4 py-2 focus:ring-[#009245] focus:border-[#009245]" required id="contact-message"><?php if (isset($_GET['message'])) echo htmlspecialchars($_GET['message']); ?></textarea>
          </div>
          <button type="submit" class="w-full bg-[#009245] text-white py-2 rounded font-semibold hover:bg-[#006837] transition">Send Message</button>
        </form>
      </div>
    </section>
  </div>
</body>

<?php
$content = ob_get_clean();
include 'layout_public.php';
?>