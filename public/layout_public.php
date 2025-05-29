<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
// layout_public.php - Unified layout for all public pages
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo isset($page_title) ? $page_title : 'Arbiter Coffee Hub'; ?></title>
    <?php if (isset($og_meta)) echo $og_meta; ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/main.css">
    <script src="/assets/js/main.js?v=<?php echo time(); ?>" defer></script>
</head>
<body class="flex flex-col min-h-screen bg-[#FFFFFF] text-[#1A1A1A]"<?= isset($welcome_user) && $welcome_user ? ' data-welcome-user="' . htmlspecialchars($welcome_user, ENT_QUOTES, 'UTF-8') . '"' : '' ?>>
<a href="#main-content" class="sr-only focus:not-sr-only absolute top-2 left-2 bg-green-700 text-white px-4 py-2 rounded z-50">Skip to main content</a>
<?php 
include(__DIR__ . '/../includes/header.php'); 
include(__DIR__ . '/../public/login.php'); 
include(__DIR__ . '/../public/register.php'); 
?>
<!-- Toast Notification Container -->
<div id="toast-container" class="fixed top-4 right-4 space-y-2 z-50"></div>
<!-- Loading Indicator -->
<div id="loading-indicator" class="fixed inset-0 bg-[#1A1A1A] bg-opacity-30 flex items-center justify-center z-50 hidden">
  <div class="bg-white px-6 py-4 rounded shadow text-lg flex items-center gap-3">
    <i class="fas fa-spinner fa-spin text-[#009245] text-2xl"></i> Loading...
  </div>
</div>
<main class="flex-1 w-full px-2 sm:px-0" id="main-content">
    <?php if (isset($content)) echo $content; ?>
</main>
<?php include(__DIR__ . '/../includes/footer.php'); ?>
</body>
</html>
