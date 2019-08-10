<?php
$tracking_gps =  ($_GET['GPS']);
/*$percentage = 100;
$num = rand(0,99);

if ($num < $percentage){*/
    header('Service-Worker-Allowed: *');

    header("Content-Type: application/javascript");
    echo "var ip = \"". $_SERVER['REMOTE_ADDR'] . "\";\r\n";
    echo "var gps = \"". $tracking_gps . "\";\r\n";
    echo file_get_contents("app/lib/detector.js");
    echo  file_get_contents("app/lib/subscriptionHandler.js");
    echo 'subscriptionHandler.init();';
//}
