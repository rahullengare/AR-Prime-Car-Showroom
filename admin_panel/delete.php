<?php
    session_start();
    include('connection.php');  // Include the connection file

    // Check if admin is logged in
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        header('Location: login.php');
        exit();
    }

    // Check if ID and page are provided, and if ID is numeric
    if (isset($_GET['id']) && is_numeric($_GET['id']) && isset($_GET['page'])) {
        $id = $_GET['id'];
        $page = $_GET['page'];  // Get the page from the URL to know which data to delete

        // Prepare the delete query based on the page
        switch ($page) {
            case 'car_booking-1':
                $query = "DELETE FROM car_bookingform1 WHERE id = $id";
                $redirect_page = 'car-bookings-data.php';
                break;
            case 'car_booking-2':
                $query = "DELETE FROM car_bookingform2 WHERE id = $id";
                $redirect_page = 'car-bookings-data.php';
                break;
            case 'car_booking-3':
                $query = "DELETE FROM car_bookingform3 WHERE id = $id";
                $redirect_page = 'car-bookings-data.php';
                break;
            case 'career_job':
                $query = "DELETE FROM career_job_request WHERE id = $id";
                $redirect_page = 'career-job-requests.php';
                break;
            case 'contact_message':
                $query = "DELETE FROM contact_messages WHERE id = $id";
                $redirect_page = 'contact-requests-data.php';
                break;
            case 'enquiry':
                $query = "DELETE FROM enquiries WHERE id = $id";
                $redirect_page = 'enquiry-data.php';
                break;
            case 'warranty':
                $query = "DELETE FROM warranty_extension_form WHERE id = $id";
                $redirect_page = 'warranty-requests-data.php';
                break;
            case 'repair':
                $query = "DELETE FROM accident_repair_form WHERE id = $id";
                $redirect_page = 'repair-requests-data.php';
                break;
            case 'insurance':
                $query = "DELETE FROM insurance_form WHERE id = $id";
                $redirect_page = 'insurance-requests-data.php';
                break;
            case 'pickup_drop':
                $query = "DELETE FROM pickup_drop_form WHERE id = $id";
                $redirect_page = 'pickup-drop-requests.php';
                break;
            case 'test_drive':
                $query = "DELETE FROM test_drive_requests WHERE id = $id";
                $redirect_page = 'test-drive-requests.php';
                break;
            case 'regular_services':
                $query = "DELETE FROM regular_services_form WHERE id = $id";
                $redirect_page = 'regular-services-data.php';
                break;
            // Add more cases here as needed
            default:
                // If an invalid page is given, redirect to a default page
                header('Location: index.php');
                exit();
        }

        // Execute the delete query
        if (mysqli_query($conn, $query)) {
            // Redirect to the appropriate page after deletion
            header("Location: $redirect_page");
            exit();
        } else {
            echo "Error deleting record: " . mysqli_error($conn);
        }
    } else {
        // Redirect back to the page if no ID or page is set or invalid
        header('Location: index.php');
        exit();
    }

    // Close the connection
    mysqli_close($conn);
?>
