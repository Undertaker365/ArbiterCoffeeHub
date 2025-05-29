<?php
// admin/audit_logs.php
require_once '../db_connect.php';
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header('Location: ../unauthorized.php');
    exit();
}
$page_title = 'Audit Logs - Arbiter Coffee Hub';
$logs = [];
$error = null;
try {
    $stmt = $conn->query("SELECT l.id, l.user_id, u.email, l.action, l.details, l.created_at FROM audit_logs l LEFT JOIN users u ON l.user_id = u.id ORDER BY l.created_at DESC LIMIT 200");
    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = 'Could not fetch audit logs.';
}
ob_start();
?>
<div class="p-6">
    <h1 class="text-2xl font-bold text-[#006837] mb-6">Audit Logs</h1>
    <?php if ($error): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 p-3 rounded mb-4"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <div class="overflow-x-auto bg-white rounded shadow px-2 sm:px-4">
        <table class="min-w-full bg-white border rounded shadow">
            <thead>
                <tr>
                    <th class="px-4 py-2 border">ID</th>
                    <th class="px-4 py-2 border">User</th>
                    <th class="px-4 py-2 border">Action</th>
                    <th class="px-4 py-2 border">Details</th>
                    <th class="px-4 py-2 border">Timestamp</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $log): ?>
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
            </tbody>
        </table>
    </div>
</div>
<?php
$content = ob_get_clean();
include 'layout_admin.php';
