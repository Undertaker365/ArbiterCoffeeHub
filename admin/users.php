<?php
require_once '../includes/db_util.php';
require_once '../includes/csrf.php';
csrf_validate();
ob_start();

$user_errors = [];
try {
    // Fetch all users except admins (remove created_at if not present)
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
} catch (PDOException $e) {
    $user_errors[] = "A system error occurred. Please try again later.";
}
?>
<h1 class="text-2xl font-bold text-[#006837] mb-6">User Management</h1>
<form method="GET" class="mb-4 flex flex-wrap gap-2 items-center">
    <?= csrf_input() ?>
    <input type="text" name="search" placeholder="Search users..." class="border px-3 py-2 rounded" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" />
    <select name="role" class="border px-2 py-2 rounded">
        <option value="">All Roles</option>
        <option value="Customer" <?= ($_GET['role'] ?? '') === 'Customer' ? 'selected' : '' ?>>Customer</option>
        <option value="Barista" <?= ($_GET['role'] ?? '') === 'Barista' ? 'selected' : '' ?>>Barista</option>
        <option value="Admin" <?= ($_GET['role'] ?? '') === 'Admin' ? 'selected' : '' ?>>Admin</option>
    </select>
    <select name="status" class="border px-2 py-2 rounded">
        <option value="">All Status</option>
        <option value="active" <?= ($_GET['status'] ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
        <option value="inactive" <?= ($_GET['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
    </select>
    <input type="date" name="start_date" class="border px-3 py-2 rounded" value="<?= htmlspecialchars($_GET['start_date'] ?? '') ?>" />
    <input type="date" name="end_date" class="border px-3 py-2 rounded" value="<?= htmlspecialchars($_GET['end_date'] ?? '') ?>" />
    <button type="submit" class="bg-[#009245] text-white px-4 py-2 rounded">Filter</button>
</form>
<div class="overflow-x-auto bg-white rounded shadow">
    <table class="w-full table-auto border-collapse">
        <thead class="bg-green-100 text-green-900">
            <tr>
                <th class="px-4 py-2">Name</th>
                <th class="px-4 py-2">Email</th>
                <th class="px-4 py-2">Role</th>
                <th class="px-4 py-2">Status</th>
                <th class="px-4 py-2">Actions</th>
            </tr>
        </thead>
        <tbody id="usersBody" class="bg-white text-gray-700">
            <?php foreach ($users as $user): ?>
            <tr class="border-b">
                <td class="px-4 py-2">
                    <button data-show-user-profile="<?= $user['id'] ?>" class="text-blue-600 hover:underline">
                        <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?>
                    </button>
                </td>
                <td class="px-4 py-2"><?= htmlspecialchars($user['email']) ?></td>
                <td class="px-4 py-2"><?= htmlspecialchars(ucfirst($user['role'])) ?></td>
                <td class="px-4 py-2">
                    <span class="inline-block px-2 py-1 text-sm rounded <?= $user['status'] === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
                        <?= ucfirst($user['status']) ?>
                    </span>
                </td>
                <td class="px-4 py-2 space-x-2">
                    <a href="edit_user.php?id=<?= $user['id'] ?>" class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded"><i class="fas fa-edit"></i> Edit</a>
                    <button data-toggle-user data-id="<?= $user['id'] ?>" data-status="<?= $user['status'] ?>" class="bg-yellow-500 hover:bg-yellow-600 text-white px-2 py-1 rounded">
                        <i class="fas fa-user-slash"></i> <?= $user['status'] === 'active' ? 'Deactivate' : 'Activate' ?>
                    </button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div id="userPagination" class="flex justify-center mt-4"></div>
</div>
<!-- User Profile Modal Placeholder -->
<div id="userProfileModal" class="hidden fixed inset-0 bg-black bg-opacity-40 items-center justify-center z-50">
    <div class="bg-white rounded shadow-lg p-6 max-w-md w-full px-2 sm:px-4">
        <h2 class="text-xl font-bold mb-2">User Profile</h2>
        <div id="userProfileContent">Loading...</div>
        <button data-close-user-profile class="mt-4 bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">Close</button>
    </div>
</div>
<?php if (!empty($user_errors)): ?>
  <div class="bg-red-100 border border-red-400 text-red-700 p-3 rounded mb-4">
    <ul class="list-disc pl-5">
      <?php foreach ($user_errors as $error): ?>
        <li><?= htmlspecialchars($error) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>
<!-- Pagination placeholder: implement server-side pagination as needed -->
<?php
$content = ob_get_clean();
include 'layout_admin.php';
?>
