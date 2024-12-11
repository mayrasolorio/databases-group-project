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
        <p><strong>Group 8</strong> - Team Members: Josiah Liebert, David Gesl, Mayra Solorio, Gabe McVean</p>
        <a href="PropertyAndLease.php" class="btn btn-primary">View Properties & Lease</a>
        <a href="OwnerAndManager.php" class="btn btn-success">View Owners & Managers</a>
        <a href="Renter.php" class="btn btn-info">View Renters & Renter Payments</a>
        <a href="getManagerAndPropOverview.php" class="btn btn-warning pull-right">View Properties with Manager Details</a>
    </div>
</body>
</html>
