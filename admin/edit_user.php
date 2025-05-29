<?php
require_once '../includes/db_util.php';
require_once '../includes/csrf.php';
csrf_validate();
ob_start();
include 'layout_admin.php';
if (!isset($_GET['id'])) {
    header('Location: users.php');
    exit;
}
$id = intval($_GET['id']);
$user = db_fetch_one("SELECT * FROM users WHERE id = ?", [$id]);
if (!$user) {
    header('Location: users.php');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $role = $_POST['role'] ?? $user['role'];
    db_execute("UPDATE users SET first_name=?, last_name=?, email=?, role=? WHERE id=?", [$first_name, $last_name, $email, $role, $id]);
    header('Location: users.php');
    exit;
}
?>
<h1 class="text-2xl font-bold text-[#006837] mb-6">Edit User</h1>
<form method="post" class="max-w-lg bg-white p-6 rounded shadow space-y-4 w-full px-2 sm:px-6">
    <?= csrf_input() ?>
    <div>
        <label class="block mb-1 font-semibold">First Name</label>
        <input required type="text" name="first_name" value="<?= htmlspecialchars($user['first_name']) ?>" class="w-full border px-3 py-2 rounded" />
    </div>
    <div>
        <label class="block mb-1 font-semibold">Last Name</label>
        <input required type="text" name="last_name" value="<?= htmlspecialchars($user['last_name']) ?>" class="w-full border px-3 py-2 rounded" />
    </div>
    <div>
        <label class="block mb-1 font-semibold">Email</label>
        <input required type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" class="w-full border px-3 py-2 rounded" />
    </div>
    <div>
        <label class="block mb-1 font-semibold">Role</label>
        <select name="role" class="w-full border px-3 py-2 rounded">
            <option value="customer" <?= $user['role'] === 'customer' ? 'selected' : '' ?>>Customer</option>
            <option value="barista" <?= $user['role'] === 'barista' ? 'selected' : '' ?>>Barista</option>
        </select>
    </div>
    <div>
        <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"><i class="fas fa-save mr-1"></i> Update User</button>
        <a href="users.php" class="ml-2 text-gray-600 hover:underline">Cancel</a>
    </div>
</form>
</div>
<script src="https://cdn.tailwindcss.com"></script>
<?php
$content = ob_get_clean();
include 'layout_admin.php';
?>
