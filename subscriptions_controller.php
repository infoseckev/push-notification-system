<?php
require __DIR__ . '/vendor/autoload.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'db.php';
//include 'SendNotification.php';
$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = 'Kj$gX%2f2019_2020';
$dbname = 'moon';

$params = json_decode($_POST['json']);


switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        header('Content-type: application/json');
        echo json_encode($endpoints);
        break;




    case 'POST':
        $subscription = json_decode(file_get_contents('php://input'), true);
        $browser = $subscription['browser'];
        if (!isset($subscription['endpoint'])) {
            echo 'Error: not a subscription';
            return;
        }
        $useragent = $browser['useragent'];
        $site = $browser['site'];
        $ip = $browser['ip'];
        $osname = $browser['osname'];
        $osversion = $browser['osversion'];
        $browsername = $browser['browsername'];
        $browserversion = $browser['browserversion'];
        $appversion = $browser['appversion'];
        $platform = $browser['platform'];
        $vendor = $browser['vendor'];
        $endpoint = $subscription['endpoint'];
        $contentEncoding = $subscription['contentEncoding'];
        //$expirationTime = $subscription['endpoint'];
        $publicKey = $subscription['publicKey']; //$browser['p256dh'];
        $authToken = $subscription['authToken']; //$browser['auth'];
        $result = $db->query('INSERT INTO user_info (
                                `useragent`, 
                                `visitingsite`, 
                                `ip`,
                                `osname`,
                                `osversion`, 
                                `browsername`, 
                                `browserversion`, 
                                `appversion`,
                                `platform`,
                                `vendor`,
                                `endpoint`,
                                `contentEncoding`,
                                `publicKey`,
                                `authToken`)
                                VALUES (?,?,?,?,?,?,?,?,?,?, ?, ?,?, ?)', $useragent,$site, $ip, $osname, $osversion, $browsername, $browserversion,
            $appversion, $platform, $vendor, $endpoint, $contentEncoding, $publicKey, $authToken);

        echo $result->affectedRows();

        break;
    case 'PUT':

        break;
    case 'DELETE':

        break;
    default:

        break;
}
/*header($response['status_code_header']);
if ($response['body']) {
    echo $response['body'];
}*/




