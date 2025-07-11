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
    $sort_order = $_GET['sort_id'] ?? 'DESC';  // Default to descending (newest first)
    $search = $_GET['search'] ?? '';
    $limit = $_GET['limit'] ?? '5';

    // Build the SQL query based on filters
    $query = "SELECT id, full_name, contact_number, email, dob, occupation, 
                     city, street_address, id_proof_type, id_proof_image, 
                    id_proof_number, preferred_communication, preferred_delivery, comments
            FROM car_bookingform2 WHERE 1";

    // Add search filter
    if ($search) {
        $search_safe = mysqli_real_escape_string($conn, $search);
        $query .= " AND (full_name LIKE '%$search_safe%' 
                    OR email LIKE '%$search_safe%' 
                    OR city LIKE '%$search_safe%' 
                    OR contact_number LIKE '%$search_safe%')";
    }

    // Add sorting by ID (newest or oldest)
    $query .= " ORDER BY id $sort_order";

    // Add limit to query
    if ($limit != 'All') {
        $query .= " LIMIT " . (int)$limit;
    }

    // Execute query
    $result2 = mysqli_query($conn, $query);

    // Check for results
    $status3 = ($result2 && mysqli_num_rows($result2) > 0) ? "" : "No records found!";

    // Fetch car models for dropdown (if needed in the future)
    $car_model_query = "SELECT DISTINCT car_model FROM car_bookingform3";
    $car_model_result = mysqli_query($conn, $car_model_query);

    // Close the connection
    mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../images/icon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css"/>
    <title>Car Booked Owner| AR Admin</title>
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
        <a href="payment-data.php">Enquiries</a>
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

<div class="table-container">
    <p class="status-message"><?php echo htmlspecialchars($status3); ?></p>
    <h2 class="FormName">Car Owner Details Form - BookingForm2</h2>

    <!-- Sorting and Filters Section -->
    <form method="GET" action="car-booked-owner-data.php" class="filter-container">
        <div>
            <label>Sort by ID:</label>
            <select name="sort_id" onchange="this.form.submit()">
                <option value="DESC" <?php echo ($sort_order == 'DESC') ? 'selected' : ''; ?>>Newest First</option>
                <option value="ASC" <?php echo ($sort_order == 'ASC') ? 'selected' : ''; ?>>Oldest First</option>
            </select>
        </div>
        <div>
            <label>Search:</label>
            <input type="text" name="search" placeholder="Search Name,Email,City,Phone." value="<?php echo htmlspecialchars($search); ?>">
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
            <button type="submit">Apply Filters</button>
            <button type="button" class="clear-btn" onclick="window.location.href='car-booked-owner-data.php'">Clear Filters</button>
        </div>
    </form>

<!-- TABLE SECTION END -->
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Contact Number</th>
                <th>Email</th>
                <th>Date of Birth</th>
                <th>Occupation</th>
                <th>City</th>
                <th>Street Address</th>
                <th>ID Proof Type</th>
                <th>ID Proof Image</th>
                <th>ID Proof Number</th>
                <th>Preferred Communication</th>
                <th>Preferred Delivery</th>
                <th>Comments</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result2 && mysqli_num_rows($result2) > 0) {
                while ($row = mysqli_fetch_assoc($result2)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['full_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['contact_number']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['dob']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['occupation']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['city']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['street_address']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['id_proof_type']) . "</td>";
                    echo "<td>";
                    if (!empty($row['id_proof_image'])) {
                        echo "<a href='/AR Cars/" . htmlspecialchars($row['id_proof_image']) . "' target='_blank'>View Image</a>";
                    } else {
                        echo "No Image";
                    }
                    echo "</td>";
                    echo "<td>" . htmlspecialchars($row['id_proof_number']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['preferred_communication']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['preferred_delivery']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['comments']) . "</td>";
                    echo "<td><a href='delete.php?id=" . $row['id'] . "&page=car_booking-2' class='delete-btn' onclick='return confirm(\"Are you sure you want to delete?\");'>Delete</a></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='16'>No user records found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
<!-- TABLE SECTION END -->

</div>
</body>
</html>