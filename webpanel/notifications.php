<?php
// We need to use sessions, so you should always start sessions using the below code.
session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
    header('Location: index.html');
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
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Notification</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.7.5/css/bulma.min.css">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script defer src="https://use.fontawesome.com/releases/v5.3.1/js/all.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
    <script src="https://blackops.f5ads.com/Notifications2019/app/lib/subscriptionHandler.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <link rel="stylesheet"  href="https://cdnjs.cloudflare.com/ajax/libs/clockpicker/0.0.7/jquery-clockpicker.css"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/clockpicker/0.0.7/jquery-clockpicker.js"></script>
    <script src="../lib/push.js"></script>
    <script src="../lib/detector.js"></script>
    <script>
        $(document).ready(function () {

            let isPushEnabled = false;

            const pushButton = document.querySelector('#push-subscription-button');

            $( function() {
                $( "#scheduleDate" ).datepicker();
            } );

            var input = $('#input-time');
            input.clockpicker({
                autoclose: true
            });

            pushButton.addEventListener('click', function () {

                if (isPushEnabled) {
                    subscriptionHandler.push_unsubscribe();
                } else {
                    subscriptionHandler.init();
                    changePushButtonState('enabled');
                }
            });

            function changePushButtonState(state) {
                switch (state) {
                    case 'enabled':
                        pushButton.disabled = false;
                        pushButton.textContent = 'Disable Push notifications';
                        isPushEnabled = true;
                        break;
                    case 'disabled':
                        pushButton.disabled = false;
                        pushButton.textContent = 'Enable Push notifications';
                        isPushEnabled = false;
                        break;
                    case 'computing':
                        pushButton.disabled = true;
                        pushButton.textContent = 'Loading...';
                        break;
                    case 'incompatible':
                        pushButton.disabled = true;
                        pushButton.textContent = 'Push notifications are not compatible with this browser';
                        break;
                    default:
                        console.error('Unhandled push button state', state);
                        break;
                }
            }

            const sendPushButton = document.querySelector('#send-push-button');

            sendPushButton.addEventListener('click', function () {
                var domainIds = [];

                $.each($("#domainId option:selected"), function(){

                    domainIds.push($(this).val());

                });
                var msgtxt = $('#message').val();
                var url = $('#url').val();
                var title = $('#title').val();
                var imageName = $('#image').val();// $('#fileimage').val();
                var iconName = $('#icon').val();//$('#fileicon').val();

                var date_send = $('#scheduleDate').val();
                var time_send = $('#input-time').val();
                var date = date_send.concat(" ").concat(time_send).concat(":00");
                var json =
                    {"message": msgtxt,  "domainIds" : domainIds, "title" : title, "icon_url" : iconName, "image_url" : imageName, "click_url" : url, "date" : date};

                console.log(json);
                $.ajax({
                    type:"POST",
                    contentType: 'application/json',
                    data: JSON.stringify({json}), //{json: JSON.stringify(info)},
                    url:"http://localhost/fluffy-octo-couscous/app/endpoints/queue.php",
                    //url:"https://blackops.f5ads.com/Notifications2019/app/endpoints/notification.php",
                    success : function(data) {
                        console.log(data);

                    },
                    error : function() {
                        console.log("error")
                    }
                });


            })
            ;
            $.ajax({
                type: "GET",
                dataType: 'json',
                url:"http://localhost/fluffy-octo-couscous/app/endpoints/tracking.php",
                //url: 'https://blackops.f5ads.com/Notifications2019/app/endpoints/tracking.php',
                success: function (result) {
                    var $dropdown = $("#domainId");
                    $.each(result, function () {
                        $dropdown.append($("<option />").val(this.domainId).text(this.domain_name));
                    });
                }
            });

        })
        ;
    </script>

    <style>
        input {
            width: 80%;
        }
    </style>
</head>
<body class="loggedin">
<nav class="navtop">
    <div>
        <a href="stats.php"><i class="fas fa-user-circle"></i>Stats</a>
        <a href="profile.php"><i class="fas fa-user-circle"></i>Profile</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a>
    </div>
</nav>
<div class="content">

    <?php

    $ipaddress = '';

    $clientIP = false;
    /*if (array_key_exists('HTTP_CLIENT_IP', $_SERVER))
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if ($_SERVER['HTTP_X_FORWARDED_FOR'])
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if ($_SERVER['HTTP_X_FORWARDED'])
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if ($_SERVER['HTTP_FORWARDED_FOR'])
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if ($_SERVER['HTTP_FORWARDED'])
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if ($_SERVER['REMOTE_ADDR'])
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
    */?>

    <?php
    echo '<input id="ip" type="hidden" value="' . $ipaddress . '">';
    ?>
    <section class="section">
        <div class="container">
            <h1 class="title">
                Send notifications
            </h1>

            <div class="select is-multiple">Domain Id
                <select multiple size="4" id="domainId">
                </select>
            </div>

            <div class="field">
                <label class="label">URL</label>
                <div class="control">
                    <input id="url" type="text"/>
                </div>
            </div>
            <div class="field">
                <label class="label">Title</label>
                <div class="control">
                    <input id="title" type="text"/>
                </div>
            </div>

            <div class="field">
                <label class="label">Icon URL</label>
                <div class="control">
                    <input id="icon" type="text"
                           value="https://www.google.ca/images/branding/googlelogo/1x/googlelogo_color_272x92dp.png"/>
                </div>
            </div>
            <div class="field">
                <label class="label">Image URL</label>
                <div class="control">
                    <input id="image" type="text"
                           value="https://www.google.ca/images/branding/googlelogo/1x/googlelogo_color_272x92dp.png"/>
                </div>
            </div>

            <div class="field">
                <label class="label">Date</label>
                <div class="control">
                    <input type="text" id="scheduleDate">
                </div>
            </div>

            <div class="field">
                <label class="label">Time</label>
                <div class="control">
                    <input id="input-time" value="">
                </div>
            </div>


            <div class="field">
                <label class="label">Message</label>
                <div class="control">
                    <textarea class="textarea" id="message" cols="50" rows="10"></textarea>
                </div>
            </div>
            <div class="field is-grouped">
                <div class="control">
                    <button id="send-push-button" class="button is-link">Send Notifications</button>
                </div>
                <div class="control">
                    <button id="push-subscription-button" class="button is-text">Add me to push notifications</button>
                </div>
            </div>

        </div>

    </section>



</div>
</body>
</html>