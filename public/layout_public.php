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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>body { font-family: 'Montserrat', sans-serif; }</style>
</head>
<body class="flex flex-col min-h-screen bg-[#f9f9f9] text-gray-900">
<?php include(__DIR__ . '/../includes/header.php'); ?>
<main class="flex-1">
    <?php if (isset($content)) echo $content; ?>
</main>
<?php include(__DIR__ . '/../includes/footer.php'); ?>
</body>
</html>
