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

    if (!$conn) {
        die("Database connection failed: " . mysqli_connect_error());
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $fullName = $_POST['fullName'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $carModel = isset($_POST['carModel']) ? $_POST['carModel'] : '';  
        $message = $_POST['message'];
    
        if (empty($carModel)) {
            $status = "Car model is required!";
        } else {
            $stmt = $conn->prepare("INSERT INTO enquiries (fullName, email, phone, carModel, message) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $fullName, $email, $phone, $carModel, $message);
        
            if ($stmt->execute()) {
                $status = "Enquiry submitted successfully!";
            } else {
                $status = "Error submitting the enquiry. Please try again.";
            }
        
            $stmt->close();
        }
    
        $conn->close();
    
        // **Alert message script**
        echo "<script>
            alert('$status');
            window.location.href='index.php'; // Redirect to prevent form resubmission
        </script>";
        exit;
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="../images/icon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css"/>
    <link rel="stylesheet" href="../CSS/header.css">
    <style>
        body {
            background: url('/AR Cars/images/back.jpg') no-repeat center center fixed;
            background-size: cover;
            overflow-y: scroll;
            overflow-x: hidden;
            font-family: math;
        }
        .main-home{
            color: white;
        }
        /* Box Styling */
        .box {
            padding: 10px;
            padding-bottom: 30px;
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
            gap: 30px;
        }
        /* Main Container 
        .Regular_Services {

        }*/

        /* Left Side */
        .left-side {
            padding: 20px;
            margin: 150px;
            margin-top: 130px;
            border-radius: 12px;
            height: 300px;
            width: 475px;
        }
        .left-side img {
            border-radius: 8px;
            display: block;
            margin-left: -41%;
        }

        /* Right Side */

        .right-side label {
            font-weight: 600;
            color: #000000;
            margin-bottom: 8px;
            display: block;
            font-size: 18px;
        }


        /* Dropdown Styling */
        .right-side select {
            cursor: pointer;
            width: 99%;
        }
        /* Checkbox Styling */
        .right-side input[type="checkbox"] {
            margin-right: 10px;
            cursor: pointer;
            margin-left: 30px;
            margin-top: 6px;
        }
        /* Textarea Styling */
        .right-side textarea {
            resize: vertical;
            min-height: 60px;
        }



        /* Transparent Form Container */
        .right-side {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(15px);
            border-radius: 10px;
            padding: 20px;
            width: 400px;
            height: 550px;
            margin-right: 12%;
        }
        .right-side h2 {
            text-align: center;
            /* color: #333; */
            font-size: 24px;
            margin-bottom: 10px;
            margin-top: -7px;
        }
        /* Focus Effects */
        .right-side input:focus,
        .right-side select:focus,
        .right-side textarea:focus {
            outline: none;
            border: 1px solid #4CAF50; /* Highlight on focus */
            background: rgba(255, 255, 255, 0.1); /* Slight opacity on focus */
        }

        /* Transparent Button Styling */
        .right-side button {
            margin-top: 15px;
            width: 100%;
            padding: 10px;
            background-color: rgba(76, 175, 80, 0.8); /* Slightly transparent button */
            color: white;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            background-color: #4CAF50;
            color: white;
            padding: 14px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            width: 100%;
            font-size: 18px;
            transition: background-color 0.3s ease-in-out;
        }

        .right-side button:hover {
            background-color: rgba(76, 175, 80, 1); /* Full opacity on hover */
            background-color: #24de2c;
        }
        /* Transparent Input Fields - Specific Targeting */
        .right-side input[type="text"],
        .right-side input[type="email"],
        .right-side input[type="tel"]{
            width: 95%;
            margin-bottom: 15px;
            font-size: 16px;
            transition: all 0.1s ease-in-out;
            background: transparent;
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 5px;
            margin-top: 3px;
            color: white;
            padding: 8px;
        }
        /* Focus and Hover Effects */
        .right-side input[type="text"]:hover, 
        .right-side input[type="email"]:hover,
        .right-side input[type="tel"]:hover,
        .right-side select:hover,
        .right-side textarea:hover,
        .right-side input[type="text"]:focus, 
        .right-side input[type="email"]:focus,
        .right-side input[type="tel"]:focus,
        .right-side select:focus,
        .right-side textarea:focus {
            border: 1px solid #0078D7;
            outline: none;
            border: 1px solid #4CAF50; /* Highlight on focus */
            background: rgba(255, 255, 255, 0.1); /* Slight opacity on focus */
        }

        /* Placeholder Styling */
        .right-side input[type="text"]::placeholder,
        .right-side input[type="email"]::placeholder,
        .right-side input[type="tel"]::placeholder,
        .right-side input::placeholder,
        .right-side textarea::placeholder {
            color: #ccc; /* Light placeholder text */
            opacity: 1; /* Ensure visibility */
        }

        /* Focus Effects */
        .right-side input[type="text"]:focus,
        .right-side input[type="email"]:focus,
        .right-side input[type="tel"]:focus {
            outline: none;
            border: 1px solid #4CAF50; /* Highlight on focus */
            background: rgba(255, 255, 255, 0.1); /* Slight opacity on focus */
        }
        /* Remove transparency specifically for select and textarea */
        .right-side select,
        .right-side textarea {
            background: transparent;
            border: 1px solid #ddd; /* Subtle border */
            border-radius: 5px;
            padding: 8px;
            box-sizing: border-box;
            width: 99%;
            margin-bottom: 15px;
        }

        /* Add hover and focus effects for select and textarea */
        .right-side select:hover,
        .right-side textarea:hover,
        .right-side select:focus,
        .right-side textarea:focus {
            border: 1px solid #4CAF50; /* Highlight border on hover and focus */
            /* Light background on hover and focus */
            outline: none;
        }

        /* Placeholder styling for textarea */
        .right-side textarea::placeholder {
            color: #aaa; /* Subtle placeholder color */
            opacity: 1;
        }
        select {
            background-color: transparent; /* Make the background of the dropdown transparent */
        }

        option {
            background-color: transparent; /* Make the background of each option transparent */
        }

        #blink {
            font-size: 20px;
            font-weight: bold;
            color: white;
        }
    </style>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            let blink = document.getElementById('blink');

            setInterval(function () {
                blink.style.visibility = (blink.style.visibility === "hidden" ? "visible" : "hidden");
            }, 1000);
        });
    </script>
    <title>Home | AR</title>
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

    <div id="box1" class="box">
        <div class="Regular_Services">
    
            <div class="left-side">
                <img src="../images/admin-back.png" alt="Main IMG">
                <p id="blink">"New Stock Available"</p>
            </div>
            
        </div>
    
        <div class="right-side">
                
            <form class="enquiry-form" action="index.php" method="POST">
                <h2>Car Services Form</h2>
                
                <label for="fullName">Full Name:</label>
                <input type="text" id="fullName" name="fullName" pattern="^[A-Za-z\s]+$" placeholder="Enter your full name" required>
                
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" pattern="^[^\s@]+@[^\s@]+\.[^\s@]+$" placeholder="Enter your email" required>
                
                <label for="phone">Phone Number:</label>
                <input type="tel" id="phone" name="phone" pattern="^[987]\d{9}$" placeholder="Enter your phone number" required>
                
                <label for="carModel">Car Model:</label>
                <div class="custom-dropdown" id="carModel">
                    <!-- Added name attribute here -->
                    <select name="carModel" required>
                        <option value="" disabled selected>Select a car model</option>
                        <option value="Hatchback">Hatchback</option>
                        <option value="Sedan">Sedan</option>
                        <option value="SUV">SUV</option>
                        <option value="MUV">MUV</option>
                        <option value="VAN">VAN</option>
                        <option value="Convertible">Commercial</option>
                    </select>
                </div>

                <label for="message">Additional Message:</label>
                <textarea id="message" name="message" rows="4" placeholder="Any specific requests or queries"></textarea>
                
                <button type="submit">Submit Enquiry</button>
            </form>

        </div>

      
    </div>


</body>
</html>
