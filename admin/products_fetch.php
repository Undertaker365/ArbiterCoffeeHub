<?php
require_once '../db_connect.php';
$search = $_GET['search'] ?? '';
$sql = "SELECT * FROM products WHERE name LIKE ? OR category LIKE ? ORDER BY name ASC";
$stmt = $conn->prepare($sql);
$searchParam = '%' . $search . '%';
$stmt->execute([$searchParam, $searchParam]);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
header('Content-Type: application/json');
echo json_encode($products);
exit;
?>
