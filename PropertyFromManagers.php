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
                        <h2 class="pull-left">View Properties</h2>
						<a href="createDependents.php" class="btn btn-success pull-right">Add Dependent</a>
                    </div>
<?php

// Check existence of id parameter before processing further
if (isset($_GET["ManagerId"]) && !empty(trim($_GET["ManagerId"]))) {
    $_SESSION["ManagerId"] = trim($_GET["ManagerId"]);
}
if (isset($_GET["Manager_Fname"]) && !empty(trim($_GET["Manager_Fname"]))) {
    $_SESSION["Manager_Fname"] = trim($_GET["Manager_Fname"]);
}

// Check if HouseId is set in the session
if (isset($_SESSION["ManagerId"])) {
    // Prepare a select statement
    $sql = "SELECT * FROM Property WHERE mid = ?";
    
    if ($stmt = mysqli_prepare($link, $sql)) {
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "s", $param_ManagerId);
        
        // Set parameters
        $param_ManagerId = $_SESSION["ManagerId"];
        
        $Manager_Fname = $_SESSION["Manager_Fname"];
        
        // Attempt to execute the prepared statement
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            
            // Display property header
            echo "<h4>Properties Managed by ".$Manager_Fname."</h4><p>";
            
            if (mysqli_num_rows($result) > 0) {
                // Start table
                echo "<table class='table table-bordered table-striped'>";
                echo "<thead>";
                echo "<tr>";
                echo "<th width=8%>House Id</th>";
                echo "<th width=10%>Street</th>";
                echo "<th width=10%>City</th>";
                echo "<th width=5%>Zip </th>";
                echo "<th width=10%>State </th>";
                echo "<th width = 5%>Availability Status</th>";
                echo "</tr>";
                echo "</thead>";
                echo "<tbody>";
                
                // Output data of each row
                while ($row = mysqli_fetch_array($result)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['HouseId']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Street']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['City']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['State']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Zip']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Availability_Status']) . "</td>";
                    echo "</tr>";
                }
                echo "</tbody>";
                echo "</table>";
                
                // Free result set
                mysqli_free_result($result);
            } else {
                echo "No Properties Yet.";
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
	<p><a href="OwnerAndManager.php" class="btn btn-primary">Back</a></p>
    </div>
   </div>        
  </div>
</div>
</body>
</html>