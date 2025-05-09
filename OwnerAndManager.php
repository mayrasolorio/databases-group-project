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
                </p>
		       
                <h2 class="pull-left">Owner Details</h2>        
                <a href="AddOwner.php" class="btn btn-success pull-right">Add Owner</a>         <!--AddOwner butto for inserting owner  -->
                    </div>
                    <?php
                    // Include config file
                    require_once "config.php";
                    
                    $sql = "SELECT * FROM Owner";
                    if($result = mysqli_query($link, $sql)){
                        if(mysqli_num_rows($result) > 0){
                            // echo "<div class='col-lg-7'>";                        
                            echo "<table class='table table-bordered table-striped'>";
                                echo "<thead>";
                                    echo "<tr>";
                                        echo "<th width=8%>Owner Id</th>";
                                        echo "<th width=10%>First Name</th>";
                                        echo "<th width=10%>Last Name</th>";
                                        echo "<th width=5%>Phone Number</th>";
                                        echo "<th width=5%>Actions</th>";
                                    echo "</tr>";
                                echo "</thead>";
                                echo "<tbody>";
                                while($row = mysqli_fetch_array($result)){
                                    echo "<tr>";
                                        echo "<td>" . $row['OwnerId'] . "</td>";
                                        echo "<td>" . $row['Owner_Fname'] . "</td>";
                                        echo "<td>" . $row['Owner_Lname'] . "</td>";
										echo "<td>" . $row['Owner_Phone'] . "</td>";	
                                        echo "<td>";
                                        echo "<a href='PropertyFromOwners.php?OwnerId=" . $row['OwnerId'] . 
                                            "&Owner_Fname=" . urlencode($row['Owner_Fname']) . 
                                            "&Owner_Lname=" . urlencode($row['Owner_Lname']) . 
                                            "' title='View Properties' data-toggle='tooltip'>
                                            <span class='glyphicon glyphicon-home'></span>
                                            </a>";

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
		echo "<br> <h2>Property Manager Details</h2> <br>";
                    
                    $sql2 = "
                    SELECT 
                        pm.ManagerId, 
                        pm.Manager_Fname, 
                        pm.Company_Name, 
                        pm.Manager_Phone, 
                        COUNT(p.HouseId) AS Total_Properties 
                    FROM 
                        Prop_Manager pm 
                    LEFT JOIN 
                        Property p 
                    ON 
                        pm.ManagerId = p.mid 
                    GROUP BY 
                        pm.ManagerId, pm.Manager_Fname, pm.Company_Name, pm.Manager_Phone";
                    
                    if ($result2 = mysqli_query($link, $sql2)) {
                        if (mysqli_num_rows($result2) > 0) {
                            echo "<table class='table table-bordered table-striped'>";
                            echo "<thead>";
                            echo "<tr>";
                            echo "<th width=10%>Manager Id</th>";
                            echo "<th width=10%>First Name</th>";
                            echo "<th width=20%>Employer</th>";
                            echo "<th width=20%>Phone Number</th>";
                            echo "<th width=15%>Total Properties Managed</th>";
                            echo "<th width=5%>Actions</th>";
                            echo "</tr>";
                            echo "</thead>";
                            echo "<tbody>";
                    
                            while ($row = mysqli_fetch_array($result2)) {
                                echo "<tr>";
                                echo "<td>" . $row['ManagerId'] . "</td>";
                                echo "<td>" . $row['Manager_Fname'] . "</td>";
                                echo "<td>" . $row['Company_Name'] . "</td>";
                                echo "<td>" . $row['Manager_Phone'] . "</td>";
                                echo "<td>" . $row['Total_Properties'] . "</td>";
                                echo "<td>";
                                echo "<a href='PropertyFromManagers.php?ManagerId=" . $row['ManagerId'] . 
                                    "&Manager_Fname=" . urlencode($row['Manager_Fname']) .  
                                    "' title='View Properties' data-toggle='tooltip'>
                                    <span class='glyphicon glyphicon-home'></span>
                                    </a>";
                                echo "</td>";								
                                echo "</tr>";
                            }
                    
                            echo "</tbody>";
                            echo "</table>";
                    
                            // Free result set
                            mysqli_free_result($result2);
                        } else {
                            echo "<p class='lead'><em>No records were found for Dept Stats.</em></p>";
                        }
                    } else {
                        echo "ERROR: Could not execute $sql2. <br>" . mysqli_error($link);
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
