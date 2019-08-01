<?php

/*Ubuntu below*/
require __DIR__ . '/vendor/autoload.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'classes/db.php';
include 'classes/SendNotification.php';
$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = '';
//TODO
//$dbpass = 'Kj$gX%2f2019_2020';
$dbname = 'moon';

$db = new db($dbhost, $dbuser, $dbpass, $dbname);

$results = $db->query('SELECT endpointId, dateToSend, click_url, title, icon_url, image_url, message, public_key, auth_token, sent_id, domain_id
                    FROM notifications_queue WHERE dateToSend <= NOW()')->fetchAll();

foreach($results as $res){
    SendNotification::sendNotification($res['endpointId'], $res['public_key'], $res['auth_token'],  $res['message'], $res['title'],  $res['icon_url'],  $res['image_url'],  $res['click_url'], $res['sent_id'],$res['domain_id'], $db);
}





