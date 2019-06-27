<?php
session_start();
$token = md5(rand(1000,9999));
$_SESSION['token'] = $token;

$ipaddress = '';
if ($_SERVER['HTTP_CLIENT_IP'])
    $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
else if($_SERVER['HTTP_X_FORWARDED_FOR'])
    $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
else if($_SERVER['HTTP_X_FORWARDED'])
    $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
else if($_SERVER['HTTP_FORWARDED_FOR'])
    $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
else if($_SERVER['HTTP_FORWARDED'])
    $ipaddress = $_SERVER['HTTP_FORWARDED'];
else if($_SERVER['REMOTE_ADDR'])
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
    <script src="main.js"></script>
    <script src="app.js"></script>
    <script src="lib/push.js"></script>
    <script src="detector.js"></script>

    <script>

        $(document).ready(function() {

            var file = document.getElementById("fileimage");
            file.onchange = function(){
                if(file.files.length > 0) {

                    document.getElementById('file-name').innerHTML = file.files[0].name;

                }
            };

            var file2 = document.getElementById("fileicon");
            file2.onchange = function(){
                if(file2.files.length > 0)  {

                    document.getElementById('file-name2').innerHTML = file2.files[0].name;

                }
            };

            var form_data = {
                token:'<?php echo $token; ?>',
                is_ajax: 1
            };

            $.ajax({
                type: "POST",
                url: 'getEP.php',
                data: form_data,
                success: function (result) {
                    var $dropdown = $("#sites");
                    $.each(result, function () {
                        $dropdown.append($("<option />").val(this.id).text(this.endpoint));
                    });

                   /* let uniqueURL = [];

                    $.each(result, function(res) {
                        if(res.visitingsite not in uniqueURL){
                            //add visiting site to URL
                        }
                    });*/

                    /////////TODO:get unique code. put in class///////////////////////
                    /*var outputArray = [];

                    for (var i = 0; i < result.length; i++)
                    {
                        if ((jQuery.inArray(inputArray[i], outputArray)) == -1)
                        {
                            outputArray.push(inputArray[i]);
                        }
                    }*/
                    ///////////////////////////////////////////////////////////

                }
            });

        });
    </script>

    <style>
        #url, #title {
            width:50%;
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

        <div class="select is-multiple">
            <select multiple size="8" id="sites">
               <!-- <option value="Argentina">Argentina</option>-->

            </select>
        </div>

        <!--<div class="field">
            <label class="label">Endpoint</label>
            <div class="control">
                <div class="select">
                <select id="endpoint">
                    <option value="">----Select----</option>
                </select>
                </div>
            </div>
        </div>-->

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
        <div class="file has-name is-boxed">
            <label class="file-label">
                <input multiple="multiple"  id="fileimage" class="file-input" type="file" name="file[]">
                <span class="file-cta">
                  <span class="file-icon">
                    <i class="fas fa-upload"></i>
                  </span>
                  <span class="file-label">
                    Choose an image...
                  </span>
                </span>
                <span class="file-name" id="file-name"></span>
            </label>
        <!--</div>
        <div class="file has-name is-boxed">-->
            <label class="file-label">
                <input multiple="multiple"  id="fileicon" class="file-input" type="file" name="file[]">
                <span class="file-cta">
                  <span class="file-icon">
                    <i class="fas fa-upload"></i>
                  </span>
                  <span class="file-label">
                    Choose an icon...
                  </span>
                </span>
                <span class="file-name" id="file-name2"></span>
            </label>
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
                <button  id="push-subscription-button" class="button is-text">Add me to push notifications</button>
            </div>
        </div>

    </div>

</section>


</body>

</html>