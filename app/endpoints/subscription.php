<?php
require __DIR__ . '/../vendor/autoload.php';
use SecureEnvPHP\SecureEnvPHP;
(new SecureEnvPHP())->parse('../../.env.enc', '../.env.key');
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/

header('Access-Control-Allow-Origin: *');

////////////////////////////////////////////////////
$subscription = json_decode(file_get_contents('php://input'), true);
$browser = $subscription['browser'];
if (!isset($subscription['endpoint'])) {
    //echo 'Error: not a subscription';
    return;
}
///////////////////////////////////////////////////
$method = $_SERVER['REQUEST_METHOD'];
switch ($method) {
    case 'POST':


        $servername = getenv('DB_HOST');
        $username = getenv('DB_USER');
        $password = getenv('DB_PASS');
        //TODO
        //$password = 'Kj$gX%2f2019_2020';
        $dbname = getenv('DB_NAME');

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

        $stmt = $conn->prepare("SELECT LAST_INSERT_ID();");

        $stmt->execute();
        $result = $stmt->get_result();
        $userId = 0;
        while ($row = $result->fetch_assoc()) {
            $userId = $row['LAST_INSERT_ID()'];
            break;
        }
        //var_dump($userId);
        /////////////////////////
        ///
        $stmt = $conn->prepare("INSERT INTO domains (`domain_name`) VALUES (?)
  ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id)");

        $stmt->bind_param("s",$domain_name);

        $domain_name = $browser['site'];
        $res = $stmt->execute();


        $stmt = $conn->prepare("SELECT LAST_INSERT_ID();");

        $stmt->execute();
        $result = $stmt->get_result();
        $domainId = 0;
        while ($row = $result->fetch_assoc()) {
            //var_dump($row);
            $domainId = $row['LAST_INSERT_ID()'];
            break;
        }
echo $domainId . "    ";
        echo $userId . "    ";
        echo "browser : " . $browser['site'];
        /////////////////////////////
        ///
       $stmt = $conn->prepare("INSERT INTO user_info_domainId (`user_info_id`, `domainId`, `domain_name`) VALUES (?, ?, ?)");

        $stmt->bind_param("sss",$var1, $var2, $var3);
        $var1 = $userId;
        $var2 = $domainId;
        $var3 = $domain_name;
        $res = $stmt->execute();


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
