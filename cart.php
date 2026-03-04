<?php
session_start();
include("db_connect.php");

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$userid = $_SESSION['user_id'];

/* ADD TO CART */
if(isset($_POST['add_to_cart'])){
    $item_id = $_POST['item_id'];
    $quantity = $_POST['quantity'];

    // Check if already exists
    $check = $conn->query("SELECT * FROM cart WHERE user_id='$userid' AND item_id='$item_id'");
    
    if($check->num_rows > 0){
        $conn->query("UPDATE cart SET quantity = quantity + $quantity 
                      WHERE user_id='$userid' AND item_id='$item_id'");
    } else {
        $conn->query("INSERT INTO cart (user_id, item_id, quantity) 
                      VALUES ('$userid','$item_id','$quantity')");
    }
}

/* UPDATE QUANTITY */
if(isset($_POST['update'])){
    foreach($_POST['qty'] as $cart_id => $qty){
        $conn->query("UPDATE cart SET quantity='$qty' WHERE id='$cart_id'");
    }
}

/* REMOVE ITEM */
if(isset($_GET['remove'])){
    $cart_id = $_GET['remove'];
    $conn->query("DELETE FROM cart WHERE id='$cart_id'");
}

/* FETCH CART */
$cart = $conn->query("
    SELECT c.*, m.name, m.price 
    FROM cart c
    JOIN menu_items m ON c.item_id = m.id
    WHERE c.user_id='$userid'
");

$total = 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Cart</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
    background: #f4f6f9;
    font-family: Arial, sans-serif;
}
        .cart-container {
            width: 70%;
            margin: 50px auto;
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
        }
        h2 { text-align: center; }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th {
            background: #667eea;
            color: white;
            padding: 10px;
        }
        td {
            padding: 10px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }
        input[type="number"] {
            width: 60px;
            padding: 5px;
            text-align: center;
        }
        .btn {
            background: #28a745;
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        .btn:hover {
            background: #218838;
        }
        .remove {
            color: red;
            text-decoration: none;
        }
        .total {
            text-align: right;
            font-weight: bold;
            margin-top: 15px;
            font-size: 18px;
        }
        .checkout-btn {
            margin-top: 15px;
            display: inline-block;
            background: #007bff;
            color: white;
            padding: 10px 20px;
            border-radius: 6px;
            text-decoration: none;
        }
        .checkout-btn:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>

<div class="cart-container">

<h2>🛒 My Cart</h2>
<a href="index.php" class="back-btn">⬅ Back to Menu</a>
<form method="POST">

<table>
    <tr>
        <th>Item</th>
        <th>Price</th>
        <th>Quantity</th>
        <th>Subtotal</th>
        <th>Action</th>
    </tr>

<?php if($cart->num_rows > 0): ?>
    <?php while($row = $cart->fetch_assoc()): 
        $subtotal = $row['price'] * $row['quantity'];
        $total += $subtotal;
    ?>
    <tr>
        <td><?php echo $row['name']; ?></td>
        <td>₹<?php echo $row['price']; ?></td>
        <td>
            <input type="number" name="qty[<?php echo $row['id']; ?>]" 
                   value="<?php echo $row['quantity']; ?>" min="1">
        </td>
        <td>₹<?php echo $subtotal; ?></td>
        <td>
            <a class="remove" href="cart.php?remove=<?php echo $row['id']; ?>">
                Remove
            </a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

<div class="total">
    Total: ₹<?php echo $total; ?>
</div>

<br>
<button type="submit" name="update" class="btn">Update Quantities</button>

</form>

<br>
<a href="checkout.php" class="checkout-btn">Proceed to Checkout</a>

<?php else: ?>
    <p>Your cart is empty. <a href="index.php">Go back to menu</a></p>
<?php endif; ?>

</div>

</body>
</html>
