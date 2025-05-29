<?php
require_once '../includes/db_util.php';
$where = [];
$params = [];
if (!empty($_GET['search'])) {
    $where[] = '(first_name LIKE ? OR last_name LIKE ? OR email LIKE ?)';
    $params[] = '%' . $_GET['search'] . '%';
    $params[] = '%' . $_GET['search'] . '%';
    $params[] = '%' . $_GET['search'] . '%';
}
if (!empty($_GET['role'])) {
    $where[] = 'role = ?';
    $params[] = $_GET['role'];
}
if (!empty($_GET['status'])) {
    $where[] = 'status = ?';
    $params[] = $_GET['status'];
}
if (!empty($_GET['start_date'])) {
    $where[] = 'created_at >= ?';
    $params[] = $_GET['start_date'] . ' 00:00:00';
}
if (!empty($_GET['end_date'])) {
    $where[] = 'created_at <= ?';
    $params[] = $_GET['end_date'] . ' 23:59:59';
}
$where_sql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';
$users = db_fetch_all("SELECT * FROM users $where_sql ORDER BY created_at DESC", $params);
foreach ($users as $user): ?>
<tr>
    <td class="px-4 py-2 border text-xs"> <?= htmlspecialchars($user['id']) ?> </td>
    <td class="px-4 py-2 border text-xs"> <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?> </td>
    <td class="px-4 py-2 border text-xs"> <?= htmlspecialchars($user['email']) ?> </td>
    <td class="px-4 py-2 border text-xs"> <?= htmlspecialchars($user['role']) ?> </td>
    <td class="px-4 py-2 border text-xs"> <?= htmlspecialchars($user['status']) ?> </td>
    <td class="px-4 py-2 border text-xs"> <?= htmlspecialchars($user['created_at']) ?> </td>
</tr>
<?php endforeach; ?>
<?php if (empty($users)): ?>
<tr><td colspan="6" class="text-center text-gray-400 py-4">No users found.</td></tr>
<?php endif; ?>
