<?php
session_start();
include("db_connect.php");

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$userid = $_SESSION['user_id'];

$cart = $conn->query("
    SELECT c.*, m.name, m.price 
    FROM cart c 
    JOIN menu_items m ON c.item_id = m.id 
    WHERE c.user_id='$userid'
");

$total = 0;
$items = [];

while($row = $cart->fetch_assoc()){
    $subtotal = $row['price'] * $row['quantity'];
    $total += $subtotal;
    $items[] = $row;
}

if(isset($_POST['pay'])){

    $payment_method = strtolower($_POST['payment_method']);

    if($payment_method == "card" || $payment_method == "upi"){
        $status = "Paid";
    } else {
        $status = "Pending";
    }

    $conn->query("
        INSERT INTO orders (user_id, total_amount, status) 
        VALUES ('$userid', '$total', '$status')
    ");

    $order_id = $conn->insert_id;

    foreach($items as $item){
        $item_id = $item['item_id'];
        $qty = $item['quantity'];
        $price = $item['price'];

        $conn->query("
            INSERT INTO order_items (order_id, menu_item_id, quantity, price)
            VALUES ('$order_id', '$item_id', '$qty', '$price')
        ");
    }

    $conn->query("DELETE FROM cart WHERE user_id='$userid'");

    header("Location: payment_success.php?order_id=".$order_id);
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Checkout</title>
<style>
body {
    margin: 0;
    font-family: Arial, sans-serif;
    background: #f8f9fa;
}

.checkout-container {
    width: 60%;
    margin: 50px auto;
    background: white;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
}

h2, h3 {
    text-align: center;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
}

th {
    background: #343a40;
    color: white;
    padding: 10px;
}

td {
    padding: 10px;
    text-align: center;
    border-bottom: 1px solid #ddd;
}

select, input[type=text] {
    width: 100%;
    padding: 10px;
    margin-top: 10px;
    border-radius: 6px;
    border: 1px solid #ccc;
}

.pay-btn {
    margin-top: 20px;
    width: 100%;
    padding: 12px;
    background: #28a745;
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 16px;
    cursor: pointer;
}

.pay-btn:hover {
    background: #218838;
}

.back-link {
    margin-top: 20px;
    display: inline-block;
}
</style>
</head>

<body>

<div class="checkout-container">

<h2>Checkout</h2>

<?php if(empty($items)): ?>
    <p>Your cart is empty. <a href="index.php">Go back to menu</a></p>
<?php else: ?>

<table>
<tr>
    <th>Item</th>
    <th>Price</th>
    <th>Qty</th>
    <th>Subtotal</th>
</tr>

<?php foreach($items as $item): ?>
<tr>
    <td><?php echo $item['name']; ?></td>
    <td>₹<?php echo $item['price']; ?></td>
    <td><?php echo $item['quantity']; ?></td>
    <td>₹<?php echo $item['price'] * $item['quantity']; ?></td>
</tr>
<?php endforeach; ?>

</table>

<h3>Total: ₹<?php echo $total; ?></h3>

<h3>Select Payment Method</h3>

<form method="POST">

<select name="payment_method" id="payment_method" onchange="toggleFields()" required>
    <option value="card">Card</option>
    <option value="upi">UPI</option>
    <option value="cod">Cash on Delivery</option>
</select>

<div id="card_fields">
    <input type="text" placeholder="Card Number" maxlength="16">
    <input type="text" placeholder="Expiry (MM/YY)">
    <input type="text" placeholder="CVV" maxlength="3">
</div>

<div id="upi_field" style="display:none;">
    <input type="text" placeholder="Enter UPI ID (example@upi)">
</div>

<input type="submit" name="pay" value="Confirm & Pay" class="pay-btn">

</form>

<?php endif; ?>

<a href="cart.php" class="back-link">⬅ Back to Cart</a>

</div>

<script>
function toggleFields(){
    var method = document.getElementById("payment_method").value;
    var card = document.getElementById("card_fields");
    var upi = document.getElementById("upi_field");

    if(method === "card"){
        card.style.display = "block";
        upi.style.display = "none";
    }
    else if(method === "upi"){
        card.style.display = "none";
        upi.style.display = "block";
    }
    else{
        card.style.display = "none";
        upi.style.display = "none";
    }
}

window.onload = toggleFields;
</script>

</body>
</html>
