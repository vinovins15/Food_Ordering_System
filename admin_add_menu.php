<?php
session_start();
include("db_connect.php");

if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header("Location: index.php");
    exit();
}

$message = "";

if(isset($_POST['add'])){
    $name = $conn->real_escape_string($_POST['name']);
    $desc = $conn->real_escape_string($_POST['description']);
    $price = $_POST['price'];

    // Image upload
    $image_name = "";
    if(isset($_FILES['image']) && $_FILES['image']['name'] != ""){
        $image_name = time() . "_" . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], .$image_name);
    }

    $conn->query("INSERT INTO menu_items (name, description, price, image) VALUES ('$name', '$desc', '$price', '$image_name')");
    header("Location: admin_menu.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Add Menu Item</title>
<style>
body { font-family: Arial; background:#f4f6f9; margin:0; padding:20px; }
form { background:white; padding:20px; width:400px; margin:auto; border-radius:8px; }
input, textarea { width:100%; padding:10px; margin:8px 0; }
button { padding:10px 15px; background:green; color:white; border:none; cursor:pointer; border-radius:4px; }
button:hover { background:darkgreen; }
</style>
</head>
<body>

<h1 style="text-align:center;">➕ Add New Menu Item</h1>
<form method="POST" enctype="multipart/form-data">
<input type="text" name="name" placeholder="Item Name" required>
<textarea name="description" placeholder="Description"></textarea>
<input type="number" step="0.01" name="price" placeholder="Price" required>
<input type="file" name="image">
<button type="submit" name="add">Add Item</button>
</form>
<br>
<a href="admin_menu.php">⬅ Back</a>
</body>
</html>
