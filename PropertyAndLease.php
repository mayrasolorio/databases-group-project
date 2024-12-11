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
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Property DB</title>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">

    <!-- jQuery library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <!-- Latest compiled JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    
    <style type="text/css">
        .wrapper {
            width: 70%;
            margin: 0 auto;
        }
        table tr td:last-child a {
            margin-right: 15px;
        }
        .available {
            background-color: #d4edda; /* Light green */
            color: #155724;
        }
        .occupied {
            background-color: #f8d7da; /* Light red */
            color: #721c24;
        }
        .active-lease {
            background-color: #d4edda; /* Light green */
            color: #155724;
        }
        .finished-lease {
            background-color: #f8d7da; /* Light red */
            color: #721c24;
        }

    </style>
</head>
<body>
    <?php
        // Include config file
        require_once "config.php";
        require_once 'updateAvailabilityStatus.php';

        $current_time = time();

        // Pass the new date format as a string and the original date in Unix time
        // $newDate = date("d-m-Y", $current_time);
        $newDate = date("Y-m-d", $current_time);
        
        
        updateAvailabilityStatus($newDate);

    ?>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
		    <div class="page-header clearfix">
            <h2>Property Management Database (CS 340)</h2> 
<p>This project demonstrates a comprehensive property management system that includes full CRUD operations. With this website, you can:
    <ol> 
        <li><strong>CREATE</strong> new properties, leases, and owners with seamless form-based input.</li>
        <li><strong>RETRIEVE</strong> detailed information on:
            <ul>
                <li>Property managers and owners associated with specific properties.</li>
                <li>Renters and payment history linked to individual leases.</li>
            </ul>
        </li>
        <li><strong>UPDATE</strong> renter records, including their name and phone number.</li>
        <li><strong>DELETE</strong> property and renter records as needed.</li>
    </ol>
    Navigate through intuitive interfaces to manage all aspects of properties, leases, renters, payments, and more efficiently.
</p>
		        <h2 class="pull-left">Property Details</h2>
                <a href="AddProperty.php" class="btn btn-success pull-right">Add Property</a>
		    </div>
		    <?php
                // Get availability_status from the Property table
                $sql = "
                SELECT 
                    HouseId, 
                    Street, 
                    City, 
                    Zip, 
                    State, 
                    Availability_Status
                FROM Property";

		        if ($result = mysqli_query($link, $sql)) {
		            if (mysqli_num_rows($result) > 0) {
		                echo "<table class='table table-bordered'>";
		                    echo "<thead>";
		                        echo "<tr>";
		                            echo "<th width=8%>House Id</th>";
		                            echo "<th width=10%>Street</th>";
		                            echo "<th width=10%>City</th>";
		                            echo "<th width=5%>State</th>";
		                            echo "<th width=10%>Zip</th>";
		                            echo "<th width=5%>Availability Status</th>";
		                            echo "<th width=10%>Action</th>";
		                        echo "</tr>";
		                    echo "</thead>";
		                    echo "<tbody>";
		                    while ($row = mysqli_fetch_array($result)) {
		                        // Determine the Availability Status based on the lease end date -------- Deprecated. Now based on availability_status
                                // $availabilityStatus = (strtotime($row['Lease_End']) < time()) ? "Available" : "Occupied";

                                // Check the Availability_Status field from the Property table
                                $availabilityStatus = ($row['Availability_Status'] == 1) ? "Available" : "Unavailable";
                                $class = ($availabilityStatus === "Available") ? "available" : "occupied";

		                        echo "<tr class='$class'>";
		                            echo "<td>" . $row['HouseId'] . "</td>";
		                            echo "<td>" . $row['Street'] . "</td>";
		                            echo "<td>" . $row['City'] . "</td>";
		                            echo "<td>" . $row['State'] . "</td>";									
		                            echo "<td>" . $row['Zip'] . "</td>";
		                            echo "<td>" . $availabilityStatus . "</td>";	
		                            echo "<td>";
		                                echo "<a href='LeaseFromProperty.php?HouseId=" . $row['HouseId'] .
		                                "&Street=" . urlencode($row['Street']) .
		                                "&City=" . urlencode($row['City']) .
		                                "&State=" . urlencode($row['State']) .
		                                "&Zip=" . urlencode($row['Zip']) .
		                                "' title='View Leases' data-toggle='tooltip'>
		                                <span class='glyphicon glyphicon-eye-open'></span></a>";
                                        echo "<a href='DeleteProperty.php?HouseId=" . $row['HouseId'] . 
                                        "' title='Delete Property' data-toggle='tooltip'>
                                        <span class='glyphicon glyphicon-trash'></span></a>";
		                            echo "</td>";
		                        echo "</tr>";
		                    }
		                    echo "</tbody>";                            
		                echo "</table>";
		                // Free result set
		                mysqli_free_result($result);
		            } else {
		                echo "<p class='lead'><em>No records were found.</em></p>";
		            }
		        } else {
		            echo "ERROR: Could not able to execute $sql. <br>" . mysqli_error($link);
		        }

                // Add New Lease button correctly placed here
                echo "<br> <h2>Current/Past Lease Details</h2> <br>";
                ?>
                <a href="addNewLease.php" class="btn btn-success pull-right">Add New Lease</a>
                <?php

                $sql2 = "
                SELECT 
                    LeaseId, 
                    hid, 
                    Lease_Start, 
                    Lease_End, 
                    Monthly_Rent,
                    TIMESTAMPDIFF(MONTH, Lease_Start, Lease_End) AS LeaseDuration,
                    SUM(Monthly_Rent) - IFNULL(SUM(P.Amount), 0) AS AllRentLeft,
                    IF(Lease_End >= CURDATE(), 'Active', 'Finished') AS LeaseStatus
                FROM 
                    Lease L
                LEFT JOIN 
                    Payments P ON L.LeaseId = P.lid
                GROUP BY 
                    L.LeaseId
                ";
                
                if ($result2 = mysqli_query($link, $sql2)) {
                    if (mysqli_num_rows($result2) > 0) {
                        echo "<table class='table table-bordered'>";
                            echo "<thead>";
                                echo "<tr>";
                                    echo "<th width=10%>Lease Id</th>";
                                    echo "<th width=10%>House Id</th>";                                        
                                    echo "<th width=20%>Start Date</th>";
                                    echo "<th width=20%>End Date</th>";
                                    echo "<th width=15%>Monthly Rent</th>";
                                    echo "<th width=15%>Lease Duration (Months)</th>";
                                    echo "<th width=15%>Rent Owed</th>";
                                    echo "<th width=10%>Status</th>";
                                echo "</tr>";
                            echo "</thead>";
                            echo "<tbody>";
                            while ($row = mysqli_fetch_array($result2)) {
                                // Determine the CSS class based on Lease Status
                                $class = ($row['LeaseStatus'] === "Active") ? "active-lease" : "finished-lease";
                
                                echo "<tr class='$class'>";
                                    echo "<td>" . $row['LeaseId'] . "</td>";
                                    echo "<td>" . $row['hid'] . "</td>";
                                    echo "<td>" . $row['Lease_Start'] . "</td>";
                                    echo "<td>" . $row['Lease_End'] . "</td>";
                                    echo "<td>" . $row['Monthly_Rent'] . "</td>";
                                    echo "<td>" . $row['LeaseDuration'] . "</td>";
                                    echo "<td>" . $row['AllRentLeft'] . "</td>";
                                    echo "<td>" . $row['LeaseStatus'] . "</td>";
                                echo "</tr>";
                            }
                            echo "</tbody>";                            
                        echo "</table>";
                        // Free result set
                        mysqli_free_result($result2);
                    } else {
                        echo "<p class='lead'><em>No lease records were found.</em></p>";
                    }
                } else {
                    echo "ERROR: Could not execute $sql2. <br>" . mysqli_error($link);
                }
                ?>
                
		    </div>

		    <!-- Back Button -->
		    <div class="form-group">
		        <a href="index.php" class="btn btn-primary">Back to Home</a>
		    </div>
		</div>
	    </div>
    </div>
</body>
</html>
