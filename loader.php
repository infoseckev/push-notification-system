<?php

require __DIR__ . '/app/vendor/autoload.php';
use SecureEnvPHP\SecureEnvPHP;
(new SecureEnvPHP())->parse('.env.enc', 'app/keys/.env.key');

$tracking_gps =  ($_GET['GPS']);

header('Service-Worker-Allowed: *');
header("Content-Type: application/javascript");

//init javascript for subscription
echo "var ip = \"". $_SERVER['REMOTE_ADDR'] . "\";\r\n";
echo "var gps = \"". $tracking_gps . "\";\r\n";
echo file_get_contents("app/lib/detector.js");
echo  file_get_contents("app/lib/subscriptionHandler.js");
echo 'subscriptionHandler.init();';

//insert load in db
$ip = '';
if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
    $ip = $_SERVER['HTTP_CLIENT_IP'];
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else {
    $ip = $_SERVER['REMOTE_ADDR'];
}
$domain_url = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST);
$dbhost = getenv('DB_HOST');
$dbuser = getenv('DB_USER');
$dbpass = getenv('DB_PASS');
$dbname = getenv('DB_NAME');

$conn = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
$stmt = $conn->prepare("INSERT INTO `moon`.`loader_stats`
(`domain_url`,
`ip`)
VALUES (?, ?)");

$stmt->bind_param("ss",$domain_url, $ip);
$stmt->execute();
//}
