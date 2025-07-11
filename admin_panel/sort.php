<?php
// Default sort order (Descending by Date)
$sort_order = isset($_GET['sort_date']) ? $_GET['sort_date'] : 'DESC';

// Add sorting to each query for car booking forms
$query1 = "SELECT * FROM car_bookingform1 ORDER BY booking_date $sort_order";
$query2 = "SELECT * FROM car_bookingform2 ORDER BY dob $sort_order";  // Assuming you want to sort by DOB for user details
$query3 = "SELECT * FROM car_bookingform3 ORDER BY payment_date $sort_order";  // Assuming you want to sort by payment date

// Apply filtering if necessary, and return the query to the main script
return $query1; // You can similarly return $query2 or $query3 based on which form you are querying
?>
