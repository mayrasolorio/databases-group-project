<?php
session_start();

if (isset($_GET["HouseId"]) && !empty(trim($_GET["HouseId"]))) {
    $_SESSION["HouseId"] = $_GET["HouseId"];
}

require_once "config.php";

// Delete a property's record after confirmation
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_SESSION["HouseId"]) && !empty($_SESSION["HouseId"])) {
        $HouseId = $_SESSION["HouseId"];
        // Prepare a delete statement
        $sql = "DELETE FROM Property WHERE HouseId = ?";

        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "i", $param_HouseId);

            // Set parameters
            $param_HouseId = $HouseId;

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Records deleted successfully. Redirect to landing page
                header("location: PropertyAndLease.php");
                exit();
            } else {
                echo "Error deleting the property.";
            }
        }
    }
    // Close statement
    mysqli_stmt_close($stmt);

    // Close connection
    mysqli_close($link);
} else {
    // Check existence of HouseId parameter
    if (empty(trim($_GET["HouseId"]))) {
        // URL doesn't contain HouseId parameter. Redirect to error page
        header("location: error.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Delete Property</title>
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
                        <h1>Delete Property</h1>
                    </div>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="alert alert-danger fade in">
                            <input type="hidden" name="HouseId" value="<?php echo ($_SESSION["HouseId"]); ?>" />
                            <p>Are you sure you want to delete the record for property ID: <?php echo ($_SESSION["HouseId"]); ?>?</p><br>
                            <input type="submit" value="Yes" class="btn btn-danger">
                            <a href="PropertyAndLease.php" class="btn btn-default">No</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
