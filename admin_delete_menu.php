<?php
session_start();
include("db_connect.php");
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header("Location: index.php"); exit();
}

$id = intval($_GET['id']);

// Optional: delete image file
$res = $conn->query("SELECT image FROM menu_items WHERE id='$id'");
$item = $res->fetch_assoc();
if(!empty($item['image']) && file_exists("assets/images/".$item['image'])){
    unlink("assets/images/".$item['image']);
}

$conn->query("DELETE FROM menu_items WHERE id='$id'");
header("Location: admin_menu.php");
exit();
