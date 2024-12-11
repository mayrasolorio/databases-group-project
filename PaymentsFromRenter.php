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
                        <h2 class="pull-left">View Payments</h2>
						<!-- <a href="AddPayment.php" class="btn btn-success pull-right">Add Payment</a> -->
                    </div>
<?php

// Check existence of id parameter before processing further
if (isset($_GET["RenterId"]) && !empty(trim($_GET["RenterId"]))) {
    $_SESSION["RenterId"] = trim($_GET["RenterId"]);
}
if (isset($_GET["Renter_FName"]) && !empty(trim($_GET["Renter_FName"]))) {
    $_SESSION["Renter_FName"] = trim($_GET["Renter_FName"]);
}

// Check if HouseId is set in the session
if (isset($_SESSION["RenterId"])) {
    // Prepare a select statement
    $sql = "
    SELECT 
        Payments.TransactionId,
        Payments.Amount,
        Payments.Payment_Type,
        Payments.Payment_Date,
        Payments.lid
    FROM 
        makes
    INNER JOIN 
        Payments 
    ON 
        makes.TransacID = Payments.TransactionId
    WHERE 
        makes.RentId = ?";
    
    if ($stmt = mysqli_prepare($link, $sql)) {
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "s", $param_RenterId);
        
        // Set parameters
        $param_RenterId = $_SESSION["RenterId"];
        
        // Use default values or session values for display purposes
        $Renter_FName = $_SESSION["Renter_FName"] ?? "Unknown Renter";

        
        // Attempt to execute the prepared statement
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            
            // Display property header
            echo "<h4>Payments from ".$Renter_FName."</h4><p>";
            
            if (mysqli_num_rows($result) > 0) {
                // Start table
                echo "<table class='table table-bordered table-striped'>";
                echo "<thead>";
                echo "<tr>";
                echo "<th width='10%'>Payment Id</th>";
                echo "<th width='10%'>Lease Id</th>";
                echo "<th width='20%'>Amount</th>";
                echo "<th width='20%'>Payment Type</th>";
                echo "<th width='15%'>Date</th>";
                echo "</tr>";
                echo "</thead>";
                echo "<tbody>";
                
                // Output data of each row
                while ($row = mysqli_fetch_array($result)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['TransactionId']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['lid']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Amount']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Payment_Type']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Payment_Date']) . "</td>";
                    echo "</tr>";
                }
                echo "</tbody>";
                echo "</table>";
                
                // Free result set
                mysqli_free_result($result);
            } else {
                echo "No Payments Yet.";
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
    echo "Invalid request. Renter ID not provided.";
}

?>					                 					
	<p><a href="Renter.php" class="btn btn-primary">Back</a></p>
    </div>
   </div>        
  </div>
</div>
</body>
</html>