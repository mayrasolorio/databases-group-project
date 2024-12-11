<?php
// Include config file for DB connection
require_once "config.php";

// Define variables and initialize with empty values
$Owner_Fname = $Owner_Lname = $OwnerId = $Owner_Phone = "";
$Owner_Fname_err = $Owner_Lname_err = $OwnerId_err = $Owner_Phone_err = "";

// Fetch the current highest OwnerId from the database (use this for auto incrementing)
$sql = "SELECT MAX(OwnerId) AS max_id FROM Owner";
if ($result = mysqli_query($link, $sql)) {
    if ($row = mysqli_fetch_assoc($result)) {
        $OwnerId = $row['max_id'] + 1; // Increment the highest OwnerId by 1
    }
} else {
    $OwnerId = 1; // If no owners exist, start with OwnerId = 1
}

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate Owner_Fname
    $Owner_Fname = trim($_POST["Owner_Fname"]);
    if (empty($Owner_Fname)) {
        $Owner_Fname_err = "Please enter the owner's first name.";
    }

    // Validate Owner_Lname
    $Owner_Lname = trim($_POST["Owner_Lname"]);
    if (empty($Owner_Lname)) {
        $Owner_Lname_err = "Please enter the owner's last name.";
    }

    // Validate Owner_Phone
    $Owner_Phone = trim($_POST["Owner_Phone"]);
    if (empty($Owner_Phone)) {
        $Owner_Phone_err = "Please enter the owner's phone number.";
    } elseif (!preg_match('/^\d{10}$/', $Owner_Phone)) {
        $Owner_Phone_err = "Phone number must be 10 digits.";
    }

    // Check input errors before inserting into database
    if (empty($Owner_Fname_err) && empty($Owner_Lname_err) && empty($Owner_Phone_err)) {
        // Prepare an insert statement
        $sql = "INSERT INTO Owner (OwnerId, Owner_Fname, Owner_Lname, Owner_Phone) 
                VALUES (?, ?, ?, ?)";

        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "isss", $param_OwnerId, $param_Owner_Fname, $param_Owner_Lname, $param_Owner_Phone);

            // Set parameters
            $param_OwnerId = $OwnerId;
            $param_Owner_Fname = $Owner_Fname;
            $param_Owner_Lname = $Owner_Lname;
            $param_Owner_Phone = $Owner_Phone;

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Record created successfully. Redirect to the landing page
                header("location: index.php");
                exit();
            } else {
                echo "<center><h4>Error while adding the owner: " . mysqli_stmt_error($stmt) . "</h4></center>";
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
    <title>Add Owner</title>
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
                        <h2>Add Owner</h2>
                    </div>
                    <p>Please fill this form to add a new owner to the database.</p>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="form-group <?php echo (!empty($OwnerId_err)) ? 'has-error' : ''; ?>">
                            <label>Owner ID</label>
                            <input type="text" name="OwnerId" class="form-control" value="<?php echo $OwnerId; ?>" readonly>
                            <span class="help-block"><?php echo $OwnerId_err; ?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($Owner_Fname_err)) ? 'has-error' : ''; ?>">
                            <label>First Name</label>
                            <input type="text" name="Owner_Fname" class="form-control" value="<?php echo $Owner_Fname; ?>">
                            <span class="help-block"><?php echo $Owner_Fname_err; ?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($Owner_Lname_err)) ? 'has-error' : ''; ?>">
                            <label>Last Name</label>
                            <input type="text" name="Owner_Lname" class="form-control" value="<?php echo $Owner_Lname; ?>">
                            <span class="help-block"><?php echo $Owner_Lname_err; ?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($Owner_Phone_err)) ? 'has-error' : ''; ?>">
                            <label>Phone Number</label>
                            <input type="text" name="Owner_Phone" class="form-control" value="<?php echo $Owner_Phone; ?>">
                            <span class="help-block"><?php echo $Owner_Phone_err; ?></span>
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
