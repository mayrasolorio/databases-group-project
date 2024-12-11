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
function updateAvailabilityStatus($currentDate) {
    // Use the global database connection
    global $link;

    // Query to get all properties
    $sql = "
        SELECT P.HouseId
        FROM Property P
    ";
    
    // Execute the query
    if ($result = mysqli_query($link, $sql)) {
        if (mysqli_num_rows($result) > 0) {
            // Loop through all properties
            while ($row = mysqli_fetch_assoc($result)) {
                $houseId = $row['HouseId'];

                // Query to check for active leases for the current property
                $leaseSql = "
                    SELECT L.Lease_Start, L.Lease_End
                    FROM Lease L
                    WHERE L.hid = '$houseId'
                    AND L.Lease_Start <= '$currentDate'
                    AND (L.Lease_End >= '$currentDate' OR L.Lease_End IS NULL)
                ";

                $activeLeaseFound = false;

                // Execute the lease query to find active leases
                if ($leaseResult = mysqli_query($link, $leaseSql)) {
                    // If there are active leases, mark the property as unavailable
                    if (mysqli_num_rows($leaseResult) > 0) {
                        $activeLeaseFound = true;
                    }
                    mysqli_free_result($leaseResult);
                }

                // Update the availability status based on whether there are active leases
                if ($activeLeaseFound) {
                    $updateSql = "
                        UPDATE Property 
                        SET Availability_Status = 0 
                        WHERE HouseId = '$houseId'
                    ";
                    if (mysqli_query($link, $updateSql)) {
                        // Commented out the echo message
                        // echo "Property with HouseId $houseId availability status updated to unavailable (active lease found).<br>";
                    } else {
                        // Commented out the error message
                        // echo "Error updating Property with HouseId $houseId: " . mysqli_error($link) . "<br>";
                    }
                } else {
                    // If no active leases, set the property as available
                    $updateSql = "
                        UPDATE Property 
                        SET Availability_Status = 1 
                        WHERE HouseId = '$houseId'
                    ";
                    if (mysqli_query($link, $updateSql)) {
                        // Commented out the echo message
                        // echo "Property with HouseId $houseId availability status updated to available (no active lease).<br>";
                    } else {
                        // Commented out the error message
                        // echo "Error updating Property with HouseId $houseId: " . mysqli_error($link) . "<br>";
                    }
                }
            }
        }

        // Free result set
        mysqli_free_result($result);
    } else {
        // Commented out the error message
        // echo "ERROR: Could not execute $sql. <br>" . mysqli_error($link);
    }
}
?>
