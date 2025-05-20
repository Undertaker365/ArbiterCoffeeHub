<?php
require_once '../db_connect.php';
include 'layout_admin.php';

if (!isset($_GET['id'])) {
    header('Location: products.php');
    exit;
}
$id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$product) {
    header('Location: products.php');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $featured = isset($_POST['featured']) ? 1 : 0;
    $image_filename = $product['image_filename'];
    if (!empty($_FILES['image']['name'])) {
        if ($image_filename && file_exists("../uploads/" . $image_filename)) {
            unlink("../uploads/" . $image_filename);
        }
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $image_filename = uniqid('prod_') . "." . $ext;
        move_uploaded_file($_FILES['image']['tmp_name'], "../uploads/" . $image_filename);
    }
    $stmt = $conn->prepare("UPDATE products SET name=?, category=?, price=?, featured=?, image_filename=? WHERE id=?");
    $stmt->execute([$name, $category, $price, $featured, $image_filename, $id]);
    header('Location: products.php');
    exit;
}
$content = ob_get_clean();
?>
<h1 class="text-2xl font-bold text-[#006837] mb-6">Edit Product</h1>
<form method="post" enctype="multipart/form-data" class="max-w-lg bg-white p-6 rounded shadow space-y-4">
    <div>
        <label class="block mb-1 font-semibold">Name</label>
        <input required type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" class="w-full border px-3 py-2 rounded" />
    </div>
    <div>
        <label class="block mb-1 font-semibold">Category</label>
        <select name="category" required class="w-full border px-3 py-2 rounded">
            <option value="">Select Category</option>
            <option value="Specialty Coffee" <?= $product['category']==='Specialty Coffee'?'selected':'' ?>>Specialty Coffee</option>
            <option value="Signature Beverages" <?= $product['category']==='Signature Beverages'?'selected':'' ?>>Signature Beverages</option>
            <option value="Rice Bowls" <?= $product['category']==='Rice Bowls'?'selected':'' ?>>Rice Bowls</option>
            <option value="Noodles" <?= $product['category']==='Noodles'?'selected':'' ?>>Noodles</option>
            <option value="Snacks" <?= $product['category']==='Snacks'?'selected':'' ?>>Snacks</option>
            <option value="Desserts" <?= $product['category']==='Desserts'?'selected':'' ?>>Desserts</option>
        </select>
    </div>
    <div>
        <label class="block mb-1 font-semibold">Price</label>
        <input required type="number" step="0.01" name="price" value="<?= htmlspecialchars($product['price']) ?>" class="w-full border px-3 py-2 rounded" />
    </div>
    <div class="flex items-center space-x-2">
        <input type="checkbox" name="featured" id="featured" <?= $product['featured'] ? 'checked' : '' ?> />
        <label for="featured">Featured Product</label>
    </div>
    <div>
        <label class="block mb-1 font-semibold">Current Image</label>
        <?php if ($product['image_filename'] && file_exists("../uploads/" . $product['image_filename'])): ?>
            <img src="../uploads/<?= htmlspecialchars($product['image_filename']) ?>" alt="Current Image" class="h-24 rounded object-cover" />
        <?php else: ?>
            <span class="text-gray-400">No Image</span>
        <?php endif; ?>
    </div>
    <div>
        <label class="block mb-1 font-semibold">Change Image</label>
        <input type="file" name="image" accept="image/*" />
    </div>
    <div>
        <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"><i class="fas fa-save mr-1"></i> Update Product</button>
        <a href="products.php" class="ml-2 text-gray-600 hover:underline">Cancel</a>
    </div>
</form>
</div>
