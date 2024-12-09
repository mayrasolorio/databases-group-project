<?php
	session_start();
    // Include config file
    require_once "config.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Leases</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.js"></script>
    <style type="text/css">
        .wrapper{
            width: 650px;
            margin: 0 auto;
        }
        .page-header h2{
            margin-top: 0;
        }
        table tr td:last-child a{
            margin-right: 15px;
        }
    </style>
	   <script type="text/javascript">
        $(document).ready(function(){
            $('[data-toggle="tooltip"]').tooltip();   
        });
    </script>
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header clearfix">
                        <h2 class="pull-left">View Leases</h2>
						<a href="createDependents.php" class="btn btn-success pull-right">Add Dependent</a>
                    </div>
<?php

// Check existence of id parameter before processing further
if (isset($_GET["HouseId"]) && !empty(trim($_GET["HouseId"]))) {
    $_SESSION["HouseId"] = trim($_GET["HouseId"]);
}
if (isset($_GET["Street"]) && !empty(trim($_GET["Street"]))) {
    $_SESSION["Street"] = trim($_GET["Street"]);
}
if (isset($_GET["City"]) && !empty(trim($_GET["City"]))) {
    $_SESSION["City"] = trim($_GET["City"]);
}
if (isset($_GET["State"]) && !empty(trim($_GET["State"]))) {
    $_SESSION["State"] = trim($_GET["State"]);
}
if (isset($_GET["Zip"]) && !empty(trim($_GET["Zip"]))) {
    $_SESSION["Zip"] = trim($_GET["Zip"]);
}

// Check if HouseId is set in the session
if (isset($_SESSION["HouseId"])) {
    // Prepare a select statement
    $sql = "SELECT * FROM Lease WHERE hid = ?";
    
    if ($stmt = mysqli_prepare($link, $sql)) {
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "s", $param_HouseId);
        
        // Set parameters
        $param_HouseId = $_SESSION["HouseId"];
        
        // Use default values or session values for display purposes
        $Street = $_SESSION["Street"] ?? "Unknown Street";
        $City = $_SESSION["City"] ?? "Unknown City";
        $Zip = $_SESSION["Zip"] ?? "Unknown Zip";
        $State = $_SESSION["State"] ?? "Unknown State";
        
        // Attempt to execute the prepared statement
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            
            // Display property header
            echo "<h4>Leases for the ".$Street.", ".$City." ".$State.", ".$Zip."</h4><p>";
            
            if (mysqli_num_rows($result) > 0) {
                // Start table
                echo "<table class='table table-bordered table-striped'>";
                echo "<thead>";
                echo "<tr>";
                echo "<th width='10%'>Lease Id</th>";
                echo "<th width='20%'>Start Date</th>";
                echo "<th width='20%'>End Date</th>";
                echo "<th width='15%'>Monthly Rent</th>";
                echo "</tr>";
                echo "</thead>";
                echo "<tbody>";
                
                // Output data of each row
                while ($row = mysqli_fetch_array($result)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['LeaseId']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Lease_Start']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Lease_End']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Monthly_Rent']) . "</td>";
                    echo "</tr>";
                }
                echo "</tbody>";
                echo "</table>";
                
                // Free result set
                mysqli_free_result($result);
            } else {
                echo "No Leases Yet.";
            }
        } else {
            echo "Error executing query. Please try again.";
        }
        // Close statement
        mysqli_stmt_close($stmt);
    } else {
        echo "Error preparing statement.";
    }
} else {
    echo "Invalid request. House ID not provided.";
}

?>					                 					
	<p><a href="PropertyAndLease.php" class="btn btn-primary">Back</a></p>
    </div>
   </div>        
  </div>
</div>
</body>
</html>