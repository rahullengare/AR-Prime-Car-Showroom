<?php
    session_start();

    // Include the connection file
    include('connection.php');

    // Check if admin is logged in
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        header('Location: login.php');
        exit();
    }

    // Retrieve admin username from the session
    $admin_name = $_SESSION['admin_username'] ?? 'Admin';

    // Initialize filter and sorting variables
    $sort_order = $_GET['sort_date'] ?? 'DESC'; // Default to newest first
    $filter_date = $_GET['filter_date'] ?? '';
    $date_filter_type = $_GET['date_filter_type'] ?? '';
    $start_date = $_GET['start_date'] ?? '';
    $end_date = $_GET['end_date'] ?? '';
    $search = $_GET['search'] ?? '';
    $carModel = $_GET['carModel'] ?? '';
    $limit = $_GET['limit'] ?? '5'; // Default limit to 5 records

    // Validate and sanitize the limit
    if (!is_numeric($limit) && $limit !== 'All') {
        $limit = '5'; // Default fallback limit
    }

    // Get available car models for filtering
    $car_model_query = "SELECT DISTINCT carModel FROM car_bookingform1";
    $car_model_result = mysqli_query($conn, $car_model_query);
    if (!$car_model_result) {
        die('Error fetching car models: ' . mysqli_error($conn));
    }

    // Build the SQL query
    $query = "SELECT id, carModel, car_color, booking_date, delivery_datetime, car_type, insurance_type, insurance_company, 
                    transmission, standard_accessories, optional_accessories, technological_addons, protective_accessories, 
                    luxury_addons, loan_option, loan_amount, loan_tenure, car_loan_provider, comments 
            FROM car_bookingform1 
            WHERE 1";

    // Add car model filter
    if ($carModel) {
        $query .= " AND carModel = '" . mysqli_real_escape_string($conn, $carModel) . "'";
    }

    // Apply search filters
    if ($search) {
        $query .= " AND (name LIKE '%$search%' OR mobile LIKE '%$search%' OR email LIKE '%$search%' OR city LIKE '%$search%')";
    }

        // Add date filters
        if ($filter_date) {
            if ($date_filter_type == 'exact') {
                $query .= " AND DATE(booking_date) = '" . mysqli_real_escape_string($conn, $filter_date) . "'";
            } elseif ($date_filter_type == 'before') {
                $query .= " AND booking_date <= '" . mysqli_real_escape_string($conn, $filter_date) . " 23:59:59'";
            } elseif ($date_filter_type == 'after') {
                $query .= " AND booking_date >= '" . mysqli_real_escape_string($conn, $filter_date) . " 00:00:00'";
            }
        }

        // Add start and end date filters
        if ($start_date) {
            $query .= " AND booking_date >= '" . mysqli_real_escape_string($conn, $start_date) . "'";
        }
        if ($end_date) {
            $query .= " AND booking_date <= '" . mysqli_real_escape_string($conn, $end_date) . "'";
        }

    // Apply sorting order
    $query .= " ORDER BY booking_date $sort_order";

    // Apply limit
    if ($limit !== 'All') {
        $query .= " LIMIT " . (int)$limit;
    }

    // Execute the query
    $result1 = mysqli_query($conn, $query);

    // Check for results
    $status1 = ($result1 && mysqli_num_rows($result1) > 0) ? "" : "No records found!";

    // Close the database connection
    mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../images/icon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css"/>
    <title>Car Booked | AR Admin</title>
    <link rel="stylesheet" href="../CSS/admin.css"> 
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
                <a href="index.php" style="background-color: yellow;"> Admin Home</a>
                <a href="car-bookings-data.php">Car Bookings</a>
                <a href="car-booked-owner-data.php">Car Booked Owners</a>
                <a href="payment-data.php">Payment Details</a>
            </div>
        </div>
        <div class="connect-db">
            <div class="user">
                <h4><span>Welcome, </span><i class="fa fa-user-circle"></i> <?php echo htmlspecialchars($admin_name); ?></h4>
                <button><a href="logout.php">Logout</a></button>
            </div> 
        </div>
    </div>

    <nav>
        <a href="enquiry-data.php">Enquiries</a>
        <a href="contact-requests-data.php">Contact-Request</a>
        <a href="regular-services-data.php">Regular-Services</a>
        <a href="repair-requests-data.php">Repair-Requests</a>
        <a href="warranty-requests-data.php">Warranty-Requests</a>
        <a href="insurance-requests-data.php">Insurance-Requests</a>
        <a href="test-drive-requests.php">Test-Drive-Requests</a>
        <a href="pickup-drop-requests.php">Pickup-Drop-Requests</a>
        <a href="career-job-requests.php">Job-Requests</a>
    </nav>
<!-- HEADER SECTION END -->

<!-- TABLE SECTION START -->
    <div class="table-container">
        <p class="status-message"><?php echo htmlspecialchars($status1); ?></p>
        <h2 class="FormName">Car Details Form - BookingForm1</h2>

        <!-- Sorting and Filters Section -->
        <form method="GET" action="car-bookings-data.php" class="filter-container">
            <div>
                <label>Sort by ID:</label>
                <select name="sort_date" onchange="this.form.submit()">
                    <option value="DESC" <?php echo ($sort_order == 'DESC') ? 'selected' : ''; ?>>Newest First</option>
                    <option value="ASC" <?php echo ($sort_order == 'ASC') ? 'selected' : ''; ?>>Oldest First</option>
                </select>
            </div>
            <div>
                <label>Filter by Booking Date:</label>
                <input type="date" name="filter_date" value="<?php echo htmlspecialchars($filter_date); ?>">
                <select name="date_filter_type" onchange="this.form.submit()">
                    <option value="">Select</option>
                    <option value="exact" <?php echo ($date_filter_type == 'exact') ? 'selected' : ''; ?>>Exact Date</option>
                    <option value="before" <?php echo ($date_filter_type == 'before') ? 'selected' : ''; ?>>Before Date</option>
                    <option value="after" <?php echo ($date_filter_type == 'after') ? 'selected' : ''; ?>>After Date</option>
                </select>
            </div>
            <div>
                <label>Start Date:</label>
                <input type="date" name="start_date" value="<?php echo htmlspecialchars($start_date); ?>">
            </div>
            <div>
                <label>End Date:</label>
                <input type="date" name="end_date" value="<?php echo htmlspecialchars($end_date); ?>">
            </div>
            <div>
                <label>Show:</label>
                <select name="limit" onchange="this.form.submit()">
                    <option value="5" <?php echo ($limit == '5') ? 'selected' : ''; ?>>5</option>
                    <option value="10" <?php echo ($limit == '10') ? 'selected' : ''; ?>>10</option>
                    <option value="15" <?php echo ($limit == '15') ? 'selected' : ''; ?>>15</option>
                    <option value="All" <?php echo ($limit === 'All') ? 'selected' : ''; ?>>All</option>
                </select>
            </div>
            <div>
                <label>Car Model:</label>
                <select name="carModel">
                    <option value="">All Models</option>
                    <?php while ($car_row = mysqli_fetch_assoc($car_model_result)) {
                        echo "<option value='" . htmlspecialchars($car_row['carModel']) . "' " . (($car_row['carModel'] == $carModel) ? 'selected' : '') . ">" . htmlspecialchars($car_row['carModel']) . "</option>";
                    } ?>
                </select>
            </div>
            <div>
                <label>Search:</label>
                <input type="text" name="search" placeholder="Search Email." value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <div>
                <button type="submit">Apply Filters</button>
                <button type="button" class="clear-btn" onclick="window.location.href='car-bookings-data.php'">Clear Filters</button>
            </div>
        </form>

        <!-- BookingForm1 Table here -->
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Car Model</th>
                    <th>Car Color</th>
                    <th>Booking Date</th>
                    <th>Delivery Date and Time</th>
                    <th>Car Type</th>
                    <th>Insurance Type</th>
                    <th>Insurance Company</th>
                    <th>Transmission</th>
                    <th>Standard Accessories</th>
                    <th>Optional Accessories</th>
                    <th>Technological Addons</th>
                    <th>Protective Accessories</th>
                    <th>Luxury Addons</th>
                    <th>Loan Option</th>
                    <th>Loan Amount</th>
                    <th>Loan Tenure</th>
                    <th>Car Loan Provider</th>
                    <th>Comments</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result1 && mysqli_num_rows($result1) > 0) {
                    while ($row = mysqli_fetch_assoc($result1)) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['carModel']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['car_color']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['booking_date']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['delivery_datetime'] ?? 'N/A') . "</td>";
                        echo "<td>" . htmlspecialchars($row['car_type']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['insurance_type']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['insurance_company']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['transmission']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['standard_accessories']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['optional_accessories']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['technological_addons']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['protective_accessories']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['luxury_addons']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['loan_option']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['loan_amount']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['loan_tenure']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['car_loan_provider']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['comments']) . "</td>";
                        echo "<td><a href='delete.php?id=" . $row['id'] . "&page=car_booking-1' class='delete-btn' onclick='return confirm(\"Are you sure you want to delete?\");'>Delete</a></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='22'>No booking records found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
<!-- TABLE SECTION END -->
</body>
</html>
