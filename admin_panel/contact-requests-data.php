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
    $sort_order = $_GET['sort_id'] ?? 'DESC';
    $filter_date = $_GET['filter_date'] ?? '';
    $date_filter_type = $_GET['date_filter_type'] ?? '';
    $start_date = $_GET['start_date'] ?? '';
    $end_date = $_GET['end_date'] ?? '';
    $search = $_GET['search'] ?? '';
    $limit = $_GET['limit'] ?? '5';

    // Validate and sanitize the limit
    if (!is_numeric($limit) && $limit !== 'All') {
        $limit = '5'; // Default fallback limit
    }

    // Build the SQL query based on filters
    $query = "SELECT id, fullName, email, phone, subject, message, created_at FROM contact_messages WHERE 1";

    // Add search condition for fullName, phone, or email
    if ($search) {
        $query .= " AND (fullName LIKE '%" . mysqli_real_escape_string($conn, $search) . "%' 
                        OR phone LIKE '%" . mysqli_real_escape_string($conn, $search) . "%' 
                        OR email LIKE '%" . mysqli_real_escape_string($conn, $search) . "%')";
    }

    // Add date filters (Check if filter_date is set and properly format the condition)
    if ($filter_date) {
        $filter_date = mysqli_real_escape_string($conn, $filter_date);
        if ($date_filter_type == 'exact') {
            $query .= " AND DATE(created_at) = '$filter_date'";
        } elseif ($date_filter_type == 'before') {
            $query .= " AND created_at <= '$filter_date 23:59:59'";
        } elseif ($date_filter_type == 'after') {
            $query .= " AND created_at >= '$filter_date 00:00:00'";
        }
    }

    // Add start and end date filters
    if ($start_date) {
        $query .= " AND created_at >= '" . mysqli_real_escape_string($conn, $start_date) . "'";
    }
    if ($end_date) {
        $query .= " AND created_at <= '" . mysqli_real_escape_string($conn, $end_date) . "'";
    }

    // Add sorting order
    $query .= " ORDER BY created_at $sort_order";

    // Add limit to query
    if ($limit !== 'All') {
        $query .= " LIMIT " . (int)$limit;
    }

    // Execute the query
    $result = mysqli_query($conn, $query);

    // Check if any results are found
    $status = ($result && mysqli_num_rows($result) > 0) ? "" : "No contact messages found!";

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
    <title>view Contacts | AR Admin</title>
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
        <h2 class="FormName">Contact Requests </h2>

        <!-- Sorting and Filters Section -->
    <form method="GET" action="contact-requests-data.php" class="filter-container">
        <div>
            <label>Sort by ID:</label>
            <select name="sort_id" onchange="this.form.submit()">
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
            <label>Search:</label>
            <input type="text" name="search" placeholder="Search Name, Email, Phone." value="<?php echo htmlspecialchars($search); ?>">
        </div>
        <div>
            <button type="submit">Apply Filters</button>
            <button type="button" class="clear-btn" onclick="window.location.href='contact-requests-data.php'">Clear Filters</button>
        </div>
    </form>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Subject</th>
                <th>Message</th>
                <th>Submitted At</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
    <?php
        if ($result && mysqli_num_rows($result) > 0) {
            while ($message = mysqli_fetch_assoc($result)) {
                // Trim spaces from the subject and check if it's empty
                $subject = trim($message['subject']);
                // If subject is empty or only contains spaces, set it to 'No Subject'
                $subject = empty($subject) ? 'No Subject' : $subject;

                echo "<tr>";
                echo "<td>" . htmlspecialchars($message['id']) . "</td>";
                echo "<td>" . htmlspecialchars($message['fullName']) . "</td>";
                echo "<td>" . htmlspecialchars($message['email']) . "</td>";
                echo "<td>" . htmlspecialchars($message['phone']) . "</td>";
                echo "<td>" . htmlspecialchars($subject) . "</td>";  // Display 'No Subject' if empty
                echo "<td>" . htmlspecialchars($message['message']) . "</td>";
                echo "<td>" . htmlspecialchars($message['created_at']) . "</td>";
                echo "<td><a href='delete.php?id=" . $message['id'] . "&page=contact_message' class='delete-btn' onclick='return confirm(\"Are you sure you want to delete?\");'>Delete</a></td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='8'>No contact messages found.</td></tr>";
        }
    ?>
</tbody>

    </table>

    </div>
<!-- TABLE SECTION END -->

</body>
</html>
