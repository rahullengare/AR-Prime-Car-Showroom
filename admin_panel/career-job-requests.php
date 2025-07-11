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
    $sort_order = $_GET['id'] ?? 'DESC';
    $filter_date = $_GET['filter_date'] ?? '';
    $date_filter_type = $_GET['date_filter_type'] ?? '';
    $start_date = $_GET['start_date'] ?? '';
    $end_date = $_GET['end_date'] ?? '';
    $search = $_GET['search'] ?? '';
    $experience_sort = $_GET['experience_sort'] ?? '';
    $limit = $_GET['limit'] ?? '5';

    // Start building the SQL query
    $query = "SELECT id, name, email, mobile, city, position, experience, resume, bio, comments, created_at 
            FROM career_job_request WHERE 1";

    // Array for binding parameters
    $params = [];
    $types = '';

    // Add search condition for name, mobile, or email
    if ($search) {
        $query .= " AND (name LIKE ? OR mobile LIKE ? OR email LIKE ? OR position LIKE ?)";
        $search_term = '%' . $search . '%';
        $params[] = $search_term;
        $params[] = $search_term;
        $params[] = $search_term;
        $params[] = $search_term;
        $types .= 'ssss';
    }

    // Add date filter logic
    if ($filter_date) {
        if ($date_filter_type === 'exact') {
            $query .= " AND DATE(created_at) = ?";
            $params[] = $filter_date;
            $types .= 's';
        } elseif ($date_filter_type === 'before') {
            $query .= " AND created_at <= ?";
            $params[] = $filter_date . ' 23:59:59';
            $types .= 's';
        } elseif ($date_filter_type === 'after') {
            $query .= " AND created_at >= ?";
            $params[] = $filter_date . ' 00:00:00';
            $types .= 's';
        }
    }

    // Apply start and end date range
    if (!empty($start_date)) {
        $query .= " AND DATE(created_at) >= ?";
        $params[] = $start_date;
        $types .= 's';
    }
    if (!empty($end_date)) {
        $query .= " AND DATE(created_at) <= ?";
        $params[] = $end_date;
        $types .= 's';
    }

    // Add sorting by experience
    if ($experience_sort === 'high_to_low') {
        $query .= " ORDER BY experience DESC";
    } elseif ($experience_sort === 'low_to_high') {
        $query .= " ORDER BY experience ASC";
    } else {
        $query .= " ORDER BY id $sort_order";
    }

    // Add limit
    if ($limit !== 'All') {
        $query .= " LIMIT ?";
        $params[] = (int)$limit;
        $types .= 'i';
    }

    // Prepare the statement
    $stmt = mysqli_prepare($conn, $query);

    if (!$stmt) {
        die('Query preparation failed: ' . mysqli_error($conn));
    }

    // Bind parameters dynamically if needed
    if ($types && $params) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }

    // Execute the query
    if (!mysqli_stmt_execute($stmt)) {
        die('Query execution failed: ' . mysqli_stmt_error($stmt));
    }

    // Get the result
    $result = mysqli_stmt_get_result($stmt);

    // Status message
    $status = ($result && mysqli_num_rows($result) > 0) ? "" : "No career & job applications found!";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../images/icon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">
    <title>Job Applicant | AR Admin</title>
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
    <h2 class="FormName">Career & Job Applications</h2>

    <form method="GET" action="career-job-requests.php" class="filter-container">
    <div>
            <label>Sort by ID:</label>
            <select name="id" onchange="this.form.submit()">
                <option value="DESC" <?php echo ($sort_order == 'DESC') ? 'selected' : ''; ?>>Newest First</option>
                <option value="ASC" <?php echo ($sort_order == 'ASC') ? 'selected' : ''; ?>>Oldest First</option>
            </select>
        </div>
        <div>
            <label>Filter by Apply Date:</label>
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
            <label>Experience:</label>
            <select name="experience_sort" onchange="this.form.submit()">
                <option value="">Select</option>
                <option value="low_to_high" <?php echo ($experience_sort === 'low_to_high') ? 'selected' : ''; ?>>Lowest</option>
                <option value="high_to_low" <?php echo ($experience_sort === 'high_to_low') ? 'selected' : ''; ?>>Highest</option>
            </select>
        </div>
        <div>
            <label>Search:</label>
            <input type="text" name="search" placeholder="Search Name, Email, Phone." value="<?php echo htmlspecialchars($search); ?>">
        </div>
        <div>
            <button type="submit">Apply Filters</button>
            <button type="button" class="clear-btn" onclick="window.location.href='career-job-requests.php'">Clear Filters</button>
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
                <th>Position</th>
                <th>Experience (Years)</th>
                <th>Resume</th>
                <th>Bio</th>
                <th>Comments</th>
                <th>Submission Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
                if ($result && mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['mobile']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['city']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['position']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['experience']) . "</td>";
                        echo "<td><a href='/AR Cars/" . htmlspecialchars($row['resume']) . "' target='_blank'>View Resume</a></td>";
                        echo "<td>" . htmlspecialchars($row['bio']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['comments']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
                        echo "<td><a href='delete.php?id=" . $row['id'] . "&page=career_job' class='delete-btn' onclick='return confirm(\"Are you sure you want to delete?\");'>Delete</a></td>";
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
