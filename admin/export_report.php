<?php
require_once '../db_connect.php';
$start = $_GET['start'] ?? date('Y-m-01');
$end = $_GET['end'] ?? date('Y-m-d');
$stmt = $conn->prepare("SELECT o.id, u.first_name, u.last_name, o.total_price, o.status, o.created_at FROM orders o JOIN users u ON o.user_id = u.id WHERE DATE(o.created_at) BETWEEN ? AND ? ORDER BY o.created_at DESC");
$stmt->execute([$start, $end]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="sales_report_'.date('Ymd').'.csv"');
$output = fopen('php://output', 'w');
fputcsv($output, ['Order ID', 'Customer', 'Total Price', 'Status', 'Date']);
foreach ($orders as $order) {
    fputcsv($output, [
        $order['id'],
        $order['first_name'] . ' ' . $order['last_name'],
        $order['total_price'],
        $order['status'],
        $order['created_at']
    ]);
}
fclose($output);
exit;
