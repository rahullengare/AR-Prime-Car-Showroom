<?php
    // Start session
    session_start();
    
    // Include database connection
    include('connection.php');
    
    // Check if admin is logged in
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        header('Location: login.php');
        exit();
    }
    
    // Retrieve admin username
    $admin_name = $_SESSION['admin_username'] ?? 'Admin';
    
    // Handle deletion of a record
    if (isset($_GET['delete_id']) && is_numeric($_GET['delete_id'])) {
        $delete_id = (int)$_GET['delete_id'];
        $delete_query = "DELETE FROM pickup_drop_form WHERE id = $delete_id";
    
        if (mysqli_query($conn, $delete_query)) {
            header("Location: pickup-drop-requests.php?status=deleted");
            exit();
        } else {
            $delete_error = "Error deleting record: " . mysqli_error($conn);
        }
    }
    
    // Initialize filter and sorting variables
    $sort_order = $_GET['sort_order'] ?? 'DESC'; // Default sort order
    $search = $_GET['search'] ?? '';
    $car_model = $_GET['car_model'] ?? '';
    $date_filter = $_GET['date_filter'] ?? '';
    $date_value = $_GET['date_value'] ?? '';
    $limit = $_GET['limit'] ?? '5';
    $start_date = $_GET['start_date'] ?? '';
    $end_date = $_GET['end_date'] ?? '';
    
    // Validate sort order
    $valid_sort_orders = ['ASC', 'DESC'];
    if (!in_array(strtoupper($sort_order), $valid_sort_orders)) {
        $sort_order = 'DESC';
    }
    
    // Build the SQL query
    $query = "SELECT * FROM pickup_drop_form WHERE 1";
    
    // Apply search filter
    if (!empty($search)) {
        $search = mysqli_real_escape_string($conn, $search);
        $query .= " AND (name LIKE '%$search%' OR email LIKE '%$search%' OR mobile LIKE '%$search%' OR city LIKE '%$search%')";
    }
    
    // Apply car model filter
    if (!empty($car_model)) {
        $car_model = mysqli_real_escape_string($conn, $car_model);
        $query .= " AND car_model = '$car_model'";
    }
    
    // Apply date filter
    if (!empty($date_filter) && !empty($date_value)) {
        $date_value = mysqli_real_escape_string($conn, $date_value);
        if ($date_filter === 'exact') {
            $query .= " AND DATE(created_at) = '$date_value'";
        } elseif ($date_filter === 'before') {
            $query .= " AND DATE(created_at) < '$date_value'";
        } elseif ($date_filter === 'after') {
            $query .= " AND DATE(created_at) > '$date_value'";
        }
    }
    
    // Apply start and end date range
    if (!empty($start_date)) {
        $start_date = mysqli_real_escape_string($conn, $start_date);
        $query .= " AND DATE(created_at) >= '$start_date'";
    }
    if (!empty($end_date)) {
        $end_date = mysqli_real_escape_string($conn, $end_date);
        $query .= " AND DATE(created_at) <= '$end_date'";
    }
    
    // Apply sorting: First by ID, then by submission date
    $query .= " ORDER BY id $sort_order, created_at $sort_order";
    
    // Apply limit
    if ($limit !== 'All') {
        $query .= " LIMIT " . (int)$limit;
    }
    
    // Execute the query
    $result = mysqli_query($conn, $query);
    $status = ($result && mysqli_num_rows($result) > 0) ? "" : "No pickup & drop requests found!";
    
    // Fetch car models for the dropdown
    $car_model_query = "SELECT DISTINCT car_model FROM pickup_drop_form WHERE car_model IS NOT NULL";
    $car_model_result = mysqli_query($conn, $car_model_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../images/icon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">
    <title>Pickup-Drop Requests | AR Admin</title>
    <link rel="stylesheet" href="../CSS/admin.css">
</head>
<body>
<!-- HEADER SECTION -->
<div class="header">
    <div class="info">
        <img src="../images/logo_AR.jpeg" alt="Showroom Logo" class="logo">
        <h4 class="name">AR PRIME SHOWROOM</h4>
    </div>
    <div class="center">
        <div class="main">
            <a href="index.php" style="background-color: yellow;">Admin Home</a>
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

<!-- Navigation -->
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

<!-- TABLE SECTION -->
<div class="table-container">
    <p class="status-message"><strong><?php echo $status; ?></strong></p>
    <h2 class="FormName">Pickup & Drop Requests</h2>
    <form method="GET" action="pickup-drop-requests.php" class="filter-container">
        <div>
            <label>Sort by:</label>
            <select name="sort_order" onchange="this.form.submit()">
                <option value="DESC" <?php echo ($sort_order === 'DESC') ? 'selected' : ''; ?>>Newest First</option>
                <option value="ASC" <?php echo ($sort_order === 'ASC') ? 'selected' : ''; ?>>Oldest First</option>
            </select>
        </div>
        <div>
            <label>Filter by Submitted Date:</label>
            <input type="date" name="date_value" value="<?php echo htmlspecialchars($date_value); ?>">
            <select name="date_filter" onchange="this.form.submit()">
                <option value="">None</option>
                <option value="exact" <?php echo ($date_filter === 'exact') ? 'selected' : ''; ?>>Exact</option>
                <option value="before" <?php echo ($date_filter === 'before') ? 'selected' : ''; ?>>Before</option>
                <option value="after" <?php echo ($date_filter === 'after') ? 'selected' : ''; ?>>After</option>
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
                <option value="5" <?php echo ($limit === '5') ? 'selected' : ''; ?>>5</option>
                <option value="10" <?php echo ($limit === '10') ? 'selected' : ''; ?>>10</option>
                <option value="15" <?php echo ($limit === '15') ? 'selected' : ''; ?>>15</option>
                <option value="All" <?php echo ($limit === 'All') ? 'selected' : ''; ?>>All</option>
            </select>
        </div>
        <div>
            <label>Car Model:</label>
            <select name="car_model" onchange="this.form.submit()">
                <option value="">All Model</option>
                <?php
                if ($car_model_result && mysqli_num_rows($car_model_result) > 0) {
                    while ($model_row = mysqli_fetch_assoc($car_model_result)) {
                        $selected = ($car_model === $model_row['car_model']) ? 'selected' : '';
                        echo "<option value='" . htmlspecialchars($model_row['car_model']) . "' $selected>" . htmlspecialchars($model_row['car_model']) . "</option>";
                    }
                }
                ?>
            </select>
        </div>
        <div>
            <label>Search:</label>
            <input type="text" name="search" placeholder="Search Name, Email, Phone." value="<?php echo htmlspecialchars($search); ?>">
        </div>
        <div>
            <button type="submit">Apply Filters</button>
            <button type="button" class="clear-btn" onclick="window.location.href='pickup-drop-requests.php'">Clear Filters</button>
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
                <th>Car Register No</th>
                <th>Pickup Date</th>
                <th>Pickup Time</th>
                <th>Drop Date</th>
                <th>Drop Time</th>
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
                    echo "<td>" . htmlspecialchars($row['car_model']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['carRegisterNo']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['pickup_date']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['pickup_time']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['drop_date']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['drop_time']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['comments']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
                    echo "<td><a href='pickup-drop-requests.php?delete_id=" . $row['id'] . "' class='delete-btn' onclick='return confirm(\"Are you sure you want to delete this record?\");'>Delete</a></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='14'>No records found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>
</body>
</html>
