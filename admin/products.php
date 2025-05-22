<?php
require_once '../db_connect.php';
ob_start();
?>
<h1 class="text-2xl font-bold text-[#006837] mb-6">Product Management</h1>
<div class="mb-4 flex flex-wrap gap-2 justify-between items-center">
    <form id="searchForm" class="flex items-center space-x-2" autocomplete="off">
        <input type="text" id="searchInput" name="search" placeholder="Search products..." class="border px-3 py-2 rounded" />
        <select id="categoryFilter" class="border px-2 py-2 rounded">
            <option value="">All Categories</option>
        </select>
        <select id="featuredFilter" class="border px-2 py-2 rounded">
            <option value="">All</option>
            <option value="1">Featured</option>
            <option value="0">Not Featured</option>
        </select>
        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700"><i class="fas fa-search"></i></button>
    </form>
    <a href="add_product.php" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded"><i class="fas fa-plus mr-1"></i> Add Product</a>
</div>
<div class="overflow-x-auto bg-white rounded shadow">
    <table class="w-full table-auto border-collapse">
        <thead class="bg-green-100 text-green-900">
            <tr>
                <th class="px-4 py-2">Image</th>
                <th class="px-4 py-2">Name</th>
                <th class="px-4 py-2">Category</th>
                <th class="px-4 py-2">Price</th>
                <th class="px-4 py-2">Featured</th>
                <th class="px-4 py-2">Actions</th>
            </tr>
        </thead>
        <tbody id="productsBody" class="bg-white text-gray-700">
            <!-- AJAX content here -->
        </tbody>
    </table>
    <div id="pagination" class="flex justify-center mt-4"></div>
</div>
<script src="https://cdn.tailwindcss.com"></script>
<script>
// Replace fetchCategories() to use fixed categories
function fetchCategories() {
    const categories = [
        'Specialty Coffee',
        'Signature Beverages',
        'Rice Bowls',
        'Noodles',
        'Snacks',
        'Desserts'
    ];
    const select = document.getElementById('categoryFilter');
    select.innerHTML = '<option value="">All Categories</option>' + categories.map(cat => `<option value="${cat}">${cat}</option>`).join('');
}
function fetchProducts(query = '', category = '', featured = '') {
    let url = `products_fetch.php?search=${encodeURIComponent(query)}&category=${encodeURIComponent(category)}&featured=${encodeURIComponent(featured)}`;
    fetch(url)
        .then(res => res.json())
        .then(data => {
            const tbody = document.getElementById('productsBody');
            tbody.innerHTML = '';
            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center p-4 text-gray-400">No products found.</td></tr>';
                return;
            }
            data.forEach(product => {
                tbody.innerHTML += `
                <tr class="border-b">
                    <td class="px-4 py-2">${product.image_filename ? `<img src="../uploads/${product.image_filename}" alt="${product.name}" class="h-16 w-16 object-cover rounded"/>` : '<span class="text-gray-400">No Image</span>'}</td>
                    <td class="px-4 py-2">${product.name}</td>
                    <td class="px-4 py-2">${product.category}</td>
                    <td class="px-4 py-2">₱${parseFloat(product.price).toFixed(2)}</td>
                    <td class="px-4 py-2">${product.featured == 1 ? '✅' : '❌'}</td>
                    <td class="px-4 py-2 space-x-2">
                        <a href="edit_product.php?id=${product.id}" class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded"><i class="fas fa-edit"></i> Edit</a>
                        <button onclick="deleteProduct(${product.id})" class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded"><i class="fas fa-trash"></i> Delete</button>
                    </td>
                </tr>`;
            });
        });
}
function deleteProduct(id) {
    if (confirm('Are you sure you want to delete this product?')) {
        fetch('products_delete.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: `id=${id}`
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                showToast('Product deleted successfully!', 'success');
                fetchProducts(document.getElementById('searchInput').value, document.getElementById('categoryFilter').value, document.getElementById('featuredFilter').value);
            } else {
                showToast(data.message || 'Delete failed.', 'error');
            }
        });
    }
}
document.getElementById('searchForm').addEventListener('submit', function(e) {
    e.preventDefault();
    fetchProducts(document.getElementById('searchInput').value, document.getElementById('categoryFilter').value, document.getElementById('featuredFilter').value);
});
document.getElementById('categoryFilter').addEventListener('change', function() {
    fetchProducts(document.getElementById('searchInput').value, this.value, document.getElementById('featuredFilter').value);
});
document.getElementById('featuredFilter').addEventListener('change', function() {
    fetchProducts(document.getElementById('searchInput').value, document.getElementById('categoryFilter').value, this.value);
});
document.addEventListener('DOMContentLoaded', function() {
    fetchCategories();
    fetchProducts();
});
</script>
<!-- Pagination placeholder: implement server-side pagination as needed -->
<?php
$content = ob_get_clean();
include 'layout_admin.php';
?>
