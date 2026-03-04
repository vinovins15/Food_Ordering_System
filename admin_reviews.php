<?php
session_start();
include("db_connect.php");

$result = $conn->query("
    SELECT r.*, u.name, m.name AS item_name
    FROM reviews r
    JOIN users u ON r.user_id = u.id
    JOIN menu_items m ON r.menu_item_id = m.id
    ORDER BY r.created_at DESC
");

if(isset($_GET['delete'])){
    $id = $_GET['delete'];
    $conn->query("DELETE FROM reviews WHERE id='$id'");
    header("Location: admin_reviews.php");
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Manage Reviews</title>
<style>
body { font-family: Arial; padding:30px; }
table { width:100%; border-collapse:collapse; }
th, td { padding:10px; border:1px solid #ddd; }
th { background:black; color:white; }
a { color:red; }
</style>
</head>
<body>

<h2>All Reviews</h2>

<table>
<tr>
    <th>User</th>
    <th>Item</th>
    <th>Rating</th>
    <th>Review</th>
    <th>Action</th>
</tr>

<?php while($row = $result->fetch_assoc()): ?>
<tr>
    <td><?php echo $row['name']; ?></td>
    <td><?php echo $row['item_name']; ?></td>
    <td><?php echo str_repeat("⭐", $row['rating']); ?></td>
    <td><?php echo $row['review']; ?></td>
    <td>
        <a href="?delete=<?php echo $row['id']; ?>">Delete</a>
    </td>
</tr>
<?php endwhile; ?>

</table>

</body>
</html>
