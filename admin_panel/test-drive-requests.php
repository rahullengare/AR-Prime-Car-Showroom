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

    // Initialize filter variables
    $sort_order = $_GET['sort_order'] ?? 'DESC';
    $search = $_GET['search'] ?? '';
    $car_model = $_GET['car_model'] ?? '';
    $limit = $_GET['limit'] ?? '5';
    $date_filter = $_GET['date_filter'] ?? '';
    $date_value = $_GET['date_value'] ?? '';
    $start_date = $_GET['start_date'] ?? '';
    $end_date = $_GET['end_date'] ?? '';

    // Validate $sort_order
    $valid_sort_orders = ['ASC', 'DESC'];
    $sort_order = in_array(strtoupper($sort_order), $valid_sort_orders) ? strtoupper($sort_order) : 'DESC';

    // Build the SQL query
    $query = "SELECT id, name, email, mobile, city, car_model, location, convenient_date, convenient_time, drivers_license_no, license_expiry_date, comments FROM test_drive_requests WHERE 1";

    // Apply filters
    if ($search) {
        $query .= " AND (name LIKE '%$search%' OR mobile LIKE '%$search%' OR email LIKE '%$search%' OR city LIKE '%$search%')";
    }

    // Add car model filter
    if ($car_model) {
        $query .= " AND car_model = '" . mysqli_real_escape_string($conn, $car_model) . "'";
    }

    // Add submission date filter
    if (!empty($date_filter) && !empty($date_value)) {
        if ($date_filter === 'exact') {
            $query .= " AND DATE(convenient_date) = '" . mysqli_real_escape_string($conn, $date_value) . "'";
        } elseif ($date_filter === 'before') {
            $query .= " AND DATE(convenient_date) < '" . mysqli_real_escape_string($conn, $date_value) . "'";
        } elseif ($date_filter === 'after') {
            $query .= " AND DATE(convenient_date) > '" . mysqli_real_escape_string($conn, $date_value) . "'";
        }
    }

    // Add start and end date filters
    if (!empty($start_date)) {
        $query .= " AND DATE(convenient_date) >= '" . mysqli_real_escape_string($conn, $start_date) . "'";
    }
    if (!empty($end_date)) {
        $query .= " AND DATE(convenient_date) <= '" . mysqli_real_escape_string($conn, $end_date) . "'";
    }

    // Add sorting and limit
    $query .= " ORDER BY id $sort_order";
    if ($limit !== 'All') {
        $query .= " LIMIT " . (int)$limit;
    }

    // Execute the query
    $result = mysqli_query($conn, $query);

    // Check for query errors
    if (!$result) {
        die("Query failed: " . mysqli_error($conn));
    }

    // Fetch car models for filter dropdown
    $car_model_query = "SELECT DISTINCT car_model FROM test_drive_requests WHERE car_model IS NOT NULL";
    $car_model_result = mysqli_query($conn, $car_model_query);

    if (!$car_model_result) {
        die("Car Model Query failed: " . mysqli_error($conn));
    }

    // Status message
    $status = (mysqli_num_rows($result) > 0) ? "" : "No test drive requests found!";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../images/icon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">
    <title>Test Drive Requests | AR Admin</title>
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

    <!-- Navigation for Admin Panel -->
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
        <p class="status-message"><strong><?php echo $status; ?></strong></p>
        <h2 class="FormName">Test Drive Requests</h2>

        <!-- Sorting and Filters Section -->
        <form method="GET" action="test-drive-requests.php" class="filter-container">
            <div>
                <label>Sort by ID:</label>
                <select name="sort_order" onchange="this.form.submit()">
                    <option value="DESC" <?php echo ($sort_order == 'DESC') ? 'selected' : ''; ?>>Newest First</option>
                    <option value="ASC" <?php echo ($sort_order == 'ASC') ? 'selected' : ''; ?>>Oldest First</option>
                </select>
            </div>
            <div>
                <label> Filter by Test Drive Date:</label>
                <input type="date" name="date_value" value="<?php echo htmlspecialchars($date_value); ?>">
                <select name="date_filter">
                    <option value="">Select</option>
                    <option value="exact" <?php echo ($date_filter == 'exact') ? 'selected' : ''; ?>>Exact</option>
                    <option value="before" <?php echo ($date_filter == 'before') ? 'selected' : ''; ?>>Before</option>
                    <option value="after" <?php echo ($date_filter == 'after') ? 'selected' : ''; ?>>After</option>
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
                <label>Show Rows:</label>
                <select name="limit" onchange="this.form.submit()">
                    <option value="5" <?php echo ($limit == '5') ? 'selected' : ''; ?>>5</option>
                    <option value="10" <?php echo ($limit == '10') ? 'selected' : ''; ?>>10</option>
                    <option value="15" <?php echo ($limit == '15') ? 'selected' : ''; ?>>15</option>
                    <option value="All" <?php echo ($limit == 'All') ? 'selected' : ''; ?>>All</option>
                </select>
            </div>
            <div>
                <label>Search:</label>
                <input type="text" name="search" placeholder="Search Name, Email, Phone." value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <div>
                <label>Car Model:</label>
                <select name="car_model">
                    <option value="">All Models</option>
                    <?php while ($car_row = mysqli_fetch_assoc($car_model_result)) {
                        echo "<option value='" . htmlspecialchars($car_row['car_model']) . "' " . (($car_row['car_model'] == $car_model) ? 'selected' : '') . ">" . htmlspecialchars($car_row['car_model']) . "</option>";
                    } ?>
                </select>
            </div>
            <div>
                <button type="submit">Apply Filters</button>
                <button type="button" class="clear-btn" onclick="window.location.href='test-drive-requests.php'">Clear Filters</button>
            </div>
        </form>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Mobile</th>
                    <th>City</th>
                    <th>Car Model</th>
                    <th>Location</th>
                    <th>Test Drive Date</th>
                    <th>Test Drive Time</th>
                    <th>Driver's License No</th>
                    <th>License Expiry Date</th>
                    <th>Comments</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Display fetched data
                if ($result && mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['mobile']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['city']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['car_model']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['location']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['convenient_date']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['convenient_time']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['drivers_license_no']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['license_expiry_date']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['comments']) . "</td>";
                        echo "<td><a href='delete.php?id=" . $row['id'] . "&page=test_drive' class='delete-btn' onclick='return confirm(\"Are you sure you want to delete?\");'>Delete</a></td>";
                        echo "</tr>";
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
    <!-- TABLE SECTION END -->
</body>
</html>
