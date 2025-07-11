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

    // Fetch cars from database
    $sql = "SELECT * FROM addcars";
    $result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="../images/icon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">
    <title>CARS | AR</title>
    <link rel="stylesheet" href="../CSS/header.css">
    <style>
        body {
            background: url('/AR Cars/images/back.jpg') no-repeat center center fixed;
            background-size: cover;
            overflow-y: scroll;
            overflow-x: hidden;
            font-family: math;
        }
        /* Container */
        .container {
            width: 100%;
            margin: 20px auto;
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }
        /* Card */
        .card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            width: 450px;
            text-align: center;
            transition: transform 0.3s ease;
            padding-bottom: 20px;
        }
        .card:hover {
            transform: translateY(-10px);
        }
        .card img {
            width: 100%;
            height: 240px;
        }
        .card h3 {
            font-size: 20px;
            color: #333;
            margin: 5px 0;
        }
        .card p {
            font-size: 16px;
            color: #666;
            margin: 0 0 15px;
        }
        /* More Details Button */
        .btn {
            background: #333;
            color: white;
            text-decoration: none;
            padding: 10px 15px;
            border-radius: 5px;
            border: none;
        }
        .card .btn:hover {
            background: rgb(40, 245, 8);
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

    <!-- Cars Display Section -->
    <div class="container">
        <?php 
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) { ?>
                <div class="card">
                    <img src="<?php echo htmlspecialchars($row['carimg']); ?>" alt="Car Image">
                    <h3>Model: <?php echo htmlspecialchars($row['carname']); ?></h3>
                    <p>Price: <?php echo htmlspecialchars($row['price']); ?> lakh starting </p>
                    <a href="BookingForm1.php" class="btn">Book Now!</a>
                    <a href="services.php?section=box5" class="btn">Test Drive Book</a>
                </div>
        <?php 
            }
        } else {
            echo "<p style='background-color: #ff0000; font-size: 27px; border-radius: 7px;'>No cars found.</p>";
        }
        ?>
    </div>

</body>
</html>
