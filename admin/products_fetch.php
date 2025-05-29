<?php
require_once '../includes/db_util.php';
$search = $_GET['search'] ?? '';
$sql = "SELECT * FROM products WHERE name LIKE ? OR category LIKE ? ORDER BY name ASC";
$searchParam = '%' . $search . '%';
$products = db_fetch_all($sql, [$searchParam, $searchParam]);
header('Content-Type: application/json');
echo json_encode($products);
exit;
?>
