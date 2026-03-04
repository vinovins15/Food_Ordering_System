<?php
session_start();
include("db_connect.php");

// Only admin access
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header("Location: index.php");
    exit();
}

// Handle Delete
if(isset($_GET['delete'])){
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM menu_items WHERE id=$id");
    header("Location: admin_menu.php");
    exit();
}

// Fetch all menu items
$menu_items = $conn->query("SELECT * FROM menu_items ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Menu Management</title>
    <style>
        body { font-family: Arial; background:#f4f6f9; margin:0; padding:20px; }
        table { width:100%; border-collapse:collapse; background:white; }
        th, td { padding:10px; border:1px solid #ddd; text-align:center; }
        th { background:#343a40; color:white; }
        a.button { text-decoration:none; padding:6px 10px; background:green; color:white; border-radius:4px; }
        a.button:hover { background:darkgreen; }
        h1 { text-align:center; margin-bottom:20px; }
    </style>
</head>
<body>
<h1>🍔 Menu Management</h1>
<a href="admin_add_menu.php" class="button">➕ Add New Item</a>
<br><br>
<table>
<tr>
<th>ID</th>
<th>Name</th>
<th>Description</th>
<th>Price</th>
<th>Image</th>
<th>Actions</th>
</tr>

<?php while($row = $menu_items->fetch_assoc()): ?>
<tr>
<td><?php echo $row['id']; ?></td>
<td><?php echo $row['name']; ?></td>
<td><?php echo $row['description']; ?></td>
<td>₹<?php echo $row['price']; ?></td>
<td>
<?php if($row['image'] != ""): ?>
<img src="<?php echo $row['image']; ?>" width="60">
<?php else: ?>
N/A
<?php endif; ?>
</td>
<td>
<a href="admin_edit_menu.php?id=<?php echo $row['id']; ?>" class="button">✏️ Edit</a>
<a href="admin_menu.php?delete=<?php echo $row['id']; ?>" class="button" style="background:red;">🗑 Delete</a>
</td>
</tr>
<?php endwhile; ?>

</table>
<br>
<a href="admin_dashboard.php">⬅ Back to Dashboard</a>
</body>
</html>
