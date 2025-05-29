<?php
// admin/announcements.php - Admin management for announcements (feature/unfeature)
require_once '../db_connect.php';
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../unauthorized.php');
    exit;
}

// Feature/unfeature logic
if (isset($_GET['feature_id'])) {
    $featureId = (int)$_GET['feature_id'];
    // Unset all featured
    $conn->exec("UPDATE announcements SET featured = 0");
    // Set selected as featured
    $stmt = $conn->prepare("UPDATE announcements SET featured = 1 WHERE id = ?");
    $stmt->execute([$featureId]);
    header('Location: announcements.php?msg=featured');
    exit;
}

// Unfeature logic
if (isset($_GET['unfeature_id'])) {
    $unfeatureId = (int)$_GET['unfeature_id'];
    $stmt = $conn->prepare("UPDATE announcements SET featured = 0 WHERE id = ?");
    $stmt->execute([$unfeatureId]);
    header('Location: announcements.php?msg=unfeatured');
    exit;
}

// Fetch all announcements
$announcements = $conn->query("SELECT * FROM announcements ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);

include 'layout_admin.php';
?>
<div class="container mx-auto py-8 px-2 sm:px-4">
    <h1 class="text-2xl font-bold mb-6">Manage Announcements</h1>
    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'featured'): ?>
        <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">Announcement set as featured.</div>
    <?php elseif (isset($_GET['msg']) && $_GET['msg'] === 'unfeatured'): ?>
        <div class="bg-yellow-100 text-yellow-800 px-4 py-2 rounded mb-4">Announcement unfeatured.</div>
    <?php endif; ?>
    <table class="min-w-full bg-white border rounded shadow text-xs sm:text-sm overflow-x-auto block w-full">
        <thead>
            <tr>
                <th class="py-2 px-4 border-b">ID</th>
                <th class="py-2 px-4 border-b">Title</th>
                <th class="py-2 px-4 border-b">Category</th>
                <th class="py-2 px-4 border-b">Featured</th>
                <th class="py-2 px-4 border-b">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($announcements as $a): ?>
            <tr class="<?= $a['featured'] ? 'bg-green-50' : '' ?>">
                <td class="py-2 px-4 border-b">#<?= $a['id'] ?></td>
                <td class="py-2 px-4 border-b"><?= htmlspecialchars($a['title']) ?></td>
                <td class="py-2 px-4 border-b"><?= htmlspecialchars($a['category']) ?></td>
                <td class="py-2 px-4 border-b text-center">
                    <?php if ($a['featured']): ?>
                        <span class="inline-block bg-green-600 text-white text-xs px-2 py-1 rounded">Featured</span>
                    <?php endif; ?>
                </td>
                <td class="py-2 px-4 border-b">
                    <?php if (!$a['featured']): ?>
                        <a href="?feature_id=<?= $a['id'] ?>" class="bg-green-600 text-white px-3 py-1 rounded text-xs hover:bg-green-700">Set as Featured</a>
                    <?php else: ?>
                        <a href="?unfeature_id=<?= $a['id'] ?>" class="bg-yellow-600 text-white px-3 py-1 rounded text-xs hover:bg-yellow-700">Unfeature</a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
