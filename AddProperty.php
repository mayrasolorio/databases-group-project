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
// Include config file for DB connection
require_once "config.php";

// Define variables and initialize with empty values
$HouseId = $Availability_Status = $Zip = $State = $Street = $City = $mid = $oid = "";
$HouseId_err = $Availability_Status_err = $Zip_err = $State_err = $Street_err = $City_err = $mid_err = $oid_err = "";

// Fetch the current highest HouseId from the database (use this for auto incrementing)
$sql = "SELECT MAX(HouseId) AS max_id FROM Property";
if ($result = mysqli_query($link, $sql)) {
    if ($row = mysqli_fetch_assoc($result)) {
        $HouseId = $row['max_id'] + 1; // Increment the highest HouseId by 1
    }
} else {
    $HouseId = 1; // If no properties exist, start with HouseId = 1
}

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate Availability_Status
    $Availability_Status = trim($_POST["Availability_Status"]);
    if ($Availability_Status !== "0" && $Availability_Status !== "1") {
        $Availability_Status_err = "Availability Status must be 0 (unavailable) or 1 (available).";
    }

    // Validate Zip
    $Zip = trim($_POST["Zip"]);
    if (empty($Zip)) {
        $Zip_err = "Please enter a ZIP code.";
    } elseif (!ctype_digit($Zip)) {
        $Zip_err = "ZIP code must be numeric.";
    }

    // Validate State
    $State = trim($_POST["State"]);
    if (empty($State)) {
        $State_err = "Please enter a state.";
    } elseif (strlen($State) != 2 || !ctype_alpha($State)) {
        $State_err = "State must be a two-letter code.";
    }

    // Validate Street
    $Street = trim($_POST["Street"]);
    if (empty($Street)) {
        $Street_err = "Please enter a street address.";
    }

    // Validate City
    $City = trim($_POST["City"]);
    if (empty($City)) {
        $City_err = "Please enter a city.";
    }

    // Validate mid (Manager ID)
    $mid = trim($_POST["mid"]);
    if (empty($mid)) {
        $mid_err = "Please enter a Manager ID.";
    } elseif (!ctype_digit($mid)) {
        $mid_err = "Manager ID must be numeric.";
    }

    // Validate oid (Owner ID)
    $oid = trim($_POST["oid"]);
    if (empty($oid)) {
        $oid_err = "Please enter an Owner ID.";
    } elseif (!ctype_digit($oid)) {
        $oid_err = "Owner ID must be numeric.";
    }

    // Check input errors before inserting in database
    if (empty($HouseId_err) && empty($Availability_Status_err) && empty($Zip_err) && empty($State_err) &&
        empty($Street_err) && empty($City_err) && empty($mid_err) && empty($oid_err)) {
        // Prepare an insert statement
        $sql = "INSERT INTO Property (HouseId, Availability_Status, Zip, State, Street, City, mid, oid) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "iissssii", $param_HouseId, $param_Availability_Status, $param_Zip,
                $param_State, $param_Street, $param_City, $param_mid, $param_oid);

            // Set parameters
            $param_HouseId = $HouseId;
            $param_Availability_Status = $Availability_Status;
            $param_Zip = $Zip;
            $param_State = $State;
            $param_Street = $Street;
            $param_City = $City;
            $param_mid = $mid;
            $param_oid = $oid;

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Records created successfully. Redirect to landing page
                header("location: index.php");
                exit();
            } else {
                echo "<center><h4>Error while adding the property: " . mysqli_stmt_error($stmt) . "</h4></center>";
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
    <title>Add Property</title>
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
                        <h2>Add Property</h2>
                    </div>
                    <p>Please fill this form to add a property to the database.</p>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="form-group <?php echo (!empty($HouseId_err)) ? 'has-error' : ''; ?>">
                            <label>House ID</label>
                            <input type="text" name="HouseId" class="form-control" value="<?php echo $HouseId; ?>" readonly>
                            <span class="help-block"><?php echo $HouseId_err; ?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($Availability_Status_err)) ? 'has-error' : ''; ?>">
                            <label>Availability Status (0 = Unavailable, 1 = Available)</label>
                            <input type="text" name="Availability_Status" class="form-control" value="<?php echo $Availability_Status; ?>">
                            <span class="help-block"><?php echo $Availability_Status_err; ?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($Zip_err)) ? 'has-error' : ''; ?>">
                            <label>ZIP Code</label>
                            <input type="text" name="Zip" class="form-control" value="<?php echo $Zip; ?>">
                            <span class="help-block"><?php echo $Zip_err; ?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($State_err)) ? 'has-error' : ''; ?>">
                            <label>State (2-letter code)</label>
                            <input type="text" name="State" class="form-control" value="<?php echo $State; ?>">
                            <span class="help-block"><?php echo $State_err; ?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($Street_err)) ? 'has-error' : ''; ?>">
                            <label>Street</label>
                            <input type="text" name="Street" class="form-control" value="<?php echo $Street; ?>">
                            <span class="help-block"><?php echo $Street_err; ?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($City_err)) ? 'has-error' : ''; ?>">
                            <label>City</label>
                            <input type="text" name="City" class="form-control" value="<?php echo $City; ?>">
                            <span class="help-block"><?php echo $City_err; ?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($mid_err)) ? 'has-error' : ''; ?>">
                            <label>Manager ID</label>
                            <input type="text" name="mid" class="form-control" value="<?php echo $mid; ?>">
                            <span class="help-block"><?php echo $mid_err; ?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($oid_err)) ? 'has-error' : ''; ?>">
                            <label>Owner ID</label>
                            <input type="text" name="oid" class="form-control" value="<?php echo $oid; ?>">
                            <span class="help-block"><?php echo $oid_err; ?></span>
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