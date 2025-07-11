<?php
    include "connection.php"; // Database connection

    // Handle Delete Request
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete'])) {
        $carid = $_POST['carid']; // Get Car ID from form

        // Delete Query
        $delete_sql = "DELETE FROM addcars WHERE id=?";
        $stmt = $conn->prepare($delete_sql);
        $stmt->bind_param("i", $carid);

        if ($stmt->execute()) {
            echo "<script>
                    alert('Car deleted successfully!');
                    window.location.href='deletecar.php'; // Reload page
                  </script>";
            exit();
        } else {
            echo "<script>
                    alert('Error deleting car: " . $conn->error . "');
                  </script>";
        }
        $stmt->close();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../images/icon.png">
    <title>Delete Car | AR Admin</title>
    <style>
        body {
            font-family: math;
            background-color: #f9f9f9;
            margin: 0;
            padding: 20px;
            text-align: center;
        }
        h2 {
            color: #333;
            margin-bottom: 30px;
            font-size: 30px;
            font-weight: 500;
        }
        a{
            text-decoration: none;
            background-color: yellow;
            padding: 8px;
            border-radius: 10px;
            color: black;
        }
        p {
            font-size: 16px;
            margin-bottom: 20px;
            color: #555;
        }
        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        th, td {
            border: 1px solid #ddd;
            padding: 15px;
            text-align: left;
        }
        th {
            background-color: #007bff;
            color: white;
            font-size: 22px;
        }
        td {
            font-size: 20px;
            color: #333;
        }
        td img {
            width: 100px;
            height: auto;
            border-radius: 8px;
        }
        button {
            background-color: #dc3545;
            color: white;
            padding: 10px 15px;
            border: none;
            cursor: pointer;
            border-radius: 8px;
            font-size: 16px;
            transition: background-color 0.1s ease;
        }
        button:hover {
            background-color: #c82333;
        }
        .message {
            padding: 10px;
            margin-top: 20px;
            border-radius: 8px;
            font-size: 16px;
            text-align: center;
        }
        .message.no-records {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <h2>Delete Car</h2>
    <a href="index.php" class="option-button">Go To Admin Dashboard</a>
    <?php
    // Fetch all cars from the database
    $sql = "SELECT * FROM addcars";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<table>";
        echo "<tr><th>Car ID</th><th>Car Name</th><th>Car Image</th><th>Action</th></tr>";
        
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>{$row['id']}</td>";
            echo "<td>{$row['carname']}</td>";
            echo "<td><img src='{$row['carimg']}' alt='{$row['carname']}'></td>";
            echo "<td>
                    <form method='POST' onsubmit='return confirmDelete();'>
                        <input type='hidden' name='carid' value='{$row['id']}'>
                        <button type='submit' name='delete'>Delete</button>
                    </form>
                </td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p class='message no-records'>No cars found in the database.</p>";
    }

    // Close database connection
    $conn->close();
    ?>

    <script>
        function confirmDelete() {
            return confirm("Are you sure you want to delete this car?");
        }
    </script>

</body>
</html>

