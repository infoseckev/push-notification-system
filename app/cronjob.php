<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require __DIR__ . '/vendor/autoload.php';
use SecureEnvPHP\SecureEnvPHP;
(new SecureEnvPHP())->parse('../.env.enc', 'keys/.env.key');
include 'classes/db.php';
include 'classes/SendNotification.php';

$dbhost = getenv('DB_HOST');
$dbuser = getenv('DB_USER');
$dbpass = getenv('DB_PASS');

$dbname = getenv('DB_NAME');
$db = new db($dbhost, $dbuser, $dbpass, $dbname);

$conn = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
$results = $db->query('SELECT id, endpointId, dateToSend, click_url, title, icon_url, image_url, message, public_key, auth_token, sent_id, domain_id
                    FROM notifications_queue WHERE dateToSend <= NOW()')->fetchAll();

//you left off troubleshooting this
foreach($results as $res){


    SendNotification::sendNotification($res['endpointId'], $res['public_key'], $res['auth_token'],  $res['message'], $res['title'],  $res['icon_url'],  $res['image_url'],  $res['click_url'], $res['sent_id'],$res['domain_id'], $db, $res['id']);
    $stmt = $conn->prepare("DELETE FROM notifications_queue WHERE `id` = ?");
    $stmt->bind_param("i",$res['id']);
    $stmt->execute();
}

$db->close();



