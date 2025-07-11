<?php
// Include your database connection (Connection.php)
include('Connection.php');

// Form submission handling
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Capture form data
    $fullName = $_POST['full_name'];
    $email = $_POST['email'];
    $contactNumber = $_POST['contact_number'];
    $loanAmount = $_POST['loan_amount'];
    $loanDuration = $_POST['loan_duration'];
    $loanPurpose = $_POST['loan_purpose'];
    
    // Insert data into the database
    $sql = "INSERT INTO loan_applications (full_name, email, contact_number, loan_amount, loan_duration, loan_purpose) 
            VALUES ('$fullName', '$email', '$contactNumber', '$loanAmount', '$loanDuration', '$loanPurpose')";
    
    if (mysqli_query($conn, $sql)) {
        echo "Loan Application Submitted Successfully!";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="Cars-IMG/icon.png">
    <title>Loan Application Form | AR Prime Showroom</title>
    <link rel="stylesheet" href="styles.css"> <!-- Add your custom CSS file -->
</head>
<body>

<!-- Loan Application Form -->
<div class="loan-form">
    <h2>Loan Application Form</h2>
    <form action="loan_form.php" method="post">
        <label for="full_name">Full Name:</label>
        <input type="text" id="full_name" name="full_name" required><br><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br><br>

        <label for="contact_number">Contact Number:</label>
        <input type="text" id="contact_number" name="contact_number" required><br><br>

        <label for="loan_amount">Loan Amount (in ₹):</label>
        <input type="number" id="loan_amount" name="loan_amount" required><br><br>

        <label for="loan_duration">Loan Duration (in years):</label>
        <input type="number" id="loan_duration" name="loan_duration" required><br><br>

        <label for="loan_purpose">Loan Purpose:</label>
        <textarea id="loan_purpose" name="loan_purpose" required></textarea><br><br>

        <input type="submit" value="Submit Application">
    </form>
</div>

</body>
</html>
