<?php
session_start();
include("db_connect.php");   // make sure this creates $conn

$message = "";

if(isset($_POST['login'])) {

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $sql = "SELECT * FROM users WHERE name = '$username'";
    $result = $conn->query($sql);

    if($result && $result->num_rows > 0) {

        $user = $result->fetch_assoc();

        // If you used password_hash() while signup
        if(password_verify($password, $user['password'])) {

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['name'];
            $_SESSION['role'] = $user['role'];
            header("Location: index.php");
            exit();

        } else {
            $message = "Invalid Password!";
        }

    } else {
        $message = "Username Not Found!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Login</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial;
            background: #f4f4f4;
        }
        .login-box {
            width: 350px;
            margin: 100px auto;
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 5px 10px rgba(0,0,0,0.1);
        }
        .login-box h2 {
            text-align: center;
        }
        .login-box input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
        }
        .login-box input[type="submit"] {
            background: green;
            color: white;
            border: none;
            cursor: pointer;
        }
        .login-box input[type="submit"]:hover {
            background: darkgreen;
        }
        .error {
            color: red;
            text-align: center;
        }
        .links {
            text-align: center;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<div class="login-box">
    <h2>User Login</h2>

    <?php if($message != ""): ?>
        <p class="error"><?php echo $message; ?></p>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="username" placeholder="Enter Username" required>
        <input type="password" name="password" placeholder="Enter Password" required>
        <input type="submit" name="login" value="Login">
    </form>

    <div class="links">
        <p>Don't have an account? <a href="signup.php">Signup</a></p>
        <p><a href="index.php">⬅ Back to Home</a></p>
    </div>
</div>

</body>
</html>
