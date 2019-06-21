<?php
require __DIR__ . '/vendor/autoload.php';
/*use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;*/
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include 'db.php';
include 'SendNotification.php';
$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = 'password';
$dbname = 'moon';
$params = json_decode($_POST['json']);
$msg = $params->msg;
$title = $params->title;
$endpointId = $params->endpointid;
$db = new db($dbhost, $dbuser, $dbpass, $dbname);

$endpoint = $db->query('SELECT endpoint, publicKey, authToken FROM user_info WHERE id = ?', $endpointId)->fetchArray();
var_dump($endpoint);
SendNotification::sendNotification($endpoint['endpoint'], $endpoint['publicKey'], $endpoint['authToken'], $msg);