<?php
require_once '../db_connect.php';
header('Content-Type: text/html; charset=UTF-8');
$logs = [];
$error = null;
try {
    $stmt = $conn->query("SELECT l.id, l.user_id, u.email, l.action, l.details, l.created_at FROM audit_logs l LEFT JOIN users u ON l.user_id = u.id ORDER BY l.created_at DESC LIMIT 200");
    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = 'Could not fetch audit logs.';
}
foreach ($logs as $log): ?>
<tr>
    <td class="px-4 py-2 border text-xs text-gray-500"><?= $log['id'] ?></td>
    <td class="px-4 py-2 border text-xs"><?= htmlspecialchars($log['email'] ?? 'System') ?></td>
    <td class="px-4 py-2 border text-xs"><?= htmlspecialchars($log['action']) ?></td>
    <td class="px-4 py-2 border text-xs"><?= htmlspecialchars($log['details']) ?></td>
    <td class="px-4 py-2 border text-xs text-gray-400"><?= htmlspecialchars($log['created_at']) ?></td>
</tr>
<?php endforeach; ?>
<?php if (empty($logs)): ?>
<tr><td colspan="5" class="text-center text-gray-400 py-4">No logs found.</td></tr>
<?php endif; ?>
