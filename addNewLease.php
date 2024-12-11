<?php
// Include config file for DB connection
require_once "config.php";

// Define variables and initialize with empty values
$LeaseId = $Lease_Start = $Lease_End = $Monthly_Rent = $HouseId = "";
$LeaseId_err = $Lease_Start_err = $Lease_End_err = $Monthly_Rent_err = $HouseId_err = "";

// Fetch the current highest LeaseId from the database (use this for auto incrementing)
$sql = "SELECT MAX(LeaseId) AS max_id FROM Lease";
if ($result = mysqli_query($link, $sql)) {
    if ($row = mysqli_fetch_assoc($result)) {
        $LeaseId = $row['max_id'] + 1; // Increment the highest LeaseId by 1
    }
} else {
    $LeaseId = 1; // If no leases exist, start with LeaseId = 1
}

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate Lease_Start
    $Lease_Start = trim($_POST["Lease_Start"]);
    if (empty($Lease_Start)) {
        $Lease_Start_err = "Please enter the lease start date.";
    }

    // Validate Lease_End
    $Lease_End = trim($_POST["Lease_End"]);
    if (empty($Lease_End)) {
        $Lease_End_err = "Please enter the lease end date.";
    }

    // Validate Monthly_Rent
    $Monthly_Rent = trim($_POST["Monthly_Rent"]);
    if (empty($Monthly_Rent)) {
        $Monthly_Rent_err = "Please enter the monthly rent.";
    } elseif (!is_numeric($Monthly_Rent)) {
        $Monthly_Rent_err = "Monthly rent must be a number.";
    }

    // Validate HouseId
    $HouseId = trim($_POST["HouseId"]);
    if (empty($HouseId)) {
        $HouseId_err = "Please enter the house ID.";
    }

    // Check input errors before inserting into database
    if (empty($Lease_Start_err) && empty($Lease_End_err) && empty($Monthly_Rent_err) && empty($HouseId_err)) {
        // Prepare an insert statement
        $sql = "INSERT INTO Lease (LeaseId, Lease_Start, Lease_End, Monthly_Rent, hid) 
                VALUES (?, ?, ?, ?, ?)";

        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "isssi", $param_LeaseId, $param_Lease_Start, $param_Lease_End, $param_Monthly_Rent, $param_HouseId);

            // Set parameters
            $param_LeaseId = $LeaseId;
            $param_Lease_Start = $Lease_Start;
            $param_Lease_End = $Lease_End;
            $param_Monthly_Rent = $Monthly_Rent;
            $param_HouseId = $HouseId;

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Lease added successfully. Redirect to the landing page
                header("location: index.php");
                exit();
            } else {
                echo "<center><h4>Error while adding the lease: " . mysqli_stmt_error($stmt) . "</h4></center>";
            }
        }

        // Close statement
        mysqli_stmt_close($stmt);
    }

    // Close connection
    mysqli_close($link);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Lease</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        .wrapper {
            width: 500px;
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
                        <h2>Add Lease</h2>
                    </div>
                    <p>Please fill this form to add a new lease to the database.</p>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="form-group <?php echo (!empty($LeaseId_err)) ? 'has-error' : ''; ?>">
                            <label>Lease ID</label>
                            <input type="text" name="LeaseId" class="form-control" value="<?php echo $LeaseId; ?>" readonly>
                            <span class="help-block"><?php echo $LeaseId_err; ?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($Lease_Start_err)) ? 'has-error' : ''; ?>">
                            <label>Lease Start</label>
                            <input type="date" name="Lease_Start" class="form-control" value="<?php echo $Lease_Start; ?>">
                            <span class="help-block"><?php echo $Lease_Start_err; ?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($Lease_End_err)) ? 'has-error' : ''; ?>">
                            <label>Lease End</label>
                            <input type="date" name="Lease_End" class="form-control" value="<?php echo $Lease_End; ?>">
                            <span class="help-block"><?php echo $Lease_End_err; ?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($Monthly_Rent_err)) ? 'has-error' : ''; ?>">
                            <label>Monthly Rent</label>
                            <input type="text" name="Monthly_Rent" class="form-control" value="<?php echo $Monthly_Rent; ?>">
                            <span class="help-block"><?php echo $Monthly_Rent_err; ?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($HouseId_err)) ? 'has-error' : ''; ?>">
                            <label>House ID</label>
                            <input type="text" name="HouseId" class="form-control" value="<?php echo $HouseId; ?>">
                            <span class="help-block"><?php echo $HouseId_err; ?></span>
                        </div>
                        <input type="submit" class="btn btn-primary" value="Submit">
                        <a href="index.php" class="btn btn-default">Cancel</a>
                    </form>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>
