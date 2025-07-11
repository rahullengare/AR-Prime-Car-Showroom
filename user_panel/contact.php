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

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Retrieve and sanitize form data
        $fullName = htmlspecialchars($_POST['fullName']);
        $email = htmlspecialchars($_POST['email']);
        $phone = htmlspecialchars($_POST['phone']);
        $subject = htmlspecialchars($_POST['subject']);
        $message = htmlspecialchars($_POST['message']);

        // Simple validation
        if (empty($fullName) || empty($email) || empty($phone) || empty($subject) || empty($message)) {
            $status = "All fields are required!";
        } else {
            // Insert data into the database
            $query = "INSERT INTO contact_messages (fullName, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)";

            if ($stmt = $conn->prepare($query)) {
                $stmt->bind_param("sssss", $fullName, $email, $phone, $subject, $message);
                if ($stmt->execute()) {
                    $status = "Your message has been sent successfully!";
                } else {
                    $status = "There was an error sending your message. Please try again.";
                }
                $stmt->close();
            } else {
                $status = "Database query failed.";
            }
        }

        // Store the status message in a session variable
        $_SESSION['status'] = $status;

        // Redirect to avoid resubmitting the form
        header("Location: contact.php");
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
        /* Main Container */
        .Regular_Services {
            display: flex;
            justify-content: space-between;
            gap: 30px;
        }
        /* Left Side */
        .left-side {
            width: 48%;
            padding: 20px;
            margin-left: 4%;
            margin-top: 7%;
            color: white;
        }
        .contact1 p, .contact2 p{
            margin-top: 35px;
            font-size: 25px;
        }
        /* Right Side */
        .right-side{
            margin-right: 8%;
            margin-top: 3%;
            color: black;
            font-weight: 900;
        }
        .transparent-form {
            background-color: rgba(255, 255, 255, 0.3); /* semi-transparent white */
            border-radius: 10px; /* rounded corners */
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* subtle shadow */
            width: 80%; /* Adjust this value as needed to make it wider */
            max-width: 600px; /* Optional: Set a max-width for larger screens */
            margin: 0 auto; /* Center the form horizontally */
        }
        .transparent-form input, .transparent-form textarea {
            background-color: rgba(255, 255, 255, 0.7); /* lighter transparent background for input fields */
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 10px;
            width: 94%;
        }
        .transparent-form button {
            background-color: rgba(0, 123, 255, 0.7); /* transparent blue */
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }
        .transparent-form button:hover {
            background-color: rgba(0, 123, 255, 1); /* solid blue on hover */
        }
    </style>
    <title>Contact | AR</title>
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
    <div id="box1" class="box">
        <div class="Regular_Services">
            <div class="left-side">
                <div class="contact">
                    <div class="contact1">
                        <p class="email"><i class="fa fa-envelope" style="font-size:29px;color:rgb(9, 9, 241)"></i> Email: showroom@arprime.com</p>
                        <p class="phone"><i class="fa fa-phone" style="font-size:29px;color:rgb(60, 0, 255)"></i> Contact: +91 8877665544</p>
                        <p class="address"><i class="fa fa-map-marker" style="font-size:29px;color:red"></i> Address: Karad, MH, India</p>
                    </div>
                    <div class="contact2">
                        <p class="WhatsApp"><i class="fab fa-whatsapp-square" style="font-size:29px;color:rgb(0, 211, 21)"></i> WhatsApp: 8877665544</p>
                        <p class="Facebook"><i class="fab fa-facebook-square" style="font-size:29px;color:rgb(38, 0, 255)"></i> Facebook: AR Prime Showroom</p>
                        <p class="Instagram"><i class="fab fa-instagram" style="font-size:29px;color:red"></i> Instagram: _ar_prime_showroom</p>
                    </div>
                </div>
            </div>
    
            <div class="right-side">
                <form action="contact.php" method="POST" class="transparent-form">
                <h2 style="margin: -8px 7px 7px 45px;">Contact To The Showroom Manager</h2>
                    <label for="fullName">Full Name:</label>
                    <input type="text" id="fullName" name="fullName" pattern="^[A-Za-z\s]+$" placeholder="Enter your full name" required>

                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" pattern="^[^\s@]+@[^\s@]+\.[^\s@]+$" placeholder="Enter your email" required>

                    <label for="phone">Phone Number:</label>
                    <input type="tel" id="phone" name="phone" pattern="^[987]\d{9}$" placeholder="Enter your phone number" required>

                    <label for="subject">Subject:</label>
                    <input type="text" id="subject" name="subject" placeholder="Subject of your message" required>

                    <label for="message">Message:</label>
                    <textarea id="message" name="message" rows="4" placeholder="Enter your message" required></textarea>

                    <button type="submit">Send Message</button>
                </form>
            </div>
        </div>
    </div>
    
</body>
</html>
