<?php
session_start();
include 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // just pass control to final handler
    header("Location: sandbox_payment_complete.php");
    exit;

} else {
    header("Location: checkout.php?gateway=khalti");
    exit;
}