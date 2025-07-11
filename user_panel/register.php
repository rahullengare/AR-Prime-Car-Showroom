<?php
    // Include database connection
    include('Connection.php');

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Collect form data
        $username = $_POST['username'];
        $city = $_POST['city'];
        $contact = $_POST['contact'];
        $age = $_POST['age'];
        $password = $_POST['password'];

        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert data into the users table
        $sql = "INSERT INTO users (username, city, contact, age, password) 
                VALUES ('$username', '$city', '$contact', '$age', '$hashed_password')";
        if (mysqli_query($conn, $sql)) {
            echo '<script>alert("Registration successful!");</script>';
            header("Location: login.php"); // Redirect to login page after registration
            exit;
        } else {
            echo '<script>alert("Error: ' . mysqli_error($conn) . '");</script>';
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../images/icon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" 
    integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ==" 
    crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>AR Prime Showroom Register Form</title>
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
        .title h3{
            margin-top: 4%;
        }
        .title a{
            text-decoration: none;
            color: aquamarine;
        }

        .container{
            width: 75%;
            height: 360px;
            background: #fff;
            margin: 0 auto;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 5);
        }
        .container .left{
            width: 50%;
            height: 430px;
            float: left;
            background: url(../images/login-back.jpg);
            background-size: cover;
            box-sizing: border-box; 
            border: 0px;
        }
        .container .right{
            width: 50%;
            height: 430px;
            float: right;
            box-sizing: border-box;
        }


        .intvalues{
            display: flex;
            width: 100%;
            margin: 15px 0px;
        }
        .intvalues input[name="Contact"]{
            margin-right: 18px;
        }

        .formBox{
            padding: 20px 40px;
            height: 430px;
            width: 100%;
            box-sizing: border-box;
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
        .formBox input[type="password"],
        .formBox input[type="int"]{
            border: none;
            border-bottom: 2px solid #a6af13;
            outline: none;
            height: 40px;
        }
        .formBox input[type="text"]:focus,
        .formBox input[type="password"]:focus,
        .formBox input[type="int"]:focus{
            border-bottom: 2px solid #262626;
        }
        .formBox input[type="submit"]{
            height: 35px;
            margin-bottom: 7px;
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
            padding: 0px 72px;
            color: #262626;
            font-size: 12px;
            font-weight: bold;
            text-decoration: none;
        }
    </style>
</head>
<body>

    <!--header section-->
    <div class="title">
        <h1>Register form</h1>
    </div>

    <div class="container">
        <div class="left"></div>
        <div class="right">
            <div class="formBox">
                <form action="register.php" method="POST">

                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" pattern="^[A-Za-z\s]+$" placeholder="&nbsp; &nbsp; &nbsp; &nbsp;Enter Your Name">

                    <label for="city">City:</label>
                    <input type="text" id="city" name="city" pattern="^[A-Za-z\s]+$" placeholder="&nbsp; &nbsp; &nbsp; &nbsp;Enter City Name">

                    <div class="intvalues">
                        <label for="contact">Contact:</label>
                        <input type="int" id="contact" name="contact" pattern="^[987]\d{9}$" placeholder=" &nbsp; &nbsp;Contact No">

                        <label for="age">Age:</label>
                        <input type="int" id="age" name="age" placeholder=" &nbsp; &nbsp;Enter Age">
                    </div>    

                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" pattern="^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$" placeholder="&nbsp; &nbsp; &nbsp; &nbsp;Enter Your Password">

                    <input type="submit" name="login.php" value="Register">
                    <a href="#">Forget Password</a> <a href="login.php">Already Registered</a>
                </form>
            </div>
        </div>
    </div>

    <div class="title">
        <h3>Register in ar prime Showroom</h3>
    </div>
</body>
</html>
