<?php
require_once '../db_connect.php';
header('Content-Type: text/html; charset=UTF-8');
$announcements = $conn->query("SELECT * FROM announcements ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
foreach ($announcements as $a): ?>
<tr>
    <td class="py-2 px-4 border-b"> <?= $a['id'] ?> </td>
    <td class="py-2 px-4 border-b"> <?= htmlspecialchars($a['title']) ?> </td>
    <td class="py-2 px-4 border-b"> <?= htmlspecialchars($a['category']) ?> </td>
    <td class="py-2 px-4 border-b"> <?= $a['featured'] ? 'Yes' : 'No' ?> </td>
    <td class="py-2 px-4 border-b">
        <a href="announcements.php?feature_id=<?= $a['id'] ?>" class="text-blue-600 hover:underline">Feature</a> |
        <a href="announcements.php?unfeature_id=<?= $a['id'] ?>" class="text-yellow-600 hover:underline">Unfeature</a>
    </td>
</tr>
<?php endforeach; ?>
<?php if (empty($announcements)): ?>
<tr><td colspan="5" class="text-center text-gray-400 py-4">No announcements found.</td></tr>
<?php endif; ?>
