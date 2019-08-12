<?php
session_start();

if (!isset($_SESSION['loggedin'])) {
    header('Location: ../index.html');
    exit();
}

require_once('preheader.php');

include ('ajaxCRUD.class.php');

# this one line of code is how you implement the class
$tblCustomer = new ajaxCRUD("Queue Item",
    "notifications_queue", "id");
$tblCustomer->disallowAdd();
$tblCustomer->turnOffAjaxEditing ();
$tblCustomer->omitField("endpointId");
$tblCustomer->omitField("public_key");
$tblCustomer->omitField("auth_token");

$tblCustomer->addAjaxFilterBox('sent_id', 100);
# don't show the primary key in the table
$tblCustomer->omitPrimaryKey();
$tblCustomer->addButton("Delete All Checked", "#", 'id=deleteAll');
$tblCustomer->addButton("Select All", "#", "id=selectAll");

$tblCustomer->defineRelationship("domain_id", "domains", "id", "domain_name");


$tblCustomer->showCheckbox = true;
?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>

    <script>
        $(document).ready(function() {
            $(':checkbox').each(function() {this.onclick = $.noop;});
            $( "#deleteAll" ).click(function() {
                var checkedIds = $(":checkbox:checked").map(function(){
                    let id = $(this).parent().parent().attr("id").split("_");
                    id = id.pop();
                    return id;
                }).get(); // <----

                deleteWithoutConfirm(checkedIds, 'notifications_queue', 'id' );

            });
            $( "#selectAll" ).click(function() {
                $(':checkbox').each(function() {$(this).prop("checked", true) });// = $.noop;});
                return false;
            });

        })
    </script>
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