<?php
session_start();

if (!isset($_SESSION['loggedin'])) {
    header('Location: ../index.html');
    exit();
}

require_once('preheader.php');

include ('ajaxCRUD.class.php');

# this one line of code is how you implement the class
$tblCustomer = new ajaxCRUD("Domains",
    "domains", "id");

# don't show the primary key in the table
$tblCustomer->omitPrimaryKey();

?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <title>Home Page</title>
        <link href="style.css" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
        <link href="style.css" rel="stylesheet" type="text/css">
    </head>
    <body class="loggedin">
    <nav class="navtop">
        <div>
            <a href="domains.php"><i class="fas fa-industry"></i>Domains</a>
            <a href="queue.php"><i class="fas fa-water"></i>Queue</a>
            <a href="notifications.php"><i class="far fa-comment-alt"></i>Send Notifications</a>
            <a href="stats.php"><i class="fas fa-poll"></i>Stats</a>
            <a href="profile.php"><i class="fas fa-user-circle"></i>Profile</a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a>
        </div>
    </nav>
<?php $tblCustomer->showTable();