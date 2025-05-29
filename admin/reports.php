<?php
require_once '../includes/db_util.php';
ob_start();
$page_title = 'Reports - Arbiter Coffee Hub';
$report_type = $_GET['type'] ?? 'orders';
$rows = [];
$error = null;
try {
    if ($report_type === 'orders') {
        $rows = db_fetch_all("SELECT * FROM order_summary ORDER BY created_at DESC LIMIT 200");
    } elseif ($report_type === 'products') {
        $rows = db_fetch_all("SELECT * FROM product_sales ORDER BY total_sold DESC LIMIT 200");
    }
} catch (PDOException $e) {
    $error = 'Could not fetch report data.';
}
// Export logic
if (isset($_GET['export']) && in_array($_GET['export'], ['csv','pdf'])) {
    $filename = $report_type . '_report_' . date('Ymd_His');
    if ($_GET['export'] === 'csv') {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
        $out = fopen('php://output', 'w');
        if (!empty($rows)) {
            fputcsv($out, array_keys($rows[0]));
            foreach ($rows as $row) fputcsv($out, $row);
        }
        fclose($out);
        exit();
    } elseif ($_GET['export'] === 'pdf') {
        require_once '../vendor/tecnickcom/tcpdf/tcpdf.php';
        $pdf = new TCPDF();
        $pdf->AddPage();
        $html = '<h2>' . ucfirst($report_type) . ' Report</h2><table border="1" cellpadding="4"><tr>';
        if (!empty($rows)) {
            foreach (array_keys($rows[0]) as $col) $html .= '<th>' . htmlspecialchars($col) . '</th>';
            $html .= '</tr>';
            foreach ($rows as $row) {
                $html .= '<tr>';
                foreach ($row as $cell) $html .= '<td>' . htmlspecialchars($cell) . '</td>';
                $html .= '</tr>';
            }
        }
        $html .= '</table>';
        $pdf->writeHTML($html);
        $pdf->Output($filename . '.pdf', 'D');
        exit();
    }
}
$content = ob_get_clean();
include 'layout_admin.php';
?>
<h1 class="text-2xl font-bold text-[#006837] mb-6">Sales Reports</h1>
<nav class="mb-4">
    <a href="?type=orders" class="mr-4">Order Summary</a>
    <a href="?type=products" class="mr-4">Product Sales</a>
</nav>
<?php if ($error): ?>
  <div class="bg-red-100 border border-red-400 text-red-700 p-3 rounded mb-4"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>
<div class="mb-4">
    <a href="?type=<?= urlencode($report_type) ?>&export=csv" class="bg-blue-600 text-white px-4 py-2 rounded mr-2">Export CSV</a>
    <a href="?type=<?= urlencode($report_type) ?>&export=pdf" class="bg-green-600 text-white px-4 py-2 rounded">Export PDF</a>
</div>
<div class="overflow-x-auto bg-white rounded shadow px-2 sm:px-4">
<table class="min-w-full bg-white border rounded shadow text-xs">
    <thead>
        <tr>
            <?php if (!empty($rows)): foreach (array_keys($rows[0]) as $col): ?>
                <th class="px-2 py-1 border"><?= htmlspecialchars($col) ?></th>
            <?php endforeach; endif; ?>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($rows as $row): ?>
        <tr>
            <?php foreach ($row as $cell): ?>
                <td class="px-2 py-1 border text-gray-700"><?= htmlspecialchars($cell) ?></td>
            <?php endforeach; ?>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($rows)): ?>
        <tr><td colspan="10" class="text-center text-gray-400 py-4">No data found.</td></tr>
        <?php endif; ?>
    </tbody>
</table>
</div>
<script src="https://cdn.tailwindcss.com"></script>
