<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['temp_cart'])) {
    header("Location: shop.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<title>eSewa Payment</title>
<script src="https://cdn.tailwindcss.com"></script>

<style>
body {
  background: url('assets/images/bg (3).jpg') no-repeat center center fixed;
  background-size: cover;
  font-family: sans-serif;
}

/* dark overlay */
.overlay {
  background: rgba(0,0,0,0.75);
  backdrop-filter: blur(8px);
}

/* glowing card */
.card {
  background: rgba(26, 26, 37, 0.9);
  box-shadow: 0 0 30px rgba(34,197,94,0.25);
  border: 1px solid rgba(34,197,94,0.2);
}

/* button glow */
.btn {
  background: #22c55e;
  transition: 0.3s;
  font-weight: bold;
}

.btn:hover {
  background: #16a34a;
  box-shadow: 0 0 15px #22c55e;
}
</style>

</head>

<body class="text-white flex items-center justify-center h-screen">

<div class="overlay w-full h-full flex items-center justify-center">

  <div class="card p-10 rounded-2xl text-center w-[380px]">

    <!-- HEADER -->
    <div class="mb-6">
      <div class="text-3xl mb-2">💳</div>
      <h1 class="text-2xl font-bold text-green-400">
        eSewa Secure Payment
      </h1>
      <p class="text-gray-400 text-sm">Complete your anime purchase</p>
    </div>

    <!-- AMOUNT -->
    <div class="mb-6">
      <p class="text-gray-400">Total Amount</p>
      <p class="text-3xl font-bold text-white">
        Rs <?= number_format($_SESSION['total_amount'],2) ?>
      </p>
    </div>

    <!-- PAY BUTTON -->
    <form action="checkout.php" method="POST">

      <input type="hidden" name="order_id" value="<?= uniqid('ORD-') ?>">
      <input type="hidden" name="total_amount" value="<?= $_SESSION['total_amount'] ?>">

      <button class="btn w-full py-3 rounded-xl text-black">
        Pay Now
      </button>

    </form>

    <!-- CANCEL -->
    <a href="shop.php" class="block mt-4 text-red-400 hover:text-red-300">
      Cancel Payment
    </a>

  </div>

</div>

</body>
</html>