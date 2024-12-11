<?php
// Include config file for DB connection
require_once "config.php";

// Define variables and initialize with empty values
$Manager_Fname = $Company_Name = $Manager_Phone = $ManagerId = "";
$Manager_Fname_err = $Company_Name_err = $Manager_Phone_err = $ManagerId_err = "";

// Fetch the current highest ManagerId from the database (use this for auto incrementing)
$sql = "SELECT MAX(ManagerId) AS max_id FROM Prop_Manager";
if ($result = mysqli_query($link, $sql)) {
    if ($row = mysqli_fetch_assoc($result)) {
        $ManagerId = $row['max_id'] + 1; // Increment the highest ManagerId by 1
    }
} else {
    $ManagerId = 1; // If no managers exist, start with ManagerId = 1
}

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate Manager_Fname
    $Manager_Fname = trim($_POST["Manager_Fname"]);
    if (empty($Manager_Fname)) {
        $Manager_Fname_err = "Please enter the manager's first name.";
    }

    // Validate Company_Name
    $Company_Name = trim($_POST["Company_Name"]);
    if (empty($Company_Name)) {
        $Company_Name_err = "Please enter the company name.";
    }

    // Validate Manager_Phone
    $Manager_Phone = trim($_POST["Manager_Phone"]);
    if (empty($Manager_Phone)) {
        $Manager_Phone_err = "Please enter the manager's phone number.";
    } elseif (!preg_match('/^\d{10}$/', $Manager_Phone)) {
        $Manager_Phone_err = "Phone number must be 10 digits.";
    }

    // Check input errors before inserting into database
    if (empty($Manager_Fname_err) && empty($Company_Name_err) && empty($Manager_Phone_err)) {
        // Prepare an insert statement
        $sql = "INSERT INTO Prop_Manager (ManagerId, Manager_Fname, Company_Name, Manager_Phone) 
                VALUES (?, ?, ?, ?)";

        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "isss", $param_ManagerId, $param_Manager_Fname, $param_Company_Name, $param_Manager_Phone);

            // Set parameters
            $param_ManagerId = $ManagerId;
            $param_Manager_Fname = $Manager_Fname;
            $param_Company_Name = $Company_Name;
            $param_Manager_Phone = $Manager_Phone;

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Record created successfully. Redirect to the landing page
                header("location: index.php");
                exit();
            } else {
                echo "<center><h4>Error while adding the property manager: " . mysqli_stmt_error($stmt) . "</h4></center>";
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
    <title>Add Property Manager</title>
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
                        <h2>Add Property Manager</h2>
                    </div>
                    <p>Please fill this form to add a new property manager to the database.</p>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="form-group <?php echo (!empty($ManagerId_err)) ? 'has-error' : ''; ?>">
                            <label>Manager ID</label>
                            <input type="text" name="ManagerId" class="form-control" value="<?php echo $ManagerId; ?>" readonly>
                            <span class="help-block"><?php echo $ManagerId_err; ?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($Manager_Fname_err)) ? 'has-error' : ''; ?>">
                            <label>First Name</label>
                            <input type="text" name="Manager_Fname" class="form-control" value="<?php echo $Manager_Fname; ?>">
                            <span class="help-block"><?php echo $Manager_Fname_err; ?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($Company_Name_err)) ? 'has-error' : ''; ?>">
                            <label>Company Name</label>
                            <input type="text" name="Company_Name" class="form-control" value="<?php echo $Company_Name; ?>">
                            <span class="help-block"><?php echo $Company_Name_err; ?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($Manager_Phone_err)) ? 'has-error' : ''; ?>">
                            <label>Phone Number</label>
                            <input type="text" name="Manager_Phone" class="form-control" value="<?php echo $Manager_Phone; ?>">
                            <span class="help-block"><?php echo $Manager_Phone_err; ?></span>
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
