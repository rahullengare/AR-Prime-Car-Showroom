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

        function uploadResume($file, $uploadDir = 'uploads/resumes/') {
        // Ensure the file exists and no error
        if ($file['error'] === UPLOAD_ERR_OK) {
            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            
            // Only allow PDF files
            if ($extension === 'pdf') {
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true); // Create directory if not exists
                }
                $filePath = $uploadDir . uniqid() . '_' . basename($file['name']);
                
                // Move the uploaded file to the target directory
                return move_uploaded_file($file['tmp_name'], $filePath) ? $filePath : false;
            } else {
                echo "<script>alert('Invalid file format. Only PDF files are allowed.');</script>";
                return false;
            }
        }
        echo "<script>alert('Error uploading resume.');</script>";
        return false;
    }

    // Check if the form is submitted
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Get the form type
        $formType = isset($_POST['formType']) ? $_POST['formType'] : '';

        // Common Fields
        $name = isset($_POST['name']) ? mysqli_real_escape_string($conn, $_POST['name']) : '';
        $email = isset($_POST['email']) ? mysqli_real_escape_string($conn, $_POST['email']) : '';
        $mobile = isset($_POST['mobile']) ? mysqli_real_escape_string($conn, $_POST['mobile']) : '';
        $city = isset($_POST['city']) ? mysqli_real_escape_string($conn, $_POST['city']) : '';
        $carModel = isset($_POST['carModel']) ? mysqli_real_escape_string($conn, $_POST['carModel']) : '';
        $carRegisterNo = isset($_POST['carRegisterNo']) ? mysqli_real_escape_string($conn, $_POST['carRegisterNo']) : '';
        $comments = isset($_POST['comments']) ? mysqli_real_escape_string($conn, $_POST['comments']) : '';
        

        // Form-Specific Fields
        switch ($formType) {
            case 'box1': // Service Form
                $services = isset($_POST['services']) ? implode(', ', $_POST['services']) : '';
                $serviceDate = isset($_POST['serviceDate']) ? mysqli_real_escape_string($conn, $_POST['serviceDate']) : '';
    
                $sql = "INSERT INTO regular_services_form (name, email, mobile, city, car_model, car_register_no, service_date, services, comments)
                    VALUES ('$name', '$email', '$mobile', '$city', '$carModel', '$carRegisterNo', '$serviceDate', '$services', '$comments')";
                break;

            case 'box2': // Accident Repair Form
                $accidentDate = mysqli_real_escape_string($conn, $_POST['accidentDate']);
                $accidentLocation = mysqli_real_escape_string($conn, $_POST['accidentLocation']);
                $repairRequired = isset($_POST['repair']) ? implode(', ', $_POST['repair']) : '';
                $insuranceNumber = mysqli_real_escape_string($conn, $_POST['insuranceNumber']);
                $insuranceExpiryDate = mysqli_real_escape_string($conn, $_POST['insuranceExpiryDate']);
                $serviceDate = mysqli_real_escape_string($conn, $_POST['serviceDate']);
            
                $sql = "INSERT INTO accident_repair_form (name, email, mobile, city, car_model, car_register_no, accident_date, accident_location, repair_required, insurance_number, insurance_expiry_date, service_date, comments)
                    VALUES ('$name', '$email', '$mobile', '$city', '$carModel', '$carRegisterNo', '$accidentDate', '$accidentLocation', '$repairRequired', '$insuranceNumber', '$insuranceExpiryDate', '$serviceDate', '$comments')";
                break;

            case 'box3': // Warranty Extension Form
                $warrantyStartDate = isset($_POST['warrantyStartDate']) ? mysqli_real_escape_string($conn, $_POST['warrantyStartDate']) : '';
                $warrantyEndDate = isset($_POST['warrantyEndDate']) ? mysqli_real_escape_string($conn, $_POST['warrantyEndDate']) : '';
                $warrantyPackage = isset($_POST['warrantyPackage']) ? implode(', ', $_POST['warrantyPackage']) : '';
                $warrantyDuration = isset($_POST['warrantyDuration']) ? mysqli_real_escape_string($conn, $_POST['warrantyDuration']) : '';
                $carCondition = isset($_POST['carCondition']) ? mysqli_real_escape_string($conn, $_POST['carCondition']) : '';
            
                $sql = "INSERT INTO warranty_extension_form (name, email, mobile, city, car_model, car_register_no, warranty_start_date, warranty_end_date, warranty_package, warranty_duration, car_condition, comments)
                    VALUES ('$name', '$email', '$mobile', '$city', '$carModel', '$carRegisterNo', '$warrantyStartDate', '$warrantyEndDate', '$warrantyPackage', '$warrantyDuration', '$carCondition', '$comments')";
                break;

            case 'box4': // Insurance Details Form
                $carManufYear = isset($_POST['carManufYear']) ? mysqli_real_escape_string($conn, $_POST['carManufYear']) : '';
                $insuranceOption = isset($_POST['insuranceOption']) ? mysqli_real_escape_string($conn, $_POST['insuranceOption']) : '';
                $insuranceType = isset($_POST['insuranceType']) ? mysqli_real_escape_string($conn, implode(', ', $_POST['insuranceType'])) : '';
                $insuranceCompany = isset($_POST['insuranceCompany']) ? mysqli_real_escape_string($conn, $_POST['insuranceCompany']) : ''; 
                $insuranceStartDate = isset($_POST['insuranceStartDate']) ? mysqli_real_escape_string($conn, $_POST['insuranceStartDate']) : '';
                $insuranceEndDate = isset($_POST['insuranceEndDate']) ? mysqli_real_escape_string($conn, $_POST['insuranceEndDate']) : '';           

                $sql = "INSERT INTO insurance_form (name, email, mobile, city, car_model, carRegisterNo, car_manuf_year, insurance_option, insurance_type, insuranceCompany, insurance_start_date, insurance_end_date, comments) 
                    VALUES ('$name', '$email', '$mobile', '$city', '$carModel', '$carRegisterNo', '$carManufYear', '$insuranceOption', '$insuranceType', '$insuranceCompany', '$insuranceStartDate', '$insuranceEndDate', '$comments')";
                break;

            case 'box5': // Test Drive Form
                $location = isset($_POST['location']) ? mysqli_real_escape_string($conn, $_POST['location']) : '';
                $convenientDate = isset($_POST['convenientDate']) ? mysqli_real_escape_string($conn, $_POST['convenientDate']) : '';
                $convenientTime = isset($_POST['convenientTime']) ? mysqli_real_escape_string($conn, $_POST['convenientTime']) : '';
                $driversLicenseNo = isset($_POST['driversLicenseNo']) ? mysqli_real_escape_string($conn, $_POST['driversLicenseNo']) : '';
                $licenseExpiryDate = isset($_POST['licenseExpiryDate']) ? mysqli_real_escape_string($conn, $_POST['licenseExpiryDate']) : '';
            
                $sql = "INSERT INTO test_drive_requests (name, email, mobile, city, car_model, location, convenient_date, convenient_time, drivers_license_no, license_expiry_date, comments) 
                        VALUES ('$name', '$email', '$mobile', '$city', '$carModel', '$location', '$convenientDate', '$convenientTime', '$driversLicenseNo', '$licenseExpiryDate', '$comments')";
                break;
                
            case 'box6': // Pick-up & Drop Service Form
                $pickupDate = isset($_POST['pickupDate']) ? mysqli_real_escape_string($conn, $_POST['pickupDate']) : '';
                $pickupTime = isset($_POST['pickupTime']) ? mysqli_real_escape_string($conn, $_POST['pickupTime']) : '';
                $dropDate = isset($_POST['dropDate']) ? mysqli_real_escape_string($conn, $_POST['dropDate']) : '';
                $dropTime = isset($_POST['dropTime']) ? mysqli_real_escape_string($conn, $_POST['dropTime']) : '';
    
                // Insert data into the database
                $sql = "INSERT INTO pickup_drop_form (name, email, mobile, city, car_model, carRegisterNo, pickup_date, pickup_time, drop_date, drop_time, comments) 
                    VALUES ('$name', '$email', '$mobile', '$city', '$carModel', '$carRegisterNo', '$pickupDate', '$pickupTime', '$dropDate', '$dropTime', '$comments')";
                break;

            
            case 'box7': // Case 'box7': Job Application Form    
                $position = isset($_POST['position']) ? mysqli_real_escape_string($conn, $_POST['position']) : '';
                $experience = isset($_POST['experience']) ? mysqli_real_escape_string($conn, $_POST['experience']) : '';
                $bio = isset($_POST['bio']) ? mysqli_real_escape_string($conn, $_POST['bio']) : '';
            
                // Handle file upload
                $resume = '';
                if (isset($_FILES['resume']) && $_FILES['resume']['error'] === UPLOAD_ERR_OK) {
                    $resumeName = basename($_FILES['resume']['name']);
                    $resumePath = '../resumes/' . $resumeName;

                    if (!file_exists('uploads/resumes')) {
                        mkdir('uploads/resumes', 0777, true);
                    }

                    if (move_uploaded_file($_FILES['resume']['tmp_name'], $resumePath)) {
                        $resume = $resumePath;
                    } else {
                        die("Failed to upload resume.");
                    }
                }
                // SQL Query to Insert Data into Database
                $sql = "INSERT INTO career_job_request (name, email, mobile, city, position, experience, resume, bio, comments) 
                    VALUES ('$name', '$email', '$mobile', '$city', '$position', '$experience', '$resume', '$bio', '$comments')";
                break;


            default:
                echo "<script>alert('Invalid form submission');</script>";
                exit;
        }

        // Execute Query
        if (mysqli_query($conn, $sql)) {
            echo "<script>alert('Details submitted successfully!');</script>";
        } else {
            echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css"/>
    <link rel="icon" href="../images/icon.png">
    <title>Services | AR</title>
    <link rel="stylesheet" href="../CSS/services1.css">
    <link rel="stylesheet" href="../CSS/header.css">
    <style>
        body {
            background: url('/AR Cars/images/back.jpg') no-repeat center center fixed;
            background-size: cover;
            overflow-y: scroll;
            overflow-x: hidden;
            font-family: math;
        }
    </style>
    <script type="text/javascript">
        // Show alert after form submission
        <?php if ($message): ?>
            window.onload = function() {
                alert("<?php echo $message; ?>");
            }
        <?php endif; ?>
    </script>

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

    <div class="service_bar">
        <ul>
            <li><a href="services.php#box1" onclick="toggleBox('box1')">Regular Services</a></li>
            <li><a href="services.php#box2" onclick="toggleBox('box2')">Accident Repair</a></li>
            <li><a href="services.php#box3" onclick="toggleBox('box3')">Extended Warranty</a></li>
            <li><a href="services.php#box4" onclick="toggleBox('box4')">Insurance</a></li>
            <li><a href="services.php#box5" onclick="toggleBox('box5')">Test Drive</a></li>
            <li><a href="services.php#box6" onclick="toggleBox('box6')">Pick-drop</a></li>
            <li><a href="services.php#box7" onclick="toggleBox('box7')">Career/Job</a></li>
        </ul>
    </div>

<div id="box1" class="box">
    <h2>Regular Services</h2>
    <div class="Regular_Services">
    
        <div class="left-side">
            <img src="../images/regular-services.png" alt="regular-services">
            <p>"Regular car servicing includes essential tasks like oil changes, wheel alignment, 
                and inspection or replacement of parts such as brakes, filters, and tires to ensure optimal 
                performance and safety. Routine maintenance helps prolong the vehicle's lifespan and 
                maintain efficiency. Additionally, it helps prevent unexpected breakdowns and ensures 
                your vehicle runs smoothly, providing a safer driving experience."
            </p>
        </div>
    
        <div class="right-side">
            <h2>Car Services Form</h2>
            <form action="services.php" method="post">
                <input type="hidden" name="formType" value="box1">

                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required><br><br>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required><br><br>

                <label for="mobile">Mobile No:</label>
                <input type="tel" id="mobile" name="mobile" pattern="^[987]\d{9}$" required><br><br>

                <label for="city">City:</label>
                <input type="text" id="city" name="city" required><br><br>

                <label for="carModel">Car Model:</label>
                <select id="carModel" name="carModel" required>
                    <option value=""> Select Your Car Model </option>
                    <option value="WagonR">WagonR</option>
                    <option value="Brezza">Brezza</option>
                    <option value="DZire">DZire</option>
                    <option value="Alto800">Alto800</option>
                    <option value="ECCO">ECCO</option>
                    <option value="Ertiga">Ertiga</option>
                    <option value="ECCO-Cargo">ECCO-Cargo</option>
                    <option value="Super_Carry">Super Carry</option>
                </select><br><br>
                    
                <label for="carRegisterNo">Car Register No:</label>
                <input type="text" id="carRegisterNo" name="carRegisterNo" placeholder="MH01AR2025" required><br><br>
                    
                <label>Services Required:</label>
                <input type="checkbox" name="services[]" value="Oil Change"> Oil Changing<br>
                <input type="checkbox" name="services[]" value="Wheel Alignment"> Wheel Alignment Set<br>
                <input type="checkbox" name="services[]" value="Filter Change"> Filter Change<br>
                <input type="checkbox" name="services[]" value="Brake Check"> Checking/Change Brake<br>
                <input type="checkbox" name="services[]" value="Battery Check"> Battery Check<br>
                <input type="checkbox" name="services[]" value="Cleaning"> External/Interior Cleaning<br>
                <input type="checkbox" name="services[]" value="Tire Replacement"> Replacing Tire<br>
                <input type="checkbox" name="services[]" value="Wiring Change"> Change Wiring<br>
                <input type="checkbox" name="services[]" value="Lights Check"> Checking Lights<br><br>
                    
                <label for="serviceDate">Service Date:</label>
                <input type="date" id="serviceDate" name="serviceDate"><br><br>
                
                <label for="comments"> Comments:</label>
                <textarea id="comments" name="comments"></textarea><br><br>
                    
                <input type="submit" value="Submit">
            </form>
        </div>
    </div>
</div>

<div id="box2" class="box">
    <h2>Accident Repair</h2>
    <div class="Regular_Services">
        <div class="left-side">
            <img src="../images/repair-car.jpg" alt="Accident Repair">
            <p>Accident repair services include fixing body damage, dent removal, repainting, and replacing damaged parts to restore the car's original condition. 
                The service ensures safety checks on key components like brakes, suspension, and airbags after repairs.
                Vehicles are repaired at our workshops, equipped with the best facilities, qualified and trained manpower and Maruti Genuine spare parts.
            </p>
        </div>
        <div class="right-side">
            <h2>Accident Repair Form</h2>
            <form action="services.php" method="post">
                <input type="hidden" name="formType" value="box2">

                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required><br><br>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required><br><br>

                <label for="mobile">Mobile No:</label>
                <input type="tel" id="mobile" name="mobile" pattern="^[987]\d{9}$" required><br><br>

                <label for="city">City:</label>
                <input type="text" id="city" name="city" required><br><br>

                <label for="carModel">Car Model:</label>
                    <select id="carModel" name="carModel" required>
                        <option value=""> Select Your Car Model </option>
                        <option value="WagonR">WagonR</option>
                        <option value="Brezza">Brezza</option>
                        <option value="DZire">DZire</option>
                        <option value="Alto800">Alto800</option>
                        <option value="ECCO">ECCO</option>
                        <option value="Ertiga">Ertiga</option>
                        <option value="ECCO-Cargo">ECCO-Cargo</option>
                        <option value="Super_Carry">Super Carry</option>
                    </select><br><br>

                <label for="carRegisterNo">Car Registration No:</label>
                <input type="text" id="carRegisterNo" name="carRegisterNo" placeholder="MH01AR2025" required><br><br>

                <label for="accidentDate">Accident Date:</label>
                <input type="date" id="accidentDate" name="accidentDate" required><br><br>

                <label for="accidentLocation">Accident Location:</label>
                <input type="text" id="accidentLocation" name="accidentLocation" required><br><br>

                <label>Repair Required:</label>
                <input type="checkbox" name="repair[]" value="Dent Removal"> Dent Removal<br>
                <input type="checkbox" name="repair[]" value="Body Repainting"> Body Repainting<br>
                <input type="checkbox" name="repair[]" value="Part Replacement"> Part Replacement<br>
                <input type="checkbox" name="repair[]" value="Reset Airbags"> Reset Airbags<br>
                <input type="checkbox" name="repair[]" value="Reset Wheel Alignment"> Reset Wheel Alignment<br>
                <input type="checkbox" name="repair[]" value="New Glass Fitting"> New Glass Fitting<br>
                <input type="checkbox" name="repair[]" value="New Door Fitting"> New Door Fitting<br>
                <input type="checkbox" name="repair[]" value="Engine Check"> Engine Check<br>
                <input type="checkbox" name="repair[]" value="Tire Changing"> Tire Changing<br>
                <input type="checkbox" name="repair[]" value="Total Car Loss"> Total Car Loss<br>
                <input type="checkbox" name="repair[]" value="Key Recovery"> Key Recovery<br><br>

                <label for="insuranceNumber">Insurance Number:</label>
                <input type="text" id="insuranceNumber" name="insuranceNumber" required><br><br>

                <label for="insuranceExpiryDate">Insurance Expiry Date:</label>
                <input type="date" id="insuranceExpiryDate" name="insuranceExpiryDate" required><br><br>

                <label for="serviceDate">Service Date:</label>
                <input type="date" id="serviceDate" name="serviceDate" required><br><br>

                <label for="comments">Comments:</label>
                <textarea id="comments" name="comments"></textarea><br><br>

                <input type="submit" value="Submit">
            </form>
        </div>        
    </div>
</div>

<div id="box3" class="box">
    <h2>Warranty Extended</h2>
    <div class="Regular_Services">
        <div class="left-side">
            <img src="../images/Extended-Warranty.png" alt="Extended Warranty">
            <p>Accident repair services include fixing body damage, dent removal, repainting, and replacing damaged parts to restore the car's original condition. 
                The service ensures safety checks on key components like brakes, suspension, and airbags after repairs.
                Vehicles are repaired at our workshops, equipped with the best facilities, qualified and trained manpower and Maruti Genuine spare parts.
            </p>
        </div>
        <div class="right-side">
            <h2>Warranty Extended Form</h2>
        
            <form action="services.php" method="post">
                <input type="hidden" name="formType" value="box3">

                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required><br><br>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required><br><br>

                <label for="mobile">Mobile No:</label>
                <input type="tel" id="mobile" name="mobile" pattern="^[987]\d{9}$" required><br><br>

                <label for="city">City:</label>
                <input type="text" id="city" name="city" required><br><br>

                <label for="carModel">Car Model:</label>
                    <select id="carModel" name="carModel" required>
                        <option value=""> Select Your Car Model </option>
                        <option value="WagonR">WagonR</option>
                        <option value="Brezza">Brezza</option>
                        <option value="DZire">DZire</option>
                        <option value="Alto800">Alto800</option>
                        <option value="ECCO">ECCO</option>
                        <option value="Ertiga">Ertiga</option>
                        <option value="ECCO-Cargo">ECCO-Cargo</option>
                        <option value="Super_Carry">Super Carry</option>
                    </select><br><br>

                <label for="carRegisterNo">Car Registration No:</label>
                <input type="text" id="carRegisterNo" name="carRegisterNo" placeholder="MH01AR2025" required><br><br>

                <label for="warrantyStartDate">Warranty Start Date:</label>
                <input type="date" id="warrantyStartDate" name="warrantyStartDate" required><br><br>

                <label for="warrantyEndDate">Warranty End Date:</label>
                <input type="date" id="warrantyEndDate" name="warrantyEndDate" required><br><br>

                <label>Warranty Package Type:</label>
                <input type="checkbox" name="warrantyPackage[]" value="Basic"> Basic<br>
                <input type="checkbox" name="warrantyPackage[]" value="Standard"> Standard<br>
                <input type="checkbox" name="warrantyPackage[]" value="Premium"> Premium<br><br>

                <label for="warrantyDuration">Warranty Extended Duration:</label>
                <select id="warrantyDuration" name="warrantyDuration" required>
                    <option value="">Select Duration</option>
                    <option value="1 Year">1 Year</option>
                    <option value="2 Years">2 Years</option>
                    <option value="3 Years">3 Years</option>
                </select><br><br>

                <label for="carCondition">Current Car Condition:</label>
                <select id="carCondition" name="carCondition" required>
                    <option value="">Select Condition</option>
                    <option value="No Accidents">No Accidents</option>
                    <option value="Minor Damage">Minor Damage</option>
                    <option value="Major Damage">Major Damage</option>
                </select><br><br>

                <label for="comments">Comments:</label>
                <textarea id="comments" name="comments"></textarea><br><br>

                <input type="submit" value="Submit">
            </form>

        </div>        
    </div>
</div>

<div id="box4" class="box">
    <h2>Insurance</h2>
    <div class="Regular_Services">
        <div class="left-side">
            <img src="../images/Insurance.png" alt="Insurance">
            <p>Car insurance is a contract between the car owner and the insurance company that provides financial protection in case of accidents, theft, or damage 
                to the vehicle. The policy typically covers liabilities, repairs, and medical costs. Insurance details include the policy number, insurance company, 
                coverage amount, premium amount, and policy duration. It is important to keep insurance updated and maintain a good claims history for better coverage options.
            </p>
        </div>
        <div class="right-side">
            <h2>Insurance Details Form</h2>
            <form action="services.php" method="post">
                <input type="hidden" name="formType" value="box4">

                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required><br><br>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required><br><br>

                <label for="mobile">Mobile No:</label>
                <input type="tel" id="mobile" name="mobile" pattern="^[987]\d{9}$" required><br><br>

                <label for="city">City:</label>
                <input type="text" id="city" name="city" required><br><br>

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

                <label for="carRegisterNo">Car Registration No:</label>
                <input type="text" id="carRegisterNo" name="carRegisterNo" placeholder="MH01AR2025" required><br><br>

                <label for="carManufYear">Car Manufacture Year:</label>
                <select id="carManufYear" name="carManufYear" required>
                    <option value="">Select Year</option>
                    <option value="2020">2020</option>
                    <option value="2021">2021</option>
                    <option value="2022">2022</option>
                    <option value="2023">2023</option>
                    <option value="2024">2024</option>
                </select><br><br>

                <label for="insuranceOption">Insurance:</label>
                <select id="insuranceOption" name="insuranceOption" required>
                    <option value="">Select Insurance Option</option>
                    <option value="New">New</option>
                    <option value="Renew">Renew</option>
                </select><br><br>

                <label>Insurance Type:</label>
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

                <label for="insuranceStartDate">Insurance Start Date:</label>
                <input type="date" id="insuranceStartDate" name="insuranceStartDate" required><br><br>

                <label for="insuranceEndDate">Insurance End Date:</label>
                <input type="date" id="insuranceEndDate" name="insuranceEndDate" required><br><br>

                <label for="comments">Additional Comments:</label>
                <textarea id="comments" name="comments" rows="4" cols="50"></textarea><br><br>

                <input type="submit" value="Submit">
            </form>
        </div>        
    </div>
</div>

<div id="box5" class="box">
    <h2>Test Drive</h2>
    <div class="Regular_Services">
        <div class="left-side">
            <img src="../images/test-drive.png" alt="Test Drive">
            <p>A test drive allows customers to experience a vehicle's performance, comfort, and features firsthand before making a purchase decision. It provides an 
                opportunity to evaluate aspects like handling, braking, interior comfort, and technology. Test drives are typically arranged at authorized dealerships 
                and are an essential step in choosing the right car
            </p>
        </div>
        <div class="right-side">
            <h2>Test Drive Booking Form</h2>
        
            <form action="services.php" method="post">
                <input type="hidden" name="formType" value="box5">

                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required><br><br>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required><br><br>

                <label for="mobile">Mobile No:</label>
                <input type="tel" id="mobile" name="mobile" pattern="^[987]\d{9}$" required><br><br>

                <label for="city">City:</label>
                <input type="text" id="city" name="city" required><br><br>

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

                <label for="location">Location:</label>
                <input type="text" id="location" name="location" required><br><br>

                <label for="convenientDate">Convenient Date:</label>
                <input type="date" id="convenientDate" name="convenientDate" required><br><br>

                <label for="convenientTime">Convenient Time:</label>
                <input type="time" id="convenientTime" name="convenientTime" required><br><br>

                <label for="driversLicenseNo">Driver's License Number:</label>
                <input type="text" id="driversLicenseNo" name="driversLicenseNo" required><br><br>

                <label for="licenseExpiryDate">License Expiry Date:</label>
                <input type="date" id="licenseExpiryDate" name="licenseExpiryDate" required><br><br>

                <label for="comments">Comments:</label><br>
                <textarea id="comments" name="comments" rows="4" cols="50"></textarea><br><br>

                <input type="submit" value="Submit">
            </form>
        </div>        
    </div>
</div>

<div id="box6" class="box">
    <h2>Pick & Drop</h2>
    <div class="Regular_Services">
        <div class="left-side">
            <img src="../images/pickup-drop.png" alt="Pick & Drop">
            <p>Our Pick & Drop service ensures hassle-free car servicing at your convenience. Schedule a service appointment, and we'll handle the pick-up and return 
                of your car. This service saves time, eliminates travel worries, and ensures your car gets professional care. Ideal for busy customers, it includes 
                routine maintenance, repairs, or emergency services.
            </p>
        </div>
        <div class="right-side">
            <h2>Pick-up & Drop Service Form</h2>
        
            <form action="services.php" method="post" style="line-height: 1.8;">
                <input type="hidden" name="formType" value="box6">

                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required><br>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required><br>

                <label for="mobile">Mobile No:</label>
                <input type="tel" id="mobile" name="mobile" pattern="^[987]\d{9}$" required><br>

                <label for="city">City:</label>
                <input type="text" id="city" name="city" required><br>

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
                </select><br>

                <label for="carRegisterNo">Car Registration No:</label>
                <input type="text" id="carRegisterNo" name="carRegisterNo" required><br>

                <label for="pickupDate">Pickup Date:</label>
                <input type="date" id="pickupDate" name="pickupDate" required><br>

                <label for="pickupTime">Pickup Time:</label>
                <input type="time" id="pickupTime" name="pickupTime" required><br>

                <label for="dropDate">Drop Date:</label>
                <input type="date" id="dropDate" name="dropDate" required><br>

                <label for="dropTime">Drop Time:</label>
                <input type="time" id="dropTime" name="dropTime" required><br>

                <label for="comments">Comments:</label>
                <textarea id="comments" name="comments" rows="4" cols="50"></textarea><br>

                <input type="submit" value="Submit">
            </form>
        </div>                
    </div>
</div>

<div id="box7" class="box">
    <h2>Career/Job</h2>
    <div class="Regular_Services">
        <div class="left-side">
            <img src="../images/career-img.jpg" alt="Career/Job">
            <p>Join our team and build your career in an innovative and dynamic environment. We offer exciting opportunities across various roles, with growth 
                potential and professional development. Whether you're a fresh graduate or an experienced professional, discover your future with us and contribute to 
                our success.
            </p>
        </div>
        <div class="right-side">
            <h2>Job Application Form</h2>
        
            <form action="services.php" method="post" enctype="multipart/form-data" style="line-height: 1.8;">
                <input type="hidden" name="formType" value="box7">

                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required><br>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required><br>

                <label for="mobile">Mobile No:</label>
                <input type="tel" id="mobile" name="mobile" pattern="^[987]\d{9}$" required><br>

                <label for="city">City:</label>
                <input type="text" id="city" name="city" required><br>

                <label for="position">Position Applying For:</label>
                <input type="text" id="position" name="position" required><br>

                <label for="experience">Years of Experience:</label>
                <input type="number" id="experience" name="experience" min="0" required><br>

                <label for="resume">Upload Resume:</label>
                <input type="file" id="resume" name="resume" accept=".pdf,.doc,.docx" required><br>

                <label for="bio">Bio (Short Introduction):</label>
                <textarea id="bio" name="bio" rows="4" cols="50" required></textarea><br>

                <label for="comments">Comments:</label>
                <textarea id="comments" name="comments" rows="4" cols="50"></textarea><br>

                <input type="submit" value="Submit">
            </form>

        </div>        
    </div>
</div>

<script>
                // Automatically open section if query parameter is passed
                window.onload = () => {
            const urlParams = new URLSearchParams(window.location.search);
            const section = urlParams.get('section');
            if (section) {
                toggleBox(section);
            }
        };

        // Toggle visibility of sections
        function toggleBox(boxId) {
            const boxes = document.querySelectorAll('.box');
            boxes.forEach(box => box.classList.remove('active'));
            const selectedBox = document.getElementById(boxId);
            if (selectedBox) {
                selectedBox.classList.add('active');
            }
        }
</script>
</body> 
</html>


