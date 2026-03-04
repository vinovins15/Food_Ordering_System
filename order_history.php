<?php
session_start();
include("db_connect.php");

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$orders = $conn->query("
    SELECT * FROM orders 
    WHERE user_id='$user_id' 
    ORDER BY order_date DESC
");
?>

<!DOCTYPE html>
<html>
<head>
<title>My Orders</title>

<style>
body {
    font-family: Arial, sans-serif;
    background: #f8f9fa;
    padding: 40px;
}

.container {
    max-width: 900px;
    margin: auto;
}

.order-box {
    background: white;
    padding: 20px;
    margin-bottom: 25px;
    border-radius: 8px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.05);
}

h2 {
    text-align: center;
    margin-bottom: 30px;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
}

th {
    background: #343a40;
    color: white;
    padding: 10px;
}

td {
    padding: 10px;
    text-align: center;
    border-bottom: 1px solid #eee;
}

.status {
    font-weight: bold;
}

.rate-btn {
    background: black;
    color: white;
    padding: 6px 10px;
    text-decoration: none;
    border-radius: 4px;
    font-size: 13px;
}

.rate-btn:hover {
    opacity: 0.8;
}

.back {
    display: block;
    text-align: center;
    margin-top: 20px;
}
</style>

</head>
<body>
    <script>
setInterval(function(){
    location.reload();
}, 10000); // 10 seconds
</script>


<div class="container">

<h2>My Orders</h2>

<?php if($orders->num_rows > 0): ?>

<?php while($order = $orders->fetch_assoc()): ?>

<div class="order-box">

<p><strong>Order ID:</strong> <?php echo $order['id']; ?></p>
<p><strong>Date:</strong> <?php echo $order['order_date']; ?></p>
<?php
$status = $order['status'];

$progress = 0;

if($status == "Pending") $progress = 25;
if($status == "Preparing") $progress = 50;
if($status == "Out for Delivery") $progress = 75;
if($status == "Delivered") $progress = 100;
?>

<div style="margin:15px 0;">
    <div style="background:#eee;height:12px;border-radius:20px;position:relative;">

        <div style="
            width:<?php echo $progress; ?>%;
            height:12px;
            background:green;
            border-radius:20px;
            transition:1s;">
        </div>

        <div class="truck" style="left:<?php echo $progress; ?>%;">
            🚚
        </div>

    </div>

    <p style="margin-top:10px;">
        Status: <strong><?php echo $status; ?></strong>
    </p>
</div>


<style>
.truck-container {
    position: relative;
    margin-top: 10px;
}

.truck {
    position: absolute;
    top: -8px;
    font-size: 20px;
    transition: left 1s ease-in-out;
}
</style>

<table>
<tr>
    <th>Item</th>
    <th>Qty</th>
    <th>Price</th>
    <th>Subtotal</th>
    <th>Review</th>
</tr>

<?php
$order_id = $order['id'];

$items = $conn->query("
    SELECT oi.*, m.name 
    FROM order_items oi
    JOIN menu_items m ON oi.menu_item_id = m.id
    WHERE oi.order_id='$order_id'
");
?>

<?php while($item = $items->fetch_assoc()): ?>
<tr>
    <td><?php echo $item['name']; ?></td>
    <td><?php echo $item['quantity']; ?></td>
    <td>₹<?php echo $item['price']; ?></td>
    <td>₹<?php echo $item['price'] * $item['quantity']; ?></td>
    <td>
        <a class="rate-btn" href="add_review.php?item_id=<?php echo $item['menu_item_id']; ?>">
            ⭐ Rate
        </a>
    </td>
</tr>
<?php endwhile; ?>

</table>

<p><strong>Total:</strong> ₹<?php echo $order['total_amount']; ?></p>

</div>

<?php endwhile; ?>

<?php else: ?>
<p style="text-align:center;">No orders found.</p>
<?php endif; ?>

<a href="index.php" class="back">⬅ Back to Menu</a>

</div>

</body>
</html>
