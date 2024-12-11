<?php
session_start();

// Include config file
require_once "config.php";

// Variable to hold the query results
$query_results = null;

// SQL query to fetch property details with manager information. This was imported from the project document
$sql = "
SELECT 
    p.HouseId, 
    CONCAT(p.Street, ', ', p.City, ', ', p.State, ' ', p.Zip) AS Full_Address, 
    pm.Manager_Fname
FROM Property p
JOIN Prop_Manager pm ON p.mid = pm.ManagerId
";

if ($result = mysqli_query($link, $sql)) {
    // If there are results, store them in $query_results
    if (mysqli_num_rows($result) > 0) {
        $query_results = $result;
    } else {
        $query_results = null;
    }
} else {
    echo "ERROR: Could not execute the query. " . mysqli_error($link);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manager and Property Overview</title>
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
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header">
                        <h2>Properties with Manager Details</h2>
                    </div>

                    <!-- Displaying the properties and manager details -->
                    <?php
                    if ($query_results) {
                        echo "<table class='table table-bordered'>";
                            echo "<thead>";
                                echo "<tr>";
                                    echo "<th>House ID</th>";
                                    echo "<th>Full Address</th>";
                                    echo "<th>Manager First Name</th>";
                                echo "</tr>";
                            echo "</thead>";
                            echo "<tbody>";
                            // Loop through the results and display them
                            while ($row = mysqli_fetch_array($query_results)) {
                                echo "<tr>";
                                    echo "<td>" . $row['HouseId'] . "</td>";
                                    echo "<td>" . $row['Full_Address'] . "</td>";
                                    echo "<td>" . $row['Manager_Fname'] . "</td>";
                                echo "</tr>";
                            }
                            echo "</tbody>";                            
                        echo "</table>";
                        // Free result set
                        mysqli_free_result($query_results);
                    } else {
                        echo "<p>No records found for the query.</p>";
                    }
                    ?>
                    
                    <!-- Back Button -->
                    <div class="form-group">
                        <a href="index.php" class="btn btn-primary">Back to Home</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
