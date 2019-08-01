<?php
require __DIR__ . '/../vendor/autoload.php';
use SecureEnvPHP\SecureEnvPHP;
(new SecureEnvPHP())->parse('../../.env.enc', '../.env.key');
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/

include '../classes/db.php';
$dbhost = getenv('DB_HOST');
$dbuser = getenv('DB_USER');
$dbpass = getenv('DB_PASS');
//TODO
//$password = 'Kj$gX%2f2019_2020';
$dbname = getenv('DB_NAME');

//$params = json_decode($_POST['json']);


switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':

        break;
    case 'POST':
        header('Access-Control-Allow-Origin: *');

        //header('Content-type: application/json');
        $db = new db($dbhost, $dbuser, $dbpass, $dbname);

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

        foreach($domainIds as $domainId){
            //find endpoints for subdomains selected on test_page.php
            $endpointarr = $db->query('SELECT user_info.endpoint, user_info.publicKey, user_info.authToken, user_info_domainId.user_info_id, user_info_domainId.domainId
                                            FROM user_info
                                            INNER JOIN user_info_domainId
                                            ON user_info.id = user_info_domainId.user_info_id 
                                            WHERE user_info_domainId.domainId = ?', $domainId)->fetchAll();

            foreach($endpointarr as $eps){

                //add to queue
                $res = $db->query('INSERT INTO  notifications_queue (endpointId, auth_token, public_key, dateToSend, click_url, title, icon_url, image_url, message, sent_id,domain_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?)',
                    $eps['endpoint'], $eps['authToken'], $eps['publicKey'], $date, $click_url, $title,  $icon_url, $image_url, $message,  $sent_id , $domainId);

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




