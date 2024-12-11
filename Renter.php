<?php
	session_start();
	//$currentpage="View Employees"; 
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
        .wrapper{
            width: 70%;
            margin:0 auto;
        }
        table tr td:last-child a{
            margin-right: 15px;
        }
    </style>
    <script type="text/javascript">
        $(document).ready(function(){
            $('[data-toggle="tooltip"]').tooltip();   
        });
        $('.selectpicker').selectpicker();
    </script>
</head>
<body>
    <?php
        // Include config file
        require_once "config.php";
    ?>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header clearfix">
                        <h2> Property Manangement Database (CS 340) </h2> 
                        <p> Project should include CRUD operations. In this website you can:
                            <ol> 
                                <li> CREATE new properties and leases </li>
                                <li> RETRIEVE all property managers and owners for a property and all renters and payments for a lease</li>
                                <li> UPDATE renter records</li>
                                <li> DELETE property manager, owner, and renter records </li>
                            </ol>
                        <h2 class="pull-left">Renter Details</h2>
                    </div>
                                       <?php
                    // Fetch Renter records from the database
                    $sql = "SELECT * FROM Renter";
                    if($result = mysqli_query($link, $sql)){
                        if(mysqli_num_rows($result) > 0){
                            echo "<table class='table table-bordered table-striped'>";
                            echo "<thead>";
                                echo "<tr>";
                                    echo "<th width=10%>Renter Id</th>";
                                    echo "<th width=10%>First Name</th>";                                        
                                    echo "<th width = 20%>Last Name</th>";
                                    echo "<th width = 20%>Phone Number</th>";
                                    echo "<th width = 15%>Lease Id</th>";
                                    echo "<th width=10%>Action</th>";
                                echo "</tr>";
                            echo "</thead>";
                            echo "<tbody>";
                                while($row = mysqli_fetch_array($result)){
                                    echo "<tr>";
                                        echo "<td>" . $row['RenterId'] . "</td>";
                                        echo "<td>" . $row['Renter_FName'] . "</td>";
                                        echo "<td>" . $row['Renter_LName'] . "</td>";
                                        echo "<td>" . $row['Renter_Phone'] . "</td>";
                                        echo "<td>" . $row['lid'] . "</td>";
                                        echo "<td>";
                                            echo "<a href='PaymentsFromRenter.php?RenterId=". $row['RenterId']."&Renter_FName=".$row['Renter_FName']."' title='View Payments' data-toggle='tooltip'><span class='glyphicon glyphicon-usd'></span></a>";
                                            echo "<a href='UpdateRenter.php?RenterId=" . $row['RenterId'] . 
                                            "&RenterFName=" . $row['Renter_FName'] . 
                                            "&RenterLName=" . $row['Renter_LName'] . 
                                            "' title='Update Renter Info' data-toggle='tooltip'>
                                            <span class='glyphicon glyphicon-pencil'></span></a>";                                       
                                        echo "</td>";
                                    echo "</tr>";
                                }
                                echo "</tbody>";                            
                            echo "</table>";
                            // Free result set
                            mysqli_free_result($result);                        } else{
                            echo "<p class='lead'><em>No records were found.</em></p>";
                        }
                    } else{
                        echo "ERROR: Could not able to execute $sql. <br>" . mysqli_error($link);
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
