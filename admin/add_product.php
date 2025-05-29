<?php
require_once '../db_connect.php';
ob_start();
?>
<h1 class="text-2xl font-bold text-[#006837] mb-6">Add Product</h1>
<form method="post" enctype="multipart/form-data" class="max-w-lg bg-white p-6 rounded shadow space-y-4">
    <div>
        <label class="block mb-1 font-semibold">Name</label>
        <input required type="text" name="name" class="w-full border px-3 py-2 rounded" />
    </div>
    <div>
        <label class="block mb-1 font-semibold">Category</label>
        <select name="category" required class="w-full border px-3 py-2 rounded">
            <option value="">Select Category</option>
            <option value="Specialty Coffee">Specialty Coffee</option>
            <option value="Signature Beverages">Signature Beverages</option>
            <option value="Rice Bowls">Rice Bowls</option>
            <option value="Noodles">Noodles</option>
            <option value="Snacks">Snacks</option>
            <option value="Desserts">Desserts</option>
        </select>
    </div>
    <div>
        <label class="block mb-1 font-semibold">Price</label>
        <input required type="number" step="0.01" name="price" class="w-full border px-3 py-2 rounded" />
    </div>
    <div class="flex items-center space-x-2">
        <input type="checkbox" name="featured" id="featured">
        <label for="featured">Featured Product</label>
    </div>
    <div>
        <label class="block mb-1 font-semibold">Image</label>
        <input type="file" name="image" accept="image/*" />
    </div>
    <div>
        <button class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700"><i class="fas fa-plus mr-1"></i> Add Product</button>
        <a href="products.php" class="ml-2 text-gray-600 hover:underline">Cancel</a>
    </div>
</form>
</div>
<?php
$content = ob_get_clean();
include 'layout_admin.php';
?>
