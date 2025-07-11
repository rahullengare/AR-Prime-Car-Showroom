<?php
// Include database connection
include('Connection.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['user'];
    $password = $_POST['pass'];

    // Prepare SQL query to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $user['password'])) {
            // Secure the session
            session_regenerate_id(true);
            $_SESSION['username'] = $username;
            header("Location: cars.php");
            exit;
        } else {
            echo '<script>alert("Incorrect password.");</script>';
        }
    } else {
        echo '<script>alert("User not found.");</script>';
    }

    $stmt->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" 
    integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ==" 
    crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="icon" href="../images/icon.png">
    <title>AR Prime Showroom Login Form</title>
    <style>
        body{
            margin: 0;
            padding: 0;
            background: url(../images/login-back.jpg);
            background-size: cover;
            font-family: math;
        }
        .title{
            text-align:center;
            padding: 50px 0 20px;
            color: #32e9d7;
            text-transform: uppercase;
        }
        .title a{
            text-decoration: none;
            color: aquamarine;
        }

        .container{
            width: 50%;
            height: 360px;
            background: #fff;
            margin: 0 auto;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 5);
        }
        .container .left{
            float: left;
            width: 50%;
            height: 360px;
            background: url(../images/login-back.jpg);
            background-size: cover;
            box-sizing: border-box; 
            border: 0px;
        }
        .container .right{
            float: right;
            width: 50%;
            height: 360px;
            box-sizing: border-box;
        }

        .formBox{
            width: 100%;
            padding: 55px 40px;
            box-sizing: border-box;
            height: 360px;
            background: #fff;
        }
        .formBox p{
            margin: 0;
            padding: 0;
            font-weight: bold;
            color: #a6af13;
        }
        .formBox input{
            margin-bottom: 20px;
            width: 100%;
        }
        .formBox input[type="text"],
        .formBox input[type="password"]{
            border: none;
            border-bottom: 2px solid #a6af13;
            outline: none;
            height: 40px;
        }
        .formBox input[type="text"]:focus,
        .formBox input[type="password"]:focus{
            border-bottom: 2px solid #262626;
        }
        .formBox input[type="submit"]{
            border: none;
            outline: none;
            height: 40px;
            color: #fff;
            background: #262626;     
            cursor: pointer;
        }
        .formBox input[type="submit"]:hover{
            background: #a6af13;
        }
        .formBox a{
            color: #262626;
            font-size: 12px;
            font-weight: bold;
            padding: 0px 23px;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <!--header section-->
    <div class="title">
    <h1>Sing In form</h1>
    </div>

    <div class="container">
        <div class="left"></div>
        <div class="right">
            <div class="formBox">
                <form action="login.php" method="POST">

                    <label for="user">Username:</label>
                    <input type="text" id="user" name="user" pattern="^[A-Za-z\s]+$" placeholder="&nbsp; &nbsp; &nbsp; &nbsp;Enter Your Name" required>

                    <label for="pass">Password:</label>
                    <input type="password" id="pass" name="pass" placeholder="&nbsp; &nbsp; &nbsp; &nbsp;Enter Your Password" required>

                    <input type="submit" value="Login" name="">
                    <a href="#">Forget Password</a> <a href="register.php">Don't Account</a>
                </form>
            </div>
        </div>
    </div>

    <div class="title">
        <h3>Sing In to Go ar prime Showroom</h3>
    </div>
</body>
</html>
