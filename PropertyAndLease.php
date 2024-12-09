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
//		include "header.php";
	?>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
		    <div class="page-header clearfix">
		     <h2> Property Manangement Database (CS 340) </h2> 
                       <p> Project should include CRUD operations. In this website you can:
				<ol> 	<li> CREATE new properties and leases </li>
					<li> RETRIEVE all property managers and owners for a property and all renters and payments for a lease</li>
                                        <li> UPDATE renter records</li>
					<li> DELETE property manager, owner, and renter records </li>
				</ol>
		       <h2 class="pull-left">Property Details</h2>
            
               <a href="createEmployee.php" class="btn btn-success pull-right">Add New Employee</a>
                    </div>
                    <?php
                    // Include config file
                    require_once "config.php";
                    
                    // Attempt select all employee query execution
					// *****
					// Insert your function for Salary Level
					/*
						$sql = "SELECT Ssn,Fname,Lname,Salary, Address, Bdate, PayLevel(Ssn) as Level, Super_ssn, Dno
							FROM EMPLOYEE";
					*/
                    $sql = "SELECT HouseId,Street,City,Zip, State, Availability_Status, oid, mid
							FROM Property";
                    if($result = mysqli_query($link, $sql)){
                        if(mysqli_num_rows($result) > 0){
                            echo "<table class='table table-bordered table-striped'>";
                                echo "<thead>";
                                    echo "<tr>";
                                        echo "<th width=8%>House Id</th>";
                                        echo "<th width=10%>Street</th>";
                                        echo "<th width=10%>City</th>";
                                        echo "<th width=5%>State</th>";
										echo "<th width=10%>Zip</th>";
										echo "<th width = 5%>Availability Status</th>";
                                        // echo "<th width=10%>Owner Id </th>";
                                        // echo "<th width =8%>Manager Id </th>";
                                        echo "<th width=10%>Action</th>";
                                    echo "</tr>";
                                echo "</thead>";
                                echo "<tbody>";
                                while($row = mysqli_fetch_array($result)){
                                    echo "<tr>";
                                        echo "<td>" . $row['HouseId'] . "</td>";
                                        echo "<td>" . $row['Street'] . "</td>";
                                        echo "<td>" . $row['City'] . "</td>";
										echo "<td>" . $row['State'] . "</td>";									
										echo "<td>" . $row['Zip'] . "</td>";
                                        echo "<td>" . $row['Availability_Status'] . "</td>";										
                                        // echo "<td>" . $row['oid'] . "</td>";
										// echo "<td>" . $row['mid'] . "</td>";
                                        echo "<td>";
                                            echo "<a href='LeaseFromProperty.php?HouseId=". $row['HouseId'].
                                            "&Street=" . urlencode($row['Street']) .
                                            "&City=" . urlencode($row['City']) .
                                            "&State=" . urlencode($row['State']) .
                                            "&Zip=" . urlencode($row['Zip']) .
                                            "' title='View Leases' data-toggle='tooltip'>
                                            <span class='glyphicon glyphicon-pencil'></span></a>";
                                        echo "</td>";
                                    echo "</tr>";
                                }
                                echo "</tbody>";                            
                            echo "</table>";
                            // Free result set
                            mysqli_free_result($result);
                        } else{
                            echo "<p class='lead'><em>No records were found.</em></p>";
                        }
                    } else{
                        echo "ERROR: Could not able to execute $sql. <br>" . mysqli_error($link);
                    }

                    echo "<br> <h2>Lease Details</h2> <br>";
                    $sql2 = "
                    SELECT 
                        L.LeaseId,
                        L.hid,
                        L.Lease_Start,
                        L.Lease_End,
                        L.Monthly_Rent,
                        TIMESTAMPDIFF(MONTH, L.Lease_Start, L.Lease_End) + 1 AS LeaseDuration,
                        SUM(L.Monthly_Rent) - IFNULL(SUM(P.Amount), 0) AS AllRentLeft
                    FROM 
                        Lease L
                    LEFT JOIN 
                        Payments P ON L.LeaseId = P.lid
                    GROUP BY 
                        L.LeaseId
                    ";

                    if($result2 = mysqli_query($link, $sql2)){
                        if(mysqli_num_rows($result2) > 0){
                            echo "<div class='col-lg-7'>";
							echo "<table class='table table-bordered table-striped'>";
                                echo "<thead>";
                                    echo "<tr>";
                                        echo "<th width=10%>Lease Id</th>";
                                        echo "<th width=10%>House Id</th>";                                        
                                        echo "<th width = 20%>Start Date</th>";
                                        echo "<th width = 20%>End Date</th>";
                                        echo "<th width = 15%>Monthly Rent</th>";
                                        echo "<th width=15%>Lease Duration (Months)</th>";
                                        echo "<th width=15%>Rent Owed</th>";
                                echo "</thead>";
                                echo "<tbody>";
                                while($row = mysqli_fetch_array($result2)){
                                    echo "<tr>";
                                        echo "<td>" . $row['LeaseId'] . "</td>";
                                        echo "<td>" . $row['hid'] . "</td>";
                                        echo "<td>" . $row['Lease_Start'] . "</td>";
                                        echo "<td>" . $row['Lease_End'] . "</td>";
                                        echo "<td>" . $row['Monthly_Rent'] . "</td>";
                                        echo "<td>" . $row['LeaseDuration'] . "</td>";
                                        echo "<td>" . $row['AllRentLeft'] . "</td>";
                                    echo "</tr>";
                                }
                                echo "</tbody>";                            
                            echo "</table>";
                            // Free result set
                            mysqli_free_result($result2);
                        } else{
                            echo "<p class='lead'><em>No records were found for Dept Stats.</em></p>";
                        }
                    } else{
                        echo "ERROR: Could not able to execute $sql2. <br>" . mysqli_error($link);
                    }
					
                    mysqli_close($link);
                    ?>
                </div>

</body>
