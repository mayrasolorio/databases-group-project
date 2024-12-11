<?php
function updateAvailabilityStatus($currentDate) {
    // Include the database connection
    require_once "config.php";
    
    // Sanitize and format the current date (assuming it's already in YYYY-MM-DD format)
    $currentDate = mysqli_real_escape_string($link, $currentDate);
    
    // SQL query to check for expired leases
    $sql = "
        SELECT L.hid 
        FROM Lease L
        WHERE L.Lease_End < '$currentDate' AND L.Lease_End IS NOT NULL
    ";
    
    // Execute the query
    if ($result = mysqli_query($link, $sql)) {
        if (mysqli_num_rows($result) > 0) {
            // Loop through each expired lease
            while ($row = mysqli_fetch_assoc($result)) {
                // Get the HouseId (hid) from the expired lease
                $houseId = $row['hid'];
                
                // Now update the corresponding property availability status to 1 (Available)
                $updateSql = "
                    UPDATE Property 
                    SET Availability_Status = 1 
                    WHERE HouseId = '$houseId'
                ";

                // Execute the update query
                if (mysqli_query($link, $updateSql)) {
                    echo "Property with HouseId $houseId availability status updated to available.<br>";
                } else {
                    echo "Error updating Property with HouseId $houseId: " . mysqli_error($link) . "<br>";
                }
            }
        } else {
            echo "No expired leases found for the current date $currentDate.<br>";
        }

        // Free result set
        mysqli_free_result($result);
    } else {
        echo "ERROR: Could not execute $sql. <br>" . mysqli_error($link);
    }

    // Close the database connection
    mysqli_close($link);
}
?>
