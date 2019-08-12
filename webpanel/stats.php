<?php
session_start();
# include this file at the very top of your script
require_once('preheader.php');

# the code for the class
include ('ajaxCRUD.class.php');

# this one line of code is how you implement the class
$tblCustomer = new ajaxCRUD("Queues",
"sent_logs", "id");

# don't show the primary key in the table
$tblCustomer->omitPrimaryKey();

$tblCustomer->displayAs("date_sent", "Date Sent");
$tblCustomer->displayAs("is_sent", "Is Sent?");
$tblCustomer->displayAs("is_received", "Is Received?");
$tblCustomer->displayAs("is_clicked", "Is Clicked?");
$tblCustomer->omitField("endpointId");

$tblCustomer->defineRelationship("domain_id", "domains", "id", "domain_name");

$tblCustomer->disallowAdd();
$tblCustomer->turnOffAjaxEditing();
$tblCustomer->disallowDelete();
$tblCustomer->addAjaxFilterBoxAllFields();
//$tblFriend->orderFields("pkFriendID, fldAddress, fldName, fldState");
# define allowable fields for my dropdown fields
# (this can also be done for a pk/fk relationship)
/*$values = array("Cash", "Credit Card", "Paypal");
$tblCustomer->defineAllowableValues("fldPaysBy", $values);

# add the filter box (above the table)
$tblCustomer->addAjaxFilterBox("fldFName");

# add validation to certain fields (via jquery in validation.js)
$tblCustomer->modifyFieldWithClass("fldPhone", "phone");
$tblCustomer->modifyFieldWithClass("fldZip", "zip");*/

# actually show to the table


?>
<?php
// We need to use sessions, so you should always start sessions using the below code.
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
    header('Location: ../index.html');
    exit();
}
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
        <a href="stats2.php"><i class="fas fa-poll"></i>Stats</a>
        <a href="profile.php"><i class="fas fa-user-circle"></i>Profile</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a>
    </div>
</nav>
<?php $tblCustomer->showTable();