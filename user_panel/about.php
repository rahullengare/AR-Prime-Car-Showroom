<?php
    // Start session
    session_start();

    // Include the database connection
    include('Connection.php');

    // Check if the username session variable is set
    if (!isset($_SESSION['username'])) {
        header("Location: login.php"); // Redirect to login if not logged in
        exit;
    }

    // Get user (name) details from the database
    $username = $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="../images/icon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css"/>
    <link rel="stylesheet" href="/AR Cars/CSS/header.css">
    <title>About | AR</title>
    <style>
        body {
            background: url('/AR Cars/images/back.jpg') no-repeat center center fixed;
            background-size: cover;
            overflow-y: scroll;
            overflow-x: hidden;
            font-family: math;
        }
        /* this css code only use for the about.php file */
        .detils img{
            margin: 5px;
            height: 160px;
            width: 300px;
            border-radius: 30px;
        }
        .container {
            width: 95%;
            margin: 20px auto;
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }   
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            width: 300px;
            text-align: center;
            transition: transform 0.3s ease;
            font-size: 20px;
        }
        .card:hover {
            transform: translateY(-10px);
        }
        .card img {
            width: 100%;
            height: 240px;
        }
    </style>
</head>
<body>

    <!-- Start Header Section -->
    <div class="header">
        <div class="header-container">
            <div class="logo-section">
                <img src="../images/logo_AR.jpeg" alt="Showroom Logo" class="logo">
                <h4 class="name">AR PRIME SHOWROOM</h4>
            </div>
            <nav class="menu__bar">
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="services.php">Services</a></li>
                    <li><a href="cars.php">Cars</a></li>
                    <li><a href="about.php">About Us</a></li>
                    <li><a href="contact.php">Contact</a></li>
                </ul>
            </nav>
            <div class="right-section">
                <form class="SearchBar" action="#">
                    <input type="text" placeholder="Search Cars.." name="searchbar">
                    <button type="submit"><i class="fa fa-search"></i></button>
                </form>
                <div class="user-info">
                    <h4><i class="fa fa-user-circle"></i> <?php echo htmlspecialchars($username); ?>!</h4>
                    <?php if ($username !== "Guest"): ?>
                        <button><a href="logout.php">Logout</a></button>
                    <?php else: ?>
                        <button><a href="login.php">Login</a></button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <!-- End Header Section -->

    <!--main body section-->
   <div style="text-align:center; color: white;" class="main-about">
        <div class="detils">
            <img src="../images/logo_AR.jpeg" alt="Showroom"/>
            <h2>Welcome to AR Prime Showroom</h2>
            <p>Your Trusted Destination for Premium Cars</p>
            <p>Introducing the latest addition to my garage.</p>
        </div>

        <div class="container">
            <div class="card">
                <img src="../images/internal-icon.jpg"class="imgofshowroom"  alt="Showroom Interior"/>
                <p>Customer Satisfaction</p>
            </div>
            <div class="card">
                <img src="../images/carsale-icon.jpg" class="imgofshowroom"  alt="Customer Focus" />
                <p>Customer Focus</p>
            </div>
            <div class="card">
                <img src="../images/trustworthy-icon.png" class="imgofshowroom"  alt="Trustworthy & Transparent" />
                <p>Trustworthy & Transparent</p>
            </div>
            <div class="card">
                <img src="../images/expert-service-icon.png" class="imgofshowroom"  alt="Expert Service" />
                <p>Expert Service</p>
            </div>
        </div>    
        
    </div> 
</body>
</html>