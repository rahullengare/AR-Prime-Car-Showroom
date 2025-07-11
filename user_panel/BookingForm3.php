<?php
    // Start session
    session_start();

    // Check if the username session variable is set
    if (isset($_SESSION['username'])) {
        // Include the database connection
        include('Connection.php');

        // Get user details from the database
        $username = $_SESSION['username'];
        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
    } else {
        header("Location: login.php"); // Redirect to login if not logged in
        exit;
    }

    // Handle form submission
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        // Collect and sanitize form data
        $carModel = htmlspecialchars($_POST['carModel'] ?? '');
        $totalCarPrice = floatval($_POST['total_car_price'] ?? 0);
        $finalAmountPayable = floatval($_POST['final_amount_payable'] ?? 0);
        $youSavingAmount = floatval($_POST['you_saving_amount'] ?? 0);
        $loanOption = htmlspecialchars($_POST['loan'] ?? '');
        $payment_date = htmlspecialchars($_POST['payment_date'] ?? '');
        $paymentTime = htmlspecialchars($_POST['payment_time'] ?? '');
        $payerName = htmlspecialchars($_POST['payer_name'] ?? '');
        $comments = htmlspecialchars($_POST['comments'] ?? '');

        // Validate required fields
        if (!$carModel || !$totalCarPrice || !$finalAmountPayable || !$payment_date || !$payerName) {
            echo "<script>alert('Please fill in all required fields.');</script>";
        } else {
            // Prepare an SQL statement with placeholders
            $sql = "INSERT INTO car_bookingform3 (
                car_model, total_car_price, final_amount_payable, you_saving_amount,
                loan_option, payment_date, payment_time, payer_name, comments
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

            // Prepare the statement
            if ($stmt = $conn->prepare($sql)) {
                // Bind parameters
                $stmt->bind_param(
                    "sdddsssss", 
                    $carModel, $totalCarPrice, $finalAmountPayable, $youSavingAmount,
                    $loanOption, $payment_date, $paymentTime, $payerName, $comments
                );

                // Execute the statement
                if ($stmt->execute()) {
                    echo "<script>alert('Booking successfully submitted!'); window.location.href='cars.php';</script>";
                } else {
                    echo "Error: " . $stmt->error;
                }

                // Close the statement
                $stmt->close();
            } else {
                echo "Error preparing the statement: " . $conn->error;
            }
        }

        // Close the connection
        $conn->close();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css"/>
    <link rel="icon" href="../images/icon.png">
    <title>Car Booking | AR</title>
    <link rel="stylesheet" href="../CSS/Booking.css">
    <script>
        //calculate total price also cut discount amount and show final amount
        function calculatePrice() {
            const carModel = document.getElementById('carModel');
            const selectedOption = carModel.options[carModel.selectedIndex];
            const totalCarPrice = selectedOption.getAttribute('data-price');
            
            if (totalCarPrice) {
                const discount = 8.70 / 100;
                const finalAmountPayable = totalCarPrice - (totalCarPrice * discount);
                const youSavingAmount = totalCarPrice - finalAmountPayable;

                document.getElementById('total_car_price').value = parseFloat(totalCarPrice).toFixed(2);
                document.getElementById('final_amount_payable').value = finalAmountPayable.toFixed(2);
                document.getElementById('you_saving_amount').value = youSavingAmount.toFixed(2);
            }
        }
    
        window.onload = function() {
            alert('This is the -Payment Details Form- Check the correct amount & then pay the applicable amount');
        };
    </script>
</head>
<body>

<!--header section START-->
    <div class="header">
        <div class="info">
            <img src="../images/logo_AR.jpeg" alt="Showroom Logo" class="logo">
            <h4 class="name">AR PRIME SHOWROOM </h4>
        </div>
        <div class="center">
            <div class="marquee-wrapper">
            <img src="../images/icon1.png" alt="car-icon" class="logo left-logo">
            <h2 class="marquee">Car Booking Form</h2>
            <img src="../images/icon2.png" alt="car-icon" class="logo right-logo">
            </div>
        </div>
        <div class="connect-db">
            <div class="user">
                <h4><i class="fa fa-user-circle"></i> <?php echo htmlspecialchars($username); ?>!</h4>
                <button><a href="logout.php">Logout</a></button>
            </div> 
        </div>
    </div>
<!--header section END-->

<div class="main">
    <div class="main-form">
        <div class="form">
            <h2>Payment Details Form </h2>
            <form action="BookingForm3.php" method="POST">
                <input type="hidden" name="formType" value="box1">

                <label for="carModel">Car Model:</label>
                <select id="carModel" name="carModel" required onchange="calculatePrice()">
                    <option value=""> Select Car Model </option>
                    <option value="WagonR" data-price="983000">WagonR</option>
                    <option value="Brezza" data-price="1264050">Brezza</option>
                    <option value="DZire" data-price="952800">DZire</option>
                    <option value="Alto800" data-price="467099">Alto800</option>
                    <option value="ECCO" data-price="641000">ECCO</option>
                    <option value="Ertiga" data-price="1140000">Ertiga</option>
                    <option value="ECCO-Cargo" data-price="674050">ECCO-Cargo</option>
                    <option value="Super_Carry" data-price="641020">Super Carry</option>
                </select><br><br>

                <label for="total_car_price">Total Car Price:</label>
                <span style="color: #41d71e; font-size: 16px; padding: 0px 30px; margin: 10px 0;">Plat 8.70% discount for online booking</span><br>
                <input type="text" id="total_car_price" name="total_car_price" readonly><br><br>

                <label for="final_amount_payable">Final Amount Payable:</label>
                <input type="text" id="final_amount_payable" name="final_amount_payable" readonly><br><br>

                <label for="you_saving_amount">You Saving Amount:</label>
                <input type="text" id="you_saving_amount" name="you_saving_amount" readonly><br><br>

                <div id="qr_section" style="display:flex; justify-content: center; align-items: center; background-color: #f0f0f0; height: 300px; width: 100%; border-radius: 10px; margin-top: 10px;">
                    <h3 style="margin-right: 10px;">Scan the QR Code <br> To make Payment:<br>
                    <span style="color: #41d71e; font-size: 12px; margin-left: -40px;">*Payment Screenshort send the company gmail</span></h3>
                    <img src="../images/QR.jpg" alt="AR Prime Showroom QR Code" style="width: 380px; height: 295px;" readonly>
                </div><br>

                <div class="loan-option-form">
                    <label for="loan">Loan Option:</label>
                    <select id="loan" name="loan" required>
                        <option value="">Select Loan Option</option>
                        <option value="Yes">Yes</option>
                        <option value="No">No</option>
                    </select><br><br>
                </div>
            
                <label for="payment_date">Payment Date:</label>
                <input type="date" id="payment_date" name="payment_date" required><br>

                <label for="payment_time">Payment Time:</label>
                <input type="time" id="payment_time" name="payment_time" required><br>

                <label for="payer_name">Payer's Name:</label>
                <input type="text" id="payer_name" name="payer_name" placeholder="Surname   MiddleName    FirstName" required><br><br>

                <label for="comments"> Comments:</label>
                <textarea id="comments" name="comments"></textarea><br>

                <div class="button-container">
                    <button type="button" class="cancel-btn" onclick="window.location.href='BookingForm2.php'">Cancel</button>
                    <button type="submit" value="Submit" class="submit-btn">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
