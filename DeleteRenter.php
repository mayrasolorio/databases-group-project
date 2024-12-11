<!-- 
    Project: Property Management Database (CS 340)
    Group: Group 8
    Team Members:
        - Josiah Liebert
        - David Gesl
        - Mayra Solorio
        - Gabe McVean
    Description:
        This project implements a property management system featuring CRUD operations 
        to manage properties, leases, renters, payments, and more. Users can create, 
        retrieve, update, and delete records through an intuitive interface. 
    Date: 12/10/2024
-->

<?php
// Include config file for database connection
require_once "config.php";

// Initialize variables
$message = "";

// Check if RenterId and LeaseId are provided
if (isset($_GET['RenterId']) && isset($_GET['LeaseId'])) {
    // Get the RenterId and LeaseId from the URL
    $renterId = $_GET['RenterId'];
    $leaseId = $_GET['LeaseId'];

    // Call the stored procedure to remove the renter
    $sql = "CALL RemoveRenterFromLease(?, ?)";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "ii", $renterId, $leaseId);

        // Execute the statement
        if (mysqli_stmt_execute($stmt)) {
            $message = "Renter deleted successfully.";
            // Redirect back to Renter.php with success message
            header("location: Renter.php?message=success");
            exit();
        } else {
            $message = "Error deleting renter: " . mysqli_error($link);
        }
        // Close the statement
        mysqli_stmt_close($stmt);
    } else {
        $message = "Error preparing the SQL statement: " . mysqli_error($link);
    }
} else {
    $message = "No Renter ID or Lease ID provided for deletion.";
}

// Close the database connection
mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Delete Renter</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1>Delete Renter</h1>
        <p><?php echo $message; ?></p>
        <a href="Renter.php" class="btn btn-primary">Back to Renter Management</a>
    </div>
</body>
</html>
