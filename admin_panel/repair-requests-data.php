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

    // Default values for sorting and filtering
    $sort_order = $_GET['sort_id'] ?? 'DESC';
    $filter_date = $_GET['filter_date'] ?? '';
    $date_filter_type = $_GET['date_filter_type'] ?? '';
    $start_date = $_GET['start_date'] ?? '';
    $end_date = $_GET['end_date'] ?? '';
    $search = $_GET['search'] ?? '';
    $car_model = $_GET['car_model'] ?? '';
    $limit = $_GET['limit'] ?? '5';

    // Validate sorting order
    $valid_sort_orders = ['ASC', 'DESC'];
    if (!in_array(strtoupper($sort_order), $valid_sort_orders)) {
        $sort_order = 'DESC';
    }

    // Validate limit
    if (!is_numeric($limit) && $limit !== 'All') {
        $limit = 5; // Default limit if invalid
    }

    // Fetch car models for the filter
    $car_model_query = "SELECT DISTINCT car_model FROM accident_repair_form";
    $car_model_result = mysqli_query($conn, $car_model_query);

    if (!$car_model_result) {
        die("Error fetching car models: " . mysqli_error($conn));
    }

    // Building the query
    $query = "SELECT * FROM accident_repair_form WHERE 1";

    // Add date filters
    if ($filter_date) {
        $filter_date = mysqli_real_escape_string($conn, $filter_date);
        if ($date_filter_type === 'exact') {
            $query .= " AND DATE(accident_date) = '$filter_date'";
        } elseif ($date_filter_type === 'before') {
            $query .= " AND accident_date <= '$filter_date 23:59:59'";
        } elseif ($date_filter_type === 'after') {
            $query .= " AND accident_date >= '$filter_date 00:00:00'";
        }
    }

    if ($start_date) {
        $start_date = mysqli_real_escape_string($conn, $start_date);
        $query .= " AND accident_date >= '$start_date'";
    }

    if ($end_date) {
        $end_date = mysqli_real_escape_string($conn, $end_date);
        $query .= " AND accident_date <= '$end_date'";
    }

    // Add search condition
    if ($search) {
        $search = mysqli_real_escape_string($conn, $search);
        $query .= " AND (name LIKE '%$search%' 
                        OR mobile LIKE '%$search%' 
                        OR email LIKE '%$search%' 
                        OR city LIKE '%$search%')";
    }

    // Add car model filter
    if ($car_model) {
        $car_model = mysqli_real_escape_string($conn, $car_model);
        $query .= " AND car_model = '$car_model'";
    }

    // Apply sorting
    $query .= " ORDER BY service_date $sort_order";

    // Apply limit
    if ($limit !== 'All') {
        $query .= " LIMIT " . intval($limit);
    }

    // Execute the query
    $result = mysqli_query($conn, $query);

    if (!$result) {
        die("Error executing query: " . mysqli_error($conn));
    }

    // Status message
    $status = ($result && mysqli_num_rows($result) > 0) ? "" : "No repair requests found!";

    // Close the connection
    mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../images/icon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">
    <title>Repair Requests | AR Admin</title>
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

<!-- FILTER SECTION START -->
<div class="table-container">
    <p class="status-message"><strong><?php echo $status; ?></strong></p>
    <h2 class="FormName">Accident Repair Requests</h2>

    <!-- Sorting and Filters Section -->
    <form method="GET" action="repair-requests-data.php" class="filter-container">
        <!-- Filter inputs (sorting, date, search, car model, limit) -->
        <div>
            <label>Sort by ID:</label>
            <select name="sort_id" onchange="this.form.submit()">
                <option value="DESC" <?php echo ($sort_order == 'DESC') ? 'selected' : ''; ?>>Newest First</option>
                <option value="ASC" <?php echo ($sort_order == 'ASC') ? 'selected' : ''; ?>>Oldest First</option>
            </select>
        </div>
        <div>
            <label>Filter by Accident Date:</label>
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
                <option value="All" <?php echo ($limit == 'All') ? 'selected' : ''; ?>>All</option>
            </select>
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
            <label>Search:</label>
            <input type="text" name="search" placeholder="Search Name,Email,Phone,City." value="<?php echo htmlspecialchars($search); ?>">
        </div>
        <div>
            <button type="submit">Apply Filters</button>
            <button type="button" class="clear-btn" onclick="window.location.href='repair-requests-data.php'">Clear Filters</button>
        </div>
    </form>
<!-- FILTER SECTION END -->

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Mobile</th>
                <th>City</th>
                <th>Car Model</th>
                <th>Car Registration No</th>
                <th>Accident Date</th>
                <th>Accident Location</th>
                <th>Repair Required</th>
                <th>Insurance Number</th>
                <th>Insurance Expiry Date</th>
                <th>Service Date</th>
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
                    echo "<td>" . htmlspecialchars($row['car_register_no']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['accident_date']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['accident_location']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['repair_required'] ?? '') . "</td>"; // Avoid undefined index
                    echo "<td>" . htmlspecialchars($row['insurance_number']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['insurance_expiry_date']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['service_date']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['comments']) . "</td>";
                    echo "<td><a href='delete.php?id=" . $row['id'] . "&page=repair' class='delete-btn' onclick='return confirm(\"Are you sure you want to delete?\");'>Delete</a></td>";                    
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
