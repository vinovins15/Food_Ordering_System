<?php
session_start();
include("db_connect.php");   // make sure this file creates $conn
?>
<?php if(isset($_SESSION['role']) && $_SESSION['role']=='admin'): ?>
    | <a href="admin_dashboard.php">Admin Panel</a>
<?php endif; ?>

<!DOCTYPE html>
<html>
<head>
    <title>Food Ordering System</title>
    <link rel="stylesheet" href="style.css">
    <style>
       body {
    background: #f4f6f9;
    font-family: Arial, sans-serif;
}
        .top-bar {
            text-align: right;
            padding: 15px;
        }
        .menu-item {
            border: 1px solid #ccc;
            padding: 15px;
            margin: 15px;
            width: 300px;
            display: inline-block;
            vertical-align: top;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .menu-item img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 6px;
        }
        .menu-item input[type="number"] {
            width: 60px;
            padding: 5px;
        }
        .menu-item input[type="submit"] {
            padding: 8px 12px;
            background: green;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .menu-item input[type="submit"]:hover {
            background: darkgreen;
        }
    </style>
</head>
<body>

<div class="top-bar">
    <?php if(isset($_SESSION['username'])): ?>
        Welcome, <strong><?php echo $_SESSION['username']; ?></strong> 👋 |
        <a href="cart.php">🛒 Cart</a> |
        <a href="order_history.php">📦 My Orders</a> |
        <a href="logout.php">🚪 Logout</a>
    <?php else: ?>
        <a href="login.php">Login</a> |
        <a href="signup.php">Signup</a>
    <?php endif; ?>
</div>

<hr>

<h2 style="text-align:center;">Food Menu</h2>

<?php
$sql = "SELECT * FROM menu_items";
$result = $conn->query($sql);

if($result && $result->num_rows > 0):
    while($row = $result->fetch_assoc()):
?>

<div class="menu-item">

    <?php if(!empty($row['image'])): ?>
        <img src="<?php echo $row['image']; ?>">
    <?php endif; ?>

    <h3><?php echo $row['name']; ?></h3>

    <?php if(isset($row['description'])): ?>
        <p><?php echo $row['description']; ?></p>
    <?php endif; ?>

    <p><strong>₹<?php echo $row['price']; ?></strong></p>

    

<?php
$item_id = $row['id'];

$rating_query = $conn->query("
    SELECT AVG(rating) AS avg_rating, COUNT(id) AS total_reviews 
    FROM reviews 
    WHERE menu_item_id='$item_id'
");

$rating_data = $rating_query->fetch_assoc();
$total_reviews = $rating_data['total_reviews'];

if($total_reviews > 0){
    $avg_rating = round($rating_data['avg_rating']);

    echo "<p>";
    for($i=1; $i<=5; $i++){
        if($i <= $avg_rating){
            echo "⭐";
        } else {
            echo "☆";
        }
    }
    echo " ($total_reviews)";
    echo "</p>";
} else {
    echo "<p>No reviews yet</p>";
}
?>








    <?php if(isset($_SESSION['username'])): ?>
        <form method="POST" action="cart.php">
    <input type="hidden" name="item_id" value="<?php echo $row['id']; ?>">
    Quantity: <input type="number" name="quantity" value="1" min="1">
    <input type="submit" name="add_to_cart" value="Add to Cart">
</form>
    <?php else: ?>
        <p><a href="login.php">Login to Order</a></p>
    <?php endif; ?>

</div>

<?php
    endwhile;
else:
    echo "<p style='text-align:center;'>No food items available.</p>";
endif;
?>

</body>
</html>
