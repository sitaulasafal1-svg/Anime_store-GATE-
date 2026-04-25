<?php
session_start();
include 'includes/db.php';

/* ================= AUTH ================= */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit();
}

$user_id = (int) $_SESSION['user_id'];

/* ================= FETCH ORDERS ================= */
$stmt = $conn->prepare("
    SELECT o.*, oi.quantity, oi.price, p.name, p.image
    FROM orders o
    JOIN order_items oi ON o.id = oi.order_id
    JOIN products p ON oi.product_id = p.id
    WHERE o.user_id=?
    ORDER BY o.created_at DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$orders = [];
while ($row = $result->fetch_assoc()) {
    $orders[$row['id']][] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Orders</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-black text-gray-200">

<!-- NAVBAR -->
<header class="fixed top-0 w-full z-50 backdrop-blur-md bg-white/5 border-b border-white/10">
  <div class="max-w-[110rem] mx-auto flex justify-between items-center px-10 py-3">

    <img src="assets/images/LOGO.png" alt="logo" class="h-12 md:h-16 object-contain">

    <nav class="flex gap-8 text-sm text-gray-300">
      <a href="newstation.php" class="hover:text-red-400">NewStation</a>
      <a href="customer_home.php" class="hover:text-red-400">Home</a>
      <a href="shop.php" class="hover:text-red-400">Shop</a>
      <a href="customer_orders.php" class="text-red-400">Orders</a>
      <a href="customer_profile.php" class="hover:text-red-400">Profile</a>
    </nav>

    <a href="logout.php"
       class="bg-red-500 px-5 py-2 rounded-lg hover:bg-red-600 text-white">
      Logout
    </a>
  </div>
</header>

<!-- MAIN -->
<main class="pt-28 px-8 max-w-[1400px] mx-auto">

<div class="mb-10">
  <h2 class="text-4xl font-bold text-red-500">My Orders</h2>
  <p class="text-gray-400">Track your anime purchases</p>
</div>

<?php if (empty($orders)): ?>

<div class="text-center py-20">
  <p class="text-gray-500 text-lg">No orders yet 😢</p>
  <a href="shop.php" class="mt-4 inline-block bg-red-500 px-6 py-3 rounded-lg">
    Go Shopping
  </a>
</div>

<?php else: ?>

<div class="grid md:grid-cols-2 xl:grid-cols-3 gap-8">

<?php foreach ($orders as $order_id => $items):

$status = $items[0]['status'] ?? 'Pending';

$statusColor = match($status) {
    'Paid' => 'bg-green-600',
    'Shipped' => 'bg-blue-600',
    'Delivered' => 'bg-green-500',
    'Cancelled' => 'bg-red-600',
    default => 'bg-yellow-500'
};
?>

<div class="bg-[#111] border border-red-900 rounded-2xl p-6 hover:scale-[1.02] transition">

  <div class="flex justify-between mb-4">
    <h3 class="text-red-400 font-bold">Order #<?= $order_id ?></h3>
    <span class="text-xs px-3 py-1 rounded-full <?= $statusColor ?>">
      <?= htmlspecialchars($status) ?>
    </span>
  </div>

  <?php foreach ($items as $item): ?>
  <div class="flex gap-4 items-center mb-3 border-b border-gray-800 pb-2">

    <img src="assets/images/<?php echo htmlspecialchars($item['image']); ?>"
     class="w-14 h-14 rounded-lg object-cover">
         

    <div class="flex-1">
      <p><?= htmlspecialchars($item['name']) ?></p>
      <p class="text-sm text-gray-400">Qty: <?= $item['quantity'] ?></p>
    </div>

    <p class="text-red-400 font-semibold">
      Rs <?= number_format($item['price'],2) ?>
    </p>

  </div>
  <?php endforeach; ?>

</div>

<?php endforeach; ?>

</div>

<?php endif; ?>

</main>

</body>
</html>