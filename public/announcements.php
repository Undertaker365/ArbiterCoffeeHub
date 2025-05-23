<?php
require_once '../db_connect.php';
session_start();
include '../includes/header.php';

// Handle new announcement post (admin only)
if (isset($_SESSION['role']) && $_SESSION['role'] === 'Admin' && isset($_POST['announcement_content'])) {
    $title = trim($_POST['announcement_title'] ?? '');
    $content = trim($_POST['announcement_content']);
    $category = trim($_POST['announcement_category'] ?? 'General');
    $image = null;
    // Handle image upload
    if (isset($_FILES['announcement_image']) && $_FILES['announcement_image']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['announcement_image']['name'], PATHINFO_EXTENSION);
        $image = uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['announcement_image']['tmp_name'], '../uploads/' . $image);
    }
    if ($content !== '' && $title !== '') {
        $stmt = $conn->prepare('INSERT INTO announcements (user_id, title, content, image, category) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$_SESSION['user_id'], $title, $content, $image, $category]);
        header('Location: announcements.php');
        exit();
    }
}

// Fetch all announcements (newest first)
$announcements = $conn->query('
    SELECT a.*, u.first_name, u.last_name, u.role
    FROM announcements a
    JOIN users u ON a.user_id = u.id
    ORDER BY a.created_at DESC
')->fetchAll(PDO::FETCH_ASSOC);

$categories = ['General', 'Event', 'Promo', 'Update'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Announcements - Arbiter Coffee Hub</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen font-[Montserrat]">
    <div class="max-w-2xl mx-auto py-8">
        <h1 class="text-3xl font-bold text-[#006837] mb-6 flex items-center gap-2"><i class="fas fa-bullhorn"></i> Announcements</h1>
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'Admin'): ?>
        <form method="post" enctype="multipart/form-data" class="mb-8 bg-white rounded-xl shadow p-4 flex flex-col gap-3">
            <input name="announcement_title" type="text" class="border border-gray-300 rounded p-2 focus:outline-none focus:ring-2 focus:ring-green-400" placeholder="Announcement Title" required>
            <textarea name="announcement_content" rows="3" class="border border-gray-300 rounded p-2 focus:outline-none focus:ring-2 focus:ring-green-400 resize-none" placeholder="Write a new announcement..." required></textarea>
            <div class="flex gap-3 items-center">
                <select name="announcement_category" class="border border-gray-300 rounded p-2 focus:outline-none focus:ring-2 focus:ring-green-400">
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= htmlspecialchars($cat) ?>"><?= htmlspecialchars($cat) ?></option>
                    <?php endforeach; ?>
                </select>
                <label class="flex items-center gap-2 cursor-pointer">
                    <i class="fas fa-image text-gray-500"></i>
                    <input type="file" name="announcement_image" accept="image/*" class="hidden">
                    <span class="text-sm text-gray-600">Add image</span>
                </label>
            </div>
            <button type="submit" class="self-end bg-[#009245] text-white px-6 py-2 rounded shadow hover:bg-[#006837] transition"><i class="fas fa-paper-plane mr-2"></i>Post</button>
        </form>
        <?php endif; ?>
        <div class="space-y-6">
            <?php foreach ($announcements as $a): ?>
            <div class="bg-white rounded-xl shadow p-5">
                <div class="flex items-center gap-3 mb-2">
                    <i class="fas fa-user-circle text-2xl text-gray-400"></i>
                    <span class="font-semibold text-gray-800">
                        <?= htmlspecialchars($a['first_name'] . ' ' . $a['last_name']) ?>
                        <?php if ($a['role'] === 'Admin'): ?>
                            <span class="ml-2 px-2 py-0.5 text-xs bg-green-100 text-green-700 rounded">Admin</span>
                        <?php endif; ?>
                    </span>
                    <span class="ml-auto text-xs text-gray-400">
                        <?= date('M d, Y H:i', strtotime($a['created_at'])) ?>
                    </span>
                </div>
                <div class="mb-2 flex gap-2 items-center">
                    <span class="text-xs px-2 py-1 rounded bg-gray-100 text-gray-600"><i class="fas fa-tag mr-1"></i><?= htmlspecialchars($a['category']) ?></span>
                    <span class="font-bold text-lg text-[#006837]"><?= htmlspecialchars($a['title']) ?></span>
                </div>
                <?php if (!empty($a['image'])): ?>
                    <img src="../uploads/<?= htmlspecialchars($a['image']) ?>" alt="Announcement Image" class="rounded mb-3 max-h-64 w-auto mx-auto">
                <?php endif; ?>
                <div class="text-gray-700 text-lg whitespace-pre-line"><?= nl2br(htmlspecialchars($a['content'])) ?></div>
            </div>
            <?php endforeach; ?>
            <?php if (empty($announcements)): ?>
                <div class="text-gray-400 text-center py-8">No announcements yet.</div>
            <?php endif; ?>
        </div>
    </div>
<?php include '../includes/footer.php'; ?>
</body>
</html>
