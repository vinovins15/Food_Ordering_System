<?php
session_start();
include("db_connect.php");

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

// Fetch order
$order_res = $conn->query("SELECT * FROM orders WHERE id='$order_id' AND user_id='".$_SESSION['user_id']."'");
if($order_res->num_rows == 0){
    echo "<p>Order not found!</p>";
    exit();
}
$order = $order_res->fetch_assoc();

// Fetch order items
$items_res = $conn->query("
    SELECT oi.*, m.name 
    FROM order_items oi 
    JOIN menu_items m ON oi.menu_item_id = m.id 
    WHERE oi.order_id='$order_id'
");









?>

<!DOCTYPE html>
<html>
<head>
    <title>Payment Success</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f4f6f8; }
        .bill-box { max-width: 600px; margin: auto; background: #fff; border: 1px solid #ccc; padding: 20px; border-radius: 8px; }
        h2, h3 { text-align: center; color: green; }
        .message { text-align: center; font-size: 18px; color: green; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table, th, td { border: 1px solid #ccc; }
        th, td { padding: 10px; text-align: center; }
        .total { text-align: right; font-weight: bold; }
        button { margin-top: 20px; padding: 10px 20px; font-size: 16px; cursor: pointer; background: #007b00; color: #fff; border: none; border-radius: 5px; }
        button:hover { background: #005e00; }
        a { display: block; text-align: center; margin-top: 20px; text-decoration: none; color: #007bff; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>

<div class="bill-box">

    <!-- ✅ Payment Success Message -->
    <div class="message">🎉 Payment Successful! Thank you for your order.</div>

    <!--<h2>Food Ordering System</h2>-->
    <h3>Order Receipt</h3>
    <p><strong>Order ID:</strong> <?php echo $order['id']; ?></p>
    <p><strong>Date:</strong> <?php echo $order['order_date']; ?></p>
    <p><strong>Status:</strong> <?php echo $order['status']; ?></p>

    <table>
        <tr>
            <th>Item</th>
            <th>Qty</th>
            <th>Price</th>
            <th>Subtotal</th>
        </tr>
        <?php 
        $total = 0;
        while($item = $items_res->fetch_assoc()):
            $subtotal = $item['price'] * $item['quantity'];
            $total += $subtotal;
        ?>
        <tr>
            <td><?php echo $item['name']; ?></td>
            <td><?php echo $item['quantity']; ?></td>
            <td>₹<?php echo $item['price']; ?></td>
            <td>₹<?php echo $subtotal; ?></td>
        </tr>
        <?php endwhile; ?>
    </table>

    <p class="total">Total: ₹<?php echo $total; ?></p>

    <!-- ✅ Print Bill Button -->
    <button onclick="window.print()">🖨️ Print Bill</button>

</div>

<a href="index.php">⬅ Back to Menu</a>

</body>
</html>
