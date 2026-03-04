<?php
session_start();
include("db_connect.php");

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$item_id = $_GET['item_id'];

if(isset($_POST['submit'])){

    $rating = $_POST['rating'];
    $review = $conn->real_escape_string($_POST['review']);

    $conn->query("
        INSERT INTO reviews (user_id, menu_item_id, rating, review)
        VALUES ('$user_id', '$item_id', '$rating', '$review')
    ");

    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Add Review</title>
<style>
body { font-family: Arial; text-align:center; padding:50px; }
form { width:300px; margin:auto; }
select, textarea, button {
    width:100%;
    padding:10px;
    margin-top:10px;
}
button {
    background:black;
    color:white;
    border:none;
}
</style>
</head>
<body>

<h2>Rate This Item</h2>

<form method="POST">
    <select name="rating" required>
        <option value="">Select Rating</option>
        <option value="5">⭐⭐⭐⭐⭐ (5)</option>
        <option value="4">⭐⭐⭐⭐ (4)</option>
        <option value="3">⭐⭐⭐ (3)</option>
        <option value="2">⭐⭐ (2)</option>
        <option value="1">⭐ (1)</option>
    </select>

    <textarea name="review" placeholder="Write your review..." required></textarea>

    <button type="submit" name="submit">Submit Review</button>
</form>

</body>
</html>
