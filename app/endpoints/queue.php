<?php
require __DIR__ . '/../vendor/autoload.php';
use SecureEnvPHP\SecureEnvPHP;
(new SecureEnvPHP())->parse('../../.env.enc', '../keys/.env.key');
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/

include '../classes/db.php';
$dbhost = getenv('DB_HOST');
$dbuser = getenv('DB_USER');
$dbpass = getenv('DB_PASS');

$dbname = getenv('DB_NAME');

//$params = json_decode($_POST['json']);


switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':

        break;
    case 'POST':
        header('Access-Control-Allow-Origin: *');

        //header('Content-type: application/json');
        $db = new db($dbhost, $dbuser, $dbpass, $dbname);
        $conn = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
        $params =  json_decode(file_get_contents('php://input'), TRUE);
        $json = $params['json'];
        $message = $json['message'];
        $title = $json['title'];
        $image_url = $json['image_url'];
        $icon_url = $json['icon_url'];
        $click_url = $json['click_url'];
        $date = $json['date'];
        $date = date("Y-m-d H:i:s", strtotime($date));
        $domainIds = $json['domainIds'];

        $sent_id = sha1(time());

        $stmt = $conn->prepare("INSERT INTO  messages (click_url, title, icon_url, image_url, message) VALUES (?, ?, ?, ?,?)");
        $stmt->bind_param("sssss", $click_url, $title, $icon_url, $image_url, $message);
        $stmt->execute();
        $msgid = $stmt->insert_id;
        $stmt->close();

        foreach($domainIds as $domainId){
            //find endpoints for subdomains selected on test_page.php
            $endpointarr = $db->query('SELECT user_info.endpoint, user_info.publicKey, user_info.authToken, user_info.gps, user_info_domainId.user_info_id, user_info_domainId.domainId
                                            FROM user_info
                                            INNER JOIN user_info_domainId
                                            ON user_info.id = user_info_domainId.user_info_id 
                                            WHERE user_info_domainId.domainId = ?', $domainId)->fetchAll();

            foreach($endpointarr as $eps){

                try {

                    $stmt = $conn->prepare("INSERT INTO  notifications_queue (endpointId, auth_token, public_key, dateToSend,message_id, sent_id,domain_id) VALUES (?, ?, ?, ?, ?, ?,?)");
                    $stmt->bind_param("ssssisi", $eps['endpoint'], $eps['authToken'], $eps['publicKey'], $date, $msgid, $sent_id, $eps['domainId']);
                    $stmt->execute();
                    $stmt->close();
                }
                catch(Exception $e)
                {
                    echo  "<br>" . $e->getMessage();
                }
               /* $res = $db->query('INSERT INTO  notifications_queue (endpointId, auth_token, public_key, dateToSend,$message_id, sent_id,domain_id) VALUES (?, ?, ?, ?, ?, ?,?)',
                    $eps['endpoint'], $eps['authToken'], $eps['publicKey'], $date, $click_url."?GPS=".$eps['gps'], $title,  $icon_url, $image_url, $message,  $sent_id , $domainId);*/

            }

        }

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




