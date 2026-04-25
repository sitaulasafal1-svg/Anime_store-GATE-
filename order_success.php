<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit();
}

/* GET ORDER ID FROM URL */
if (!isset($_GET['order_id'])) {
    die("Invalid access");
}

$order_id = $_GET['order_id'];

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT * FROM orders WHERE transaction_uuid = ? AND user_id = ?");
$stmt->bind_param("si", $order_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

$order = $result->fetch_assoc();

if (!$order) {
    die("Order not found!");
}

/* prevent refresh duplicate */
unset($_SESSION['order_done']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Order Success - Anime Store</title>
<script src="https://cdn.tailwindcss.com"></script>

<style>
:root {
  --cream: #E3DEC0;
  --dark: #1A1A25;
  --teal: #7FA19E;
  --peach: #EACAA5;
  --nearblack: #11101D;
}
body {
  background: url('assets/images/wallpaper.jpg') no-repeat center center fixed;
  background-size: cover;
  color: var(--cream);
}
.overlay-dark { background-color: rgba(17,16,29,0.9); backdrop-filter: blur(6px); }
.overlay-light { background-color: rgba(42,42,53,0.85); backdrop-filter: blur(6px); }
.btn {
  background: var(--peach);
  color: var(--nearblack);
  font-weight: 600;
  padding: 10px 20px;
  border-radius: 8px;
  transition: 0.3s;
}
.btn:hover {
  background: var(--teal);
}
</style>
</head>

<body>

<!-- NAVBAR -->
<header class="overlay-dark py-4 px-8 flex justify-between items-center shadow-md">
  <h1 class="text-2xl font-bold text-[color:var(--peach)]">Anime Store</h1>

  <nav class="flex gap-6 text-sm">
    <a href="newstation.php" class="hover:text-[color:var(--peach)]">NewStation</a>
    <a href="customer_home.php" class="hover:text-[color:var(--peach)]">Home</a>
    <a href="shop.php" class="hover:text-[color:var(--peach)]">Shop</a>
    <a href="customer_orders.php" class="hover:text-[color:var(--peach)]">Orders</a>
    <a href="customer_profile.php" class="hover:text-[color:var(--peach)]">Profile</a>
  </nav>

  <a href="logout.php" class="btn">Logout</a>
</header>

<!-- SUCCESS CARD -->
<section class="overlay-light max-w-2xl mx-auto py-16 px-8 mt-16 rounded-3xl shadow-lg text-center">

  <div class="text-5xl mb-4">🎉</div>

  <h2 class="text-4xl font-bold text-[color:var(--peach)] mb-4">
  Order Successful!
</h2>

<p class="text-sm text-gray-300 mb-4">
  Order ID: <strong><?= htmlspecialchars($order_id) ?></strong>
</p>

  <p class="text-[color:var(--cream)]/80 mb-6">
    Your payment was completed successfully.<br>
    Your anime items are on the way 🚚
  </p>

  <div class="flex justify-center gap-4">

    <a href="customer_orders.php" class="btn">
      View Orders
    </a>

    <a href="shop.php" class="btn">
      Continue Shopping
    </a>

  </div>

</section>

<!-- FOOTER -->
<footer class="overlay-light py-6 text-center text-sm text-[color:var(--cream)] mt-10">
  © <?= date('Y'); ?> Anime Store — All Rights Reserved
</footer>

</body>
</html>