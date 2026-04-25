<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

function getValue($conn, $query) {
    $res = $conn->query($query);
    if (!$res) {
        die("SQL Error: " . $conn->error);
    }
    return $res->fetch_assoc()['total'] ?? 0;
}

$total_users = getValue($conn, "SELECT COUNT(*) as total FROM users");
$total_products = getValue($conn, "SELECT COUNT(*) as total FROM products");
$total_revenue = getValue($conn, "SELECT COALESCE(SUM(total_price),0) as total FROM orders");

$labels = [];
$data = [];
$res = $conn->query("SELECT DATE(created_at) as d, SUM(total_price) as total FROM orders GROUP BY d ORDER BY d ASC LIMIT 30");

if ($res) {
    while ($row = $res->fetch_assoc()) {
        $labels[] = $row['d'];
        $data[] = (float)$row['total'];
    }
}

if (empty($labels)) {
    $labels = ['No Data'];
    $data = [0];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard</title>
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
body { background: #f8fafc; } /* WHITE PAGE */
.sidebar { background: #0b3c91; }
.card { background: #ffffff; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
</style>
</head>

<body class="text-gray-800">
<div class="flex min-h-screen">

<!-- Sidebar -->
<aside class="sidebar w-72 p-6 flex flex-col text-white shadow-xl">
    <h1 class="text-2xl font-bold mb-8">Anime Admin</h1>

    <nav class="space-y-3">
        <a href="admin_dashboard.php" class="block p-3 rounded-lg bg-white/20">Dashboard</a>
        <a href="manage_users.php" class="block p-3 rounded-lg hover:bg-white/20">Users</a>
        <a href="manage_products.php" class="block p-3 rounded-lg hover:bg-white/20">Products</a>
        <a href="manage_orders.php" class="block p-3 rounded-lg hover:bg-white/20">Orders</a>
        <a href="manage_news.php" class="block p-3 rounded-lg hover:bg-white/20">News</a>
    </nav>

  <div class="fixed bottom-5 left-5 z-50">
    <a href="logout.php"
    class="bg-white text-black px-6 py-3 rounded-xl font-semibold shadow-md hover:bg-gray-100 transition">

        Logout
    </a>
</div>
</aside>

<!-- Main -->
<main class="flex-1 p-8">

<div class="flex justify-between items-center mb-8">
    <div>
        <h2 class="text-3xl font-bold">Dashboard</h2>
        <p class="text-gray-500">Welcome back, <?php echo htmlspecialchars($_SESSION['username']); ?></p>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="card p-6 rounded-xl">
        <p class="text-gray-500">Users</p>
        <h3 class="text-3xl font-bold"><?php echo $total_users; ?></h3>
    </div>

    <div class="card p-6 rounded-xl">
        <p class="text-gray-500">Products</p>
        <h3 class="text-3xl font-bold"><?php echo $total_products; ?></h3>
    </div>

    <div class="card p-6 rounded-xl">
        <p class="text-gray-500">Revenue</p>
        <h3 class="text-3xl font-bold">Rs. <?php echo number_format($total_revenue,2); ?></h3>
    </div>
</div>

<div class="card p-6 rounded-xl">
    <h3 class="text-xl font-semibold mb-4">Sales Trend</h3>
    <canvas id="chart" height="100"></canvas>
</div>

</main>
</div>

<script>
new Chart(document.getElementById('chart'), {
    type: 'line',
    data: {
        labels: <?php echo json_encode($labels); ?>,
        datasets: [{
            label: 'Sales',
            data: <?php echo json_encode($data); ?>,
            borderColor: '#0b3c91',
            backgroundColor: 'rgba(11,60,145,0.1)',
            fill: true,
            tension: 0.4
        }]
    }
});
</script>

</body>
</html>