<?php
    // Include the database connection
    include "connection.php";

    // Initialize variables for message handling
    $message = '';
    $message_color = '';

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Get the form values safely
        $carname = isset($_POST['carname']) ? $_POST['carname'] : '';
        $price = isset($_POST['price']) ? $_POST['price'] : '';

        // Check if carname and price are empty
        if (empty($carname) || empty($price)) {
            // Do not show message on page, only alert in case of error
            $message = "Car name and price are required!";
            $message_color = "red";
        } else {
            // Handle the image upload
            if (isset($_FILES['carimg']) && $_FILES['carimg']['error'] === 0) {
                $image_name = $_FILES['carimg']['name'];
                $image_tmp = $_FILES['carimg']['tmp_name'];
                $target_dir = "../uploads/";
                $target_file = $target_dir . basename($image_name);

                // Move the uploaded file to the target directory
                if (move_uploaded_file($image_tmp, $target_file)) {
                    // Prepare the SQL statement to insert the car details
                    $sql = $conn->prepare("INSERT INTO addcars (carname, price, carimg) VALUES (?, ?, ?)");
                    $sql->bind_param("sss", $carname, $price, $target_file);

                    // Execute the query
                    if ($sql->execute()) {
                        // Success message in alert, but no message shown on page
                        echo "<script>
                                alert('Car added successfully!');
                              </script>";
                    } else {
                        // Error message for failed insert, shown in alert
                        echo "<script>
                                alert('Error adding car: " . $conn->error . "');
                              </script>";
                    }
                } else {
                    // Image upload error alert
                    echo "<script>
                            alert('Error uploading image.');
                          </script>";
                }
            } else {
                // No image uploaded error alert
                echo "<script>
                        alert('Please upload a valid image.');
                      </script>";
            }
        }
    }

    // Close the connection
    $conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../images/icon.png">
    <title>Add Car | AR Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: math;
            background-color: #f2f2f2;
            text-align: center;
        }
        h2 {
            color: #333;
            margin-top: 50px;
            font-size: 28px;
            font-weight: 500;
        }
        a{
            text-decoration: none;
            background-color: yellow;
            padding: 8px;
            border-radius: 10px;
            color: black;
        }
        form {
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            display: inline-block;
            text-align: left;
            max-width: 500px;
            width: 100%;
            margin: 30px auto;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            font-size: 20px;
        }
        input[type="text"], input[type="file"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
            background-color: #fafafa;
        }
        input[type="text"]:focus, input[type="file"]:focus {
            border-color: #007bff;
            outline: none;
        }
        button {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 12px;
            width: 100%;
            border-radius: 8px;
            font-size: 18px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #218838;
        }
        .message {
            padding: 15px;
            margin-top: 20px;
            border-radius: 8px;
            font-size: 16px;
        }
        .message.green {
            background-color: #d4edda;
            color: #155724;
        }
        .message.red {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <h2>Add a New Car</h2>
    <div><a href="index.php">Go To Admin Dashboard</a></div>
    <!-- The form to add car details -->
    <form action="addcar.php" method="post" enctype="multipart/form-data">
        <label for="carimg">Car Image:</label>
        <input type="file" name="carimg" accept="image/*" required>

        <label for="carname">Car Name:</label>
        <input type="text" name="carname" required>

        <label for="price">Price:</label>
        <input type="text" name="price" required>

        <button type="submit" name="insert">Add Car</button>
    </form>
</body>
</html>
