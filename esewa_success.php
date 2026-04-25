<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if (!isset($_GET['data'])) {
    die("Invalid request. No data received.");
}

$data_base64 = $_GET['data'];
$data_json = base64_decode($data_base64);
$response_data = json_decode($data_json, true);

if (!$response_data) {
    die("Invalid response format.");
}

// Extract variables
$transaction_code = $response_data['transaction_code'] ?? '';
$status = $response_data['status'] ?? '';
$total_amount = $response_data['total_amount'] ?? 0;
$transaction_uuid = $response_data['transaction_uuid'] ?? '';
$product_code = $response_data['product_code'] ?? '';
$signed_field_names = $response_data['signed_field_names'] ?? '';
$received_signature = $response_data['signature'] ?? '';

// Verify Signature
$secret_key = "8gBm/:&EnhH.1/q";

$signed_fields_array = explode(',', $signed_field_names);
$message_parts = [];
foreach ($signed_fields_array as $field) {
    $message_parts[] = $field . "=" . ($response_data[$field] ?? '');
}
$message = implode(',', $message_parts);
$generated_signature = base64_encode(hash_hmac('sha256', $message, $secret_key, true));

if ($generated_signature !== $received_signature) {
    die("Signature verification failed. Potential tampering detected.");
}

if ($status === 'COMPLETE') {

    // Check if order exists
    $check_stmt = $conn->prepare("SELECT id FROM orders WHERE transaction_uuid = ?");
    $check_stmt->bind_param("s", $transaction_uuid);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    // If order exists → update & insert items
    if ($check_result->num_rows > 0) {

        // ✅ Update order status
        $update = $conn->prepare("
            UPDATE orders 
            SET status='paid', payment_status='Paid', ref_id=? 
            WHERE transaction_uuid=?
        ");
        $update->bind_param("ss", $transaction_code, $transaction_uuid);
        $update->execute();

        // ✅ Get order ID
        $orderRow = $check_result->fetch_assoc();
        $new_order_id = $orderRow['id'];

        // ✅ Insert order items (ONLY if not already inserted)
        if (!empty($_SESSION['checkout_cart'])) {

            $insert_item = $conn->prepare("
                INSERT INTO order_items (order_id, product_id, price, quantity) 
                VALUES (?, ?, ?, ?)
            ");

            foreach ($_SESSION['checkout_cart'] as $pid => $qty) {

                $product = $conn->query("SELECT price FROM products WHERE id=$pid")->fetch_assoc();

                if ($product) {
                    $insert_item->bind_param("iidi", $new_order_id, $pid, $product['price'], $qty);
                    $insert_item->execute();
                }
            }
        }
    }

    // ✅ ALWAYS CLEAR CART (FIXED 🔥)
    unset($_SESSION['cart']);
    unset($_SESSION['checkout_cart']);
    unset($_SESSION['checkout_total']);

    // cleanup
    unset($_SESSION['checkout_data']);
    unset($_SESSION['transaction_uuid']);

    // redirect
    header("Location: order_success.php?order_id=" . urlencode($transaction_uuid));
    exit();
}
?>