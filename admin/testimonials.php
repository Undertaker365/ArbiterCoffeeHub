<?php
require_once '../db_connect.php';

// Handle add, edit, delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $name = trim($_POST['name']);
        $role = trim($_POST['role']);
        $content = trim($_POST['content']);
        $rating = intval($_POST['rating']);
        $photo = '';
        if (!empty($_FILES['photo']['name'])) {
            $target = '../uploads/' . basename($_FILES['photo']['name']);
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $target)) {
                $photo = basename($_FILES['photo']['name']);
            }
        }
        $stmt = $conn->prepare("INSERT INTO testimonials (name, role, photo, rating, content) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $role, $photo, $rating, $content]);
    } elseif (isset($_POST['delete'])) {
        $stmt = $conn->prepare("DELETE FROM testimonials WHERE id = ?");
        $stmt->execute([$_POST['id']]);
    }
}

// Fetch testimonials
$testimonials = $conn->query("SELECT * FROM testimonials ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include 'layout_admin.php'; ?>
<div class="max-w-3xl mx-auto p-6">
    <h1 class="text-2xl font-bold text-[#006837] mb-6">Manage Testimonials</h1>
    <form method="post" enctype="multipart/form-data" class="mb-8 bg-white p-4 rounded shadow">
        <div class="mb-2">
            <input type="text" name="name" required placeholder="Customer Name" class="border rounded px-3 py-2 w-full">
        </div>
        <div class="mb-2">
            <input type="text" name="role" placeholder="Role (e.g. Regular Customer)" class="border rounded px-3 py-2 w-full">
        </div>
        <div class="mb-2">
            <input type="file" name="photo" accept="image/*" class="border rounded px-3 py-2 w-full">
        </div>
        <div class="mb-2">
            <select name="rating" class="border rounded px-3 py-2 w-full">
                <option value="5">★★★★★</option>
                <option value="4">★★★★☆</option>
                <option value="3">★★★☆☆</option>
                <option value="2">★★☆☆☆</option>
                <option value="1">★☆☆☆☆</option>
            </select>
        </div>
        <div class="mb-2">
            <textarea name="content" required placeholder="Testimonial..." class="border rounded px-3 py-2 w-full"></textarea>
        </div>
        <button type="submit" name="add" class="bg-[#009245] text-white px-6 py-2 rounded hover:bg-[#006837]">Add Testimonial</button>
    </form>
    <h2 class="text-xl font-semibold mb-4">All Testimonials</h2>
    <div class="overflow-x-auto bg-white rounded shadow px-2 sm:px-4">
        <div class="space-y-4">
            <?php foreach ($testimonials as $t): ?>
            <div class="flex items-start bg-gray-50 p-4 rounded-lg shadow-sm">
                <?php if ($t['photo']): ?>
                    <img src="../uploads/<?= htmlspecialchars($t['photo']) ?>" class="w-14 h-14 rounded-full object-cover border border-[#009245]" alt="Photo">
                <?php endif; ?>
                <div class="flex-1 ml-4">
                    <div class="font-bold text-[#009245]"><?= htmlspecialchars($t['name']) ?></div>
                    <div class="text-xs text-gray-400 mb-1"><?= htmlspecialchars($t['role']) ?></div>
                    <div class="text-yellow-500 mb-1">
                        <?php for ($i=0; $i<$t['rating']; $i++) echo '★'; for ($i=$t['rating']; $i<5; $i++) echo '☆'; ?>
                    </div>
                    <div class="text-gray-700 italic">“<?= htmlspecialchars($t['content']) ?>”</div>
                </div>
                <form method="post" class="ml-4">
                    <input type="hidden" name="id" value="<?= $t['id'] ?>">
                    <button type="submit" name="delete" class="text-red-600 hover:underline">Delete</button>
                </form>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
