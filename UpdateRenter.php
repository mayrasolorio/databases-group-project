<?php
session_start();

// Include config file
require_once "config.php";

// Define variables and initialize with empty values
$Renter_FName = $Renter_LName = $Renter_Phone = "";
$Renter_FName_err = $Renter_LName_err = $Renter_Phone_err = "";

// Check if the RenterId is passed via GET and if the logged-in user matches the RenterId
if (isset($_GET["RenterId"]) && !empty(trim($_GET["RenterId"]))) {
    $_SESSION["RenterId"] = $_GET["RenterId"];

        

        // Prepare a select statement to fetch renter details
    $sql1 = "SELECT * FROM Renter WHERE RenterId = ?";
    if ($stmt1 = mysqli_prepare($link, $sql1)) {
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt1, "i", $param_RenterId);

        // Set parameters
        $param_RenterId = $_SESSION["RenterId"];

        // Execute the prepared statement
        if (mysqli_stmt_execute($stmt1)) {
            $result1 = mysqli_stmt_get_result($stmt1);
            if (mysqli_num_rows($result1) > 0) {
                $row = mysqli_fetch_array($result1);

                // Assign values to variables for pre-filling the form
                $Renter_FName = $row['Renter_FName'];
                $Renter_LName = $row['Renter_LName'];
                $Renter_Phone = $row['Renter_Phone'];
            } 
        }

    }
}
// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $RenterId = $_SESSION["RenterId"];
    $old_Renter_FName = $_SESSION["Renter_FName"];
    // Validate First Name
    $Renter_FName = trim($_POST["Renter_FName"]);
    if (empty($Renter_FName)) {
        $Renter_FName_err = "Please enter the first name.";
    }

    // Validate Last Name
    $Renter_LName = trim($_POST["Renter_LName"]);
    if (empty($Renter_LName)) {
        $Renter_LName_err = "Please enter the last name.";
    }

    // Validate Phone (only allow phone change for logged-in user)

        $Renter_Phone = trim($_POST["Renter_Phone"]);
        if (empty($Renter_Phone)) {
            $Renter_Phone_err = "Please enter the phone number.";
        if (!preg_match("/^[0-9]{10}$/", $Renter_Phone)) {
            $Renter_Phone_err = "Please enter a valid 10-digit phone number.";
        }
    } 

    // Check for errors before updating in the database
    if (empty($Renter_FName_err) && empty($Renter_LName_err) && empty($Renter_Phone_err)) {
        // Prepare an update statement
        $sql = "UPDATE Renter SET Renter_FName = ?, Renter_LName = ?, Renter_Phone = ? WHERE RenterId = ?";

        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement
            mysqli_stmt_bind_param($stmt, "sssi", $param_Renter_FName, $param_Renter_LName, $param_Renter_Phone, $param_RenterId);

            // Set parameters
            $param_Renter_FName = $Renter_FName;
            $param_Renter_LName = $Renter_LName;
            $param_Renter_Phone = $Renter_Phone;
            $param_RenterId = $_SESSION["RenterId"];

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Redirect to the main page after successful update
                header("location: Renter.php");
                exit();
            } else {
                echo "Error updating the record.";
            }
        }
        // Close the statement
        mysqli_stmt_close($stmt);
    }

    // Close the database connection
    mysqli_close($link);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Renter</title>
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
                        <h3>Update Renter Record for <?php echo htmlspecialchars($Renter_FName . " " . $Renter_LName); ?></h3>
                    </div>
                    <p>Please edit the input values and submit to update the record.</p>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="form-group <?php echo (!empty($Renter_FName_err)) ? 'has-error' : ''; ?>">
                            <label>First Name</label>
                            <input type="text" name="Renter_FName" class="form-control" value="<?php echo $Renter_FName; ?>">
                            <span class="help-block"><?php echo $Renter_FName_err; ?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($Renter_LName_err)) ? 'has-error' : ''; ?>">
                            <label>Last Name</label>
                            <input type="text" name="Renter_LName" class="form-control" value="<?php echo $Renter_LName; ?>">
                            <span class="help-block"><?php echo $Renter_LName_err; ?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($Renter_Phone_err)) ? 'has-error' : ''; ?>">
                            <label>Phone</label>
                            <input type="text" name="Renter_Phone" class="form-control" value="<?php echo $Renter_Phone; ?>">
                            <span class="help-block"><?php echo $Renter_Phone_err; ?></span>
                        </div>
                        <input type="submit" class="btn btn-primary" value="Submit">
                        <a href="Renter.php" class="btn btn-default">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
