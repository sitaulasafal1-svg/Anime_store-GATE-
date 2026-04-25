<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $total_amount = 0;

    // ✅ Calculate from session cart
    if (!empty($_SESSION['checkout_cart'])) {

        foreach ($_SESSION['checkout_cart'] as $pid => $qty) {
            $product = $conn->query("SELECT price FROM products WHERE id=$pid")->fetch_assoc();
            if ($product) {
                $total_amount += $product['price'] * $qty;
            }
        }

    } else {
        die("Cart is empty.");
    }

    // ✅ VALIDATE TOTAL
    if ($total_amount <= 0) {
        die("Invalid total amount.");
    }

    $total_amount = (string)(float)$total_amount;

    // ✅ SAVE CUSTOMER DATA
    $_SESSION['checkout_data'] = [
        'cust_name'    => $_POST['cust_name']    ?? '',
        'cust_phone'   => $_POST['cust_phone']   ?? '',
        'cust_email'   => $_POST['cust_email']   ?? '',
        'cust_address' => $_POST['cust_address'] ?? '',
        'cust_city'    => $_POST['cust_city']    ?? '',
        'cust_state'   => $_POST['cust_state']   ?? '',
        'cust_postal'  => $_POST['cust_postal']  ?? ''
    ];

    // ✅ CREATE ORDER
    $transaction_uuid = uniqid("ORD-");

    $conn->query("
    INSERT INTO orders (user_id, total_price, status, transaction_uuid)
    VALUES ($user_id, $total_amount, 'pending', '$transaction_uuid')
    ");

    $_SESSION['transaction_uuid'] = $transaction_uuid;

    $amount = $total_amount;
    $tax_amount = 0;
    $product_service_charge = 0;
    $product_delivery_charge = 0;
    $product_code = "EPAYTEST";

    // Build absolute URL for callbacks
    $base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]/anime_store";
    $success_url = $base_url . "/esewa_success.php";
    $failure_url = $base_url . "/esewa_fail.php";

    $signed_field_names = "total_amount,transaction_uuid,product_code";

    // 4. Generate Signature
    $secret_key = "8gBm/:&EnhH.1/q";
    // The format required: total_amount=100,transaction_uuid=11-201-13,product_code=EPAYTEST
    $message = "total_amount=$total_amount,transaction_uuid=$transaction_uuid,product_code=$product_code";
    $signature = base64_encode(hash_hmac('sha256', $message, $secret_key, true));

    // 5. Render auto-submitting form
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Redirecting to eSewa...</title>
        <style>
            body { font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background: #1a1a25; color: #fff; }
            .loader { border: 4px solid #f3f3f3; border-top: 4px solid #22c55e; border-radius: 50%; width: 40px; height: 40px; animation: spin 1s linear infinite; margin: 0 auto 20px; }
            @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
            .text-center { text-align: center; }
        </style>
    </head>
    <body>
        <div class="text-center">
            <div class="loader"></div>
            <p>Please wait while we redirect you to eSewa...</p>
        </div>
        <form id="esewaForm" action="https://rc-epay.esewa.com.np/api/epay/main/v2/form" method="POST" style="display: none;">
            <input type="text" id="amount" name="amount" value="<?php echo $amount; ?>" required>
            <input type="text" id="tax_amount" name="tax_amount" value="<?php echo $tax_amount; ?>" required>
            <input type="text" id="total_amount" name="total_amount" value="<?php echo $total_amount; ?>" required>
            <input type="text" id="transaction_uuid" name="transaction_uuid" value="<?php echo $transaction_uuid; ?>" required>
            <input type="text" id="product_code" name="product_code" value="<?php echo $product_code; ?>" required>
            <input type="text" id="product_service_charge" name="product_service_charge" value="<?php echo $product_service_charge; ?>" required>
            <input type="text" id="product_delivery_charge" name="product_delivery_charge" value="<?php echo $product_delivery_charge; ?>" required>
            <input type="text" id="success_url" name="success_url" value="<?php echo $success_url; ?>" required>
            <input type="text" id="failure_url" name="failure_url" value="<?php echo $failure_url; ?>" required>
            <input type="text" id="signed_field_names" name="signed_field_names" value="<?php echo $signed_field_names; ?>" required>
            <input type="text" id="signature" name="signature" value="<?php echo $signature; ?>" required>
        </form>
        <script>
            document.getElementById('esewaForm').submit();
        </script>
    </body>
    </html>
    <?php
    exit();
} 

?>
