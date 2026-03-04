<?php
session_start();
include("db_connect.php");

if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header("Location: index.php");
    exit();
}

$order_id = $_POST['order_id'];
$status = $_POST['status'];

$conn->query("UPDATE orders SET status='$status' WHERE id='$order_id'");

header("Location: admin_dashboard.php");
exit();
?>
