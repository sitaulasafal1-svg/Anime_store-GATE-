<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

/* CALCULATE TOTAL FROM CART (SAFE) */
$total_amount = 0;

if (!empty($_SESSION['cart'])) {

    foreach ($_SESSION['cart'] as $pid => $qty) {
        $product = $conn->query("SELECT price FROM products WHERE id=$pid")->fetch_assoc();
        $total_amount += $product['price'] * $qty;
    }

} else {
    echo "<script>alert('Cart is empty'); window.location='shop.php';</script>";
    exit();
}

/* ❌ STOP if cart empty */
if ($total_amount <= 0) {
    echo "<script>alert('Cart is empty'); window.location='shop.php';</script>";
    exit();
}

/* STORE IN SESSION (for payment step) */
$_SESSION['checkout_total'] = $total_amount;
$_SESSION['checkout_cart'] = $_SESSION['cart'];

/* GENERATE ORDER ID */
$order_id = uniqid("ORD-");

$process_url = "esewa_process.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Checkout - eSewa</title>
<script src="https://cdn.tailwindcss.com"></script>

<style>
body {
  background: url('assets/images/bg (2).jpg') no-repeat center center fixed;
  background-size: cover;
}

.overlay {
  background: rgba(10,10,15,0.85);
  backdrop-filter: blur(10px);
}

.input {
  background:#111;
  border:1px solid #444;
  padding:10px;
  border-radius:8px;
  width:100%;
  color:white;
}

.input:focus {
  border-color:#22c55e;
  outline:none;
}

.btn {
  background:#22c55e;
  padding:12px;
  border-radius:8px;
  font-weight:bold;
  width:100%;
}

.btn:hover {
  background:#16a34a;
}
</style>
</head>

<body class="text-white min-h-screen flex items-center justify-center">

<div class="overlay w-[1000px] rounded-2xl shadow-2xl flex overflow-hidden">

  <!-- LEFT SIDE (ANIME IMAGE) -->
  <div class="w-1/2 relative hidden md:block">
    <img src="assets/images/sp.jpg"
         class="h-full w-full object-cover">

    <div class="absolute bottom-0 left-0 p-6">
      <h2 class="text-2xl font-bold text-green-400">GATE</h2>
      <p class="text-gray-300 text-sm">Your world of anime merch</p>
    </div>
  </div>

  <!-- RIGHT SIDE (FORM) -->
  <div class="w-full md:w-1/2 p-8">

    <h2 class="text-2xl font-bold mb-6 text-green-400">
      Checkout - eSewa
    </h2>

    <form method="POST" action="<?php echo $process_url; ?>" class="space-y-3">

      <!-- HIDDEN -->
      <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
      <input type="hidden" name="total_amount" value="<?php echo $total_amount; ?>">

      <input type="text" name="cust_name" placeholder="Full Name" required class="input">
      <input type="text" name="cust_phone" placeholder="Phone Number" required class="input">
      <input type="email" name="cust_email" placeholder="Email" required class="input">
      <input type="text" name="cust_address" placeholder="Address" required class="input">
      <input type="text" name="cust_city" placeholder="City" required class="input">

      <select name="cust_state" required class="input">
  <option value="">Select Province</option>

  <optgroup label="Nepal Provinces">
    <option value="Koshi">Koshi Province</option>
    <option value="Madhesh">Madhesh Province</option>
    <option value="Bagmati">Bagmati Province</option>
    <option value="Gandaki">Gandaki Province</option>
    <option value="Lumbini">Lumbini Province</option>
    <option value="Karnali">Karnali Province</option>
    <option value="Sudurpashchim">Sudurpashchim Province</option>
  </optgroup>
</select>

      <input type="text" name="cust_postal" placeholder="Postal Code" required class="input">

      <button class="btn mt-4">
        Pay Now (eSewa)
      </button>

    </form>

  </div>

</div>

</body>
</html>
