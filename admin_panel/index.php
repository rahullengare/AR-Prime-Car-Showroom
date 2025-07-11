<?php
    session_start();
    include('connection.php');
    
    // Check if admin is logged in
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        header('Location: login.php');
        exit();
    }
    
    // Retrieve admin username from the session
    $admin_name = $_SESSION['admin_username'] ?? 'Admin';
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
    <title>Admin | AR | Home</title>
    <link rel="stylesheet" href="../CSS/admin.css">
    <style>
        body {
            background: url(admin-back.png) no-repeat center;
            background-size: 100% auto;
            background-attachment: fixed;
        }
        .options {
            text-align: center;
            margin-top: 25px;
        }
        .options h2 {
            font-size: 28px;
            color: #333;
        }
        .options .option-button {
            display: inline-block;
            margin: 10px;
            padding: 20px 40px;
            font-size: 18px;
            background-color: #28a745;
            color: white;
            border-radius: 10px;
            text-decoration: none;
            transition: background-color 0.3s;
        }
        .options .option-button:hover {
            background-color: #218838;
        }
        .options .option-button.delete {
            background-color: #dc3545;
        }
        .options .option-button.delete:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
<!-- HEADER SECTION START -->
    <div class="header">
        <div class="info">
            <img src="../images/logo_AR.jpeg" alt="Showroom Logo" class="logo">
            <h4 class="name">AR PRIME SHOWROOM</h4>
        </div>
        <div class="center">
            <div class="main">
                <a href="data_from_user.php" target="_blank"> Data From the USER</a>
            </div>
        </div>
        <div class="connect-db">
            <div class="user">
                <h4><span>Welcome, </span><i class="fa fa-user-circle"></i> <?php echo htmlspecialchars($admin_name); ?></h4>
                <button><a href="logout.php">Logout</a></button>
            </div> 
        </div>
    </div>
<!-- HEADER SECTION END -->

<!-- ADMIN OPTIONS SECTION START -->
    <div class="options">
        <h2>Admin Dashboard</h2>
        <div class="option-button-container">
            <a href="addcar.php" class="option-button">Add Car</a>
            <a href="deletecar.php" class="option-button delete">Remove Car</a>
            <a href="car-bookings-data.php" class="option-button">Booked Cars</a>
            <a href="payment-data.php" class="option-button">Payment Data</a>
            <a href="enquiry-data.php" class="option-button">Enquiry Data</a>
            <a href="test-drive-requests.php" class="option-button">Test-Drive-Requests</a>
        </div>
    </div>
<!-- ADMIN OPTIONS SECTION END -->
</body>
</html>
