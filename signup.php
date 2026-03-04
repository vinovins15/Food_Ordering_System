<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include("db_connect.php");

$message = "";

if(isset($_POST['signup'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check if email already exists
    $check = $conn->query("SELECT * FROM users WHERE email='$email'");

    if($check->num_rows > 0) {
        $message = "Email already registered!";
    } else {
        $sql = "INSERT INTO users (name, email, password, phone) VALUES ('$name', '$email', '$password', '$phone')";
        if($conn->query($sql) === TRUE) {
            $_SESSION['user_id'] = $conn->insert_id;
            $_SESSION['username'] = $name;
            header("Location: index.php");
            exit();
        } else {
            $message = "Error: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Signup</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .signup-box {
            background: #fff;
            padding: 30px 40px;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            width: 320px;
            text-align: center;
        }
        .signup-box h2 {
            margin-bottom: 25px;
            color: #333;
        }
        .signup-box input[type="text"],
        .signup-box input[type="email"],
        .signup-box input[type="password"] {
            width: 100%;
            padding: 12px 15px;
            margin: 8px 0 20px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 14px;
        }
        .signup-box input[type="submit"] {
            background-color: #007b00;
            color: white;
            padding: 12px;
            width: 100%;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
        }
        .signup-box input[type="submit"]:hover {
            background-color: #005e00;
        }
        .signup-box p {
            margin-top: 20px;
            font-size: 14px;
            color: #555;
        }
        .signup-box a {
            color: #007bff;
            text-decoration: none;
        }
        .signup-box a:hover {
            text-decoration: underline;
        }
        .message {
            color: red;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

<div class="signup-box">
    <h2>User Signup</h2>
    <?php if($message != "") { echo "<div class='message'>$message</div>"; } ?>
    <form method="POST" action="">
        <input type="text" name="name" placeholder="Enter Full Name" required>
        <input type="email" name="email" placeholder="Enter Email" required>
        <input type="text" name="phone" placeholder="Enter Phone (optional)">
        <input type="password" name="password" placeholder="Enter Password" required>
        <input type="submit" name="signup" value="Sign Up">
    </form>
    <p>Already have an account? <a href="login.php">Login</a></p>
    <p><a href="index.php">← Back to Home</a></p>
</div>

</body>
</html>
