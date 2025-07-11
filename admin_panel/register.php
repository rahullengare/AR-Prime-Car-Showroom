<?php
session_start();
include 'connection.php';

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);

    // Check if passwords match
    if ($password !== $confirm_password) {
        $_SESSION['error'] = "Passwords do not match!";
    } else {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert the admin credentials into the database
        $query = "INSERT INTO admin_login (username, password) VALUES ('$username', '$hashed_password')";
        if (mysqli_query($conn, $query)) {
            $_SESSION['success'] = "Admin registered successfully!";
            header('Location: login.php'); // Redirect to the login page
            exit();
        } else {
            $_SESSION['error'] = "Failed to register admin. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../images/icon.png">
    <title>Admin Registration</title>
    <link rel="stylesheet" href="admin.css"> 
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
        .msgBox .success {
            color: green;
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
        <h2>Admin Registration</h2>
        <form action="register.php" method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="password" name="confirm_password" placeholder="Confirm Password" required>
            <input type="submit" value="Register">
        </form>
        <?php if (isset($_SESSION['error'])): ?>
            <p class="error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
        <?php endif; ?>
        <?php if (isset($_SESSION['success'])): ?>
            <p class="success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></p>
        <?php endif; ?>
    </div>
</body>
</html>
