<?php
require_once '../db_connect.php';
ob_start();
date_default_timezone_set('Asia/Manila'); // Ensure correct timezone

// Fetch dashboard metrics from the database
$totalProducts = $conn->query("SELECT COUNT(*) FROM products")->fetchColumn();
$totalUsers = $conn->query("SELECT COUNT(*) FROM users WHERE role IN ('customer','barista')")->fetchColumn();
$totalOrders = $conn->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$totalSales = $conn->query("SELECT SUM(total_price) FROM orders WHERE status='completed'")->fetchColumn();
if ($totalSales === null) $totalSales = 0.00;
// Fetch total sales for today within shop hours (9am to 1am next day)
$start = date('Y-m-d') . ' 09:00:00';
$end = date('Y-m-d', strtotime('+1 day')) . ' 00:00:00';
$stmt = $conn->prepare("SELECT SUM(total_price) FROM orders WHERE status='completed' AND created_at >= ? AND created_at < ?");
$stmt->execute([$start, $end]);
$totalSalesToday = $stmt->fetchColumn();
if ($totalSalesToday === null) $totalSalesToday = 0.00;
// Fetch pending orders today
$pendingToday = $conn->prepare("SELECT COUNT(*) FROM orders WHERE status = 'pending' AND DATE(created_at) = CURDATE()");
$pendingToday->execute();
$pendingTodayCount = $pendingToday->fetchColumn();
// Fetch recent orders (last 5)
$recent_orders = $conn->query("
    SELECT o.id, u.first_name, u.last_name, o.total_price, o.status, o.created_at 
    FROM orders o 
    JOIN users u ON o.user_id = u.id 
    ORDER BY o.created_at DESC LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);
// Fetch users, orders, and sales per day for the last 7 days
$chartLabels = [];
$usersData = [];
$ordersData = [];
$salesData = [];

// Prepare date keys for last 7 days
$dateKeys = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $dateKeys[] = $date;
    $chartLabels[] = $date;
}

// Fetch users per day
$usersPerDay = $conn->query("SELECT DATE(created_at) as date, COUNT(*) as count FROM users WHERE role IN ('customer','barista') AND created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY) GROUP BY DATE(created_at) ORDER BY date ASC")->fetchAll(PDO::FETCH_KEY_PAIR);
// Fetch orders per day
$ordersPerDay = $conn->query("SELECT DATE(created_at) as date, COUNT(*) as count FROM orders WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY) GROUP BY DATE(created_at) ORDER BY date ASC")->fetchAll(PDO::FETCH_KEY_PAIR);
// Fetch sales per day
$salesPerDay = $conn->query("SELECT DATE(created_at) as date, SUM(total_price) as total FROM orders WHERE status='completed' AND created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY) GROUP BY DATE(created_at) ORDER BY date ASC")->fetchAll(PDO::FETCH_KEY_PAIR);

foreach ($dateKeys as $date) {
    $usersData[] = isset($usersPerDay[$date]) ? (int)$usersPerDay[$date] : 0;
    $ordersData[] = isset($ordersPerDay[$date]) ? (int)$ordersPerDay[$date] : 0;
    $salesData[] = isset($salesPerDay[$date]) ? (float)$salesPerDay[$date] : 0.00;
}
// Fetch trend for sales and pending orders (compare today vs yesterday)
$salesYesterday = $conn->prepare("SELECT SUM(total_price) FROM orders WHERE status='completed' AND DATE(created_at) = DATE_SUB(CURDATE(), INTERVAL 1 DAY) AND TIME(created_at) >= '09:00:00' AND TIME(created_at) <= '23:59:59'");
$salesYesterday->execute();
$salesYesterday = $salesYesterday->fetchColumn();
if ($salesYesterday === null) $salesYesterday = 0.00;
$pendingYesterday = $conn->prepare("SELECT COUNT(*) FROM orders WHERE status = 'pending' AND DATE(created_at) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)");
$pendingYesterday->execute();
$pendingYesterdayCount = $pendingYesterday->fetchColumn();
if ($pendingYesterdayCount === null) $pendingYesterdayCount = 0;
// Calculate trends
$salesTrend = ($salesYesterday > 0) ? (($totalSalesToday - $salesYesterday) / $salesYesterday) * 100 : 0;
$pendingTrend = ($pendingYesterdayCount > 0) ? (($pendingTodayCount - $pendingYesterdayCount) / $pendingYesterdayCount) * 100 : 0;
// Fetch featured items
$featuredItems = $conn->query("SELECT * FROM products WHERE featured = 1 ORDER BY name ASC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
// Fetch popular items only if order_items table exists
$popularItems = [];
try {
    $popularItems = $conn->query("SELECT p.*, SUM(oi.quantity) as total_sold FROM order_items oi JOIN products p ON oi.product_id = p.id GROUP BY oi.product_id ORDER BY total_sold DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Table does not exist, skip popular items
}

// Fetch sales per day by category for the last 7 days
$salesCategories = [];
$salesByCategory = [];
// Use fixed categories for the stacked chart
$salesCategories = [
    'Specialty Coffee',
    'Signature Beverages',
    'Rice Bowls',
    'Noodles',
    'Snacks',
    'Desserts'
];

// Initialize salesByCategory with zeros for each date
foreach ($salesCategories as $cat) {
    $salesByCategory[$cat] = array_fill(0, count($dateKeys), 0.00);
}

if (!empty($salesCategories)) {
    $catSql = "SELECT p.category, DATE(o.created_at) as date, SUM(oi.quantity * oi.price) as total
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        JOIN orders o ON oi.order_id = o.id
        WHERE o.status = 'completed' AND o.created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
        AND p.category IN ('Specialty Coffee','Signature Beverages','Rice Bowls','Noodles','Snacks','Desserts')
        GROUP BY p.category, DATE(o.created_at)";
    $catSales = $conn->query($catSql)->fetchAll(PDO::FETCH_ASSOC);
    // Map results to salesByCategory
    foreach ($catSales as $row) {
        $cat = $row['category'];
        $date = $row['date'];
        $idx = array_search($date, $dateKeys);
        if ($idx !== false && isset($salesByCategory[$cat])) {
            $salesByCategory[$cat][$idx] = (float)$row['total'];
        }
    }
}
?>
<h1 class="text-2xl font-bold text-[#006837] mb-2">Admin Dashboard</h1>
<div class="mb-6">
    <div class="bg-green-700 text-white rounded-xl px-6 py-4 shadow flex items-center gap-4">
        <i class="fas fa-user-shield text-2xl"></i>
        <div>
            <span class="block text-lg font-semibold">Welcome back, Admin!</span>
            <span class="block text-sm opacity-90">Here's a quick overview of today's activity and stats.</span>
        </div>
    </div>
</div>
<!-- Quick action buttons only (no sidebar duplicates) -->
<div class="flex flex-wrap gap-3 mb-8 items-center justify-center w-full">
    <a href="add_product.php" class="flex items-center bg-green-700 text-white px-4 py-2 rounded shadow hover:bg-green-800 transition min-w-[160px] justify-center"><i class="fas fa-plus mr-2"></i> Add Product</a>
    <a href="reports.php" class="flex items-center bg-green-700 text-white px-4 py-2 rounded shadow hover:bg-green-800 transition min-w-[160px] justify-center"><i class="fas fa-file-export mr-2"></i> Export Report</a>
    <a href="orders.php" class="flex items-center bg-green-700 text-white px-4 py-2 rounded shadow hover:bg-green-800 transition min-w-[160px] justify-center"><i class="fas fa-list mr-2"></i> View All Orders</a>
    <button onclick="toggleDarkMode()" class="flex items-center bg-gray-800 text-white px-4 py-2 rounded shadow hover:bg-black transition min-w-[160px] justify-center" id="darkModeBtn"><i class="fas fa-moon mr-2"></i> Toggle Dark Mode</button>
</div>
<!-- Metrics Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white p-6 rounded-xl shadow-md border border-gray-200 flex items-center space-x-4 transition-transform duration-200 hover:-translate-y-1 hover:shadow-lg">
        <div class="bg-[#009245] text-white p-4 rounded-full">
            <i class="fas fa-coffee text-2xl"></i>
        </div>
        <div>
            <p class="text-gray-500">Total Products</p>
            <h2 class="text-xl font-bold" id="totalProducts"><?= $totalProducts ?></h2>
        </div>
    </div>
    <div class="bg-white p-6 rounded-xl shadow-md border border-gray-200 flex items-center space-x-4 transition-transform duration-200 hover:-translate-y-1 hover:shadow-lg">
        <div class="bg-[#009245] text-white p-4 rounded-full">
            <i class="fas fa-coins text-2xl"></i>
        </div>
        <div>
            <p class="text-gray-500">Total Sales Today
                <?php if ($salesTrend > 0): ?>
                    <span class="text-green-600 ml-2">▲ <?= number_format($salesTrend, 1) ?>%</span>
                <?php elseif ($salesTrend < 0): ?>
                    <span class="text-red-600 ml-2">▼ <?= number_format(abs($salesTrend), 1) ?>%</span>
                <?php endif; ?>
            </p>
            <h2 class="text-xl font-bold" id="totalSalesToday">₱<?= number_format($totalSalesToday, 2) ?></h2>
        </div>
    </div>
    <div class="bg-white p-6 rounded-xl shadow-md border border-gray-200 flex items-center space-x-4 transition-transform duration-200 hover:-translate-y-1 hover:shadow-lg">
        <div class="bg-[#009245] text-white p-4 rounded-full">
            <i class="fas fa-clock text-2xl"></i>
        </div>
        <div>
            <p class="text-gray-500">Pending Orders Today
                <?php if ($pendingTrend > 0): ?>
                    <span class="text-red-600 ml-2">▲ <?= number_format($pendingTrend, 1) ?>%</span>
                <?php elseif ($pendingTrend < 0): ?>
                    <span class="text-green-600 ml-2">▼ <?= number_format(abs($pendingTrend), 1) ?>%</span>
                <?php endif; ?>
            </p>
            <h2 class="text-xl font-bold" id="pendingTodayCount"><?= $pendingTodayCount ?></h2>
        </div>
    </div>
</div>
<!-- Sales Chart Placeholder -->
<div class="bg-white p-6 rounded-xl shadow mb-8">
    <h2 class="text-lg font-semibold text-[#006837] mb-2">Sales Trend (Last 7 Days)</h2>
    <canvas id="salesTrendChart" height="80"></canvas>
</div>
<!-- Orders & Users Charts Side by Side -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    <div class="bg-white p-6 rounded-xl shadow mb-8">
        <h2 class="text-lg font-semibold text-[#006837] mb-2">Orders Placed (Last 7 Days)</h2>
        <canvas id="ordersChart" height="80"></canvas>
    </div>
    <div class="bg-white p-6 rounded-xl shadow mb-8">
        <h2 class="text-lg font-semibold text-[#006837] mb-2">Users Registered (Last 7 Days)</h2>
        <canvas id="usersChart" height="80"></canvas>
    </div>
</div>
<!-- Recent Orders Table -->
<div class="bg-white p-6 rounded-xl shadow gap-6 mb-8">
    <!-- Add search/filter to Recent Orders Table -->
    <div class="flex justify-between items-center mb-2">
        <h2 class="text-xl font-semibold text-[#006837]">Recent Orders</h2>
        <input type="text" id="orderSearch" placeholder="Search orders..." class="border border-gray-300 rounded px-3 py-1 focus:outline-none focus:ring-2 focus:ring-green-400" onkeyup="filterOrders()">
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left table-auto border-collapse">
            <thead class="bg-gray-200 text-gray-700">
                <tr>
                    <th class="p-2">Order ID</th>
                    <th class="p-2">Customer</th>
                    <th class="p-2">Total Price</th>
                    <th class="p-2">Status</th>
                    <th class="p-2">Date</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $recent_orders = $recent_orders ?? [];
                foreach ($recent_orders as $order): ?>
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-2"><?= htmlspecialchars($order['id']) ?></td>
                        <td class="p-2"><?= htmlspecialchars($order['first_name'] . ' ' . $order['last_name']) ?></td>
                        <td class="p-2">₱<?= number_format($order['total_price'], 2) ?></td>
                        <td class="p-2">
                            <span class="inline-block px-2 py-1 text-sm rounded
                                <?= $order['status'] === 'completed' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' ?>">
                                <?= ucfirst($order['status']) ?>
                            </span>
                        </td>
                        <td class="p-2"><?= date('M d, Y H:i', strtotime($order['created_at'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<!-- Featured & Popular Items as Carousels -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    <!-- Featured Items Carousel -->
    <div class="bg-white p-6 rounded-xl shadow relative">
        <h2 class="text-lg font-semibold text-[#006837] mb-2 flex items-center"><i class="fas fa-star text-yellow-400 mr-2"></i> Featured Items</h2>
        <div class="relative">
            <button type="button" class="absolute left-0 top-1/2 -translate-y-1/2 z-10 bg-white border border-gray-200 rounded-full p-2 shadow hover:bg-gray-100 focus:outline-none" onclick="scrollCarousel('featuredCarousel', -1)">
                <i class="fas fa-chevron-left"></i>
            </button>
            <div id="featuredCarousel" class="flex overflow-x-auto gap-4 scrollbar-hide py-2 px-8">
                <?php foreach ($featuredItems as $item): ?>
                    <div class="min-w-[180px] bg-gray-50 rounded-lg shadow flex flex-col items-center p-4">
                        <?php if ($item['image_filename']): ?>
                            <img src="../uploads/<?= htmlspecialchars($item['image_filename']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="h-16 w-16 object-cover rounded mb-2">
                        <?php endif; ?>
                        <span class="font-semibold text-gray-800 text-center mb-1"><?= htmlspecialchars($item['name']) ?></span>
                        <span class="text-gray-500 text-sm">₱<?= number_format($item['price'], 2) ?></span>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($featuredItems)): ?>
                    <div class="text-gray-400 flex items-center justify-center h-20">No featured items.</div>
                <?php endif; ?>
            </div>
            <button type="button" class="absolute right-0 top-1/2 -translate-y-1/2 z-10 bg-white border border-gray-200 rounded-full p-2 shadow hover:bg-gray-100 focus:outline-none" onclick="scrollCarousel('featuredCarousel', 1)">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </div>
    <!-- Popular Items Carousel -->
    <div class="bg-white p-6 rounded-xl shadow relative">
        <h2 class="text-lg font-semibold text-[#006837] mb-2 flex items-center"><i class="fas fa-fire text-red-500 mr-2"></i> Popular Items</h2>
        <div class="relative">
            <button type="button" class="absolute left-0 top-1/2 -translate-y-1/2 z-10 bg-white border border-gray-200 rounded-full p-2 shadow hover:bg-gray-100 focus:outline-none" onclick="scrollCarousel('popularCarousel', -1)">
                <i class="fas fa-chevron-left"></i>
            </button>
            <div id="popularCarousel" class="flex overflow-x-auto gap-4 scrollbar-hide py-2 px-8">
                <?php foreach ($popularItems as $item): ?>
                    <div class="min-w-[180px] bg-gray-50 rounded-lg shadow flex flex-col items-center p-4">
                        <?php if ($item['image_filename']): ?>
                            <img src="../uploads/<?= htmlspecialchars($item['image_filename']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="h-16 w-16 object-cover rounded mb-2">
                        <?php endif; ?>
                        <span class="font-semibold text-gray-800 text-center mb-1"><?= htmlspecialchars($item['name']) ?></span>
                        <span class="text-gray-500 text-sm">Sold: <?= (int)$item['total_sold'] ?></span>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($popularItems)): ?>
                    <div class="text-gray-400 flex items-center justify-center h-20">No popular items data.</div>
                <?php endif; ?>
            </div>
            <button type="button" class="absolute right-0 top-1/2 -translate-y-1/2 z-10 bg-white border border-gray-200 rounded-full p-2 shadow hover:bg-gray-100 focus:outline-none" onclick="scrollCarousel('popularCarousel', 1)">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </div>
</div>
<!-- Activity Feed -->
<div class="bg-white p-6 rounded-xl shadow mb-8">
    <h2 class="text-lg font-semibold text-[#006837] mb-2 flex items-center"><i class="fas fa-bolt text-yellow-400 mr-2"></i> Recent Activity</h2>
    <ul class="divide-y divide-gray-200">
        <?php
        // Show last 5 activities: new orders, new users, status changes
        $activities = $conn->query("(
            SELECT 'Order' as type, id, created_at, status, total_price, NULL as name FROM orders ORDER BY created_at DESC LIMIT 3
        ) UNION ALL (
            SELECT 'User' as type, id, created_at, NULL as status, NULL as total_price, CONCAT(first_name, ' ', last_name) as name FROM users ORDER BY created_at DESC LIMIT 2
        ) ORDER BY created_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($activities as $act): ?>
            <li class="py-2 flex items-center gap-3">
                <?php if ($act['type'] === 'Order'): ?>
                    <i class="fas fa-receipt text-green-600"></i>
                    <span>Order <b>#<?= htmlspecialchars($act['id']) ?></b> <?= $act['status'] ? '('.htmlspecialchars($act['status']).')' : '' ?> placed for <b>₱<?= number_format($act['total_price'],2) ?></b></span>
                <?php else: ?>
                    <i class="fas fa-user-plus text-blue-600"></i>
                    <span>New user registered: <b><?= htmlspecialchars($act['name']) ?></b></span>
                <?php endif; ?>
                <span class="ml-auto text-xs text-gray-400"><?= date('M d, H:i', strtotime($act['created_at'])) ?></span>
            </li>
        <?php endforeach; ?>
        <?php if (empty($activities)): ?>
            <li class="py-2 text-gray-400">No recent activity.</li>
        <?php endif; ?>
    </ul>
</div>
<!-- Floating Date & Time Widget -->
<div id="dateTimeWidget" class="fixed top-6 right-8 z-50 bg-white shadow-lg rounded-xl px-6 py-3 flex items-center gap-3 border border-gray-200 transition-all duration-300 opacity-100 pointer-events-auto">
    <i class="fas fa-clock text-[#009245] text-xl"></i>
    <div>
        <div id="dateWidget" class="font-semibold text-gray-700 text-sm"></div>
        <div id="timeWidget" class="font-mono text-lg text-[#009245]"></div>
    </div>
</div>
<script>
// Live Date & Time
function updateDateTimeWidget() {
    const now = new Date();
    const dateStr = now.toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'short', day: 'numeric' });
    const timeStr = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
    document.getElementById('dateWidget').textContent = dateStr;
    document.getElementById('timeWidget').textContent = timeStr;
}
setInterval(updateDateTimeWidget, 1000);
updateDateTimeWidget();
// Hide widget on scroll down, show at top
let lastScrollY = 0;
window.addEventListener('scroll', function() {
    const widget = document.getElementById('dateTimeWidget');
    if (!widget) return;
    if (window.scrollY > 20) {
        widget.classList.add('opacity-0', 'pointer-events-none');
        widget.classList.remove('opacity-100', 'pointer-events-auto');
    } else {
        widget.classList.remove('opacity-0', 'pointer-events-none');
        widget.classList.add('opacity-100', 'pointer-events-auto');
    }
    checkSidebarAndHideWidget();
});
// Hide Date & Time widget on mobile if sidebar is open
function checkSidebarAndHideWidget() {
    const widget = document.getElementById('dateTimeWidget');
    const sidebar = document.getElementById('sidebar'); // Assumes sidebar has id="sidebar"
    const overlay = document.querySelector('.sidebar-overlay'); // Assumes overlay has this class
    const isMobile = window.innerWidth < 768;
    let sidebarOpen = false;
    // Check if sidebar is open (by overlay visible or sidebar class)
    if (isMobile) {
        if (overlay && overlay.style.display !== 'none' && overlay.offsetParent !== null) {
            sidebarOpen = true;
        } else if (sidebar && (sidebar.classList.contains('translate-x-0') || sidebar.classList.contains('open'))) {
            sidebarOpen = true;
        }
    }
    if (widget) {
        if (sidebarOpen) {
            widget.classList.add('opacity-0', 'pointer-events-none');
            widget.classList.remove('opacity-100', 'pointer-events-auto');
        } else if (window.scrollY <= 20) {
            widget.classList.remove('opacity-0', 'pointer-events-none');
            widget.classList.add('opacity-100', 'pointer-events-auto');
        }
    }
}
// Listen for sidebar toggle and window resize
window.addEventListener('resize', checkSidebarAndHideWidget);
document.addEventListener('DOMContentLoaded', function() {
    // If your sidebar toggle button has a known id or class, listen for clicks
    const sidebarToggle = document.getElementById('sidebarToggle');
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            setTimeout(checkSidebarAndHideWidget, 350); // Wait for animation
        });
    }
    // Also observe overlay clicks (to close sidebar)
    const overlay = document.querySelector('.sidebar-overlay');
    if (overlay) {
        overlay.addEventListener('click', function() {
            setTimeout(checkSidebarAndHideWidget, 350);
        });
    }
    checkSidebarAndHideWidget();
});
// Optionally, observe sidebar class changes (for frameworks that animate sidebar)
const sidebar = document.getElementById('sidebar');
if (sidebar && typeof MutationObserver !== 'undefined') {
    const observer = new MutationObserver(checkSidebarAndHideWidget);
    observer.observe(sidebar, { attributes: true, attributeFilter: ['class', 'style'] });
}
// Dark mode toggle
function toggleDarkMode() {
    document.documentElement.classList.toggle('dark');
    localStorage.setItem('dashboardDarkMode', document.documentElement.classList.contains('dark'));
}
// On load, apply dark mode if set
if (localStorage.getItem('dashboardDarkMode') === 'true') {
    document.documentElement.classList.add('dark');
}
// Filter/search for recent orders
function filterOrders() {
    const input = document.getElementById('orderSearch').value.toLowerCase();
    const rows = document.querySelectorAll('table tbody tr');
    rows.forEach(row => {
        const text = row.innerText.toLowerCase();
        row.style.display = text.includes(input) ? '' : 'none';
    });
}
// Animate metric numbers
function animateValue(id, start, end, duration) {
    const obj = document.getElementById(id);
    let startTimestamp = null;
    const step = (timestamp) => {
        if (!startTimestamp) startTimestamp = timestamp;
        const progress = Math.min((timestamp - startTimestamp) / duration, 1);
        const value = Math.floor(progress * (end - start) + start);
        obj.innerText = value.toLocaleString();
        if (progress < 1) {
            window.requestAnimationFrame(step);
        } else {
            obj.innerText = end.toLocaleString();
        }
    };
    window.requestAnimationFrame(step);
}
window.addEventListener('DOMContentLoaded', function() {
    // Animate metrics
    animateValue('totalProducts', 0, <?= $totalProducts ?>, 800);
    animateValue('totalSalesToday', 0, <?= (int)$totalSalesToday ?>, 800);
    animateValue('pendingTodayCount', 0, <?= $pendingTodayCount ?>, 800);
    // Users Chart
    const usersLabels = <?= json_encode($chartLabels) ?>;
    const usersData = <?= json_encode($usersData) ?>;
    const usersCtx = document.getElementById('usersChart').getContext('2d');
    new Chart(usersCtx, {
        type: 'line',
        data: {
            labels: usersLabels,
            datasets: [{
                label: 'Users',
                data: usersData,
                borderColor: '#009245',
                backgroundColor: 'rgba(0,146,69,0.1)',
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'top' }, title: { display: false } },
            scales: { y: { beginAtZero: true, precision: 0 } }
        }
    });
    // Orders Chart
    const ordersLabels = <?= json_encode($chartLabels) ?>;
    const ordersData = <?= json_encode($ordersData) ?>;
    const ordersCtx = document.getElementById('ordersChart').getContext('2d');
    new Chart(ordersCtx, {
        type: 'line',
        data: {
            labels: ordersLabels,
            datasets: [{
                label: 'Orders',
                data: ordersData,
                borderColor: '#f59e42',
                backgroundColor: 'rgba(245,158,66,0.1)',
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'top' }, title: { display: false } },
            scales: { y: { beginAtZero: true, precision: 0 } }
        }
    });
    // Sales Trend Chart (Stacked by Category)
    const salesTrendLabels = <?= json_encode($chartLabels) ?>;
    const salesCategories = <?= json_encode(array_values($salesCategories)) ?>;
    const salesByCategory = <?= json_encode($salesByCategory) ?>;
    const salesTrendCtx = document.getElementById('salesTrendChart').getContext('2d');
    // Prepare datasets for each category
    const colorPalette = [
        '#009245', // Specialty Coffee
        '#f59e42', // Signature Beverages
        '#eab308', // Rice Bowls
        '#3b82f6', // Noodles
        '#ef4444', // Snacks
        '#a855f7'  // Desserts
    ];
    const salesDatasets = salesCategories.map((cat, i) => ({
        label: cat,
        data: salesByCategory[cat],
        backgroundColor: colorPalette[i % colorPalette.length],
        stack: 'salesStack',
        borderRadius: 6,
        maxBarThickness: 38,
        order: i < 2 ? 0 : 1 // main categories on top
    }));
    new Chart(salesTrendCtx, {
        type: 'bar',
        data: {
            labels: salesTrendLabels,
            datasets: salesDatasets
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        boxWidth: 18,
                        font: { size: 14 },
                        padding: 18
                    }
                },
                title: { display: false },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) label += ': ';
                            if (context.parsed.y !== null) {
                                label += '₱' + context.parsed.y.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
                            }
                            return label;
                        }
                    }
                }
            },
            interaction: { mode: 'index', intersect: false },
            scales: {
                y: {
                    beginAtZero: true,
                    stacked: true,
                    ticks: {
                        callback: function(value) {
                            return '₱' + value.toLocaleString();
                        }
                    }
                },
                x: {
                    stacked: true,
                    ticks: {
                        callback: function(value, index, values) {
                            const label = this.getLabelForValue(value);
                            return label ? label.slice(5) : value;
                        },
                        font: { size: 13 }
                    },
                    grid: { display: false }
                }
            }
        }
    });
    // Carousel touch/auto-scroll
    ['featuredCarousel','popularCarousel'].forEach(id => {
        const el = document.getElementById(id);
        if (!el) return;
        let isDown = false, startX, scrollLeft;
        el.addEventListener('mousedown', e => {
            isDown = true;
            el.classList.add('cursor-grabbing');
            startX = e.pageX - el.offsetLeft;
            scrollLeft = el.scrollLeft;
        });
        el.addEventListener('mouseleave', () => { isDown = false; el.classList.remove('cursor-grabbing'); });
        el.addEventListener('mouseup', () => { isDown = false; el.classList.remove('cursor-grabbing'); });
        el.addEventListener('mousemove', e => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - el.offsetLeft;
            el.scrollLeft = scrollLeft - (x - startX);
        });
        // Touch support
        let touchStartX = 0, touchScrollLeft = 0;
        el.addEventListener('touchstart', e => {
            touchStartX = e.touches[0].pageX;
            touchScrollLeft = el.scrollLeft;
        });
        el.addEventListener('touchmove', e => {
            const x = e.touches[0].pageX;
            el.scrollLeft = touchScrollLeft - (x - touchStartX);
        });
        // Auto-scroll
        setInterval(() => {
            if (el.scrollWidth > el.clientWidth) {
                el.scrollBy({ left: 220, behavior: 'smooth' });
                if (el.scrollLeft + el.clientWidth >= el.scrollWidth - 10) {
                    el.scrollTo({ left: 0, behavior: 'smooth' });
                }
            }
        }, 4000);
    });
});
</script>
</body>
</html>
<?php
$content = ob_get_clean();
include 'layout_admin.php';
?>
