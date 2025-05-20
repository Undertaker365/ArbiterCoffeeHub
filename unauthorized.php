<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Unauthorized Access - Arbiter Coffee Hub</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Montserrat', sans-serif; }
  </style>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen px-4">
  <div class="bg-white p-10 rounded-2xl shadow-lg max-w-md text-center">
    <div class="text-red-500 text-5xl mb-4">
      <i class="fas fa-exclamation-triangle"></i>
    </div>
    <h1 class="text-2xl font-bold mb-2 text-[#006837]">Unauthorized Access</h1>
    <p class="text-gray-600 mb-6">You do not have permission to view this page.</p>
    <a href="javascript:history.back()" class="inline-block px-5 py-2 bg-[#009245] text-white rounded-md hover:bg-[#006837] transition">
      Go Back
    </a>
  </div>
</body>
</html>
