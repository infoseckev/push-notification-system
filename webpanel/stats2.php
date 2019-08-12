<?php
session_start();

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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jsgrid/1.5.3/jsgrid.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jsgrid/1.5.3/jsgrid-theme.min.css"/>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jsgrid/1.5.3/jsgrid.min.js"></script>

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
    <link href="style.css" rel="stylesheet" type="text/css">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <script>
        $(function () {

            $("#jsGrid").jsGrid({
                width: "100%",
                height: "auto",

                autoload: true,
                filtering: false,
                inserting: false,
                editing: false,
                selecting: true,
                sorting: true,
                paging: true,
                pageSize: 15,
                pageButtonCount: 5,
                pageIndex: 1,
                sortname: "date_sent",
                sortorder: "asc",
                controller: {
                    loadData: function () {
                        return $.ajax({
                            url: "https://hits.f5ads.com/loadData/",
                            dataType: "json"
                        });
                    }
                },
                fields: [
                    {name: "sent_id", width: 80},
                    {name: "date_sent", width: 50},
                    {name: "sent_amount", width: 50},
                    {name: "received_amount", width: 50},
                    {name: "clicked_amount", width: 50}
                ]
            });

        });
    </script>
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

<div id="jsGrid"></div>

</body>
</html>
