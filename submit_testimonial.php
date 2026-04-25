<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'];
$name = $_SESSION['username'];
$message = trim($_POST['message'] ?? '');

if ($message === '') {
    echo json_encode(['status' => 'error', 'message' => 'Please enter a testimonial.']);
    exit();
}

$stmt = $conn->prepare("INSERT INTO testimonials (user_id, name, message) VALUES (?, ?, ?)");
$stmt->bind_param("iss", $user_id, $name, $message);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Thank you! Your testimonial has been submitted.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Something went wrong.']);
}
?>
