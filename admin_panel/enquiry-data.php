<?php
    session_start();
    include('connection.php');

    // Check if admin is logged in
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        header('Location: login.php');
        exit();
    }

    // Get admin username
    $admin_name = $_SESSION['admin_username'] ?? 'Admin';

    // Handle record deletion
    if (isset($_GET['delete_id'])) {
        $delete_id = mysqli_real_escape_string($conn, $_GET['delete_id']);
        $delete_query = "DELETE FROM enquiries WHERE id = '$delete_id'";
        if (mysqli_query($conn, $delete_query)) {
            header("Location: enquiry-data.php");
            exit();
        } else {
            $status = "Error: Could not delete the record.";
        }
    }

    // Fetch car models for dropdown
    $car_model_query = "SELECT DISTINCT carModel FROM enquiries";
    $car_model_result = mysqli_query($conn, $car_model_query);

    // Initialize filter variables
    $sort_order = $_GET['sort_order'] ?? 'DESC';
    $filter_date = $_GET['filter_date'] ?? '';
    $date_filter_type = $_GET['date_filter_type'] ?? '';
    $start_date = $_GET['start_date'] ?? '';
    $end_date = $_GET['end_date'] ?? '';
    $search = $_GET['search'] ?? '';
    $car_model = $_GET['car_model'] ?? '';
    $limit = $_GET['limit'] ?? '5';

    // Build SQL query with filters
    $query = "SELECT id, fullName, email, phone, carModel, message, submittedAt FROM enquiries WHERE 1";

    // Add search filter
    if ($search) {
        $query .= " AND (fullName LIKE '%" . mysqli_real_escape_string($conn, $search) . "%' 
                    OR phone LIKE '%" . mysqli_real_escape_string($conn, $search) . "%' 
                    OR email LIKE '%" . mysqli_real_escape_string($conn, $search) . "%')";
    }

    // Add car model filter
    if ($car_model) {
        $query .= " AND carModel = '" . mysqli_real_escape_string($conn, $car_model) . "'";
    }

    // Add date filters
    if ($filter_date) {
        $filter_date = mysqli_real_escape_string($conn, $filter_date);
        if ($date_filter_type == 'exact') {
            $query .= " AND DATE(submittedAt) = '$filter_date'";
        } elseif ($date_filter_type == 'before') {
            $query .= " AND submittedAt <= '$filter_date 23:59:59'";
        } elseif ($date_filter_type == 'after') {
            $query .= " AND submittedAt >= '$filter_date 00:00:00'";
        }
    }
    if ($start_date) {
        $query .= " AND submittedAt >= '" . mysqli_real_escape_string($conn, $start_date) . "'";
    }
    if ($end_date) {
        $query .= " AND submittedAt <= '" . mysqli_real_escape_string($conn, $end_date) . "'";
    }

    // Add sorting and limit
    $query .= " ORDER BY id $sort_order";
    if ($limit !== 'All') {
        $query .= " LIMIT " . (int)$limit;
    }

    // Execute query
    $result = mysqli_query($conn, $query);
    $status = (mysqli_num_rows($result) > 0) ? "" : "No Record found!";

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
    <title>view Enquiries | AR Admin</title>
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
    <h2 class="FormName">All Enquiries Forms Data</h2>

    <!-- Sorting and Filters Section -->
    <form method="GET" action="enquiry-data.php" class="filter-container">
        <div>
        <label>Sort by ID:</label>
        <select name="sort_order" onchange="this.form.submit()">
            <option value="DESC" <?php echo ($sort_order == 'DESC') ? 'selected' : ''; ?>>Newest First</option>
            <option value="ASC" <?php echo ($sort_order == 'ASC') ? 'selected' : ''; ?>>Oldest First</option>
        </select>
        </div>

        <div>
            <label>Filter by Submitted Date:</label>
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
            <label>Show Rows:</label>
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
                    echo "<option value='" . htmlspecialchars($car_row['carModel']) . "' " . (($car_row['carModel'] == $car_model) ? 'selected' : '') . ">" . htmlspecialchars($car_row['carModel']) . "</option>";
                } ?>
            </select>
        </div>
        <div>
            <label>Search:</label>
            <input type="text" name="search" placeholder="Search Name, Email, Phone." value="<?php echo htmlspecialchars($search); ?>">
        </div>
        <div>
            <button type="submit">Apply Filters</button>
            <button type="button" class="clear-btn" onclick="window.location.href='enquiry-data.php'">Clear Filters</button>
        </div>
    </form>

    <!-- Table to Display Data -->
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Car Model</th>
                <th>Message</th>
                <th>Submitted At</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php
        if ($result && mysqli_num_rows($result) > 0) {
            while ($message = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($message['id']) . "</td>";
                echo "<td>" . htmlspecialchars($message['fullName']) . "</td>";
                echo "<td>" . htmlspecialchars($message['email']) . "</td>";
                echo "<td>" . htmlspecialchars($message['phone']) . "</td>";
                echo "<td>" . htmlspecialchars($message['carModel']) . "</td>";
                echo "<td>" . htmlspecialchars($message['message']) . "</td>";
                echo "<td>" . htmlspecialchars($message['submittedAt']) . "</td>";  
                echo "<td><a href='enquiry-data.php?delete_id=" . $message['id'] . "' class='delete-btn' onclick='return confirm(\"Are you sure you want to delete?\");'>Delete</a></td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='8'>No enquiries found.</td></tr>";
        }
        ?>
        </tbody>
    </table>
</div>

</body>
</html>
