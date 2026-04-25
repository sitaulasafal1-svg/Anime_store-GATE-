<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$product_id = intval($_GET['id'] ?? 0);

if ($product_id <= 0) {
    die("Invalid product");
}

// Check if already in cart
$stmt = $conn->prepare("SELECT id, quantity FROM cart WHERE user_id=? AND product_id=?");
$stmt->bind_param("ii", $user_id, $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Update quantity
    $row = $result->fetch_assoc();
    $new_qty = $row['quantity'] + 1;

    $update = $conn->prepare("UPDATE cart SET quantity=? WHERE id=?");
    $update->bind_param("ii", $new_qty, $row['id']);
    $update->execute();

} else {
    // Insert new item
    $insert = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, 1)");
    $insert->bind_param("ii", $user_id, $product_id);
    $insert->execute();
}

// redirect back
header("Location: shop.php");
exit();