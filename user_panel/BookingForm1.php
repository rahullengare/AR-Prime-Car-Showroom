<?php
    // Start session
    session_start();

    // Check if the username session variable is set
    if (isset($_SESSION['username'])) {
        // Include the database connection
        include('Connection.php');

        // Get user details from the database
        $username = $_SESSION['username'];
        $sql = "SELECT * FROM users WHERE username = '$username'";
        $result = mysqli_query($conn, $sql);
        $user = mysqli_fetch_array($result, MYSQLI_ASSOC);
    } else {
        header("Location: login.php"); // Redirect to login if not logged in
        exit;
    }

    // Check if form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Sanitize and collect form data
        $carModel = $conn->real_escape_string($_POST['carModel']);
        $car_color = $conn->real_escape_string($_POST['car_color']);
        $booking_date = $_POST['booking_date'];
        $delivery_datetime = $_POST['delivery_datetime'];
        $car_type = $_POST['car_type'];
        
        // Collect insurance types (array)
        $insurance_type = isset($_POST['insuranceType']) ? json_encode($_POST['insuranceType']) : null;
        
        $insurance_company = $conn->real_escape_string($_POST['insuranceCompany']);
        $transmission = $conn->real_escape_string($_POST['transmission']);
        
        // Collect accessories (arrays)
        $standard_accessories = isset($_POST['standard_accessories']) ? json_encode($_POST['standard_accessories']) : null;
        $optional_accessories = isset($_POST['optional_accessories']) ? json_encode($_POST['optional_accessories']) : null;
        $technological_addons = isset($_POST['technological_addons']) ? json_encode($_POST['technological_addons']) : null;
        $protective_accessories = isset($_POST['protective_accessories']) ? json_encode($_POST['protective_accessories']) : null;
        $luxury_addons = isset($_POST['luxury_addons']) ? json_encode($_POST['luxury_addons']) : null;
        
        // Loan details (if loan is selected)
        $loan_option = $conn->real_escape_string($_POST['loan']);
        $loan_amount = null;
        $loan_tenure = null;

        if ($loan_option == 'Yes') {
            $loan_amount = isset($_POST['loan_amount']) ? $_POST['loan_amount'] : null;
            $loan_tenure = isset($_POST['loan_tenure']) ? $_POST['loan_tenure'] : null;
        }
        
        $car_loan_provider = ($loan_option == 'No') ? NULL : $conn->real_escape_string($_POST['car_loan_provider']);
        
        $comments = $conn->real_escape_string($_POST['comments']);
        
        // Prepare SQL query
        $sql = "INSERT INTO car_bookingform1 (carModel, car_color, booking_date, delivery_datetime, car_type, insurance_type, insurance_company, transmission, standard_accessories, optional_accessories, technological_addons, protective_accessories, luxury_addons, loan_option, loan_amount, loan_tenure, car_loan_provider, comments) 
                VALUES ('$carModel', '$car_color', '$booking_date', '$delivery_datetime', '$car_type', '$insurance_type', '$insurance_company', '$transmission', '$standard_accessories', '$optional_accessories', '$technological_addons', '$protective_accessories', '$luxury_addons', '$loan_option', '$loan_amount', '$loan_tenure','$car_loan_provider', '$comments')";

        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Booking successfully submitted!'); window.location.href='BookingForm2.php';</script>";
        } else {
            echo "<script>alert('Error: " . $conn->error . "'); window.location.href='BookingForm1.php';</script>";
        }

        $conn->close();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../images/icon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css"/>
    <title>Car Booking | AR</title>
    <link rel="stylesheet" href="../CSS/Booking.css">
    <!-- script only for the loan option Yes or No -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const loan = document.getElementById('loan');
            const loanDetails = document.getElementById('loan_details');
            const loanAmountField = document.getElementById('loan_amount');
            const loanTenureField = document.getElementById('loan_tenure');
            const calculateButton = document.getElementById('calculate_button');  // Using button id
            const carLoanProviderField = document.getElementById('car_loan_provider');
            const emiResult = document.getElementById('emi_result');  // EMI result div

            // Loan option change event listener
            loan.addEventListener('change', function () {
                if (loan.value === 'Yes') {
                    loanDetails.classList.add('active');  // Show loan details section
                    loanAmountField.disabled = false;  // Enable loan amount field
                    loanTenureField.disabled = false;  // Enable loan tenure field
                    calculateButton.disabled = false;  // Enable calculate EMI button
                    carLoanProviderField.disabled = false;  // Enable car loan provider dropdown
                } else {
                    loanDetails.classList.remove('active');  // Hide loan details section
                    loanAmountField.disabled = true;  // Disable loan amount field
                    loanTenureField.disabled = true;  // Disable loan tenure field
                    calculateButton.disabled = true;  // Disable calculate EMI button
                    carLoanProviderField.disabled = true;  // Disable car loan provider dropdown
                }
            });

            // EMI Calculation function
            function calculateEMI() {
                const loanAmount = parseFloat(loanAmountField.value);
                const loanTenure = parseInt(loanTenureField.value);
                const interestRate = 9; // Fixed interest rate at 9%

                // Check if the loan amount and tenure are valid
                if (loanAmount && loanTenure) {
                    const monthlyInterestRate = interestRate / 12 / 100;
                    const numberOfMonths = loanTenure * 12;

                    // EMI Calculation Formula
                    const emi = loanAmount * monthlyInterestRate * Math.pow(1 + monthlyInterestRate, numberOfMonths) / 
                                (Math.pow(1 + monthlyInterestRate, numberOfMonths) - 1);

                    // Display EMI result
                    emiResult.innerHTML = `Your EMI is ₹${emi.toFixed(2)} per month.`;
                } else {
                    // Error message for invalid input
                    emiResult.innerHTML = 'Please enter valid details for EMI calculation.';
                }
            }

            // Attach click event listener to the calculate EMI button
            calculateButton.addEventListener('click', function () {
                calculateEMI();
            });

            // Alert message on page load
            alert('This is the car details form. Please fill in the correct and accurate information.');
        });
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
            <h2>Car Details Form </h2>
            <form action="BookingForm1.php" method="POST">
                <input type="hidden" name="formType" value="main">

                <label for="carModel">Car Model:</label>
                <select id="carModel" name="carModel" required>
                    <option value="">Select Car Model</option>
                    <option value="WagonR">WagonR</option>
                    <option value="Brezza">Brezza</option>
                    <option value="DZire">DZire</option>
                    <option value="Alto800">Alto800</option>
                    <option value="ECCO">ECCO</option>
                    <option value="Ertiga">Ertiga</option>
                    <option value="ECCO-Cargo">ECCO-Cargo</option>
                    <option value="Super_Carry">Super Carry</option>
                </select><br><br>

                <label for="car_color">Preferred Color:</label>
                <select id="car_color" name="car_color" required>
                    <option value="">Select Color</option>
                    <option value="red" style="background-color: #ff0000;">Red</option>
                    <option value="blue" style="background-color: #0000ff;">Blue</option>
                    <option value="green" style="background-color: #008000;">Green</option>
                    <option value="black" style="background-color: #000000;">Black</option>
                    <option value="white" style="background-color: #ffffff;">White</option>
                    <option value="silver" style="background-color: #c0c0c0;">Silver</option>
                    <option value="grey" style="background-color: #808080;">Grey</option>
                    <option value="yellow" style="background-color: #ffff00;">Yellow</option>
                </select><br><br>                    

                <label for="booking_date">Booking Date:</label>
                <input type="date" id="booking_date" name="booking_date" required><br><br>

                <label for="delivery_datetime">Preferred Delivery Date and Time:</label><br>
                <input type="datetime-local" id="delivery_datetime" name="delivery_datetime" required><br><br>

                <label for="car_type">Car Type:</label>
                <select id="car_type" name="car_type" required>
                    <option value=""> Select Car Type </option>
                    <option value="petrol">Petrol</option>
                    <option value="diesel">Diesel</option>
                    <option value="cng">CNG</option>
                    <option value="ev"> EV (Electric Vehicle)</option>
                    <option value="cng_diesel">CNG with Diesel</option>
                    <option value="cng_petrol">CNG with Petrol</option>
                </select><br><br>

                <label>Insurance Type:</label><br>
                <input type="checkbox" name="insuranceType[]" value="Third-party liability"> Third-party Liability<br>
                <input type="checkbox" name="insuranceType[]" value="Comprehensive"> Comprehensive<br>
                <input type="checkbox" name="insuranceType[]" value="Own damage"> Own Damage<br>
                <input type="checkbox" name="insuranceType[]" value="Roadside assistance"> Roadside Assistance<br>
                <input type="checkbox" name="insuranceType[]" value="Zero depreciation"> Zero Depreciation<br><br>

                <label for="insuranceCompany">Insurance Company:</label>
                <select id="insuranceCompany" name="insuranceCompany" required>
                    <option value="">Select Insurance Company</option>
                    <option value="HDFC ERGO">HDFC ERGO</option>
                    <option value="Tata AIG">Tata AIG</option>
                    <option value="Bajaj Allianz">Bajaj Allianz</option>
                </select><br><br>

                <label for="transmission">Transmission (Gears):</label>
                <select id="transmission" name="transmission" required>
                    <option value=""> Select Transmission </option>
                    <option value="manual">Manual Transmission</option>
                    <option value="automatic">Automatic Transmission</option>
                    <option value="torque_converter">Torque Converter Transmission</option>
                    <option value="cvt">Continuously Variable Transmission (CVT)</option>
                    <option value="semi_automatic">Semi-Automatic Transmission</option>
                    <option value="dual_clutch">Dual-Clutch Transmission</option>
                </select><br><br>

                <!-- Standard Accessories -->
                <label for="standard_accessories">Standard Accessories (Usually Free):</label>
                <span style="color: #41d71e; font-size: 13px; padding: 0px 30px; margin: 10px 0;">This are Free Accessories</span><br>
                <input type="checkbox" name="standard_accessories[]" value="Floor Mats"> Floor Mats (Rubber or Fabric)<br>
                <input type="checkbox" name="standard_accessories[]" value="Mud Flaps"> Mud Flaps<br>
                <input type="checkbox" name="standard_accessories[]" value="Car Cover"> Car Cover<br>
                <input type="checkbox" name="standard_accessories[]" value="Number Plate Frame" checked> Number Plate Frame<br>
                <input type="checkbox" name="standard_accessories[]" value="Tool Kit"> Tool Kit<br>
                <input type="checkbox" name="standard_accessories[]" value="First Aid Kit"> First Aid Kit<br>
                <input type="checkbox" name="standard_accessories[]" value="Emergency Warning Triangle" checked> Emergency Warning Triangle<br><br>

                <!-- Optional Accessories -->
                <label for="optional_accessories">Optional Accessories (Chargeable):</label>
                <span style="color: #41d71e; font-size: 13px; padding: 0px 30px; margin: 10px 0;">Now 10% off for these Accessories</span><br>
                <input type="checkbox" name="optional_accessories[]" value="Seat Covers"> Seat Covers (Leather or Fabric)<br>
                <input type="checkbox" name="optional_accessories[]" value="Window Sunshades"> Window Sunshades<br>
                <input type="checkbox" name="optional_accessories[]" value="Reverse Parking Camera/Sensors"> Reverse Parking Camera/Sensors<br>
                <input type="checkbox" name="optional_accessories[]" value="Touchscreen Infotainment System"> Touchscreen Infotainment System<br>
                <input type="checkbox" name="optional_accessories[]" value="Alloy Wheels"> Alloy Wheels<br>
                <input type="checkbox" name="optional_accessories[]" value="Body Side Molding"> Body Side Molding<br>
                <input type="checkbox" name="optional_accessories[]" value="Roof Rails"> Roof Rails<br>
                <input type="checkbox" name="optional_accessories[]" value="Fog Lamps"> Fog Lamps<br>
                <input type="checkbox" name="optional_accessories[]" value="Door Visors"> Door Visors<br>
                <input type="checkbox" name="optional_accessories[]" value="Ambient Lighting System"> Ambient Lighting System<br><br>

                <!-- Technological Add-ons -->
                <label for="technological_addons">Technological Add-ons:</label>
                <span style="color: #41d71e; font-size: 13px; padding: 0px 30px; margin: 10px 0;">Now 15% off for these Accessories</span><br>
                <input type="checkbox" name="technological_addons[]" value="GPS Navigation System"> GPS Navigation System<br>
                <input type="checkbox" name="technological_addons[]" value="Car Dash Cam"> Car Dash Cam<br>
                <input type="checkbox" name="technological_addons[]" value="Bluetooth Hands-free Kit"> Bluetooth Hands-free Kit<br>
                <input type="checkbox" name="technological_addons[]" value="Remote Engine Start System"> Remote Engine Start System<br><br>

                <!-- Protective Accessories -->
                <label for="protective_accessories">Protective Accessories:</label>
                <span style="color: #41d71e; font-size: 13px; padding: 0px 30px; margin: 10px 0;">Now 17% off for these Accessories</span><br>
                <input type="checkbox" name="protective_accessories[]" value="Anti-Rust Coating"> Anti-Rust Coating<br>
                <input type="checkbox" name="protective_accessories[]" value="Underbody Coating"> Underbody Coating<br>
                <input type="checkbox" name="protective_accessories[]" value="Ceramic Coating"> Ceramic Coating<br>
                <input type="checkbox" name="protective_accessories[]" value="Paint Protection Film (PPF)"> Paint Protection Film (PPF)<br><br>

                <!-- Luxury Add-ons -->
                <label for="luxury_addons">Luxury Add-ons:</label>
                <span style="color: #41d71e; font-size: 13px; padding: 0px 30px; margin: 10px 0;">Now 20% off for these Accessories</span><br>
                <input type="checkbox" name="luxury_addons[]" value="Wireless Phone Charger"> Wireless Phone Charger<br>
                <input type="checkbox" name="luxury_addons[]" value="Digital Instrument Cluster"> Digital Instrument Cluster<br>
                <input type="checkbox" name="luxury_addons[]" value="Sunroof or Moonroof"> Sunroof or Moonroof<br><br>
                    
                <div class="loan-option-form">
                    <label for="loan">Loan Option:</label>
                    <select id="loan" name="loan" required>
                        <option value="">Select Loan Option</option>
                        <option value="Yes">Yes</option>
                        <option value="No">No</option>
                    </select><br><br>
               
                    <!-- Loan Details -->
                    <div id="loan_details" class="loan-details">
                        <label for="loan_amount">Loan Amount (₹):</label>
                        <input type="number" id="loan_amount" name="loan_amount" placeholder="Loan Amount (₹)" min="100000" max="20000000" disabled><br><br>

                        <label for="loan_tenure">Loan Tenure (Years):</label>
                        <select id="loan_tenure" name="loan_tenure" disabled>
                            <option value="1">1 Year</option>
                            <option value="2">2 Years</option>
                            <option value="3">3 Years</option>
                            <option value="4">4 Years</option>
                            <option value="5">5 Years</option>
                        </select><br><br>

                        <label for="interest_rate">Interest Rate: 9% (Fixed)</label><br>
                        <button type="button" id="calculate_button" disabled>Calculate EMI</button>

                        <div id="emi_result" class="result">
                            EMI PER MONTH :
                        </div><br><br>

                        <label for="car_loan_provider">Car Loan Provider:</label>
                        <select id="car_loan_provider" name="car_loan_provider" required>
                            <option value="">Select Car Loan Provider</option>
                            <option value="No">No Any Loan Required</option>
                            <option value="HDFC">HDFC</option>
                            <option value="ICICI">ICICI</option>
                            <option value="SBI">SBI</option>
                            <option value="Axis">Axis</option>
                            <option value="Bajaj">Bajaj Finance</option>
                            <option value="Kotak">Kotak Mahindra</option>
                            <option value="Tata">Tata Capital</option>
                            <option value="Other">Other</option>
                        </select><br><br>

                    </div>               

                <label for="comments"> Comments:</label>
                <textarea id="comments" name="comments"></textarea><br>
                                    
                <div class="button-container">
                    <button type="button" class="cancel-btn" onclick="window.location.href='cars.php'">Cancel</button>
                    <input type="submit" value="Submit" class="submit-btn">
                </div>

            </form>
        </div>
    </div>
</div>

</body>
</html>