<?php
session_start();
include("db_connect.php");

// Only admin allowed
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header("Location: index.php");
    exit();
}

if(isset($_POST['update_status'])){
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];

    $conn->query("UPDATE orders SET status='$status' WHERE id='$order_id'");
}

// Get Stats
$total_orders = $conn->query("SELECT COUNT(*) as count FROM orders")->fetch_assoc()['count'];
$total_users = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
$total_menu = $conn->query("SELECT COUNT(*) as count FROM menu_items")->fetch_assoc()['count'];
$total_revenue = $conn->query("SELECT SUM(total_amount) as total FROM orders WHERE status='Delivered'")->fetch_assoc()['total'];

if(!$total_revenue) $total_revenue = 0;
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin Dashboard</title>
<style>
body { font-family: Arial; background:#f4f6f9; margin:0; }
.container { width:90%; margin:auto; padding:20px; }
.cards { display:flex; gap:20px; margin-bottom:30px; }
.card {
    flex:1;
    background:white;
    padding:20px;
    border-radius:10px;
    box-shadow:0 4px 10px rgba(0,0,0,0.1);
    text-align:center;
}
.card h2 { margin:10px 0; }
h1 { text-align:center; margin-bottom:30px; }
table { width:100%; border-collapse:collapse; background:white; }
th, td { padding:12px; text-align:center; border-bottom:1px solid #ddd; }
th { background:#343a40; color:white; }
select { padding:5px; }
button { padding:6px 10px; background:green; color:white; border:none; cursor:pointer; border-radius:4px; }
button:hover { background:darkgreen; }

a:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 15px rgba(0,0,0,0.3);
    transition: 0.2s;
}
</style>
</head>
<body>

<div class="container">
<h1>🛠 Admin Dashboard</h1>

<div style="display:flex; justify-content:center; gap:20px; flex-wrap:wrap;">

    <a href="admin_menu.php" 
       style="text-decoration:none; background:#28a745; color:white; padding:20px 30px; border-radius:8px; font-size:18px; text-align:center; box-shadow:0 4px 10px rgba(0,0,0,0.2);">
        🍔 Manage Menu Items
    </a>

    <a href="order_history.php" 
       style="text-decoration:none; background:#007bff; color:white; padding:20px 30px; border-radius:8px; font-size:18px; text-align:center; box-shadow:0 4px 10px rgba(0,0,0,0.2);">
        📦 View Orders
    </a>


    <a href="index.php" style="text-decoration:none; background:#007bff; color:white; padding:20px 30px; border-radius:8px; font-size:18px; text-align:center; box-shadow:0 4px 10px rgba(0,0,0,0.2);"
     class="btn btn-back">🏠 Back to Site</a>

    <a href="logout.php" 
       style="text-decoration:none; background:#6c757d; color:white; padding:20px 30px; border-radius:8px; font-size:18px; text-align:center; box-shadow:0 4px 10px rgba(0,0,0,0.2);">
        🚪 Logout
    </a>

</div>

<div class="cards">
    <div class="card">
        <h3>Total Orders</h3>
        <h2><?php echo $total_orders; ?></h2>
    </div>
    <div class="card">
        <h3>Total Revenue</h3>
        <h2>₹<?php echo $total_revenue; ?></h2>
    </div>
    <div class="card">
        <h3>Total Users</h3>
        <h2><?php echo $total_users; ?></h2>
    </div>
    <div class="card">
        <h3>Total Menu Items</h3>
        <h2><?php echo $total_menu; ?></h2>
    </div>
</div>

<h2>📦 All Orders</h2>

<table>
<tr>
<th>Order ID</th>
<th>User ID</th>
<th>Total</th>
<th>Status</th>
<th>Change Status</th>
</tr>

<?php
$orders = $conn->query("SELECT * FROM orders ORDER BY order_date DESC");
while($row = $orders->fetch_assoc()):
?>

<tr>
<td><?php echo $row['id']; ?></td>
<td><?php echo $row['user_id']; ?></td>
<td>₹<?php echo $row['total_amount']; ?></td>
<td><?php echo $row['status']; ?></td>
<td>
<form method="POST">
    <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">

    <select name="status">
        <option value="Pending">Pending</option>
        <option value="Preparing">Preparing</option>
        <option value="Out for Delivery">Out for Delivery</option>
        <option value="Delivered">Delivered</option>
    </select>

    <button type="submit" name="update_status">Update</button>
</form>

</td>
</tr>

<?php endwhile; ?>

</table>

</div>
</body>
</html>
