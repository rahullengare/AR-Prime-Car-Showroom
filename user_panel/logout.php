<?php
session_start(); // Start the session

// Destroy the session
session_destroy();

// Display a thank you message and redirect to login page
echo "<script>
    alert('Thank you for visiting! Please come again.');
    window.location.href = 'login.php'; // Redirect to login page
</script>";

exit;
?>
