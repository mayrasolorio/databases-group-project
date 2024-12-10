<?php
session_start();

// Include config file
require_once "config.php";

// Retrieve RenterId and LeaseId from GET or SESSION
if (isset($_GET["RenterId"])) {
    $_SESSION["RenterId"] = $_GET["RenterId"];
}
if (isset($_GET["lid"])) {
    $_SESSION["lid"] = $_GET["lid"];
}

$RenterId = $_SESSION["RenterId"] ?? null;
$lid = $_SESSION["lid"] ?? null;

if (!$RenterId || !$lid) {
    echo "Error: Missing Renter or Lease information.";
    exit;
}

// Initialize variables
$Amount = $Payment_Type = $Payment_Date = "";
$Amount_err = $Payment_Type_err = "";
$TransactionID = null;

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate amount
    $Amount = trim($_POST["Amount"]);
    if (empty($Amount) || !is_numeric($Amount) || $Amount <= 0) {
        $Amount_err = "Please enter a valid amount.";
    } else {
        // Check if the payment exceeds the remaining lease balance
        $sql = "SELECT Monthly_Rent - COALESCE(SUM(P.Amount), 0) AS Balance 
                FROM Lease L 
                LEFT JOIN Payments P ON L.lid = P.lid 
                WHERE L.lid = ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "i", $param_lid);
            $param_lid = $lid;
            if (mysqli_stmt_execute($stmt)) {
                $result = mysqli_stmt_get_result($stmt);
                if ($row = mysqli_fetch_assoc($result)) {
                    $Balance = $row["Balance"];
                    if ($Amount > $Balance) {
                        $Amount_err = "Payment exceeds remaining balance of $Balance.";
                    }
                }
                mysqli_free_result($result);
            }
            mysqli_stmt_close($stmt);
        }
    }

    // Validate payment type
    $Payment_Type = trim($_POST["Payment_Type"]);
    if (empty($Payment_Type)) {
        $Payment_Type_err = "Please select a payment type.";
    }

    // If no errors, proceed with payment insertion
    if (empty($Amount_err) && empty($Payment_Type_err)) {
        // Increment TransactionID
        $sql = "SELECT MAX(TransactionID) AS MaxTransactionID FROM Payments";
        $result = mysqli_query($link, $sql);
        if ($result) {
            $row = mysqli_fetch_assoc($result);
            $TransactionID = $row["MaxTransactionID"] + 1;
        } else {
            $TransactionID = 1; // Start with 1 if no transactions exist
        }

        // Insert payment record
        $sql = "INSERT INTO Payments (TransactionID, Amount, Payment_Type, Payment_Date, lid) VALUES (?, ?, ?, NOW(), ?)";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "iisi", $TransactionID, $Amount, $Payment_Type, $lid);
            if (mysqli_stmt_execute($stmt)) {
                // Link payment to renter in the "makes" table
                $sql_makes = "INSERT INTO makes (RentId, TransacID) VALUES (?, ?)";
                if ($stmt_makes = mysqli_prepare($link, $sql_makes)) {
                    mysqli_stmt_bind_param($stmt_makes, "ii", $RenterId, $TransactionID);
                    if (mysqli_stmt_execute($stmt_makes)) {
                        header("location: ViewPayments.php"); // Redirect to view payments
                        exit();
                    } else {
                        echo "Error inserting into makes table.";
                    }
                    mysqli_stmt_close($stmt_makes);
                }
            } else {
                echo "Error inserting payment.";
            }
            mysqli_stmt_close($stmt);
        }
    }
    mysqli_close($link);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Payment</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style>
        .wrapper {
            width: 500px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2>Add Payment</h2>
        <p>Complete the form below to add a payment for Renter ID: <?php echo htmlspecialchars($RenterId); ?>, Lease ID: <?php echo htmlspecialchars($lid); ?>.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group <?php echo (!empty($Amount_err)) ? 'has-error' : ''; ?>">
                <label>Amount</label>
                <input type="number" name="Amount" class="form-control" value="<?php echo $Amount; ?>" step="0.01">
                <span class="help-block"><?php echo $Amount_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($Payment_Type_err)) ? 'has-error' : ''; ?>">
                <label>Payment Type</label>
                <select name="Payment_Type" class="form-control">
                    <option value="" disabled selected>Select a type</option>
                    <option value="Credit Card" <?php echo ($Payment_Type == "Credit Card") ? "selected" : ""; ?>>Credit Card</option>
                    <option value="Bank Transfer" <?php echo ($Payment_Type == "Bank Transfer") ? "selected" : ""; ?>>Bank Transfer</option>
                    <option value="Cash" <?php echo ($Payment_Type == "Cash") ? "selected" : ""; ?>>Cash</option>
                </select>
                <span class="help-block"><?php echo $Payment_Type_err; ?></span>
            </div>
            <input type="submit" class="btn btn-primary" value="Submit">
            <a href="ViewPayments.php" class="btn btn-default">Cancel</a>
        </form>
    </div>
</body>
</html>