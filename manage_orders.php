<?php
session_start();
include 'includes/db.php';

// Set currency symbol
$currency = 'Rs. ';

// Admin session check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Handle status update
if (isset($_POST['update_status'])) {
    $order_id = intval($_POST['order_id']);
    $new_status = $_POST['status'];
    $stmt = $conn->prepare("UPDATE orders SET status=? WHERE id=?");
    $stmt->bind_param("si", $new_status, $order_id);
    $stmt->execute();

    // Log activity
    $admin_id = $_SESSION['user_id'];
    $action = "Updated order #$order_id status to $new_status";
    $stmt = $conn->prepare("INSERT INTO activity_logs (admin_id, action) VALUES (?, ?)");
    $stmt->bind_param("is", $admin_id, $action);
    $stmt->execute();

    header("Location: manage_orders.php");
    exit();
}

//handle search
$search = "";
$search_sql = "";
$params = [];
$types = "";

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = "%" . $_GET['search'] . "%";
    $search_sql = "WHERE orders.id LIKE ? OR users.username LIKE ? OR orders.status LIKE ?";
    $params = [$search, $search, $search];
    $types = "sss";
}

$sql = "SELECT orders.*, users.username 
        FROM orders 
        JOIN users ON orders.user_id = users.id 
        $search_sql
        ORDER BY orders.created_at DESC";

$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$orders = $stmt->get_result();

// Prepare sales report data
$daily_sales = $conn->query("SELECT DATE(created_at) as day, SUM(total_price) as total FROM orders GROUP BY DATE(created_at) ORDER BY DATE(created_at) ASC LIMIT 30");
$weekly_sales = $conn->query("SELECT YEARWEEK(created_at) as week, SUM(total_price) as total FROM orders GROUP BY YEARWEEK(created_at) ORDER BY YEARWEEK(created_at) ASC LIMIT 12");
$monthly_sales = $conn->query("SELECT DATE_FORMAT(created_at,'%Y-%m') as month, SUM(total_price) as total FROM orders GROUP BY DATE_FORMAT(created_at,'%Y-%m') ORDER BY DATE_FORMAT(created_at,'%Y-%m') ASC LIMIT 12");

// DAILY
$daily_labels = [];
$daily_data = [];

if ($daily_sales && $daily_sales->num_rows > 0) {
    while ($r = $daily_sales->fetch_assoc()) {
        $daily_labels[] = $r['day'];
        $daily_data[] = (float)$r['total'];
    }
} else {
    $daily_labels = ['No Data'];
    $daily_data = [0];
}

// WEEKLY
$weekly_labels = [];
$weekly_data = [];

if ($weekly_sales && $weekly_sales->num_rows > 0) {
    while ($r = $weekly_sales->fetch_assoc()) {
        $weekly_labels[] = 'W' . $r['week'];
        $weekly_data[] = (float)$r['total'];
    }
} else {
    $weekly_labels = ['No Data'];
    $weekly_data = [0];
}

// MONTHLY
$monthly_labels = [];
$monthly_data = [];

if ($monthly_sales && $monthly_sales->num_rows > 0) {
    while ($r = $monthly_sales->fetch_assoc()) {
        $monthly_labels[] = $r['month'];
        $monthly_data[] = (float)$r['total'];
    }
} else {
    $monthly_labels = ['No Data'];
    $monthly_data = [0];
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Manage Orders</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.tailwindcss.com"></script>

<style>
body { background:#f8fafc; }
.sidebar { background:#0b3c91; }
.card { background:#fff; box-shadow:0 4px 20px rgba(0,0,0,0.05); }
</style>
</head>

<body class="text-gray-800">
<div class="flex min-h-screen">

<!-- ✅ SAME SIDEBAR -->
<aside class="sidebar w-72 p-6 flex flex-col text-white">
    <h1 class="text-2xl font-bold mb-8">WELCOME, Admin</h1>

    <nav class="space-y-3">
        <a href="admin_dashboard.php" class="block p-3 rounded-lg hover:bg-white/20">Dashboard</a>
        <a href="manage_users.php" class="block p-3 rounded-lg hover:bg-white/20">Users</a>
        <a href="manage_products.php" class="block p-3 rounded-lg hover:bg-white/20">Products</a>
        <a href="manage_orders.php" class="block p-3 rounded-lg bg-white/20">Orders</a>
        <a href="manage_news.php" class="block p-3 rounded-lg hover:bg-white/20">News</a>
    </nav>

    <div class="fixed bottom-5 left-5 z-50">
    <a href="logout.php"
    class="bg-white text-black px-6 py-3 rounded-xl font-semibold shadow-md hover:bg-gray-100 transition">

        Logout
    </a>
</div>
</aside>

<!-- MAIN -->
<main class="flex-1 p-8">

<h2 class="text-3xl font-bold mb-6">Manage Orders</h2>

<!-- SEARCH -->
<div class="card p-4 mb-6 flex gap-3">
<form method="GET" class="flex gap-3 w-full">
<input type="text" name="search"
placeholder="Search orders..."
value="<?php echo htmlspecialchars($search); ?>"
class="flex-1 p-3 border rounded-lg">

<button class="bg-[#0b3c91] text-white px-5 rounded-lg">
Search
</button>
</form>
</div>

<!-- TABLE -->
<div class="card p-6 rounded-xl overflow-x-auto">

<table class="w-full text-sm">
<thead class="border-b">
<tr>
<th class="p-3">Order</th>
<th class="p-3">Customer</th>
<th class="p-3">Products</th>
<th class="p-3">Total</th>
<th class="p-3">Status</th>
<th class="p-3">Date</th>
<th class="p-3">Update</th>
</tr>
</thead>

<tbody>
<?php while ($order = $orders->fetch_assoc()): ?>
<tr class="border-b hover:bg-gray-50">
<td class="p-3"><?php echo $order['id']; ?></td>
<td class="p-3"><?php echo htmlspecialchars($order['username']); ?></td>

<td class="p-3 text-sm">
<?php
$products = !empty($order['products']) ? json_decode($order['products'], true) : [];

if (is_array($products)) {
foreach ($products as $p) {
$res = $conn->query("SELECT name,image FROM products WHERE id=" . intval($p['id']));
if ($res && $prod = $res->fetch_assoc()) {
echo '<div class="flex items-center gap-2 mb-1">';
echo '<img src="assets/images/'.$prod['image'].'" class="w-8 h-8 rounded">';
echo $prod['name']." × ".$p['qty'];
echo '</div>';
}
}
}
?>
</td>

<td class="p-3 font-semibold">
<?php echo $currency . number_format($order['total_price'],2); ?>
</td>

<td class="p-3 capitalize"><?php echo $order['status']; ?></td>
<td class="p-3"><?php echo $order['created_at']; ?></td>

<td class="p-3">
<form method="POST" class="flex gap-2">
<input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">

<select name="status" class="border p-1 rounded">
<option value="pending" <?php if($order['status']=='pending') echo 'selected'; ?>>Pending</option>
<option value="shipped" <?php if($order['status']=='shipped') echo 'selected'; ?>>Shipped</option>
<option value="delivered" <?php if($order['status']=='delivered') echo 'selected'; ?>>Delivered</option>
</select>

<button name="update_status"
class="bg-green-600 text-white px-3 py-1 rounded">
Update
</button>
</form>
</td>
</tr>
<?php endwhile; ?>
</tbody>

</table>
</div>

<!-- CHARTS -->
<div class="card p-6 mt-8">

<div class="flex gap-3 mb-4">
<button class="tab-btn bg-[#0b3c91] text-white px-3 py-1 rounded" data-tab="daily">Daily</button>
<button class="tab-btn bg-gray-300 px-3 py-1 rounded" data-tab="weekly">Weekly</button>
<button class="tab-btn bg-gray-300 px-3 py-1 rounded" data-tab="monthly">Monthly</button>
</div>

<div class="h-80">
    <canvas id="chart"></canvas>
</div>
</div>

</main>
</div>

<script>

const dailyLabels = <?php echo json_encode($daily_labels); ?>;
const dailyData = <?php echo json_encode($daily_data); ?>;

const weeklyLabels = <?php echo json_encode($weekly_labels); ?>;
const weeklyData = <?php echo json_encode($weekly_data); ?>;

const monthlyLabels = <?php echo json_encode($monthly_labels); ?>;
const monthlyData = <?php echo json_encode($monthly_data); ?>;

let chart;

function renderChart(labels, data, type) {
    if (chart) chart.destroy();

    chart = new Chart(document.getElementById('chart'), {
        type: type,
        data: {
            labels: labels,
            datasets: [{
                label: 'Sales',
                data: data,
                borderColor: '#0b3c91',
                backgroundColor: 'rgba(11,60,145,0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
}

// default chart
renderChart(dailyLabels, dailyData, 'line');

// tabs
document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', () => {

        document.querySelectorAll('.tab-btn').forEach(b => {
            b.classList.remove('bg-[#0b3c91]', 'text-white');
            b.classList.add('bg-gray-300');
        });

        btn.classList.remove('bg-gray-300');
        btn.classList.add('bg-[#0b3c91]', 'text-white');

        if (btn.dataset.tab === 'daily') renderChart(dailyLabels, dailyData, 'line');
        if (btn.dataset.tab === 'weekly') renderChart(weeklyLabels, weeklyData, 'bar');
        if (btn.dataset.tab === 'monthly') renderChart(monthlyLabels, monthlyData, 'line');
    });
});
</script>
</body>
</html>