<?php
session_start();
include("db_connect.php");

if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header("Location: index.php");
    exit();
}

$id = intval($_GET['id']);
$item = $conn->query("SELECT * FROM menu_items WHERE id=$id")->fetch_assoc();

if(isset($_POST['update'])){
    $name = $conn->real_escape_string($_POST['name']);
    $desc = $conn->real_escape_string($_POST['description']);
    $price = $_POST['price'];

    $image_name = $item['image'];
    if(isset($_FILES['image']) && $_FILES['image']['name'] != ""){
        $image_name = time() . "_" . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], "assets/images/".$image_name);
    }

    $conn->query("UPDATE menu_items SET name='$name', description='$desc', price='$price', image='$image_name' WHERE id=$id");
    header("Location: admin_menu.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Edit Menu Item</title>
<style>
body { font-family: Arial; background:#f4f6f9; margin:0; padding:20px; }
form { background:white; padding:20px; width:400px; margin:auto; border-radius:8px; }
input, textarea { width:100%; padding:10px; margin:8px 0; }
button { padding:10px 15px; background:green; color:white; border:none; cursor:pointer; border-radius:4px; }
button:hover { background:darkgreen; }
img { margin-top:10px; }
</style>
</head>
<body>

<h1 style="text-align:center;">✏️ Edit Menu Item</h1>
<form method="POST" enctype="multipart/form-data">
<input type="text" name="name" value="<?php echo $item['name']; ?>" required>
<textarea name="description"><?php echo $item['description']; ?></textarea>
<input type="number" step="0.01" name="price" value="<?php echo $item['price']; ?>" required>
<?php if($item['image'] != ""): ?>
<img src="<?php echo $item['image']; ?>" width="80">
<?php endif; ?>
<input type="file" name="image">
<button type="submit" name="update">Update Item</button>
</form>
<br>
<a href="admin_menu.php">⬅ Back</a>
</body>
</html>
