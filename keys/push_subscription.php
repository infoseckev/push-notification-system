<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$subscription = json_decode(file_get_contents('php://input'), true);
$browser = $subscription['browser'];
if (!isset($subscription['endpoint'])) {
    echo 'Error: not a subscription';
    return;
}

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'POST':

        $servername = "localhost";
        $username = "root";
        $password = 'Kj$gX%2f2019_2020';
        //$password = '';
        $dbname = "moon";

        $conn = new mysqli($servername, $username, $password, $dbname);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $stmt = $conn->prepare("INSERT INTO user_info (
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
                                VALUES (?,?,?,?,?,?,?,?,?,?, ?, ?,?, ?)");
        if ($stmt === false) {
            echo $conn->error;
        } else {
            $stmt->bind_param("ssssssssssssss",$useragent,$site, $ip, $osname, $osversion, $browsername, $browserversion,
                $appversion, $platform, $vendor, $endpoint, $contentEncoding, $publicKey, $authToken);

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

            $stmt->execute();
        }

        $stmt->close();
        $conn->close();

        break;
    case 'PUT':
        // update the key and token of subscription corresponding to the endpoint
        break;
    case 'DELETE':
        // delete the subscription corresponding to the endpoint
        break;
    default:
        echo "Error: method not handled";
        return;
}
