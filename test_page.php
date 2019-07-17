<?php

$ipaddress = '';

$clientIP = false;
if (array_key_exists('HTTP_CLIENT_IP', $_SERVER))
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
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Notification</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.7.5/css/bulma.min.css">
    <script defer src="https://use.fontawesome.com/releases/v5.3.1/js/all.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
    <script src="https://blackops.f5ads.com/Notifications2019/app/lib/subscriptionHandler.js"></script>
    <script src="app/lib/push.js"></script>
    <script src="app/lib/detector.js"></script>
    <script>
        $(document).ready(function () {

            let isPushEnabled = false;

            const pushButton = document.querySelector('#push-subscription-button');

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

   sendPushButton.addEventListener('click', () =>
       navigator.serviceWorker.ready
           .then(serviceWorkerRegistration => serviceWorkerRegistration.pushManager.getSubscription())
           .then(subscription => {
             if (!subscription) {
               alert('Please enable push notifications');
               //return;
             }

             var domainIds = [];

             $.each($("#domainId option:selected"), function(){

               domainIds.push($(this).val());

             });
             //var endpointid =  $('#sites').children("option:selected").val() ;
             var msgtxt = $('#message').val();
             var url = $('#url').val();
             var title = $('#title').val();
             var imageName = $('#image').val();// $('#fileimage').val();
             var iconName = $('#icon').val();//$('#fileicon').val();

             var json =
                 {"msg": msgtxt,  "domainIds" : domainIds, "title" : title, "icon" : iconName, "image" : imageName, "url" : url};

             //var file_data = $('#fileimage').prop('files')[0];
             //var file_data2 = $('#fileicon').prop('files')[0];

             //var form_data = new FormData();
             //let jsonres = JSON.stringify(info);

             //form_data.append('image', file_data);
             //form_data.append('icon', file_data2);
             //form_data.append('json',jsonres );
            //console.log(form_data);
             $.ajax({
               type:"POST",
                 contentType: 'application/json',
               data: JSON.stringify({json}), //{json: JSON.stringify(info)},
               url:"https://blackops.f5ads.com/Notifications2019/app/endpoints/notification.php",
               //beforeSend: function (xhr) { // Add this line
                // xhr.setRequestHeader('X-CSRF-Token',csrfToken);
               //},  // Add this line
               success : function(data) {
                 console.log(data);// will alert "ok"

               },
               error : function() {
                 //alert("false");
               }
             });

           })
   );
            $.ajax({
                type: "GET",
                dataType: 'json',
                url: 'https://blackops.f5ads.com/Notifications2019/app/endpoints/tracking.php',
                success: function (result) {
                    var $dropdown = $("#domainId");
                    $.each(result, function () {
                        $dropdown.append($("<option />").val(this.domainId).text(this.domain_name));
                    });
                }
            });

        });
    </script>

    <style>
        #url, #title {
            width: 50%;
        }
    </style>
</head>
<body>
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
                <input id="icon" type="text" value="https://www.google.ca/images/branding/googlelogo/1x/googlelogo_color_272x92dp.png"/>
            </div>
        </div>
        <div class="field">
            <label class="label">Image URL</label>
            <div class="control">
                <input id="image" type="text" value="https://www.google.ca/images/branding/googlelogo/1x/googlelogo_color_272x92dp.png"/>
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


</body>

</html>