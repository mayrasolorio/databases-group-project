<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Navigation</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        .wrapper {
            width: 500px;
            margin: 50px auto;
            text-align: center;
        }
        .btn {
            width: 100%;
            margin-bottom: 15px;
            padding: 15px;
            font-size: 16px;
        }
        h2 {
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2>Welcome to the Management Portal</h2>
        <a href="PropertyAndLease.php" class="btn btn-primary">View Properties & Lease</a>
        <a href="OwnerAndManager.php" class="btn btn-success">View Owners & Managers</a>
        <a href="Renter.php" class="btn btn-info">View Renters & Make Payments</a>
        <a href="getManagerAndPropOverview.php" class="btn btn-warning pull-right">View Properties with Manager Details</a>
    </div>
</body>
</html>
