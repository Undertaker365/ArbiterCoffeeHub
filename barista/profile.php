<?php
session_start();
require_once '../includes/db_util.php';
require_once '../includes/csrf.php';
csrf_validate();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Barista') {
    header('Location: ../public/login.php');
    exit();
}
$user_id = $_SESSION['user_id'];
// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    db_execute("UPDATE users SET first_name = ?, email = ? WHERE id = ?", [$name, $email, $user_id]);
    $msg = 'Profile updated!';
}
$user = db_fetch_one("SELECT * FROM users WHERE id = ?", [$user_id]);
ob_start();
?>
<div class="flex-1 p-6 md:ml-64">
    <h1 class="text-2xl font-bold text-[#006837] mb-2">Profile</h1>
    <div class="bg-white rounded-xl shadow p-6">
        <?php if (!empty($msg)): ?><div class="mb-4 text-green-600 font-semibold"><?= $msg ?></div><?php endif; ?>
        <form method="post">
            <?= csrf_input() ?>
            <div class="mb-4">
                <label class="block mb-1 font-semibold text-gray-700">Name</label>
                <input type="text" name="name" class="w-full border rounded px-3 py-2" value="<?= htmlspecialchars($user['first_name']) ?>">
            </div>
            <div class="mb-4">
                <label class="block mb-1 font-semibold text-gray-700">Email</label>
                <input type="email" name="email" class="w-full border rounded px-3 py-2" value="<?= htmlspecialchars($user['email']) ?>">
            </div>
            <button type="submit" class="bg-[#009245] text-white px-6 py-2 rounded hover:bg-green-800">Update Profile</button>
        </form>
    </div>
</div>
<?php
$content = ob_get_clean();
include 'layout_barista.php';
?>
