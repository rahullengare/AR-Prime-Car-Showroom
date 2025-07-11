<?php
session_start();
include 'connection.php';

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Query to fetch admin credentials
    $query = "SELECT * FROM admin_login WHERE username = '$username'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $admin = mysqli_fetch_assoc($result);

        // Verify the password
        if (password_verify($password, $admin['password'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $username;
            header('Location: index.php'); // Redirect to the admin dashboard
            exit();
        } else {
            $_SESSION['error'] = "Invalid Password!";
        }
    } else {
        $_SESSION['error'] = "Invalid Username!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../images/icon.png">
    <title>Admin Login</title>
    <link rel="stylesheet" href="../CSS/admin.css"> 
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background: url('admin-back.png') no-repeat center 80px;
            background-size: 100% auto;
            background-attachment: fixed;
            font-family: math;
        }
        /* Common Box Styling */
        .msgBox {
            background-color: #dcdcdc;
            border-radius: 160px;
            box-shadow: 0 8px 9px rgba(1, 1, 1, 0.1);
            text-align: center;
            max-width: 650px;
            width: 100%;
            margin-top: 19%;
            padding: 46px;
        }
        /* Headings */
        .msgBox h2 {
            font-size: 30px;
            margin: -22px 0 0 0;
            color: #4CAF50;
        }
        /* Paragraph Text (Error Message) */
        .msgBox .error {
            color: red;
            text-align: center;
            margin-top: 10px;
            font-size: 12px;
        }
        /* Form Inputs */
        .msgBox input[type="text"],
        .msgBox input[type="password"] {
            width: 85%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        /* Submit Button */
        .msgBox input[type="submit"] {
            width: 95%;
            padding: 10px;
            background-color: #2af158;
            color: white;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-size: 20px;
        }
        .msgBox input[type="submit"]:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="msgBox">
        <h2>Admin Login</h2>
        <form action="login.php" method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="submit" value="Login">
        </form>
        <?php if (isset($_SESSION['error'])): ?>
            <p class="error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
        <?php endif; ?>
    </div>
</body>
</html>
