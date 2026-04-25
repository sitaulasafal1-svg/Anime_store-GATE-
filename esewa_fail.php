<?php
session_start();

/* OPTIONAL: clear temporary payment data */
unset($_SESSION['payment_success']);
unset($_SESSION['sandbox_order']);
// DO NOT clear cart (user may retry payment)
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Payment Failed</title>

<style>
body{
  margin:0;
  font-family:sans-serif;
  background:#0b0b0f;
  color:white;
  display:flex;
  justify-content:center;
  align-items:center;
  height:100vh;
}

.container{
  background:#1a1a25;
  padding:40px;
  border-radius:12px;
  text-align:center;
  width:350px;
  box-shadow:0 10px 40px rgba(0,0,0,0.5);
}

.icon{
  font-size:50px;
  color:#ef4444;
  margin-bottom:10px;
}

h1{
  color:#ef4444;
  margin-bottom:10px;
}

p{
  color:#aaa;
  font-size:14px;
}

.btn{
  margin-top:20px;
  display:block;
  padding:12px;
  background:#ef4444;
  color:white;
  text-decoration:none;
  border-radius:8px;
  font-weight:bold;
}

.btn:hover{
  background:#dc2626;
}
</style>
</head>

<body>

<div class="container">

  <div class="icon">❌</div>

  <h1>Payment Failed</h1>

  <p>Your transaction could not be completed.</p>
  <p>Please try again.</p>

  <a href="shop.php" class="btn">Back to Store</a>

</div>

</body>
</html>