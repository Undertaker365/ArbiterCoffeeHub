<?php
require_once '../db_connect.php';
ob_start();
?>
<h1 class="text-2xl font-bold text-[#006837] mb-6">User Management</h1>
<div class="mb-4 flex flex-wrap gap-2 justify-between items-center">
    <form id="userSearchForm" class="flex items-center space-x-2" autocomplete="off">
        <input type="text" id="userSearchInput" name="search" placeholder="Search users..." class="border px-3 py-2 rounded" />
        <select id="roleFilter" class="border px-2 py-2 rounded">
            <option value="">All Roles</option>
            <option value="customer">Customer</option>
            <option value="barista">Barista</option>
        </select>
        <select id="statusFilter" class="border px-2 py-2 rounded">
            <option value="">All Status</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
        </select>
        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700"><i class="fas fa-search"></i></button>
    </form>
</div>
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
            <?php
            // Fetch all users except admins (remove created_at if not present)
            $users = $conn->query("SELECT id, first_name, last_name, email, role, status FROM users WHERE role != 'Admin' ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
            foreach ($users as $user): ?>
            <tr class="border-b">
                <td class="px-4 py-2">
                    <button onclick="showUserProfile(<?= $user['id'] ?>)" class="text-blue-600 hover:underline">
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
                    <button onclick="toggleUserStatus(<?= $user['id'] ?>, '<?= $user['status'] ?>')" class="bg-yellow-500 hover:bg-yellow-600 text-white px-2 py-1 rounded">
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
    <div class="bg-white rounded shadow-lg p-6 max-w-md w-full">
        <h2 class="text-xl font-bold mb-2">User Profile</h2>
        <div id="userProfileContent">Loading...</div>
        <button onclick="closeUserProfile()" class="mt-4 bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">Close</button>
    </div>
</div>
<script>
function toggleUserStatus(id, currentStatus) {
    const action = currentStatus === 'active' ? 'deactivate' : 'activate';
    if (confirm(`Are you sure you want to ${action} this user?`)) {
        fetch('users_toggle_status.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: `id=${id}&action=${action}`
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                showToast(`User ${action}d successfully!`, 'success');
                location.reload();
            } else {
                showToast(data.message || 'Action failed.', 'error');
            }
        });
    }
}
function showUserProfile(id) {
    const modal = document.getElementById('userProfileModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.getElementById('userProfileContent').innerHTML = 'Loading...';
    // Placeholder: fetch user details via AJAX if needed
    setTimeout(() => {
        document.getElementById('userProfileContent').innerHTML = 'User details for ID ' + id + ' (implement AJAX fetch)';
    }, 500);
}
function closeUserProfile() {
    const modal = document.getElementById('userProfileModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}
document.getElementById('userSearchForm').addEventListener('submit', function(e) {
    e.preventDefault();
    showToast('Search/filter not yet implemented (demo only)', 'error');
});
document.getElementById('roleFilter').addEventListener('change', function() {
    showToast('Search/filter not yet implemented (demo only)', 'error');
});
document.getElementById('statusFilter').addEventListener('change', function() {
    showToast('Search/filter not yet implemented (demo only)', 'error');
});
</script>
<!-- Pagination placeholder: implement server-side pagination as needed -->
<?php
$content = ob_get_clean();
include 'layout_admin.php';
?>
